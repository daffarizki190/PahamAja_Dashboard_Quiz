<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;

class AdminAuthController extends Controller
{
    public function show()
    {
        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $configured = trim((string) (config('admin.password') ?? ''));

        if ($configured === '') {
            return back()->with('error', 'ADMIN_PASSWORD belum dikonfigurasi di Environment Variable Vercel.')->withInput();
        }

        $provided = trim((string) $request->input('password'));

        $ok = false;

        // Support both hashed (bcrypt/argon) and plain text passwords
        if (str_starts_with($configured, '$2y$') || str_starts_with($configured, '$2a$') || str_starts_with($configured, '$argon2')) {
            $ok = Hash::check($provided, $configured);
        } else {
            $ok = hash_equals($configured, $provided);
        }

        if (! $ok) {
            return back()->with('error', 'Password admin salah. Pastikan ADMIN_PASSWORD di Vercel Dashboard sudah benar (saat ini terisi '.strlen($configured).' karakter).')->withInput();
        }

        if ($request->hasSession()) {
            $request->session()->put('admin.authenticated', true);
            $request->session()->regenerate();
        }

        Cookie::queue('admin_auth', '1', 60 * 24 * 30);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->forget('admin.authenticated');
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        Cookie::queue(Cookie::forget('admin_auth'));

        return redirect()->route('admin.login');
    }
}
