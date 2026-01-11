<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use App\Mail\KunjunganStatusMail;
use App\Models\Kunjungan;
use App\Models\Wbp;
use App\Models\Pengikut;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\File;

class KunjunganController extends Controller
{
    // =========================================================================
    // HALAMAN PUBLIK (GUEST)
    // =========================================================================

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
        // 0. PRE-PROCESSING: Set Sesi Otomatis
        if ($request->has('tanggal_kunjungan')) {
            try {
                $date = Carbon::parse($request->tanggal_kunjungan);
                if (!$date->isMonday()) {
                    $request->merge(['sesi' => 'pagi']);
                }
            } catch (\Exception $e) {
            }
        }

        // 1. VALIDASI INPUT
        $rules = [
            'nama_pengunjung'               => 'required|string|max:255',
            'nik_ktp'                       => 'required|numeric|digits:16',
            'nomor_hp'                      => 'required|string',
            'email_pengunjung'              => 'required|email',
            'alamat_lengkap'                => 'required|string',
            'barang_bawaan'                 => 'nullable|string',
            'jenis_kelamin'                 => 'required|in:Laki-laki,Perempuan',
            'foto_ktp'                      => 'required|image|max:5000',
            'wbp_id'                        => 'required|exists:wbps,id',
            'hubungan'                      => 'required|string',
            'tanggal_kunjungan'             => 'required|date',
            'preferred_notification_channel' => 'required|in:email,whatsapp',
            'sesi'                          => 'required',
            // Validasi Maksimal 4 Pengikut
            'pengikut_nama'                 => 'nullable|array|max:4',
            'pengikut_nik'                  => 'nullable|array|max:4',
            'pengikut_hubungan'             => 'nullable|array|max:4',
            'pengikut_foto'                 => 'nullable|array|max:4',
            'pengikut_foto.*'               => 'nullable|image|max:5000',
        ];

