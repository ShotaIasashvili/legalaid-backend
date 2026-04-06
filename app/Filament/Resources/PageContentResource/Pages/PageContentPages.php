<?php

namespace App\Filament\Resources\PageContentResource\Pages;

use App\Filament\Resources\PageContentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListPageContents extends ListRecords
{
    protected static string $resource = PageContentResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('+ კონტენტ ბლოკის დამატება')];
    }
}

class CreatePageContent extends CreateRecord
{
    protected static string $resource = PageContentResource::class;
}

class EditPageContent extends EditRecord
{
    protected static string $resource = PageContentResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
