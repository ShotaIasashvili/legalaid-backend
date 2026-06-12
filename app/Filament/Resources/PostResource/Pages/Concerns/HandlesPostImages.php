<?php

namespace App\Filament\Resources\PostResource\Pages\Concerns;

use App\Models\Post;
use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait HandlesPostImages
{
    protected function processFeaturedImageUpload(array $data, ?Post $existingPost = null): array
    {
        $featuredImagePath = $this->uploadedPath($data['featured_image'] ?? null);

        if ($featuredImagePath === null) {
            return $this->preserveExistingPostImages($data, $existingPost);
        }

        if (! Str::startsWith($featuredImagePath, 'posts/raw/')) {
            $data['featured_image'] = $featuredImagePath;

            return $data;
        }

        $fullPath = Storage::disk('public')->path($featuredImagePath);

        if (! is_file($fullPath)) {
            unset($data['featured_image']);

            return $this->preserveExistingPostImages($data, $existingPost);
        }

        $file = new UploadedFile(
            $fullPath,
            basename($featuredImagePath),
            mime_content_type($fullPath) ?: null,
            null,
            true
        );

        $paths = app(ImageService::class)->processUpload($file, 'news');

        if (
            $existingPost?->featured_image
            && Storage::disk('public')->exists($existingPost->featured_image)
            && dirname($existingPost->featured_image) !== dirname($paths['original'])
        ) {
            app(ImageService::class)->deleteAll($existingPost->featured_image);
        }

        Storage::disk('public')->delete($featuredImagePath);

        $data['featured_image'] = $paths['original'];
        $data['featured_image_thumbnail'] = $paths['thumbnail'];
        $data['featured_image_popup'] = $paths['popup'];
        $data['featured_image_single'] = $paths['single'];
        $data['og_image'] = $paths['og'] ?? null;
        $data['featured_image_webp'] = $paths['original_webp'] ?? null;
        $data['featured_image_thumbnail_webp'] = $paths['thumbnail_webp'] ?? null;

        return $data;
    }

    protected function normalizeGalleryImages(array $data, ?Post $existingPost = null): array
    {
        if (! array_key_exists('extra_images', $data)) {
            return $data;
        }

        $images = Post::normalizeImagePaths($data['extra_images']);

        if ($images === [] && $existingPost !== null) {
            $existingImages = Post::normalizeImagePaths($existingPost->extra_images ?? []);

            if ($existingImages !== []) {
                $data['extra_images'] = $existingImages;

                return $data;
            }
        }

        $data['extra_images'] = $images;

        return $data;
    }

    protected function uploadedPath(mixed $value): ?string
    {
        if (is_string($value)) {
            $path = trim($value);

            return $path !== '' ? ltrim(str_replace('\\', '/', $path), '/') : null;
        }

        if ($value instanceof \Illuminate\Support\Collection) {
            $value = $value->all();
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                $path = $this->uploadedPath($item);

                if ($path !== null) {
                    return $path;
                }
            }
        }

        return null;
    }

    protected function preserveExistingPostImages(array $data, ?Post $existingPost): array
    {
        if ($existingPost === null) {
            return $data;
        }

        foreach (Post::IMAGE_FIELDS as $field) {
            if (blank($data[$field] ?? null) && filled($existingPost->{$field})) {
                $data[$field] = $existingPost->{$field};
            }
        }

        return $data;
    }
}
