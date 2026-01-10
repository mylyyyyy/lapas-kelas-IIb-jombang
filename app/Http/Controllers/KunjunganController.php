<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\KunjunganPending;
use App\Mail\KunjunganRejected;
use App\Mail\KunjunganApproved;
use App\Models\Kunjungan;
use App\Models\Wbp;
use App\Models\Pengikut;
use Illuminate\Http\Request;
use App\Mail\KunjunganConfirmationMail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator; // <--- POSISI BENAR DI SINI

class KunjunganController extends Controller
{
    public function create()
    {
        $datesByDay = ['Senin' => [], 'Selasa' => [], 'Rabu' => [], 'Kamis' => []];
        $date = Carbon::today();
        $dayMapping = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis'];

        for ($i = 0; $i < 60; $i++) {
            $currentDate = $date->copy()->addDays($i);
            $dayOfWeek = $currentDate->dayOfWeek;
            if (array_key_exists($dayOfWeek, $dayMapping)) {
                $dayName = $dayMapping[$dayOfWeek];
                if (count($datesByDay[$dayName]) < 4) {
                    $datesByDay[$dayName][] = [
                        'value' => $currentDate->format('Y-m-d'),
                        'label' => $currentDate->translatedFormat('d F Y'),
                    ];
                }
            }
        }
        return view('guest.kunjungan.create', ['datesByDay' => $datesByDay]);
    }

    public function searchWbp(Request $request)
    {
        $search = $request->get('q');
        $wbps = Wbp::query()
            ->where('nama', 'LIKE', "%{$search}%")
            ->orWhere('no_registrasi', 'LIKE', "%{$search}%")
            ->limit(10)->get();

        $results = $wbps->map(function ($wbp) {
            return [
                'id' => $wbp->id,
                'nama' => $wbp->nama,
                'no_registrasi' => $wbp->no_registrasi,
                'blok' => $wbp->blok ?? '-',
                'kamar' => $wbp->kamar ?? '-'
            ];
        });
        return response()->json($results);
    }

