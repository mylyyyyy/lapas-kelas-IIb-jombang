<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekapitulasi Kunjungan – Lapas Kelas IIB Jombang</title>
</head>
<body>

@php
    $title    = 'Laporan Rekapitulasi Data Kunjungan';
    $subtitle = 'Ringkasan statistik kunjungan sistem';
@endphp
@include('partials.kop_surat_pdf')

{{-- 1. Statistik Gender --}}
<h3 style="margin-top: 20px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">I. Statistik Gender Pengunjung</h3>
<table style="width: 50%;">
    <thead>
        <tr>
            <th>Jenis Kelamin</th>
            <th>Total Pengunjung</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>Laki-laki</td>
            <td class="text-center">{{ $visitorGender['Laki-laki'] }} orang</td>
        </tr>
        <tr>
            <td>Perempuan</td>
            <td class="text-center">{{ $visitorGender['Perempuan'] }} orang</td>
        </tr>
        <tr style="background: #f1f5f9; font-weight: bold;">
            <td>TOTAL</td>
            <td class="text-center">{{ array_sum($visitorGender) }} orang</td>
        </tr>
    </tbody>
</table>

{{-- 2. WBP Paling Sering Dikunjungi --}}
<h3 style="margin-top: 25px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">II. WBP Paling Sering Dikunjungi (Top 10)</h3>
<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th>Nama WBP</th>
            <th>No. Registrasi</th>
            <th>Blok / Sel</th>
            <th style="width: 100px;">Total Kunjungan</th>
        </tr>
    </thead>
    <tbody>
        @forelse($mostVisitedWbp as $index => $wbp)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="fw-bold">{{ strtoupper($wbp->nama) }}</td>
            <td class="text-center">{{ $wbp->no_registrasi }}</td>
            <td class="text-center">{{ $wbp->blok ?? '-' }} / {{ $wbp->lokasi_sel ?? '-' }}</td>
            <td class="text-center fw-bold">{{ $wbp->visit_count }} kali</td>
        </tr>
        @empty
        <tr>
            <td colspan="5" class="text-center">Tidak ada data.</td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- 3. Sesi Kunjungan Teramai --}}
<h3 style="margin-top: 25px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">III. Sesi Kunjungan Teramai</h3>
<table style="width: 60%;">
    <thead>
        <tr>
            <th style="width: 40px;">No</th>
            <th>Hari & Sesi</th>
            <th style="width: 120px;">Jumlah Pendaftar</th>
        </tr>
    </thead>
    <tbody>
        @php $no = 1; @endphp
        @forelse($sessionCounts as $label => $count)
        <tr>
            <td class="text-center">{{ $no++ }}</td>
            <td>{{ $label }}</td>
            <td class="text-center">{{ $count }} orang</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">Tidak ada data.</td>
        </tr>
        @endforelse
    </tbody>
</table>

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
