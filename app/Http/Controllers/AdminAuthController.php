<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

        $adminPass = trim((string) (config('admin.password') ?? ''));
        $devPass = trim((string) (config('dev.password') ?? ''));

        if ($adminPass === '') {
            return back()->with('error', 'ADMIN_PASSWORD belum dikonfigurasi.')->withInput();
        }

        $provided = trim((string) $request->input('password'));

        // 🟢 Check Admin Password
        $isAdminOk = false;
        if (str_starts_with($adminPass, '$2y$') || str_starts_with($adminPass, '$2a$') || str_starts_with($adminPass, '$argon2')) {
            $isAdminOk = Hash::check($provided, $adminPass);
        } else {
            $isAdminOk = hash_equals($adminPass, $provided);
        }

        if ($isAdminOk) {
            if ($request->hasSession()) {
                $request->session()->put('admin.authenticated', true);
                $request->session()->regenerate();
            }

            return redirect()->route('admin.dashboard');
        }

        // 🔵 Check Dev Password
        $isDevOk = false;
        if ($devPass !== '') {
            if (str_starts_with($devPass, '$2y$') || str_starts_with($devPass, '$argon2')) {
                $isDevOk = Hash::check($provided, $devPass);
            } else {
                $isDevOk = hash_equals($devPass, $provided);
            }
        }

        if ($isDevOk) {
            if ($request->hasSession()) {
                $request->session()->put('dev.authenticated', true);
                $request->session()->regenerate();
            }

            return redirect()->route('dev.health');
        }

        // 🔴 Fail
        return back()->with('error', 'Password salah. Pastikan password Admin atau Dev yang Anda masukkan benar.')->withInput();
    }

    public function logout(Request $request)
    {
        if ($request->hasSession()) {
            $request->session()->forget('admin.authenticated');
            $request->session()->forget('dev.authenticated');
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('admin.login');
    }
}
