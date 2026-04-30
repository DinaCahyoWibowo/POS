<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $mode = request()->query('app_mode') ?: request()->cookie('app_mode') ?: session('app_mode', 'live');

        try {
            $conn = $mode === 'demo' ? 'demo' : 'mysql';
            $user = \App\Models\User::on($conn)->find(Auth::id());
        } catch (\Exception $e) {
            $user = Auth::user();
        }

        return view('profile', ['user' => $user]);
    }

    public function update(Request $request)
    {
        // load the user from the correct connection similarly to show()
        $mode = request()->query('app_mode') ?: request()->cookie('app_mode') ?: session('app_mode', 'live');
        $conn = $mode === 'demo' ? 'demo' : 'mysql';
        try {
            $user = \App\Models\User::on($conn)->find($request->user()->id);
        } catch (\Exception $e) {
            $user = $request->user();
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        $user->email = $request->input('email');
        $user->save();

        return redirect()->route('profile')->with('success', 'Profile updated.');
    }

    public function updatePassword(Request $request)
    {
        $mode = request()->query('app_mode') ?: request()->cookie('app_mode') ?: session('app_mode', 'live');
        $conn = $mode === 'demo' ? 'demo' : 'mysql';
        try {
            $user = \App\Models\User::on($conn)->find($request->user()->id);
        } catch (\Exception $e) {
            $user = $request->user();
        }

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if (! Hash::check($request->input('current_password'), $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('profile')->with('success', 'Password updated.');
    }
}
