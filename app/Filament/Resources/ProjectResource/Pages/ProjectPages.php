<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Services\ImageService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListProjects extends ListRecords
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (!empty($data['_raw_image'])) {
            $data = $this->processImage($data);
        }
        unset($data['_raw_image']);
        return $data;
    }

    private function processImage(array $data): array
    {
        $rawPath  = $data['_raw_image'];
        $fullPath = Storage::disk('public')->path($rawPath);
        if (!file_exists($fullPath)) {
            return $data;
        }
        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($rawPath), mime_content_type($fullPath), null, true);
        $paths = app(ImageService::class)->processUpload($file, 'projects');
        Storage::disk('public')->delete($rawPath);

        $data['featured_image']                   = $paths['original'];
        $data['featured_image_thumbnail']         = $paths['thumbnail'];
        $data['featured_image_popup']             = $paths['popup'];
        $data['featured_image_webp']              = $paths['original_webp'] ?? null;
        $data['featured_image_thumbnail_webp']    = $paths['thumbnail_webp'] ?? null;

        return $data;
    }
}

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (!empty($data['_raw_image'])) {
            if ($this->record->featured_image) {
                app(ImageService::class)->deleteAll($this->record->featured_image);
            }
            $data = $this->processImage($data);
        }
        unset($data['_raw_image']);
        return $data;
    }

    private function processImage(array $data): array
    {
        $rawPath  = $data['_raw_image'];
        $fullPath = Storage::disk('public')->path($rawPath);
        if (!file_exists($fullPath)) {
            return $data;
        }
        $file = new \Illuminate\Http\UploadedFile($fullPath, basename($rawPath), mime_content_type($fullPath), null, true);
        $paths = app(ImageService::class)->processUpload($file, 'projects');
        Storage::disk('public')->delete($rawPath);

        $data['featured_image']                   = $paths['original'];
        $data['featured_image_thumbnail']         = $paths['thumbnail'];
        $data['featured_image_popup']             = $paths['popup'];
        $data['featured_image_webp']              = $paths['original_webp'] ?? null;
        $data['featured_image_thumbnail_webp']    = $paths['thumbnail_webp'] ?? null;

        return $data;
    }
}
