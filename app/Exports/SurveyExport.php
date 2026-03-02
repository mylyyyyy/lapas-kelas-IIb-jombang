<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SurveyExport implements WithMultipleSheets
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new SurveySummarySheet($this->data['stats'], $this->data['averageRating']),
            new SurveyDetailSheet($this->data['surveys']),
        ];
    }
}

class SurveySummarySheet implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    private $stats;
    private $average;

    public function __construct($stats, $average)
    {
        $this->stats = $stats;
        $this->average = $average;
    }

    public function collection()
    {
        $score = round($this->average / 4 * 100, 1);
        return collect([
            ['Ringkasan Survei IKM', ''],
            ['Total Responden', $this->stats->total . ' Orang'],
            ['Rata-rata Rating', number_format($this->average, 2) . ' / 4.00'],
            ['Indeks IKM (Konversi)', $score . ' / 100'],
            ['', ''],
            ['Kategori Penilaian', 'Jumlah'],
            ['Sangat Baik (Bintang 4)', $this->stats->sangat_baik],
            ['Baik (Bintang 3)', $this->stats->baik],
            ['Cukup (Bintang 2)', $this->stats->cukup],
            ['Buruk (Bintang 1)', $this->stats->buruk],
        ]);
    }

    public function headings(): array { return []; }

    public function title(): string { return 'Statistik IKM'; }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            6 => ['font' => ['bold' => true]],
        ];
    }
}

class SurveyDetailSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize
{
    private $surveys;

    public function __construct($surveys)
    {
        $this->surveys = $surveys;
    }

    public function collection()
    {
        return $this->surveys->map(function($s, $idx) {
            return [
                $idx + 1,
                $s->rating . ' Bintang',
                $s->saran ?: '-',
                $s->created_at->format('d/m/Y H:i')
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Rating', 'Kritik & Saran', 'Waktu Masuk'];
    }

    public function title(): string { return 'Feedback Detail'; }
}
