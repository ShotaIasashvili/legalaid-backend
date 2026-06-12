<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Resources\PostResource\Pages\Concerns\HandlesPostImages;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    use HandlesPostImages;

    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->processFeaturedImageUpload($data, $this->record);
        $data = $this->normalizeGalleryImages($data, $this->record);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->ensureDefaultNewsCategory();
    }
}
