<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Exception;

class ImageService
{
    /**
     * Compress and optionally resize an uploaded image.
     * Returns binary image content (JPEG) ready to store or encode.
     * Uses GD (no extra dependencies).
     *
     * @param UploadedFile $file
     * @param int $maxWidth
     * @param int $quality 0-100
     * @return string
     * @throws Exception
     */
    public static function compressUploadedFile(UploadedFile $file, int $maxWidth = 1200, int $quality = 80): string
    {
        return self::compressFromPath($file->getRealPath(), $maxWidth, $quality);
    }

    /**
     * Compress an image given a filesystem path (local path).
     * Returns binary JPEG content.
     */
    public static function compressFromPath(string $path, int $maxWidth = 1200, int $quality = 80): string
    {
        if (!file_exists($path)) {
            throw new Exception('File not found at path: ' . $path);
        }

        $info = getimagesize($path);
        if ($info === false) {
            throw new Exception('File is not a valid image.');
        }

        $mime = $info['mime'] ?? '';

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $src = imagecreatefromjpeg($path);
                break;
            case 'image/png':
                $src = imagecreatefrompng($path);
                break;
            case 'image/gif':
                $src = imagecreatefromgif($path);
                break;
            default:
                $data = file_get_contents($path);
                $src = @imagecreatefromstring($data);
                if ($src === false) {
                    throw new Exception('Unsupported image type: ' . $mime);
                }
        }

        $width = imagesx($src);
        $height = imagesy($src);

        // If width is bigger than maxWidth, scale down
        if ($width > $maxWidth) {
            $ratio = $maxWidth / $width;
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        // Create destination image (true color)
        $dst = imagecreatetruecolor($newWidth, $newHeight);
        // Preserve transparency for PNG/GIF by filling with white (we convert to JPEG)
        $white = imagecolorallocate($dst, 255, 255, 255);
        imagefill($dst, 0, 0, $white);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Output to memory as JPEG with specified quality
        ob_start();
        imagejpeg($dst, null, $quality);
        $contents = ob_get_clean();

        // Cleanup
        imagedestroy($src);
        imagedestroy($dst);

        return $contents;
    }
}
