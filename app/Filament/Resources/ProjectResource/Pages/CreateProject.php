<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['_raw_image'])) {
            $data = $this->processImage($data);
        }

        unset($data['_raw_image']);

        return $data;
    }

    private function processImage(array $data): array
    {
        $rawPath = $data['_raw_image'];
        $fullPath = Storage::disk('public')->path($rawPath);

        if (! file_exists($fullPath)) {
            return $data;
        }

        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($rawPath), mime_content_type($fullPath), null, true);
        $paths = app(ImageService::class)->processUpload($file, 'projects');

        Storage::disk('public')->delete($rawPath);

        $data['featured_image'] = $paths['original'];
        $data['featured_image_thumbnail'] = $paths['thumbnail'];
        $data['featured_image_popup'] = $paths['popup'];
        $data['featured_image_webp'] = $paths['original_webp'] ?? null;
        $data['featured_image_thumbnail_webp'] = $paths['thumbnail_webp'] ?? null;

        return $data;
    }
}