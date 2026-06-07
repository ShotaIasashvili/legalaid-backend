<?php

namespace Database\Seeders;

use App\Models\Journal;
use Illuminate\Database\Seeder;

class JournalSeeder extends Seeder
{
    public function run(): void
    {
        $journals = [
            [
                'issue_number'  => '1',
                'year'          => '2020',
                'title'         => 'ნომერი 1',
                'slug'          => 'advokati-1',
                'description'   => 'პირველი ნომერი მიეძღვნა იურიდიული დახმარების სამსახურის დაარსების 15 წლისთავს.',
                'file_path'     => 'journals/ADVOKAT_N1.pdf',
                'cover_image'   => 'journals/journal_n1.png',
            ],
            [
                'issue_number'  => '2',
                'year'          => '2021',
                'title'         => 'ნომერი 2',
                'slug'          => 'advokati-2',
                'description'   => 'მეორე გამოცემა — 2021 წელი.',
                'file_path'     => 'journals/ADVOKATI_N2_2021_FOR WEB.pdf',
                'cover_image'   => 'journals/journal_n2.png',
            ],
            [
                'issue_number'  => '3',
                'year'          => '2022',
                'title'         => 'ნომერი 3',
                'slug'          => 'advokati-3',
                'description'   => 'მესამე გამოცემა.',
                'file_path'     => 'journals/ADVOKATI_3_compressed.pdf',
                'cover_image'   => 'journals/journal_n3.png',
            ],
            [
                'issue_number'  => '4',
                'year'          => '2023',
                'title'         => 'ნომერი 4',
                'slug'          => 'advokati-4',
                'description'   => 'მეოთხე გამოცემა.',
                'file_path'     => 'journals/ADVOKAT_N4.pdf',
                'cover_image'   => 'journals/journal_n4.png',
            ],
            [
                'issue_number'  => '5',
                'year'          => '2024',
                'title'         => 'ნომერი 5',
                'slug'          => 'advokati-5',
                'description'   => 'მეხუთე გამოცემა — 2024 წელი.',
                'file_path'     => 'journals/ADVOKAT_5_2024_.pdf',
                'cover_image'   => 'journals/journal_n5.png',
            ],
            [
                'issue_number'  => '6',
                'year'          => '2025',
                'title'         => 'ნომერი 6',
                'slug'          => 'advokati-6',
                'description'   => 'მეექვსე გამოცემა.',
                'file_path'     => 'journals/ADVOCATE_N6.pdf',
                'cover_image'   => 'journals/journal_n6.png',
            ],
            [
                'issue_number'  => '7',
                'year'          => '2025',
                'title'         => 'ნომერი 7',
                'slug'          => 'advokati-7',
                'description'   => 'უახლესი, მეშვიდე გამოცემა.',
                'file_path'     => 'journals/ADVOCATI_N7.pdf',
                'cover_image'   => 'journals/journal_n7.png',
            ],
        ];

        foreach ($journals as $i => $data) {
            Journal::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge($data, [
                    'cover_image_thumbnail' => $data['cover_image'],
                    'volume'                => '1',
                    'is_active'             => true,
                    'sort_order'            => $i + 1,
                    'download_count'        => 0,
                    'published_at'          => $data['year'] . '-01-01',
                ])
            );
        }
    }
}
