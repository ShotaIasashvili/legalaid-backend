<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditStaff extends EditRecord
{
    protected static string $resource = StaffResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['_raw_photo'])) {
            if ($this->record->photo) {
                app(ImageService::class)->deleteAll($this->record->photo);
            }

            $rawPath = $data['_raw_photo'];
            $fullPath = Storage::disk('public')->path($rawPath);

            if (file_exists($fullPath)) {
                $file = new \Illuminate\Http\UploadedFile($fullPath, basename($rawPath), mime_content_type($fullPath), null, true);
                $paths = app(ImageService::class)->processUpload($file, 'staff');

                Storage::disk('public')->delete($rawPath);

                $data['photo'] = $paths['original'];
                $data['photo_thumbnail'] = $paths['thumbnail'];
            }
        }

        unset($data['_raw_photo']);

        return $data;
    }
}