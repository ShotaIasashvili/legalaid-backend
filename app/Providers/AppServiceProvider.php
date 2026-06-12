<?php

namespace App\Providers;

use App\Livewire\SafeFilamentNotifications;
use App\Models\Document;
use App\Models\LegalQuestion;
use App\Models\Office;
use App\Models\PageContent;
use App\Models\Post;
use App\Models\Staff;
use App\Models\Vacancy;
use App\Services\AdminDashboardMetrics;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageService::class, fn () => new ImageService());
        $this->app->singleton(AdminDashboardMetrics::class, fn () => new AdminDashboardMetrics());
    }

    public function boot(): void
    {
        $this->app->booted(static function (): void {
            Livewire::component('notifications', SafeFilamentNotifications::class);
        });

        $this->registerDashboardMetricsInvalidation([
            Document::class,
            LegalQuestion::class,
            Office::class,
            PageContent::class,
            Post::class,
            Staff::class,
            Vacancy::class,
        ]);
    }

    protected function registerDashboardMetricsInvalidation(array $models): void
    {
        $refreshDashboardMetrics = static function (): void {
            app(AdminDashboardMetrics::class)->flush();
        };

        foreach ($models as $model) {
            $model::saved($refreshDashboardMetrics);
            $model::deleted($refreshDashboardMetrics);

            if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                $model::restored($refreshDashboardMetrics);
                $model::forceDeleted($refreshDashboardMetrics);
            }
        }
    }
}
