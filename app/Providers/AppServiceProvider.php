<?php

namespace App\Providers;

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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageService::class, fn () => new ImageService());
        $this->app->singleton(AdminDashboardMetrics::class, fn () => new AdminDashboardMetrics());
    }

    public function boot(): void
    {
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
        $flushSiteCache = static function (): void {
            app(AdminDashboardMetrics::class)->flush();

            app()->terminating(static function (): void {
                try {
                    Cache::flush();
                    Artisan::call('view:clear');
                } catch (\Throwable $exception) {
                    Log::warning('Automatic cache clear failed.', [
                        'message' => $exception->getMessage(),
                    ]);
                }
            });
        };

        foreach ($models as $model) {
            $model::saved($flushSiteCache);
            $model::deleted($flushSiteCache);

            if (in_array(SoftDeletes::class, class_uses_recursive($model), true)) {
                $model::restored($flushSiteCache);
                $model::forceDeleted($flushSiteCache);
            }
        }
    }
}