        $messages = [
            'pengikut_nama.max' => 'Jumlah pengikut tidak boleh lebih dari 4 orang.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        $validatedData = $validator->validated();

        // 2. LOGIKA VALIDASI BISNIS
        $requestDate = Carbon::parse($validatedData['tanggal_kunjungan'])->startOfDay();
        $today = Carbon::now()->startOfDay();
        $wbp = Wbp::find($validatedData['wbp_id']);

        // A. Cek Hari Libur (Jumat-Minggu)
        if ($requestDate->isFriday() || $requestDate->isSaturday() || $requestDate->isSunday()) {
            return back()->with('error', 'Layanan kunjungan TUTUP pada hari Jumat, Sabtu, dan Minggu.')->withInput();
        }

        // B. Cek Aturan H-1 & Senin
        if ($requestDate->isMonday()) {
            // Senin: Boleh daftar Jumat, Sabtu, Minggu
            if (!($today->isFriday() || $today->isSaturday() || $today->isSunday())) {
                return back()->with('error', 'Pendaftaran untuk hari Senin hanya dibuka pada hari Jumat, Sabtu, dan Minggu sebelumnya.')->withInput();
            }
            // Pastikan Senin yang dipilih adalah Senin terdekat (masa depan)
            if ($today->diffInDays($requestDate, false) < 1 || $today->diffInDays($requestDate, false) > 3) {
                return back()->with('error', 'Tanggal Senin tidak valid. Pilih Senin terdekat.')->withInput();
            }
        } else {
            // Selasa-Kamis: Wajib H-1
            if ($today->diffInDays($requestDate, false) !== 1) {
                return back()->with('error', 'Pendaftaran kunjungan wajib dilakukan H-1 (satu hari sebelum jadwal kunjungan).')->withInput();
            }
        }

        // C. Cek Tipe WBP (A vs B)
        $kodeReg = strtoupper(substr($wbp->no_registrasi, 0, 1));
        $hariKunjungan = $requestDate->dayOfWeek; // 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis

        if ($kodeReg === 'A') {
            // Tahanan: Hanya Selasa (2) & Kamis (4)
            if (!in_array($hariKunjungan, [2, 4])) {
                return back()->with('error', "WBP ini berstatus TAHANAN (Kode A). Jadwal kunjungan hanya SELASA dan KAMIS.")->withInput();
            }
        } elseif ($kodeReg === 'B') {
            // Narapidana: Hanya Senin (1) & Rabu (3)
            if (!in_array($hariKunjungan, [1, 3])) {
                return back()->with('error', "WBP ini berstatus NARAPIDANA (Kode B). Jadwal kunjungan hanya SENIN dan RABU.")->withInput();
            }
        }

        // D. Kunci WBP 1 Minggu (Locking)
        $startWindow = $requestDate->copy()->subDays(6);
        $recentVisit = Kunjungan::where('wbp_id', $validatedData['wbp_id'])
            ->whereIn('status', ['pending', 'approved'])
            ->whereBetween('tanggal_kunjungan', [$startWindow->format('Y-m-d'), $requestDate->format('Y-m-d')])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->first();

        if ($recentVisit) {
            $lastDate = Carbon::parse($recentVisit->tanggal_kunjungan)->translatedFormat('d M Y');
            return back()->with('error', "WBP ini sudah terdaftar kunjungan pada tanggal $lastDate. Sesuai aturan, WBP hanya boleh dikunjungi 1x dalam seminggu.")->withInput();
        }

        // E. (FIX) CEK NIK PENGUNJUNG GANDA DI TANGGAL SAMA
        // Ini memastikan satu orang (NIK) tidak bisa booking berkali-kali di hari yang sama
        $existingVisitor = Kunjungan::where('nik_ktp', $validatedData['nik_ktp'])
            ->whereDate('tanggal_kunjungan', $requestDate)
            ->whereIn('status', ['pending', 'approved']) // Cek yang statusnya aktif
            ->first();

        if ($existingVisitor) {
            return back()->with('error', "NIK Anda ({$validatedData['nik_ktp']}) sudah terdaftar untuk kunjungan pada tanggal ini. Mohon cek riwayat kunjungan Anda.")->withInput();
        }

        // 3. SIMPAN DATA
        try {
            DB::beginTransaction();

            $pathFotoUtama = $request->file('foto_ktp')->store('uploads/ktp', 'public');

            // --- FIX NOMOR ANTRIAN & RACE CONDITION ---
            // Gunakan lockForUpdate agar nomor antrian tidak bentrok saat akses bersamaan
            $lastQueue = Kunjungan::where('tanggal_kunjungan', $validatedData['tanggal_kunjungan'])
                ->where('sesi', $validatedData['sesi'])
                ->lockForUpdate() // Kunci DB sebentar
                ->orderBy('nomor_antrian_harian', 'desc')
                ->first();

            $nomorAntrian = $lastQueue ? $lastQueue->nomor_antrian_harian + 1 : 1;

            // Merge data tambahan
            $fullData = array_merge($validatedData, [
                'no_wa_pengunjung'  => $request->nomor_hp,
                'alamat_pengunjung' => $request->alamat_lengkap,
            ]);

            $kunjungan = Kunjungan::create(array_merge($fullData, [
                'kode_kunjungan'       => 'VIS-' . strtoupper(Str::random(6)),
                'nomor_antrian_harian' => $nomorAntrian,
                'status'               => 'pending',
                'qr_token'             => Str::uuid(),
                'foto_ktp'             => $pathFotoUtama,
            ]));

            // Simpan Pengikut
            if ($request->has('pengikut_nama')) {
                foreach ($request->pengikut_nama as $index => $nama) {
                    if (!empty($nama)) {
                        $pathFotoPengikut = null;
                        if ($request->hasFile("pengikut_foto.$index")) {
                            $pathFotoPengikut = $request->file("pengikut_foto")[$index]->store('uploads/pengikut', 'public');
                        }
                        Pengikut::create([
                            'kunjungan_id' => $kunjungan->id,
                            'nama' => $nama,
                            'nik' => $request->pengikut_nik[$index] ?? null,
                            'hubungan' => $request->pengikut_hubungan[$index] ?? null,
                            'barang_bawaan' => $request->pengikut_barang[$index] ?? null,
                            'foto_ktp' => $pathFotoPengikut
                        ]);
                    }
                }
            }

            // Generate QR Code (Gunakan Format SVG agar aman)
            $path = storage_path('app/public/qrcodes');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }

            $qrContent = QrCode::format('svg')->size(300)->generate($kunjungan->qr_token);
            $qrCodePath = 'qrcodes/' . $kunjungan->id . '.svg';
            Storage::disk('public')->put($qrCodePath, $qrContent);

            DB::commit();

            // Kirim Notifikasi Pending
            try {
                if ($kunjungan->preferred_notification_channel === 'whatsapp') {
                    // Logic WA Service
                    (new WhatsAppService())->sendPending($kunjungan, Storage::disk('public')->url($qrCodePath));
                } else {
                    // Logic Email
                    Mail::to($kunjungan->email_pengunjung)->send(new KunjunganStatusMail($kunjungan, null));
                }
            } catch (\Exception $e) {
                Log::error('Gagal kirim notifikasi awal: ' . $e->getMessage());
            }

            return redirect()->route('kunjungan.status', $kunjungan->id)
                ->with('success', "PENDAFTARAN BERHASIL! Anda mendapat antrian Nomor {$nomorAntrian} (Sesi " . ucfirst($validatedData['sesi']) . ").");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing visit: ' . $e->getMessage());

            // Tangkap error duplicate entry jika masih terjadi
            if (str_contains($e->getMessage(), 'Duplicate entry')) {
                return back()->with('error', 'Terjadi kepadatan antrian. Mohon klik tombol kirim sekali lagi.')->withInput();
            }

            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    public function status(Kunjungan $kunjungan)
    {
        return view('guest.kunjungan.status', compact('kunjungan'));
    }

    public function getQuotaStatus(Request $request)
    {
        // Logic kuota sederhana
        $validator = Validator::make($request->all(), [
            'tanggal_kunjungan' => 'required|date_format:Y-m-d',
            'sesi' => 'nullable|string',
        ]);

        if ($validator->fails()) return response()->json(['error' => 'Invalid'], 400);

        $validated = $validator->validated();
        $tanggal = Carbon::parse($validated['tanggal_kunjungan']);
        $sesi = $validated['sesi'] ?? 'pagi';

        $totalQuota = 150; // Default Selasa-Kamis
        if ($tanggal->isMonday()) {
            $totalQuota = ($sesi === 'pagi') ? 120 : 40;
        }

        // --- FIX KUOTA PER SESI ---
        $query = Kunjungan::where('tanggal_kunjungan', $tanggal->format('Y-m-d'))
            ->whereIn('status', ['pending', 'approved']);

        // Filter sesi jika hari Senin agar kuota Pagi & Siang tidak campur
        if ($tanggal->isMonday()) {
            $query->where('sesi', $sesi);
        }

        $registeredCount = $query->count();

        $sisaKuota = max(0, $totalQuota - $registeredCount);
        return response()->json(['sisa_kuota' => $sisaKuota]);
    }

    public function printProof(Kunjungan $kunjungan)
    {
        if ($kunjungan->status != 'approved') {
            return redirect()->route('kunjungan.status', $kunjungan->id)
                ->with('error', 'Tiket belum tersedia.');
        }
        return view('guest.kunjungan.print', compact('kunjungan'));
    }

    // UPDATE STATUS OLEH ADMIN
    public function update(Request $request, $id, WhatsAppService $whatsAppService)
    {
        $kunjungan = Kunjungan::findOrFail($id);
        $oldStatus = $kunjungan->status;
        $newStatus = $request->status;

        if ($oldStatus === $newStatus) return back();

        $kunjungan->status = $newStatus;
        $kunjungan->save();

        // Kirim Notifikasi Update
        try {
            // Gunakan SVG path yang sudah dibuat saat store
            $qrCodePath = 'qrcodes/' . $kunjungan->id . '.svg';

            if ($kunjungan->preferred_notification_channel === 'whatsapp') {
                if ($newStatus == 'approved') {
                    $whatsAppService->sendApproved($kunjungan, Storage::disk('public')->url($qrCodePath));
                } elseif ($newStatus == 'rejected') {
                    $whatsAppService->sendRejected($kunjungan);
                }
            } else {
                $fullQrPath = Storage::disk('public')->path($qrCodePath);
                Mail::to($kunjungan->email_pengunjung)->send(new KunjunganStatusMail($kunjungan, $fullQrPath));
            }
        } catch (\Exception $e) {
            Log::error('Gagal kirim notifikasi update: ' . $e->getMessage());
        }

        return back()->with('success', 'Status diperbarui & notifikasi dikirim.');
    }

    public function verifikasiPage()
    {
        return view('admin.kunjungan.verifikasi');
    }

    public function riwayat()
    {
        $kunjungans = Kunjungan::with('wbp')
            ->where('email_pengunjung', auth()->user()->email ?? '')
            ->orderBy('tanggal_kunjungan', 'desc')
            ->paginate(10);

        return view('guest.kunjungan.riwayat', compact('kunjungans'));
    }

    public function verifikasiSubmit(Request $request, WhatsAppService $whatsAppService)
    {
        $token = $request->qr_token;
        $kunjungan = Kunjungan::with(['wbp', 'pengikuts'])
            ->where('qr_token', $token)
            ->orWhere('kode_kunjungan', $token)
            ->first();

        if ($kunjungan) {
            $message = null;
            if ($kunjungan->status === 'pending') {
                $kunjungan->status = 'approved';
                $kunjungan->save();
                $message = 'Kunjungan otomatis DISETUJUI saat scan.';

                // Kirim notifikasi approval saat scan (Opsional)
                try {
                    if ($kunjungan->preferred_notification_channel === 'whatsapp') {
                        $whatsAppService->sendApproved($kunjungan, null);
                    } else {
                        Mail::to($kunjungan->email_pengunjung)->send(new KunjunganStatusMail($kunjungan, null));
                    }
                } catch (\Exception $e) {
                }
            }

            return view('admin.kunjungan.verifikasi', [
                'status_verifikasi' => 'success',
                'kunjungan' => $kunjungan,
                'approval_message' => $message
            ]);
        } else {
            return view('admin.kunjungan.verifikasi', [
                'status_verifikasi' => 'failed',
                'qr_token' => $token
            ]);
        }
    }
}
