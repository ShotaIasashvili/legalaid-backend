<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('+ დოკუმენტის ატვირთვა')];
    }
}

class CreateDocument extends CreateRecord
{
    protected static string $resource = DocumentResource::class;
}

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
