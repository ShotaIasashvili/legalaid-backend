<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\Concerns\HandlesPostImages;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    use HandlesPostImages;

    protected static string $resource = PostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = $this->processFeaturedImageUpload($data);
        $data = $this->normalizeGalleryImages($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->ensureDefaultNewsCategory();
    }
}
