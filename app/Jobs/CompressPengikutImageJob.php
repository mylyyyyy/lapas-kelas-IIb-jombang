<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Pengikut;
use App\Services\ImageService;

class CompressPengikutImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pengikutId;
    public $path;

    public $tries = 3;

    public function __construct(int $pengikutId, string $path)
    {
        $this->pengikutId = $pengikutId;
        $this->path = $path;
    }

    public function handle()
    {
        $pengikut = Pengikut::find($this->pengikutId);
        if (!$pengikut) {
            Log::warning('CompressPengikutImageJob: Pengikut not found: ' . $this->pengikutId);
            return;
        }

        $localPath = Storage::disk('local')->path($this->path);
        if (!file_exists($localPath)) {
            Log::warning('CompressPengikutImageJob: file not found: ' . $localPath);
            return;
        }

        try {
            $compressed = ImageService::compressFromPath($localPath, 1200, 80);
            $base64 = 'data:image/jpeg;base64,' . base64_encode($compressed);

            $pengikut->foto_ktp = $base64;
            $pengikut->foto_ktp_processed_at = now();
            $pengikut->foto_ktp_path = null;
            $pengikut->save();

            Storage::disk('local')->delete($this->path);

            Log::info('CompressPengikutImageJob: processed pengikut ' . $this->pengikutId);
        } catch (\Exception $e) {
            Log::error('CompressPengikutImageJob error: ' . $e->getMessage());
            throw $e;
        }
    }
}
