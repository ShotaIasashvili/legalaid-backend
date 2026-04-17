<?php

namespace App\Filament\Widgets;

use App\Services\AdminDashboardMetrics;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $metrics = app(AdminDashboardMetrics::class);

        return [
            Stat::make('სიახლეები', (string) $metrics->value('posts_total'))
                ->description('სულ სიახლე')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('success')
                ->chart($metrics->postTrend()),

            Stat::make('მონახაზები', (string) $metrics->value('posts_drafts'))
                ->description('გამოქვ. მოლოდინშია')
                ->descriptionIcon('heroicon-m-pencil')
                ->color('warning'),

            Stat::make('დოკუმენტები', (string) $metrics->value('documents_total'))
                ->description('PDF / ფ.')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),

            Stat::make('ვაკ. (ღია)', (string) $metrics->value('vacancies_open'))
                ->description('ახ. ვაკ.')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('primary'),

            Stat::make('თანამშრომლ.', (string) $metrics->value('staff_active'))
                ->description('აქტ. პ.')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
