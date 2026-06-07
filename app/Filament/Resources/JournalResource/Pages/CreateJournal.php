<?php

namespace App\Filament\Resources\JournalResource\Pages;

use App\Filament\Resources\JournalResource;
use App\Services\ImageService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Storage;

class CreateJournal extends CreateRecord
{
    protected static string $resource = JournalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->processCover($data);
    }

    private function processCover(array $data): array
    {
        if (empty($data['_raw_cover'])) {
            unset($data['_raw_cover']);

            return $data;
        }

        $rawPath = $data['_raw_cover'];
        $fullPath = Storage::disk('public')->path($rawPath);

        if (file_exists($fullPath)) {
            $file = new \Illuminate\Http\UploadedFile($fullPath, basename($rawPath), mime_content_type($fullPath), null, true);
            $paths = app(ImageService::class)->processUpload($file, 'journals');

            Storage::disk('public')->delete($rawPath);

            $data['cover_image'] = $paths['original'];
            $data['cover_image_thumbnail'] = $paths['thumbnail'];
        }

        unset($data['_raw_cover']);

        return $data;
    }
}