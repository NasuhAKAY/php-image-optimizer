<?php

declare(strict_types=1);

namespace NasuhAkay\ImageOptimizer\Drivers;

use Imagick;
use NasuhAkay\ImageOptimizer\Contracts\DriverInterface;
use NasuhAkay\ImageOptimizer\DTO\OptimizationResult;
use NasuhAkay\ImageOptimizer\Exceptions\DriverNotAvailableException;
use NasuhAkay\ImageOptimizer\Exceptions\UnsupportedFormatException;

class ImagickDriver implements DriverInterface
{
    public static function isSupported(): bool
    {
        return extension_loaded('imagick') && class_exists(Imagick::class);
    }

    public function optimize(
        string $sourcePath,
        string $targetPath,
        int $quality = 82,
        ?string $format = null
    ): OptimizationResult {
        if (!self::isSupported()) {
            throw new DriverNotAvailableException('Imagick eklentisi sistemde kurulu değil.');
        }

        if (!is_file($sourcePath) || !is_readable($sourcePath)) {
            throw new UnsupportedFormatException("Kaynak dosya okunamadı veya bulunamadı: {$sourcePath}");
        }

        $quality = max(1, min(100, $quality));
        $startTime = microtime(true);
        $originalSize = (int) filesize($sourcePath);

        $image = new Imagick($sourcePath);

        // Gereksiz EXIF ve metadata yükünü temizle
        $image->stripImage();

        if ($format !== null) {
            $image->setImageFormat(strtolower($format));
        }

        $image->setImageCompressionQuality($quality);

        // WebP için optimizasyon bayrakları
        if (strtolower($format ?? '') === 'webp') {
            $image->setOption('webp:method', '6');
            $image->setOption('webp:lossless', 'false');
        }

        $targetDir = dirname($targetPath);
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $image->writeImage($targetPath);
        $image->clear();
        $image->destroy();

        clearstatcache(true, $targetPath);
        $optimizedSize = (int) filesize($targetPath);

        return new OptimizationResult(
            sourcePath: $sourcePath,
            targetPath: $targetPath,
            originalSize: $originalSize,
            optimizedSize: $optimizedSize,
            driverUsed: 'Imagick',
            executionTimeMs: round((microtime(true) - $startTime) * 1000, 2)
        );
    }
}
