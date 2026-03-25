<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthWebController extends Controller
{
    public function process(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'nik' => $credentials['username'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'));

        if (!$attempt) {
            return back()
                ->withInput($request->only('username'))
                ->with('warning', 'Username/NIK atau password salah.');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

