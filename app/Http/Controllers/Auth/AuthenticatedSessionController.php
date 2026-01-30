<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

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

        // Default: redirect to dashboard for consistency with tests and app behavior
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
