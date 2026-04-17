<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostImageResolutionTest extends TestCase
{
    public function test_storage_backed_post_thumbnail_resolves_to_storage_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('images/news/example/thumbnail.jpg', 'test');

        $post = new Post([
            'featured_image_thumbnail' => 'images/news/example/thumbnail.jpg',
        ]);

        $this->assertSame(
            asset('storage/images/news/example/thumbnail.jpg'),
            $post->featured_image_thumbnail_url,
        );
    }

    public function test_legacy_seeded_post_thumbnail_uses_backend_asset_proxy_route(): void
    {
        config()->set('app.legacy_frontend_public_path', base_path('tests/Fixtures/legacy-public'));

        $post = new Post([
            'featured_image_thumbnail' => 'news-assets/generated/test-thumb.svg',
        ]);

        $expectedUrl = url('/legacy-post-assets/news-assets/generated/test-thumb.svg');

        $this->assertSame($expectedUrl, $post->featured_image_thumbnail_url);

        $this->get('/legacy-post-assets/news-assets/generated/test-thumb.svg')
            ->assertOk();
    }
}