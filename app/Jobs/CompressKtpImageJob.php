<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Kunjungan;
use App\Services\ImageService;

class CompressKtpImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $kunjunganId;
    public $path;

    public $tries = 3;

    public function __construct(int $kunjunganId, string $path)
    {
        $this->kunjunganId = $kunjunganId;
        $this->path = $path;
    }

    public function handle()
    {
        $kunjungan = Kunjungan::find($this->kunjunganId);
        if (!$kunjungan) {
            Log::warning('CompressKtpImageJob: Kunjungan not found: ' . $this->kunjunganId);
            return;
        }

        // local path to file
        $localPath = Storage::disk('local')->path($this->path);
        if (!file_exists($localPath)) {
            Log::warning('CompressKtpImageJob: file not found: ' . $localPath);
            return;
        }

        try {
            $compressed = ImageService::compressFromPath($localPath, 1200, 80);
            $base64 = 'data:image/jpeg;base64,' . base64_encode($compressed);

            // Update model
            $kunjungan->foto_ktp = $base64;
            $kunjungan->foto_ktp_processed_at = now();
            $kunjungan->foto_ktp_path = null; // remove original path
            $kunjungan->save();

            // delete original file
            Storage::disk('local')->delete($this->path);

            Log::info('CompressKtpImageJob: processed kunjungan ' . $this->kunjunganId);
        } catch (\Exception $e) {
            Log::error('CompressKtpImageJob error: ' . $e->getMessage());
            // rethrow to let queue retry
            throw $e;
        }
    }
}
