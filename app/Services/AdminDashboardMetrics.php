<?php

namespace App\Services;

use App\Models\Document;
use App\Models\LegalQuestion;
use App\Models\Office;
use App\Models\PageContent;
use App\Models\Post;
use App\Models\Staff;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Cache;

class AdminDashboardMetrics
{
    private const CACHE_KEY = 'admin.dashboard.metrics.v1';

    protected ?array $cachedMetrics = null;

    public function get(): array
    {
        return $this->cachedMetrics ??= Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(5),
            fn () => $this->build()
        );
    }

    public function value(string $key): int
    {
        return (int) ($this->get()[$key] ?? 0);
    }

    public function badge(string $key, bool $hideZero = false): ?string
    {
        $value = $this->value($key);

        if ($hideZero && $value === 0) {
            return null;
        }

        return (string) $value;
    }

    public function postTrend(): array
    {
        return $this->get()['posts_trend'] ?? [];
    }

    public function flush(): void
    {
        $this->cachedMetrics = null;

        Cache::forget(self::CACHE_KEY);
    }

    protected function build(): array
    {
        $trendStart = now()->startOfDay()->subDays(6);

        $postSummary = Post::query()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as drafts")
            ->first();

        $postTrendByDate = Post::query()
            ->selectRaw('DATE(created_at) as day, COUNT(*) as aggregate')
            ->where('created_at', '>=', $trendStart)
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->pluck('aggregate', 'day');

        $postsTrend = collect(range(0, 6))
            ->map(fn (int $offset): int => (int) ($postTrendByDate[$trendStart->copy()->addDays($offset)->toDateString()] ?? 0))
            ->all();

        return [
            'posts_total' => (int) ($postSummary->total ?? 0),
            'posts_drafts' => (int) ($postSummary->drafts ?? 0),
            'documents_total' => Document::count(),
            'vacancies_open' => Vacancy::open()->count(),
            'staff_active' => Staff::query()->where('is_active', true)->count(),
            'offices_total' => Office::count(),
            'legal_questions_total' => LegalQuestion::count(),
            'page_contents_total' => PageContent::count(),
            'posts_trend' => $postsTrend,
        ];
    }
}