    public function store(Request $request)
    {
        // ====================================================
        // 1. DYNAMIC & CONDITIONAL VALIDATION
        // ====================================================
        
        $rules = [
            'nama_pengunjung'   => 'required|string|max:255',
            'nik_ktp'           => 'required|numeric|digits:16',
            'nomor_hp'          => 'required|string',
            'email_pengunjung'  => 'required|email',
            'alamat_lengkap'    => 'required|string',
            'barang_bawaan'     => 'nullable|string',
            'jenis_kelamin'     => 'required|in:Laki-laki,Perempuan',
            'foto_ktp'          => 'required|image|max:5000',
            'wbp_id'            => 'required|exists:wbps,id',
            'hubungan'          => 'required|string',
            'tanggal_kunjungan' => 'required|date',
        ];

        if ($request->has('tanggal_kunjungan')) {
            try {
                $date = Carbon::parse($request->tanggal_kunjungan);
                if ($date->isMonday()) {
                    $rules['sesi'] = 'required|in:pagi,siang';
                } else {
                    $request->merge(['sesi' => 'pagi']);
                }
            } catch (\Exception $e) {
                // Let main validation handle format errors
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();

        // ====================================================
        // 2. BUSINESS LOGIC VALIDATION
        // ====================================================
        $requestDate = Carbon::parse($validatedData['tanggal_kunjungan'])->startOfDay();
        $today       = Carbon::now()->startOfDay();
        $wbp         = Wbp::find($validatedData['wbp_id']);

        if ($requestDate->isFriday() || $requestDate->isSaturday() || $requestDate->isSunday()) {
            return back()->with('error', 'Layanan kunjungan tidak tersedia pada hari Jumat, Sabtu, dan Minggu.')->withInput();
        }

        if ($requestDate->isMonday()) {
            if (!($today->isFriday() || $today->isSaturday() || $today->isSunday())) {
                return back()->with('error', 'Pendaftaran untuk hari Senin hanya dibuka pada hari Jumat, Sabtu, dan Minggu sebelumnya.')->withInput();
            }
            if ($today->diffInDays($requestDate, false) < 1) {
                 return back()->with('error', 'Pendaftaran untuk hari Senin minimal dilakukan pada hari Minggu.')->withInput();
            }
        } else {
            if ($today->diffInDays($requestDate, false) !== 1) {
                return back()->with('error', 'Pendaftaran kunjungan wajib dilakukan H-1 (satu hari sebelum jadwal kunjungan).')->withInput();
            }
        }

        $isSeninRabu = $requestDate->isMonday() || $requestDate->isWednesday();
        $isSelasaKamis = $requestDate->isTuesday() || $requestDate->isThursday();

        if ($isSeninRabu && !Str::startsWith($wbp->no_registrasi, 'B')) {
            return back()->with('error', "Jadwal hari Senin & Rabu hanya untuk Narapidana (Kode Registrasi B). WBP yang Anda pilih tidak termasuk dalam kategori ini.")->withInput();
        }
        if ($isSelasaKamis && !Str::startsWith($wbp->no_registrasi, 'A')) {
            return back()->with('error', "Jadwal hari Selasa & Kamis hanya untuk Tahanan (Kode Registrasi A). WBP yang Anda pilih tidak termasuk dalam kategori ini.")->withInput();
        }

        $oneWeekAgo = $requestDate->copy()->subWeek();
        $recentVisit = Kunjungan::where('wbp_id', $validatedData['wbp_id'])
            ->where('status', 'approved')
            ->where('tanggal_kunjungan', '>=', $oneWeekAgo->format('Y-m-d'))
            ->orderBy('tanggal_kunjungan', 'desc')
            ->first();
            
        if ($recentVisit) {
            $nextAllowedDate = Carbon::parse($recentVisit->tanggal_kunjungan)->addWeek()->translatedFormat('l, d F Y');
            return back()->with('error', "WBP ini sudah menerima kunjungan pada " . Carbon::parse($recentVisit->tanggal_kunjungan)->translatedFormat('d M Y') . ". WBP baru bisa dikunjungi lagi setelah 1 minggu, yaitu mulai " . $nextAllowedDate)->withInput();
        }

        // ====================================================
        // 3. DATABASE TRANSACTION
        // ====================================================
        try {
            DB::beginTransaction();

            $pathFotoUtama = $request->file('foto_ktp')->store('uploads/ktp', 'public');
            $nomorAntrian = Kunjungan::where('tanggal_kunjungan', $validatedData['tanggal_kunjungan'])->count() + 1;
            
            // Re-add non-validated but necessary fields from the request
            $fullData = array_merge($validatedData, [
                'no_wa_pengunjung'  => $request->nomor_hp,
                'email_pengunjung'  => $request->email_pengunjung,
                'alamat_pengunjung' => $request->alamat_lengkap,
            ]);

            $kunjungan = Kunjungan::create(array_merge($fullData, [
                'kode_kunjungan'       => 'VIS-' . strtoupper(Str::random(6)),
                'nomor_antrian_harian' => $nomorAntrian,
                'status'               => 'pending',
                'qr_token'             => Str::uuid(),
                'foto_ktp'             => $pathFotoUtama,
            ]));

            if ($request->has('pengikut_nama')) {
                foreach ($request->pengikut_nama as $index => $nama) {
                    if (!empty($nama)) {
                        Pengikut::create([
                            'kunjungan_id'  => $kunjungan->id,
                            'nama'          => $nama,
                            'nik'           => $request->pengikut_nik[$index] ?? null,
                            'hubungan'      => $request->pengikut_hubungan[$index] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            try {
                Mail::to($validatedData['email_pengunjung'])->send(new KunjunganPending($kunjungan));
            } catch (\Exception $e) {
                \Log::error('Email sending failed for Kunjungan ID ' . $kunjungan->id . ': ' . $e->getMessage());
            }

            return redirect()->route('kunjungan.status', $kunjungan->id)
                ->with('success', "PENDAFTARAN BERHASIL! Nomor Antrian Anda: {$nomorAntrian}");

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error storing visit: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return back()->with('error', 'Terjadi kesalahan internal saat memproses pendaftaran. Silakan coba lagi.')->withInput();
        }
    }

    public function status(Kunjungan $kunjungan)
    {
        return view('guest.kunjungan.status', compact('kunjungan'));
    }

    public function getQuotaStatus(Request $request)
    {
        // (Isi API Quota - biarkan seperti kode sebelumnya)
        return response()->json(['sisa_kuota' => 100]);
    }
    /**
     * Menampilkan Halaman Cetak Tiket
     */
    public function printProof(Kunjungan $kunjungan)
    {
        // Pastikan hanya yang status approved yang bisa cetak (opsional)
        if ($kunjungan->status != 'approved') {
            return redirect()->route('kunjungan.status', $kunjungan->id)
                ->with('error', 'Tiket belum tersedia. Menunggu persetujuan admin.');
        }

        return view('guest.kunjungan.print', compact('kunjungan'));
    }
    public function updateStatus(Request $request, Kunjungan $kunjungan)
    {
        $statusBaru = $request->status; // 'approved' atau 'rejected'

        $kunjungan->update(['status' => $statusBaru]);

        // --- LOGIKA KIRIM EMAIL OTOMATIS ---
        try {
            if ($statusBaru == 'approved') {
                // Kirim Email Tiket
                // Mail::to($kunjungan->email_pengunjung)->send(new \App\Mail\KunjunganApproved($kunjungan));
            } elseif ($statusBaru == 'rejected') {
                // Kirim Email Penolakan
                // Mail::to($kunjungan->email_pengunjung)->send(new \App\Mail\KunjunganRejected($kunjungan));
            }
        } catch (\Exception $e) {
            // Log error email tapi biarkan status berubah
        }

        return back()->with('success', 'Status berhasil diperbarui!');
    }
    public function update(Request $request, $id)
    {
        $kunjungan = Kunjungan::findOrFail($id);
        $oldStatus = $kunjungan->status;

        // Update Status
        $kunjungan->status = $request->status;
        $kunjungan->save();

        // --- [BARU] LOGIKA KIRIM EMAIL OTOMATIS ---
        try {
            if ($kunjungan->email_pengunjung) {
                // 1. Jika berubah jadi APPROVED
                if ($request->status == 'approved' && $oldStatus != 'approved') {
                    Mail::to($kunjungan->email_pengunjung)->send(new KunjunganApproved($kunjungan));
                }
                // 2. Jika berubah jadi REJECTED
                elseif ($request->status == 'rejected' && $oldStatus != 'rejected') {
                    Mail::to($kunjungan->email_pengunjung)->send(new KunjunganRejected($kunjungan));
                }
            }
        } catch (\Exception $e) {
            // Abaikan error email agar admin tidak terganggu
        }
        // -------------------------------------------

        return back()->with('success', 'Status diperbarui & Notifikasi email dikirim.');
    }
    // 1. Menampilkan Halaman Awal (Scan/Input)
    public function verifikasiPage()
    {
        return view('admin.kunjungan.verifikasi');
    }

    // 2. Memproses Data Saat Tombol Ditekan
    public function verifikasiSubmit(Request $request)
    {
        // Ambil token dari input form
        $token = $request->qr_token;

        // Cari data berdasarkan qr_token (atau kode_kunjungan)
        $kunjungan = Kunjungan::with(['wbp', 'pengikuts']) // Load relasi biar lengkap
            ->where('qr_token', $token)
            ->orWhere('kode_kunjungan', $token) // Biar bisa cari pakai kode manual juga
            ->first();

        if ($kunjungan) {
            // JIKA KETEMU:
            // Kembalikan ke view yang sama, tapi bawa data 'status_verifikasi' = 'success'
            // dan bawa data '$kunjungan' nya.
            return view('admin.kunjungan.verifikasi', [
                'status_verifikasi' => 'success',
                'kunjungan' => $kunjungan
            ]);
        } else {
            // JIKA TIDAK KETEMU:
            // Kembalikan ke view yang sama, status 'failed'
            return view('admin.kunjungan.verifikasi', [
                'status_verifikasi' => 'failed',
                'qr_token' => $token
            ]);
        }
    }
}
