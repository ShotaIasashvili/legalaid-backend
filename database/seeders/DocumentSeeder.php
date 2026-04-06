<?php

namespace Database\Seeders;

use App\Models\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('../legalaid2/public/seed-data/registry-acts.json');

        if (!file_exists($jsonPath)) {
            $this->command->warn("registry-acts.json not found at: {$jsonPath}");
            $this->command->info("Run: node scripts/extract-seed-data.mjs  inside legalaid2/ first.");
            return;
        }

        $acts = json_decode(file_get_contents($jsonPath), true);

        if (empty($acts)) {
            $this->command->warn('registry-acts.json is empty or invalid.');
            return;
        }

        $this->command->info('Seeding ' . count($acts) . ' registry acts / documents...');
        $bar = $this->command->getOutput()->createProgressBar(count($acts));
        $bar->start();

        foreach ($acts as $i => $data) {
            $slug = \Illuminate\Support\Str::slug($data['id'] ?? 'doc-'.($i+1));
            if (!$slug) {
                $slug = 'doc-'.($i+1);
            }

            Document::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'       => $data['title']       ?? '',
                    'description' => $data['description'] ?? null,
                    'badge'       => $data['badge']       ?? null,
                    'file_path'   => $data['local_path']  ?? null, // legacy PDF path from frontend
                    'file_type'   => 'pdf',
                    'type'        => 'registry',          // registry, legal_act, council, form
                    'sort_order'  => $i + 1,
                    'is_active'   => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Documents seeded successfully.');
    }
}
