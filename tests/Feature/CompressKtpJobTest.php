<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Bus;
use App\Jobs\CompressKtpImageJob;
use App\Models\Kunjungan;
use Illuminate\Http\UploadedFile;

class CompressKtpJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_compresses_and_updates_model()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('ktp.jpg', 2000, 1600)->size(1500);
        $path = $file->store('kunjungan/originals', 'local');

        $kunjungan = Kunjungan::factory()->create([ 'foto_ktp' => null, 'foto_ktp_path' => $path ]);

        $job = new CompressKtpImageJob($kunjungan->id, $path);
        $job->handle();

        $kunjungan->refresh();

        $this->assertNotNull($kunjungan->foto_ktp);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $kunjungan->foto_ktp);
        $this->assertNotNull($kunjungan->foto_ktp_processed_at);
        Storage::disk('local')->assertMissing($path);
    }
}
