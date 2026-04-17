<?php

namespace App\Filament\Resources\LegalQuestionResource\Pages;

use App\Filament\Resources\LegalQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLegalQuestions extends ListRecords
{
    protected static string $resource = LegalQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}