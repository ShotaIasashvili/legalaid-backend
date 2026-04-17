<?php

namespace App\Filament\Resources\JournalResource\Pages;

use App\Filament\Resources\JournalResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditJournal extends EditRecord
{
    protected static string $resource = JournalResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['_raw_cover'])) {
            if ($this->record->cover_image) {
                app(ImageService::class)->deleteAll($this->record->cover_image);
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
        }

        unset($data['_raw_cover']);

        return $data;
    }
}