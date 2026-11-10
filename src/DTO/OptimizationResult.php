<?php

declare(strict_types=1);

namespace NasuhAkay\ImageOptimizer\DTO;

readonly class OptimizationResult
{
    public function __construct(
        public string $sourcePath,
        public string $targetPath,
        public int $originalSize,
        public int $optimizedSize,
        public string $driverUsed,
        public float $executionTimeMs
    ) {}

    public function getSavedBytes(): int
    {
        return max(0, $this->originalSize - $this->optimizedSize);
    }

    public function getCompressionRatio(): float
    {
        if ($this->originalSize === 0) {
            return 0.0;
        }

        return round((1 - ($this->optimizedSize / $this->originalSize)) * 100, 2);
    }
}
