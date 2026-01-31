<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use App\Models\Wbp;
use App\Jobs\CompressKtpImageJob;
use App\Jobs\CompressPengikutImageJob;
use App\Jobs\SendWhatsAppPendingNotification;
use Carbon\Carbon;

class KunjunganDispatchJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobs_are_dispatched_when_registering()
    {
        Storage::fake('local');
        Storage::fake('public');
        Bus::fake();

        // create a WBP
        $wbp = Wbp::create(['nama' => 'Test WBP', 'no_registrasi' => 'A100']);

        $fotoKtp = UploadedFile::fake()->image('ktp.jpg')->size(1500);
        $pengikutFoto = UploadedFile::fake()->image('pengikut.jpg')->size(1500);

        $visitDate = Carbon::now()->addDays(2);
        if ($visitDate->isMonday()) {
            // if landing on Monday, push to next day to avoid requiring session param
            $visitDate = $visitDate->addDay();
        }

        $response = $this->post(route('kunjungan.store'), [
            'nama_pengunjung' => 'Budi Santoso',
            'nik_ktp' => '1234567890123456',
            'nomor_hp' => '081234567890',
            'email_pengunjung' => 'budi@example.com',
            'alamat_lengkap' => 'Jl. Contoh No.1',
            'jenis_kelamin' => 'Laki-laki',
            'foto_ktp' => $fotoKtp,
            'wbp_id' => $wbp->id,
            'hubungan' => 'Istri/Suami',
            'tanggal_kunjungan' => $visitDate->format('Y-m-d'),
            'preferred_notification_channel' => 'both',
            // pengikut arrays
            'pengikut_nama' => ['Sahabat'],
            'pengikut_nik' => ['1234567890123456'],
            'pengikut_hubungan' => ['Saudara'],
            'pengikut_foto' => [$pengikutFoto],
        ]);

        $response->assertSessionHas('success');

        // Assert compress jobs dispatched
        Bus::assertDispatched(CompressKtpImageJob::class);
        Bus::assertDispatched(CompressPengikutImageJob::class);
        // Assert WA notification job dispatched
        Bus::assertDispatched(SendWhatsAppPendingNotification::class);
    }
}
