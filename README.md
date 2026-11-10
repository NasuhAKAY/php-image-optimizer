# PHP Image Optimizer & WebP/AVIF Converter

[![PHP Version Require](https://img.shields.io/badge/php-%3E%3D8.2-8892BF.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Yüksek performanslı, **Imagick** ve **GD** sürücüleri arasında otomatik yedekleme (fallback) yapabilen, EXIF/Metadata temizliği ve modern **WebP / AVIF** dönüştürme desteği sunan hafif PHP 8.2+ kütüphanesi.

## Özellikler

- **Otomatik Sürücü Seçimi (Auto-Fallback):** Sistemde `Imagick` varsa öncelikli olarak kullanır; yoksa otomatik olarak `GD` sürücüsüne geçer.
- **Modern Format Dönüşümü:** JPEG ve PNG görsellerini tek satırla `WebP` veya `AVIF` formatına dönüştürür.
- **EXIF & Metadata Temizliği:** Gereksiz meta verileri temizleyerek görüntü kalitesinden ödün vermeden dosya boyutunu küçültür.
- **Şeffaflık (Alpha Channel) Koruması:** PNG ve WebP dönüşümlerinde şeffaf arka planları korur.
- **Detaylı Optimizasyon Raporu (DTO):** Sıkıştırma oranı (%), kazanılan byte miktarı, kullanılan sürücü ve işlem süresini (`ms`) döndürür.
- **Sıfır Dış Bağımlılık:** Sadece standart PHP 8.2+ ve yerleşik görüntü işleme eklentileriyle çalışır.

## Kurulum

Composer üzerinden projeye dahil edin:

```bash
composer require nasuhakay/image-optimizer
```

## Kullanım

### 1. Hızlı Görsel Optimizasyonu ve WebP Dönüşümü

```php
<?php

require 'vendor/autoload.php';

use NasuhAkay\ImageOptimizer\ImageOptimizer;

$optimizer = new ImageOptimizer();

// Görseli WebP formatına dönüştür ve sıkıştır
$result = $optimizer->convertToWebp(
    source: __DIR__ . '/photo.jpg',
    destination: __DIR__ . '/photo.webp',
    quality: 82
);

echo "Kullanılan Sürücü: " . $result->driverUsed . PHP_EOL;
echo "Orijinal Boyut: " . $result->originalSize . " byte" . PHP_EOL;
echo "Yeni Boyut: " . $result->optimizedSize . " byte" . PHP_EOL;
echo "Tasarruf Oranı: %" . $result->getCompressionRatio() . PHP_EOL;
echo "Kazanılan Alan: " . $result->getSavedBytes() . " byte" . PHP_EOL;
echo "İşlem Süresi: " . $result->executionTimeMs . " ms" . PHP_EOL;
```

### 2. Format Değiştirmeden Sıkıştırma

```php
$result = $optimizer->optimize(
    source: __DIR__ . '/banner.png',
    destination: __DIR__ . '/banner-optimized.png',
    quality: 80
);
```

### 3. Manuel Sürücü Seçimi

```php
use NasuhAkay\ImageOptimizer\Drivers\GdDriver;
use NasuhAkay\ImageOptimizer\ImageOptimizer;

$optimizer = new ImageOptimizer(new GdDriver());
$result = $optimizer->convertToWebp('input.jpg', 'output.webp', 85);
```

## Mimari Yapı

```text
src/
├── Contracts/
│   └── DriverInterface.php
├── Drivers/
│   ├── ImagickDriver.php
│   └── GdDriver.php
├── DTO/
│   └── OptimizationResult.php
├── Exceptions/
│   ├── DriverNotAvailableException.php
│   └── UnsupportedFormatException.php
└── ImageOptimizer.php
```

## Lisans

Bu proje [MIT Lisansı](LICENSE) altında lisanslanmıştır.
