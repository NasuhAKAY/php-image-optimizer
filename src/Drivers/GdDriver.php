<?php

declare(strict_types=1);

namespace NasuhAkay\ImageOptimizer\Drivers;

use GdImage;
use NasuhAkay\ImageOptimizer\Contracts\DriverInterface;
use NasuhAkay\ImageOptimizer\DTO\OptimizationResult;
use NasuhAkay\ImageOptimizer\Exceptions\DriverNotAvailableException;
use NasuhAkay\ImageOptimizer\Exceptions\UnsupportedFormatException;

class GdDriver implements DriverInterface
{
    public static function isSupported(): bool
    {
        return extension_loaded('gd') && function_exists('gd_info');
    }

    public function optimize(
        string $sourcePath,
        string $targetPath,
        int $quality = 82,
        ?string $format = null
    ): OptimizationResult {
        if (!self::isSupported()) {
            throw new DriverNotAvailableException('GD eklentisi sistemde kurulu değil.');
        }

        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new UnsupportedFormatException("Kaynak dosya okunamadı veya bulunamadı: {$sourcePath}");
        }

        $quality = max(1, min(100, $quality));
        $startTime = microtime(true);
        $originalSize = (int) filesize($sourcePath);

        $imageInfo = @getimagesize($sourcePath);
        if ($imageInfo === false) {
            throw new UnsupportedFormatException("Geçersiz görsel dosyası: {$sourcePath}");
        }

        $mime = $imageInfo['mime'];
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($sourcePath),
            'image/png'  => @imagecreatefrompng($sourcePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            'image/avif' => function_exists('imagecreatefromavif') ? @imagecreatefromavif($sourcePath) : false,
            default      => throw new UnsupportedFormatException("Desteklenmeyen mime türü: {$mime}")
        };

        if (!$image instanceof GdImage) {
            throw new UnsupportedFormatException("Görsel kaynak nesnesi oluşturulamadı.");
        }

        // Truecolor dönüşümü ve şeffaflık (alpha channel) koruması
        imagepalettetotruecolor($image);
        imagealphablending($image, false);
        imagesavealpha($image, true);

        $targetFormat = strtolower($format ?? pathinfo($targetPath, PATHINFO_EXTENSION));

        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $saved = match ($targetFormat) {
            'webp' => function_exists('imagewebp')
                ? imagewebp($image, $targetPath, $quality)
                : throw new UnsupportedFormatException('GD WebP desteği aktif değil.'),
            'avif' => function_exists('imageavif')
                ? imageavif($image, $targetPath, $quality)
                : throw new UnsupportedFormatException('GD AVIF desteği aktif değil.'),
            'jpeg', 'jpg' => imagejpeg($image, $targetPath, $quality),
            'png'  => imagepng($image, $targetPath, (int) min(9, max(0, round((100 - $quality) / 10)))),
            default => throw new UnsupportedFormatException("GD için desteklenmeyen çıktı formatı: {$targetFormat}")
        };

        imagedestroy($image);

        if (!$saved) {
            throw new UnsupportedFormatException("Görsel hedef yola kaydedilemedi: {$targetPath}");
        }

        clearstatcache(true, $targetPath);
        $optimizedSize = (int) filesize($targetPath);

        return new OptimizationResult(
            sourcePath: $sourcePath,
            targetPath: $targetPath,
            originalSize: $originalSize,
            optimizedSize: $optimizedSize,
            driverUsed: 'GD',
            executionTimeMs: round((microtime(true) - $startTime) * 1000, 2)
        );
    }
}
