<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Survei IKM – Lapas Kelas IIB Jombang</title>
</head>
<body>

@php
    $title    = 'Laporan Hasil Survei Kepuasan Masyarakat (IKM)';
    $subtitle = 'Hasil penilaian dan feedback layanan kunjungan';
@endphp
@include('partials.kop_surat_pdf')

{{-- 1. Ringkasan Skor --}}
<h3 style="margin-top: 20px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">I. Ringkasan Indeks Kepuasan</h3>
<table style="width: 100%;">
    <tr>
        <td style="width: 50%; padding: 10px; background: #f8fafc; text-align: center; border: 1px solid #e2e8f0;">
            <p style="margin: 0; font-size: 10px; color: #64748b; text-transform: uppercase;">Skor IKM (Konversi)</p>
            <h1 style="margin: 5px 0; color: #1e3a5f; font-size: 32px;">{{ round($averageRating / 4 * 100, 1) }}</h1>
            <p style="margin: 0; font-weight: bold; font-size: 12px; color: #10b981;">Mutu Pelayanan: A (Sangat Baik)</p>
        </td>
        <td style="padding: 10px; border: 1px solid #e2e8f0;">
            <table style="width: 100%; border: none;">
                <tr style="background: transparent;">
                    <td style="border: none; padding: 2px;">Total Responden</td>
                    <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ $stats->total }} orang</td>
                </tr>
                <tr style="background: transparent;">
                    <td style="border: none; padding: 2px;">Rata-rata Rating</td>
                    <td style="border: none; padding: 2px; text-align: right; font-weight: bold;">{{ number_format($averageRating, 2) }} / 4.00</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- 2. Distribusi Nilai --}}
<h3 style="margin-top: 25px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">II. Rincian Distribusi Penilaian</h3>
<table>
    <thead>
        <tr>
            <th>Kategori Penilaian</th>
            <th>Skor</th>
            <th>Jumlah Responden</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @php $rows = [
            ['Sangat Baik', 4, $stats->sangat_baik, '#10b981'],
            ['Baik',        3, $stats->baik,        '#3b82f6'],
            ['Cukup Baik',  2, $stats->cukup,       '#f59e0b'],
            ['Kurang Baik', 1, $stats->buruk,       '#ef4444'],
        ]; @endphp
        @foreach($rows as $r)
        <tr>
            <td>{{ $r[0] }}</td>
            <td class="text-center">{{ $r[1] }}</td>
            <td class="text-center">{{ $r[2] }} orang</td>
            <td class="text-center">{{ $stats->total > 0 ? round($r[2] / $stats->total * 100) : 0 }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- 3. Daftar Saran --}}
<h3 style="margin-top: 25px; color: #1e3a5f; border-left: 4px solid #1e3a5f; padding-left: 8px;">III. Daftar Kritik & Saran Pengunjung</h3>
<table>
    <thead>
        <tr>
            <th style="width: 30px;">No</th>
            <th style="width: 80px;">Rating</th>
            <th>Isi Saran / Feedback</th>
            <th style="width: 100px;">Waktu</th>
        </tr>
    </thead>
    <tbody>
        @forelse($surveys as $index => $s)
        <tr>
            <td class="text-center">{{ $index + 1 }}</td>
            <td class="text-center fw-bold">{{ $s->rating }} Bintang</td>
            <td>{{ $s->saran ?: '-' }}</td>
            <td class="text-center" style="font-size: 9px;">{{ $s->created_at->format('d/m/Y H:i') }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">Belum ada feedback masuk.</td>
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
        <span>Laporan Survei IKM – Dicetak secara otomatis oleh sistem.</span>
        <span>Halaman 1 dari 1</span>
    </div>
</div>

</body>
</html>
