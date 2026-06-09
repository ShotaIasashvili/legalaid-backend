<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $frontendPublicPath = rtrim((string) config('app.legacy_frontend_public_path', base_path('../legalaid2/public')), "\\/");

        $documents = array_merge(
            $this->registryDocuments($frontendPublicPath)
        );

        if (empty($documents)) {
            $this->command->warn('No legacy document sources were found for DocumentSeeder.');
            return;
        }

        $this->command->info('Seeding ' . count($documents) . ' documents...');
        $bar = $this->command->getOutput()->createProgressBar(count($documents));
        $bar->start();

        foreach ($documents as $index => $data) {
            $slug = $data['slug'] ?: 'doc-' . ($index + 1);

            Document::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'       => $data['title'] ?? '',
                    'description' => $data['description'] ?? null,
                    'badge'       => $data['badge'] ?? null,
                    'file_path'   => $data['file_path'] ?? null,
                    'file_name'   => $data['file_name'] ?? null,
                    'file_type'   => 'pdf',
                    'type'        => $data['type'],
                    'sort_order'  => $index + 1,
                    'is_active'   => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Documents seeded successfully.');
    }

    private function registryDocuments(string $frontendPublicPath): array
    {
        $jsonPath = $frontendPublicPath . DIRECTORY_SEPARATOR . 'seed-data' . DIRECTORY_SEPARATOR . 'registry-acts.json';

        if (! file_exists($jsonPath)) {
            $this->command->warn("registry-acts.json not found at: {$jsonPath}");
            return [];
        }

        $acts = json_decode(file_get_contents($jsonPath), true);

        if (! is_array($acts) || empty($acts)) {
            $this->command->warn('registry-acts.json is empty or invalid.');
            return [];
        }

        return array_values(array_filter(array_map(function (array $data, int $index): ?array {
            $normalizedPath = isset($data['local_path']) ? ltrim((string) $data['local_path'], '/') : null;

            if (blank($normalizedPath) || blank($data['title'] ?? null)) {
                return null;
            }

            $slug = Str::slug((string) ($data['id'] ?? 'registry-act-' . ($index + 1)));

            return [
                'slug' => $slug ?: 'registry-act-' . ($index + 1),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'badge' => $data['badge'] ?? null,
                'file_path' => $normalizedPath,
                'file_name' => basename($normalizedPath),
                'type' => 'registry_act',
            ];
        }, $acts, array_keys($acts))));
    }

}
