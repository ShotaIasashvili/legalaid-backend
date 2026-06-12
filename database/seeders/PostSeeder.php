<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // Load from the existing posts.json in the frontend public folder
        $frontendPublicPath = rtrim((string) config('app.legacy_frontend_public_path', base_path('../legalaid2/public')), "\\/");
        $jsonPath = $frontendPublicPath . DIRECTORY_SEPARATOR . 'news-data' . DIRECTORY_SEPARATOR . 'posts.json';

        if (!file_exists($jsonPath)) {
            $this->command->warn("posts.json not found at: {$jsonPath}");
            $this->command->info("Skipping PostSeeder (no JSON data source available).");
            return;
        }

        $posts = json_decode(file_get_contents($jsonPath), true);

        if (empty($posts)) {
            $this->command->warn('posts.json is empty or invalid.');
            return;
        }

        $this->command->info('Seeding ' . count($posts) . ' posts...');

        $bar = $this->command->getOutput()->createProgressBar(count($posts));
        $bar->start();

        foreach ($posts as $data) {
            $galleryImages = $data['extraImages'] ?? $data['extra_images'] ?? $data['images'] ?? null;
            $featuredImage = isset($data['featuredImage']) ? ltrim($data['featuredImage'], '/') : null;

            $post = Post::updateOrCreate(
                ['legacy_id' => $data['id']],
                [
                    'title'           => $data['title'],
                    'slug'            => (string) $data['id'], // Keep numeric slug for compatibility
                    'content'         => $data['content'] ?? '',
                    'excerpt'         => $data['excerpt'] ?? null,
                    'status'          => 'published',
                    'published_at'    => isset($data['date']) ? Carbon::parse($data['date']) : null,
                    'source_url'      => $data['sourceUrl'] ?? null,
                    'extra_images'    => !empty($galleryImages) ? $galleryImages : null,
                    'featured_image'  => $featuredImage,
                    'featured_image_thumbnail' => $featuredImage,
                    'featured_image_popup' => $featuredImage,
                    'featured_image_single' => $featuredImage,
                    'og_image' => $featuredImage,
                ]
            );

            // Attach categories
            if (!empty($data['categories'])) {
                $categoryIds = [];
                foreach ($data['categories'] as $catName) {
                    $category = Category::firstOrCreate(
                        ['name' => $catName, 'type' => 'news'],
                        ['slug' => \Illuminate\Support\Str::slug($catName)]
                    );
                    $categoryIds[] = $category->id;
                }
                $post->categories()->syncWithoutDetaching($categoryIds);
            }

            $post->ensureDefaultNewsCategory();

            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Posts seeded successfully.');
    }
}
