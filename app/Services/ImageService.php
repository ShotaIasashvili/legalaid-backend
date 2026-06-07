<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageService
{
    private ImageManager $manager;
    private array $config;

    public function __construct()
    {
        $driver = config('image_processing.driver', 'gd') === 'imagick'
            ? new ImagickDriver()
            : new GdDriver();

        $this->manager = new ImageManager($driver);
        $this->config  = config('image_processing');
    }

    /**
     * Process an uploaded image and generate all required sizes.
     *
     * Returns an array with paths for each size variant.
     */
    public function processUpload(UploadedFile $file, string $folder = 'news'): array
    {
        $baseName  = Str::uuid();
        $extension = 'jpg'; // Always save as JPEG for consistency
        $quality   = $this->config['quality'] ?? 82;
        $prefix    = "images/{$folder}/{$baseName}";

        $original = $this->manager->read($file->getRealPath());

        // Save original (max 1920px wide, not upscaled)
        $orig = clone $original;
        if ($orig->width() > 1920) {
            $orig->scaleDown(1920);
        }
        $origPath = "{$prefix}/original.{$extension}";
        Storage::disk('public')->put($origPath, $orig->toJpeg($quality)->toString());

        $result = [
            'original'  => $origPath,
            'thumbnail' => $this->resize($original, $prefix, 'thumbnail', $extension, $quality),
            'popup'     => $this->resize($original, $prefix, 'popup', $extension, $quality),
            'single'    => $this->resize($original, $prefix, 'single', $extension, $quality),
            'og'        => $this->resize($original, $prefix, 'og', $extension, $quality),
        ];

        // Generate WebP versions if enabled
        if ($this->config['generate_webp'] ?? true) {
            $result['original_webp']  = $this->toWebP($original, "{$prefix}/original.webp", $quality);
            $result['thumbnail_webp'] = $this->toWebP(
                $this->manager->read(Storage::disk('public')->path($result['thumbnail'])),
                "{$prefix}/thumbnail.webp",
                $quality
            );
        }

        return $result;
    }

    /**
     * Resize an image to a named preset (thumbnail, popup, single, og).
     */
    private function resize($image, string $prefix, string $preset, string $extension, int $quality): string
    {
        $sizes = $this->config['sizes'][$preset];
        $img   = clone $image;

        // Cover crop: fill the exact dimensions
        $img->cover($sizes['width'], $sizes['height']);

        $path = "{$prefix}/{$preset}.{$extension}";
        Storage::disk('public')->put($path, $img->toJpeg($quality)->toString());

        return $path;
    }

    /**
     * Convert an image object to WebP and store it.
     */
    private function toWebP($image, string $path, int $quality): string
    {
        Storage::disk('public')->put($path, $image->toWebp($quality)->toString());
        return $path;
    }

    /**
     * Delete all size variants for a given original path prefix.
     */
    public function deleteAll(string $originalPath): void
    {
        $prefix = dirname($originalPath);
        Storage::disk('public')->deleteDirectory($prefix);
    }

    /**
     * Resize inline to specific dimensions on-the-fly.
     * Used for quick resizes when exact preset is not needed.
     */
    public function resizeTo(string $sourcePath, int $width, int $height, int $quality = 82): string
    {
        $img  = $this->manager->read(Storage::disk('public')->path($sourcePath));
        $img->cover($width, $height);

        $outPath = Str::beforeLast($sourcePath, '.') . "_{$width}x{$height}.jpg";
        Storage::disk('public')->put($outPath, $img->toJpeg($quality)->toString());

        return $outPath;
    }

    /**
     * Get all public URLs for the given image paths array.
     */
    public static function urls(array $paths): array
    {
        $urls = [];
        foreach ($paths as $key => $path) {
            $urls[$key] = $path ? asset('storage/' . $path) : null;
        }
        return $urls;
    }

    /**
     * Validate an uploaded image (type & size).
     */
    public function validate(UploadedFile $file): void
    {
        $maxKb       = $this->config['max_upload_kb'] ?? 10240;
        $allowedMime = $this->config['allowed_types'] ?? ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMime, true)) {
            throw new \InvalidArgumentException('Invalid image type. Allowed: ' . implode(', ', $allowedMime));
        }

        if ($file->getSize() > $maxKb * 1024) {
            throw new \InvalidArgumentException("Image too large. Max size: {$maxKb} KB.");
        }
    }
}
