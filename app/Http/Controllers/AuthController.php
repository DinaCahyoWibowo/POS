<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

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

        // =========================
        // 1️⃣ TRY LIVE DATABASE
        // =========================
        session(['app_mode' => 'live']);

        Config::set('database.default', 'mysql');
        DB::purge('mysql');
        DB::reconnect('mysql'); // 🔥 IMPORTANT

        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            dd(DB::connection()->getDatabaseName());
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        // =========================
        // 2️⃣ TRY DEMO DATABASE
        // =========================
        session(['app_mode' => 'demo']);

        Config::set('database.default', 'demo');
        DB::purge('mysql');   // 🔥 clear old connection too
        DB::purge('demo');
        DB::reconnect('demo'); // 🔥 IMPORTANT

        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            dd(DB::connection()->getDatabaseName());
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
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
