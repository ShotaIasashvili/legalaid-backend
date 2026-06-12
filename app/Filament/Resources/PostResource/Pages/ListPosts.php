<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Services\AdminDashboardMetrics;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ ახალი სიახლე'),
            Actions\Action::make('refresh_data')
                ->label('მონაცემების განახლება')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    app(AdminDashboardMetrics::class)->flush();

                    Notification::make()
                        ->title('მონაცემები განახლდა')
                        ->success()
                        ->send();
                }),
        ];
    }
}
