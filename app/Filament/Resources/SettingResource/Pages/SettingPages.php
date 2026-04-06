<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('+ პარამეტრის დამატება')];
    }
}

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;
}

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
