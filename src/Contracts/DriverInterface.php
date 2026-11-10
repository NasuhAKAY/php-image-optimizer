<?php

declare(strict_types=1);

namespace NasuhAkay\ImageOptimizer\Contracts;

use NasuhAkay\ImageOptimizer\DTO\OptimizationResult;

interface DriverInterface
{
    /**
     * Sürücünün sistemde kullanılabilir olup olmadığını doğrular.
     */
    public static function isSupported(): bool;

    /**
     * Görseli optimize eder ve hedef formata dönüştürür.
     *
     * @param string $sourcePath Kaynak görsel dosya yolu
     * @param string $targetPath Çıktı dosya yolu
     * @param int $quality Sıkıştırma kalitesi (1-100)
     * @param string|null $format webp, avif veya orijinal format
     */
    public function optimize(
        string $sourcePath,
        string $targetPath,
        int $quality = 82,
        ?string $format = null
    ): OptimizationResult;
}
