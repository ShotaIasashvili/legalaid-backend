<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+ ახალი სიახლე'),
            Actions\Action::make('clear_cache')
                ->label('ქეშის გასუფთავება')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->action(function (): void {
                    Artisan::call('cache:clear');
                    Artisan::call('view:clear');

                    Notification::make()
                        ->title('ქეში გასუფთავდა')
                        ->success()
                        ->send();
                }),
        ];
    }
}
