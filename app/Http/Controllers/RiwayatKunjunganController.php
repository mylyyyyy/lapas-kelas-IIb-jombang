<?php

namespace App\Http\Controllers;

use App\Models\Kunjungan;
use Illuminate\Http\Request;

class RiwayatKunjunganController extends Controller
{
    public function __invoke()
    {
        $kunjungans = Kunjungan::where('email_pengunjung', auth()->user()->email)
            ->orderBy('tanggal_kunjungan', 'desc')
            ->paginate(10);

        return view('guest.kunjungan.riwayat', compact('kunjungans'));
    }
}
