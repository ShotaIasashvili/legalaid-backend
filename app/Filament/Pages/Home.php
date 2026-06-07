<?php

namespace App\Filament\Pages;

use App\Filament\Resources\VacancyResource;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Home extends Page
{
    protected static string $routePath = '/';

    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament-panels::pages.dashboard';

    public function mount(): void
    {
        $user = Auth::user();

        if ($user instanceof User && $user->isHr()) {
            $this->redirect(VacancyResource::getUrl(panel: 'admin'), navigate: true);

            return;
        }

        $this->redirect(Dashboard::getUrl(panel: 'admin'), navigate: true);
    }
}