<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class RecapExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new GenderStatsSheet($this->data['visitorGender']),
            new MostVisitedWbpSheet($this->data['mostVisitedWbp']),
            new BusiestSessionsSheet($this->data['sessionCounts']),
        ];
    }
}

class GenderStatsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $genderData;

    public function __construct($genderData)
    {
        $this->genderData = $genderData;
    }

    public function collection()
    {
        $total = array_sum($this->genderData);
        return collect([
            ['Laki-laki', $this->genderData['Laki-laki'] . ' Orang'],
            ['Perempuan', $this->genderData['Perempuan'] . ' Orang'],
            ['TOTAL', $total . ' Orang'],
        ]);
    }

    public function headings(): array
    {
        return ['Jenis Kelamin', 'Total Pengunjung'];
    }

    public function title(): string
    {
        return 'Statistik Gender';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            4 => ['font' => ['bold' => true]],
        ];
    }
}

class MostVisitedWbpSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $wbpData;

    public function __construct($wbpData)
    {
        $this->wbpData = $wbpData;
    }

    public function collection()
    {
        return $this->wbpData->map(function($wbp, $index) {
            return [
                $index + 1,
                $wbp->nama,
                $wbp->no_registrasi,
                ($wbp->blok ?? '-') . ' / ' . ($wbp->lokasi_sel ?? '-'),
                $wbp->visit_count . ' Kali'
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama WBP', 'No. Registrasi', 'Blok / Sel', 'Total Kunjungan'];
    }

    public function title(): string
    {
        return 'Top 10 WBP';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

class BusiestSessionsSheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $sessionData;

    public function __construct($sessionData)
    {
        $this->sessionData = $sessionData;
    }

    public function collection()
    {
        $rows = [];
        $no = 1;
        foreach ($this->sessionData as $label => $count) {
            $rows[] = [$no++, $label, $count . ' Orang'];
        }
        return collect($rows);
    }

    public function headings(): array
    {
        return ['No', 'Hari & Sesi', 'Jumlah Pendaftar'];
    }

    public function title(): string
    {
        return 'Sesi Teramai';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
