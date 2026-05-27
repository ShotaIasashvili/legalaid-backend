<?php

namespace Tests\Unit;

use App\Models\Post;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageUrlTest extends TestCase
{
    public function test_it_prefers_frontend_public_urls_for_news_assets(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('news-assets/generated/example.svg', '<svg></svg>');

        Config::set('app.frontend_url', 'https://frontend.test');

        $this->assertSame(
            'https://frontend.test/news-assets/generated/example.svg',
            Post::resolveImageUrl('news-assets/generated/example.svg'),
        );
    }

    public function test_it_keeps_public_storage_urls_for_non_news_assets(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('documents/example.pdf', 'pdf');

        $this->assertSame(
            rtrim(config('app.url'), '/') . '/storage/documents/example.pdf',
            Post::resolveImageUrl('documents/example.pdf'),
        );
    }
}