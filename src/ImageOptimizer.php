<?php

declare(strict_types=1);

namespace NasuhAkay\ImageOptimizer;

use NasuhAkay\ImageOptimizer\Contracts\DriverInterface;
use NasuhAkay\ImageOptimizer\Drivers\GdDriver;
use NasuhAkay\ImageOptimizer\Drivers\ImagickDriver;
use NasuhAkay\ImageOptimizer\DTO\OptimizationResult;
use NasuhAkay\ImageOptimizer\Exceptions\DriverNotAvailableException;

class ImageOptimizer
{
    private DriverInterface $driver;

    public function __construct(?DriverInterface $driver = null)
    {
        $this->driver = $driver ?? $this->resolveBestDriver();
    }

    /**
     * Sistemde var olan en güçlü sürücüyü seçer (Öncelik: Imagick -> GD).
     */
    private function resolveBestDriver(): DriverInterface
    {
        if (ImagickDriver::isSupported()) {
            return new ImagickDriver();
        }

        if (GdDriver::isSupported()) {
            return new GdDriver();
        }

        throw new DriverNotAvailableException(
            'Sistemde aktif bir görsel işleme kütüphanesi (Imagick veya GD) bulunamadı.'
        );
    }

    public function getDriver(): DriverInterface
    {
        return $this->driver;
    }

    public function convertToWebp(string $source, string $destination, int $quality = 82): OptimizationResult
    {
        return $this->driver->optimize($source, $destination, $quality, 'webp');
    }

    public function convertToAvif(string $source, string $destination, int $quality = 80): OptimizationResult
    {
        return $this->driver->optimize($source, $destination, $quality, 'avif');
    }

    public function optimize(string $source, string $destination, int $quality = 82): OptimizationResult
    {
        return $this->driver->optimize($source, $destination, $quality);
    }
}
