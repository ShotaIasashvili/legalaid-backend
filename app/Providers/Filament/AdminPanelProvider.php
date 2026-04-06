<?php

namespace App\Providers\Filament;

use App\Filament\Resources\DocumentResource;
use App\Filament\Resources\FaqResource;
use App\Filament\Resources\JournalResource;
use App\Filament\Resources\LegalQuestionResource;
use App\Filament\Resources\OfficeResource;
use App\Filament\Resources\PageContentResource;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\SettingResource;
use App\Filament\Resources\StaffResource;
use App\Filament\Resources\StatResource;
use App\Filament\Resources\VacancyResource;
use App\Filament\Resources\VideoResource;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::hex('#8B2635'), // Legal Aid brand red
                'gray'    => Color::Slate,
            ])
            ->brandName('Legal Aid — Admin')
            ->brandLogo(asset('img/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->favicon(asset('img/favicon.ico'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([Pages\Dashboard::class])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
            ])
            ->navigationGroups([
                NavigationGroup::make('კონტენტი')->collapsed(false),
                NavigationGroup::make('სამართლებრივი')->collapsed(false),
                NavigationGroup::make('ორგანიზაცია')->collapsed(false),
                NavigationGroup::make('სისტემა')->collapsed(true),
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
