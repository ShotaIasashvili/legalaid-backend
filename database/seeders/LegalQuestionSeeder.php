<?php

namespace Database\Seeders;

use App\Models\LegalQuestion;
use Illuminate\Database\Seeder;

class LegalQuestionSeeder extends Seeder
{
    public function run(): void
    {
        $jsonPath = base_path('../legalaid2/public/seed-data/legal-questions.json');

        if (!file_exists($jsonPath)) {
            $this->command->warn("legal-questions.json not found at: {$jsonPath}");
            $this->command->info("Run: node scripts/extract-seed-data.mjs  inside legalaid2/ first.");
            return;
        }

        $questions = json_decode(file_get_contents($jsonPath), true);

        if (empty($questions)) {
            $this->command->warn('legal-questions.json is empty or invalid.');
            return;
        }

        $this->command->info('Seeding ' . count($questions) . ' legal questions...');
        $bar = $this->command->getOutput()->createProgressBar(count($questions));
        $bar->start();

        foreach ($questions as $i => $data) {
            LegalQuestion::updateOrCreate(
                ['id' => $data['id']],
                [
                    'question'    => $data['question']    ?? '',
                    'answer_html' => $data['answer_html'] ?? '',
                    'answer_text' => $data['answer_text'] ?? '',
                    'category'    => $data['category']    ?? 'ზოგადი',
                    'sort_order'  => $data['id']          ?? $i,
                    'is_active'   => true,
                ]
            );
            $bar->advance();
        }

        $bar->finish();
        $this->command->newLine();
        $this->command->info('Legal questions seeded successfully.');
    }
}
