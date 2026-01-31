<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Jobs\CompressPengikutImageJob;
use App\Models\Pengikut;
use App\Models\Kunjungan;
use Illuminate\Http\UploadedFile;

class CompressPengikutJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_compresses_pengikut_and_updates_model()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->image('pengikut.jpg', 2000, 1600)->size(1500);
        $path = $file->store('kunjungan/pengikut/originals', 'local');

        $kunjungan = Kunjungan::factory()->create();
        $pengikut = Pengikut::create([
            'kunjungan_id' => $kunjungan->id,
            'nama' => 'Sahabat',
            'nik' => '1234567890123456',
            'hubungan' => 'Saudara',
            'foto_ktp' => null,
            'foto_ktp_path' => $path
        ]);

        $job = new CompressPengikutImageJob($pengikut->id, $path);
        $job->handle();

        $pengikut->refresh();

        $this->assertNotNull($pengikut->foto_ktp);
        $this->assertStringStartsWith('data:image/jpeg;base64,', $pengikut->foto_ktp);
        $this->assertNotNull($pengikut->foto_ktp_processed_at);
        Storage::disk('local')->assertMissing($path);
    }
}
