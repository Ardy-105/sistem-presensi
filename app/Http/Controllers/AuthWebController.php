<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthWebController extends Controller
{
    public function process(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $attempt =
            Auth::attempt([
                'nik' => $credentials['username'],
                'password' => $credentials['password'],
            ], $request->boolean('remember')) ||
            Auth::attempt([
                'email' => $credentials['username'],
                'password' => $credentials['password'],
            ], $request->boolean('remember'));

        if (!$attempt) {
            return back()
                ->withInput($request->only('username'))
                ->with('warning', 'Username/NIK atau password salah.');
        }

        $request->session()->regenerate();

        if (Auth::user()?->role !== 'admin') {
            Auth::logout();
            throw ValidationException::withMessages([
                'username' => 'Akun ini tidak memiliki akses ke halaman admin.',
            ]);
        }

        return redirect()->intended(route('admin.dashboard'))
            ->with('success', 'Login berhasil. Selamat datang!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

