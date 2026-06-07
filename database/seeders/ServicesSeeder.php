<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $frontendPublicPath = rtrim((string) config('app.legacy_frontend_public_path', base_path('../legalaid2/public')), "\\/");

        // Prefer the full-content JSON; fall back to the basic one
        $jsonPath = $frontendPublicPath . DIRECTORY_SEPARATOR . 'seed-data' . DIRECTORY_SEPARATOR . 'services-full.json';
        if (!file_exists($jsonPath)) {
            $jsonPath = $frontendPublicPath . DIRECTORY_SEPARATOR . 'seed-data' . DIRECTORY_SEPARATOR . 'services.json';
        }

        if (!file_exists($jsonPath)) {
            $this->command->warn("services JSON not found at: {$jsonPath}");
            $this->command->info("Run: bun scripts/export_services_json.ts  inside legalaid2/ first.");
            return;
        }

        $services = json_decode(file_get_contents($jsonPath), true);

        if (empty($services)) {
            $this->command->warn('services JSON is empty or invalid.');
            return;
        }

        $this->command->info('Seeding ' . count($services) . ' services...');
        $bar = $this->command->getOutput()->createProgressBar(count($services));
        $bar->start();

        foreach ($services as $data) {
            Service::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'title'                        => $data['title']                        ?? '',
                    'subtitle'                     => $data['subtitle']                     ?? null,
                    'description'                  => $data['description']                  ?? null,
                    'full_content'                 => $data['fullContent']                  ?? null,
                    'icon'                         => $data['icon']                         ?? null,
                    'category'                     => $data['category']                     ?? 'ძირითადი სერვისები',
                    'color'                        => $data['color']                        ?? null,
                    'requirements'                 => $data['requirements']                 ?? null,
                    'how_to_apply'                 => $data['howToApply']                   ?? null,
                    'related_services'             => $data['relatedServices']              ?? null,
                    'special_eligibility_categories' => $data['specialEligibilityCategories'] ?? null,
                    'download_links'               => $data['downloadLinks']                ?? null,
                    'sort_order'                   => $data['id']                           ?? 0,
                    'is_active'                    => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Services seeded successfully.');
    }
}
