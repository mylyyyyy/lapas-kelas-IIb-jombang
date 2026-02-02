<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     * Tambahkan path 'kunjungan/daftar' untuk bypass darurat.
     * Contoh sementara (untuk debugging):
     * protected $except = [
     *     'kunjungan/daftar',
     * ];
     */
    protected $except = [
        // 'kunjungan/daftar',
    ];
}
