<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use NasuhAkay\ImageOptimizer\ImageOptimizer;

// Test amaçlı geçici bir PNG görseli oluşturup WebP'ye dönüştürme örneği
$samplePng = __DIR__ . '/sample.png';
$outputWebp = __DIR__ . '/sample_optimized.webp';

if (extension_loaded('gd')) {
    $im = imagecreatetruecolor(800, 600);
    $bg = imagecolorallocate($im, 30, 144, 255);
    imagefill($im, 0, 0, $bg);
    imagepng($im, $samplePng, 0); // Sıkıştırmasız örnek PNG
    imagedestroy($im);
}

$optimizer = new ImageOptimizer();
$result = $optimizer->convertToWebp($samplePng, $outputWebp, 82);

echo "=== Optimizasyon Raporu ===" . PHP_EOL;
echo "Sürücü         : {$result->driverUsed}" . PHP_EOL;
echo "Orijinal Boyut : {$result->originalSize} Bytes" . PHP_EOL;
echo "Optimize Boyut : {$result->optimizedSize} Bytes" . PHP_EOL;
echo "Kazanılan Alan : {$result->getSavedBytes()} Bytes" . PHP_EOL;
echo "Sıkıştırma     : %{$result->getCompressionRatio()}" . PHP_EOL;
echo "Süre           : {$result->executionTimeMs} ms" . PHP_EOL;
