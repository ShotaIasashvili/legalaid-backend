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
        $frontendRepoPath = dirname($frontendPublicPath);

        $documents = array_merge(
            $this->registryDocuments($frontendPublicPath),
            $this->councilDecisionDocuments($frontendPublicPath, $frontendRepoPath)
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

    private function councilDecisionDocuments(string $frontendPublicPath, string $frontendRepoPath): array
    {
        $jsonPath = $frontendPublicPath . DIRECTORY_SEPARATOR . 'seed-data' . DIRECTORY_SEPARATOR . 'council-decisions.json';

        if (file_exists($jsonPath)) {
            $decisions = json_decode(file_get_contents($jsonPath), true);

            if (is_array($decisions) && ! empty($decisions)) {
                return $this->normalizeCouncilDecisionDocuments($decisions);
            }
        }

        $tsPath = $frontendRepoPath . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'councilDecisionsData.ts';

        if (! file_exists($tsPath)) {
            $this->command->warn("councilDecisionsData.ts not found at: {$tsPath}");
            return [];
        }

        $contents = file_get_contents($tsPath);

        if ($contents === false || $contents === '') {
            $this->command->warn('councilDecisionsData.ts is empty or unreadable.');
            return [];
        }

        preg_match_all('/\{\s*title:\s*"((?:[^"\\\\]|\\\\.)*)",\s*url:\s*"((?:[^"\\\\]|\\\\.)*)"\s*,?\s*\}/su', $contents, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            $this->command->warn('Could not parse council decisions from councilDecisionsData.ts.');
            return [];
        }

        $decisions = array_map(function (array $match): array {
            return [
                'title' => json_decode('"' . $match[1] . '"') ?? $match[1],
                'url' => json_decode('"' . $match[2] . '"') ?? $match[2],
            ];
        }, $matches);

        return $this->normalizeCouncilDecisionDocuments($decisions);
    }

    private function normalizeCouncilDecisionDocuments(array $decisions): array
    {
        $normalizedDocuments = [];

        foreach ($decisions as $index => $decision) {
            $normalizedPath = isset($decision['url']) ? ltrim((string) $decision['url'], '/') : null;
            $title = isset($decision['title']) ? trim((string) $decision['title']) : null;

            if (blank($normalizedPath) || blank($title)) {
                continue;
            }

            $slugSource = pathinfo($normalizedPath, PATHINFO_FILENAME) ?: $title;
            $slug = Str::slug($slugSource);

            $normalizedDocuments[] = [
                'slug' => $slug ?: 'council-decision-' . ($index + 1),
                'title' => $title,
                'description' => null,
                'badge' => null,
                'file_path' => $normalizedPath,
                'file_name' => basename($normalizedPath),
                'type' => 'council_decision',
            ];
        }

        return $normalizedDocuments;
    }
}
