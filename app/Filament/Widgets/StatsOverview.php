<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use App\Models\Vacancy;
use App\Models\Document;
use App\Models\Staff;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('სიახლეები', Post::count())
                ->description('სულ სიახლე')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('success')
                ->chart([
                    Post::whereDate('created_at', '>=', now()->subDays(7))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(6))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(5))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(4))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(3))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(2))->count(),
                    Post::whereDate('created_at', '>=', now()->subDays(1))->count(),
                ]),

            Stat::make('მონახაზები', Post::where('status', 'draft')->count())
                ->description('გამოქვ. მოლოდინშია')
                ->descriptionIcon('heroicon-m-pencil')
                ->color('warning'),

            Stat::make('დოკუმენტები', Document::count())
                ->description('PDF / ფ.')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('ვაკ. (ღია)', Vacancy::open()->count())
                ->description('ახ. ვაკ.')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('თანამშრომლ.', Staff::where('is_active', true)->count())
                ->description('აქტ. პ.')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
