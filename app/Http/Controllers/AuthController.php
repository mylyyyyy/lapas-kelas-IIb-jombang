<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request)
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Cek status "Ingat Saya" (Remember Me)
        // name="remember" diambil dari input checkbox di view login
        $remember = $request->boolean('remember');

        // 3. Proses Login
        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Cek role setelah login berhasil
            $userRole = Auth::user()->role;

            // Treat any role containing 'admin' or explicit superadmin as admin-level
            $isAdmin = $userRole && (str_contains($userRole, 'admin') || in_array($userRole, ['superadmin', 'super_admin']));

            if ($isAdmin) {
                // Jika admin, always redirect to dashboard (do not honor intended to avoid redirecting to API/admin endpoints)
                return redirect()->route('dashboard');
            }

            // If there is an intended URL and it doesn't point to API or admin area, honor it
            $intended = $request->session()->get('url.intended');
            if ($intended) {
                $intendedPath = parse_url($intended, PHP_URL_PATH) ?? $intended;
                if (!str_starts_with($intendedPath, '/api') && !str_contains($intendedPath, '/admin/')) {
                    return redirect()->intended();
                }
                // Otherwise clear the intended so we don't redirect non-admins to admin routes
                $request->session()->forget('url.intended');
            }

            // Default: for non-admin, redirect to user area (kunjungan riwayat) if available, otherwise fallback to '/'
            if (\Illuminate\Support\Facades\Route::has('kunjungan.riwayat')) {
                return redirect()->route('kunjungan.riwayat');
            }

            return redirect('/');
        }

        // 4. Jika login gagal, kembalikan ke halaman login dengan error
        return back()->withErrors([
            'email' => 'Email atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

