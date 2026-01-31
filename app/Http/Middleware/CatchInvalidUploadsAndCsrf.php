<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CatchInvalidUploadsAndCsrf
{
    /**
     * Handle an incoming request.
     * Catch large uploads and CSRF token mismatch and return a friendly redirect.
     */
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (PostTooLargeException $e) {
            Log::warning('PostTooLargeException caught while processing request: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Ukuran unggahan terlalu besar. Maksimum 2MB per file. Mohon kurangi ukuran file atau kompres gambar dan coba lagi.');
        } catch (TokenMismatchException $e) {
            Log::warning('TokenMismatchException (possible session expired or missing POST data): ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Sesi Anda telah berakhir atau permintaan hilang (biasanya karena file terlalu besar). Silakan coba lagi. Jika mengunggah file, pastikan ukurannya tidak melebihi 2MB.');
        }
    }
}
