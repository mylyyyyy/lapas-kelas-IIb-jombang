<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageService;

class ImageServiceTest extends TestCase
{
    public function test_compress_uploaded_file_reduces_size_and_returns_jpeg()
    {
        Storage::fake('public');

        // Create a large fake image (PNG) and ensure size is large via ->size()
        $file = UploadedFile::fake()->image('large.png', 3000, 2000)->size(3000); // 3MB

        $originalSize = filesize($file->getRealPath());

        $content = ImageService::compressUploadedFile($file, 1200, 80);

        $this->assertIsString($content);
        $this->assertNotEmpty($content);

        // JPEG files start with 0xFF 0xD8
        $this->assertStringStartsWith("\xFF\xD8", $content);
    }
}
