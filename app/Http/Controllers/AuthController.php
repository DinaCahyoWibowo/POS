<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
        public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Please enter your username or email.',
            'password.required' => 'Please enter your password.',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        $throttleKey = Str::lower($login) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'login' => "Too many login attempts. Try again in {$seconds} seconds."
            ])->onlyInput('login');
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Allow forcing which database to try first (e.g. demo buttons)
        $forceMode = $request->input('force_mode'); // expected 'demo' or 'live'

        $tryOrder = [];
        if ($forceMode === 'demo') {
            $tryOrder = ['demo', 'mysql'];
        } elseif ($forceMode === 'live') {
            $tryOrder = ['mysql', 'demo'];
        } else {
            // default behavior: try live then demo
            $tryOrder = ['mysql', 'demo'];
        }

        // Explicitly check users on each connection to avoid accidental cross-authentication
        foreach ($tryOrder as $db) {
            // query the users table on the specific connection
            try {
                $row = DB::connection($db)->table('users')->where($field, $login)->first();
            } catch (\Exception $e) {
                $row = null;
            }

            if (!$row) {
                continue;
            }

            // verify password hash from that connection
            if (!Hash::check($password, $row->password)) {
                continue;
            }

            // Switch default connection to the one we authenticated against
            if ($db === 'mysql') {
                session(['app_mode' => 'live']);
                Config::set('database.default', 'mysql');
                DB::purge('mysql');
                DB::reconnect('mysql');
            } else {
                session(['app_mode' => 'demo']);
                Config::set('database.default', 'demo');
                DB::purge('mysql'); // clear any leftover mysql connection
                DB::purge('demo');
                DB::reconnect('demo');
            }

            // Load the Eloquent user via the (now) default connection and log in
            $userModel = \App\Models\User::find($row->id);
            if ($userModel) {
                Auth::login($userModel, $remember);
                // ensure the app_mode is persisted and session is saved before redirect
                $mode = $db === 'mysql' ? 'live' : 'demo';
                $request->session()->put('app_mode', $mode);
                // also set a cookie so concurrent requests will carry the intended mode
                Cookie::queue(Cookie::make('app_mode', $mode, 60));
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();
                $request->session()->save();
                // redirect to the intended URL (rely on session+cookie for app_mode)
                $intended = session()->pull('url.intended', route('dashboard'));
                return redirect()->to($intended);
            }
            // If we couldn't load via Eloquent (unexpected), fall back to attempt with Auth facade
            if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
                // fallback path: ensure app_mode persisted
                $mode = $db === 'mysql' ? 'live' : 'demo';
                $request->session()->put('app_mode', $mode);
                Cookie::queue(Cookie::make('app_mode', $mode, 60));
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();
                $request->session()->save();
                $intended = session()->pull('url.intended', route('dashboard'));
                return redirect()->to($intended);
            }
        }

        // =========================
        // 3️⃣ FAILED LOGIN
        // =========================
        RateLimiter::hit($throttleKey, 60);

        Config::set('database.default', 'mysql');

        return back()->withErrors([
            'login' => 'These credentials do not match our records.'
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('app_mode');
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
