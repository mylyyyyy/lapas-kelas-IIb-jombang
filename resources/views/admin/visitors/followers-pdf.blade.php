<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Database Pengikut – Lapas Kelas IIB Jombang</title>
</head>
<body>

@php
    $title    = 'Laporan Database Profil Pengikut';
    $subtitle = 'Data seluruh pengikut yang pernah menyertai kunjungan';
@endphp
@include('partials.kop_surat_pdf')

<table>
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th>NIK Pengikut</th>
            <th>Nama Pengikut</th>
            <th>Hubungan</th>
            <th>Pengunjung Utama</th>
            <th>Tujuan WBP</th>
            <th style="width:70px">Tgl Daftar</th>
        </tr>
    </thead>
    <tbody>
        @forelse($followers as $follower)
        <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td style="font-family:monospace; font-size:9px;">{{ $follower->nik ?: '—' }}</td>
            <td class="fw-bold">{{ strtoupper($follower->nama) }}</td>
            <td class="text-center">{{ $follower->hubungan ?: '—' }}</td>
            <td>{{ optional($follower->kunjungan->profilPengunjung)->nama ?: '—' }}</td>
            <td>{{ optional($follower->kunjungan->wbp)->nama ?: '—' }}</td>
            <td class="text-center" style="font-size:9px">{{ $follower->created_at ? $follower->created_at->format('d/m/Y') : '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="7" class="text-center" style="padding:20px;color:#94a3b8;font-style:italic;">Tidak ada data pengikut.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Ringkasan --}}
<div style="margin-top:14px; padding:8px 12px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:6px; font-size:10px; color:#475569;">
    <strong>Ringkasan:</strong> Total pengikut unik terdaftar: <strong>{{ $followers->count() }}</strong> orang.
</div>

{{-- Footer TTD --}}
<div class="doc-footer">
    <div class="footer-ttd">
        <div class="ttd-block">
            <p class="ttd-kota">Jombang, {{ now()->translatedFormat('d F Y') }}</p>
            <p>Kepala Lapas Kelas IIB Jombang</p>
            <div class="ttd-space"></div>
            <p class="ttd-nama">_____________________________</p>
            <p class="ttd-nip">NIP. ___________________________</p>
        </div>
    </div>
    <div class="footer-system">
        <span>Dokumen ini dicetak secara otomatis oleh Sistem Informasi Layanan Kunjungan Lapas Jombang.</span>
        <span>{{ now()->format('d/m/Y H:i') }} WIB</span>
    </div>
</div>

</body>
</html>
