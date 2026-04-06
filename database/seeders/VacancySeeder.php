<?php

namespace Database\Seeders;

use App\Models\Vacancy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class VacancySeeder extends Seeder
{
    public function run(): void
    {
        Vacancy::truncate();

        $title = 'გურჯაანის იურიდიული დახმარების ბიუროს ადვოკატი - სისხლის სამართლის მიმართულებით (შტატგარეშე, შრომითი ხელშეკრულებით დასაქმებული)';

        Vacancy::create([
            'title'            => $title,
            'slug'             => Str::slug($title),
            'excerpt'          => 'სადემო ვაკანსია დამატებულია გვერდის სანახავად. სრული ინფორმაცია, პირობები და კონკურსის დეტალები ხელმისაწვდომია ოფიციალურ ბმულზე.',
            'content'          => '<p>სრული ინფორმაცია კონკურსის პირობების, მოთხოვნებისა და განაცხადის წარდგენის შესახებ ხელმისაწვდომია ოფიციალურ პორტალზე.</p>',
            'department'       => 'გურჯაანის იურიდიული დახმარების ბიურო',
            'location'         => 'გურჯაანი',
            'type'             => 'full_time',
            'status'           => 'open',
            'is_active'        => true,
            'sort_order'       => 0,
            'requirements'     => [],
            'responsibilities' => [],
            'application_url'  => 'https://vacancy.hr.gov.ge/JobProvider/UserOrgVaks/Details/98138',
        ]);

        $this->command->info('Vacancies seeded: 1 real vacancy from frontend.');
    }
}
