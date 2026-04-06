<?php

namespace App\Filament\Resources\LegalQuestionResource\Pages;

use App\Filament\Resources\LegalQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListLegalQuestions extends ListRecords
{
    protected static string $resource = LegalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

class CreateLegalQuestion extends CreateRecord
{
    protected static string $resource = LegalQuestionResource::class;
}

class EditLegalQuestion extends EditRecord
{
    protected static string $resource = LegalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
