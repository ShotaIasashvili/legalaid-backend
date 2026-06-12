<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $rawPath = $this->uploadedPath($data['_raw_image'] ?? null);

        if ($rawPath !== null) {
            $fullPath = Storage::disk('public')->path($rawPath);

            if (file_exists($fullPath)) {
                $file = new \Illuminate\Http\UploadedFile(
                    $fullPath,
                    basename($rawPath),
                    mime_content_type($fullPath),
                    null,
                    true
                );

                $paths = app(ImageService::class)->processUpload($file, 'news');

                if ($this->record->featured_image && Storage::disk('public')->exists($this->record->featured_image)) {
                    app(ImageService::class)->deleteAll($this->record->featured_image);
                }

                Storage::disk('public')->delete($rawPath);

                $data['featured_image']                = $paths['original'];
                $data['featured_image_thumbnail']      = $paths['thumbnail'];
                $data['featured_image_popup']          = $paths['popup'];
                $data['featured_image_single']         = $paths['single'];
                $data['og_image']                      = $paths['og'] ?? null;
                $data['featured_image_webp']           = $paths['original_webp'] ?? null;
                $data['featured_image_thumbnail_webp'] = $paths['thumbnail_webp'] ?? null;
            }
        }

        unset($data['_raw_image']);
        return $data;
    }

    private function uploadedPath(mixed $value): ?string
    {
        if (is_string($value)) {
            $path = trim($value);

            return $path !== '' ? $path : null;
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
}
