<?php

namespace App\Filament\Resources\VacancyResource\Pages;

use App\Filament\Resources\VacancyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListVacancies extends ListRecords
{
    protected static string $resource = VacancyResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('+ ვაკანსიის დამატება')];
    }
}

class CreateVacancy extends CreateRecord
{
    protected static string $resource = VacancyResource::class;
}

class EditVacancy extends EditRecord
{
    protected static string $resource = VacancyResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
