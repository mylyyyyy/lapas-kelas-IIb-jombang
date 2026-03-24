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
        // Get unique followers by NIK or Name
        return Pengikut::select('nama', 'nik', 'hubungan', 'barang_bawaan', 'created_at')
            ->orderBy('nama')
            ->get()
            ->unique(function ($item) {
                return ($item->nik ?: $item->nama);
            });
    }

    public function headings(): array
    {
        return [
            'Nama Pengikut',
            'NIK / Identitas',
            'Hubungan Terakhir',
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
