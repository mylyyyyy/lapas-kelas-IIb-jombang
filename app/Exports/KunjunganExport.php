<?php

namespace App\Exports;

use App\Models\Kunjungan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class KunjunganExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $period;
    protected $date;

    public function __construct(string $period = 'all', ?string $date = null)
    {
        $this->period = $period;
        $this->date = $date ? Carbon::parse($date) : null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Kunjungan::with(['wbp', 'pengikuts']);

        if ($this->date) {
            switch ($this->period) {
                case 'day':
                    $query->whereDate('tanggal_kunjungan', $this->date->format('Y-m-d'));
                    break;
                case 'week':
                    $query->whereBetween('tanggal_kunjungan', [
                        $this->date->startOfWeek(Carbon::MONDAY)->format('Y-m-d'),
                        $this->date->endOfWeek(Carbon::SUNDAY)->format('Y-m-d')
                    ]);
                    break;
                case 'month':
                    $query->whereYear('tanggal_kunjungan', $this->date->year)
                        ->whereMonth('tanggal_kunjungan', $this->date->month);
                    break;
                case 'all': // Fallback if 'all' is passed but date is present
                default:
                    // No date filter for 'all'
                    break;
            }
        }

        return $query->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID Kunjungan',
            'Kode Kunjungan',
            'Status',
            'Nomor Antrian Harian',
            'QR Token',
            'Nama Pengunjung',
            'NIK KTP Pengunjung',
            'Nomor HP Pengunjung',
            'Email Pengunjung',
            'Alamat Lengkap Pengunjung',
            'Barang Bawaan',
            'Jenis Kelamin Pengunjung',
            'Hubungan Dengan WBP',
            'Nama WBP',
            'No Registrasi WBP',
            'Blok WBP',
            'Kamar WBP',
            'Tanggal Kunjungan',
            'Sesi Kunjungan',
            'Pengikut',
            'Tanggal Dibuat',
            'Tanggal Diperbarui',
        ];
    }

    /**
     * @param mixed $kunjungan
     * @return array
     */
    public function map($kunjungan): array
    {
        $pengikutNames = $kunjungan->pengikuts->pluck('nama')->implode(', ');

        return [
            $kunjungan->id,
            $kunjungan->kode_kunjungan,
            ucfirst($kunjungan->status),
            $kunjungan->nomor_antrian_harian,
            $kunjungan->qr_token,
            $kunjungan->nama_pengunjung,
            (string) $kunjungan->nik_ktp, // Ensure NIK is treated as string to prevent scientific notation
            $kunjungan->nomor_hp_pengunjung,
            $kunjungan->email_pengunjung,
            $kunjungan->alamat_lengkap_pengunjung,
            $kunjungan->barang_bawaan,
            $kunjungan->jenis_kelamin,
            $kunjungan->hubungan,
            $kunjungan->wbp->nama ?? 'N/A',
            $kunjungan->wbp->no_registrasi ?? 'N/A',
            $kunjungan->wbp->blok ?? 'N/A',
            $kunjungan->wbp->kamar ?? 'N/A',
            Date::dateTimeToExcel($kunjungan->tanggal_kunjungan), // Use PhpSpreadsheet's date conversion
            ucfirst($kunjungan->sesi),
            $pengikutNames,
            Date::dateTimeToExcel($kunjungan->created_at),
            Date::dateTimeToExcel($kunjungan->updated_at),
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_NUMBER, // NIK KTP as number, but stored as string
            'R' => NumberFormat::FORMAT_DATE_DDMMYYYY, // Tanggal Kunjungan
            'U' => NumberFormat::FORMAT_DATE_DATETIME, // Tanggal Dibuat
            'V' => NumberFormat::FORMAT_DATE_DATETIME, // Tanggal Diperbarui
        ];
    }
}