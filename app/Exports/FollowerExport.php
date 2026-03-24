<?php

namespace App\Exports;

use App\Models\Pengikut;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FollowerExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        // Mendapatkan pengikut unik dengan kunjungan terbaru
        return Pengikut::with(['kunjungan.profilPengunjung', 'kunjungan.wbp'])
            ->whereIn('id', function($q) {
                $q->select(\DB::raw('MAX(id)'))
                    ->from('pengikuts')
                    ->groupBy(\DB::raw('COALESCE(nik, nama)'));
            })
            ->orderBy('nama')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pengikut',
            'NIK / Identitas',
            'Hubungan',
            'Pengunjung Utama',
            'Tujuan WBP',
            'Barang Bawaan Terakhir',
            'Tanggal Terdaftar',
        ];
    }

    public function map($follower): array
    {
        return [
            $follower->nama,
            $follower->nik ?: '-',
            $follower->hubungan,
            optional($follower->kunjungan->profilPengunjung)->nama ?: '-',
            optional($follower->kunjungan->wbp)->nama ?: '-',
            $follower->barang_bawaan ?: 'Nihil',
            $follower->created_at ? $follower->created_at->format('d/m/Y H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']]],
        ];
    }
}
