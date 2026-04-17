<?php

namespace Tests\Feature;

use App\Models\Document;
use App\Models\LegalQuestion;
use App\Models\Office;
use App\Models\PageContent;
use App\Models\Post;
use App\Models\Staff;
use App\Models\Vacancy;
use App\Services\AdminDashboardMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardMetricsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_builds_dashboard_metrics_from_a_shared_cached_payload(): void
    {
        $publishedPost = Post::create([
            'title' => 'Published Post',
            'content' => 'Published body',
            'status' => 'published',
        ]);

        $draftPost = Post::create([
            'title' => 'Draft Post',
            'content' => 'Draft body',
            'status' => 'draft',
        ]);

        Post::query()->whereKey($publishedPost)->update([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Post::query()->whereKey($draftPost)->update([
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        Document::create([
            'title' => 'Policy PDF',
            'slug' => 'policy-pdf',
            'file_path' => 'documents/policy.pdf',
        ]);

        Vacancy::create([
            'title' => 'Open Vacancy',
            'slug' => 'open-vacancy',
            'content' => 'Open vacancy details',
            'status' => 'open',
            'deadline' => now()->addWeek()->toDateString(),
        ]);

        Vacancy::create([
            'title' => 'Closed Vacancy',
            'slug' => 'closed-vacancy',
            'content' => 'Closed vacancy details',
            'status' => 'closed',
        ]);

        Staff::create([
            'name' => 'Active Staff',
            'position' => 'Lawyer',
            'is_active' => true,
        ]);

        Staff::create([
            'name' => 'Inactive Staff',
            'position' => 'Advisor',
            'is_active' => false,
        ]);

        Office::create([
            'name' => 'Central Office',
        ]);

        LegalQuestion::create([
            'question' => 'How do I apply?',
            'answer_html' => '<p>Submit the required form.</p>',
        ]);

        PageContent::create([
            'page' => 'home',
            'key' => 'hero_title',
            'value' => 'Legal Aid',
        ]);

        $metrics = app(AdminDashboardMetrics::class);

        $this->assertSame(2, $metrics->value('posts_total'));
        $this->assertSame(1, $metrics->value('posts_drafts'));
        $this->assertSame(1, $metrics->value('documents_total'));
        $this->assertSame(1, $metrics->value('vacancies_open'));
        $this->assertSame(1, $metrics->value('staff_active'));
        $this->assertSame(1, $metrics->value('offices_total'));
        $this->assertSame(1, $metrics->value('legal_questions_total'));
        $this->assertSame(1, $metrics->value('page_contents_total'));
        $this->assertSame('1', $metrics->badge('posts_drafts', hideZero: true));
        $this->assertSame('1', $metrics->badge('legal_questions_total'));
        $this->assertCount(7, $metrics->postTrend());
        $this->assertSame(1, $metrics->postTrend()[4]);
        $this->assertSame(1, $metrics->postTrend()[6]);
    }

    public function test_it_invalidates_cached_metrics_when_relevant_models_change(): void
    {
        $metrics = app(AdminDashboardMetrics::class);

        $this->assertSame(0, $metrics->value('offices_total'));

        $office = Office::create([
            'name' => 'Regional Office',
        ]);

        $this->assertSame(1, $metrics->value('offices_total'));

        $office->delete();

        $this->assertSame(0, $metrics->value('offices_total'));

        $office->restore();

        $this->assertSame(1, $metrics->value('offices_total'));
    }
}