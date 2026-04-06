<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class VideoSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            [
                'youtube_id' => '8-a_Wp-kS44',
                'title'      => 'იურიდიული დახმარების სამსახურის შესახებ',
                'category'   => 'სამსახურის შესახებ',
            ],
            [
                'youtube_id' => '4IgI8OiKlmg',
                'title'      => 'მოქალაქეთა სამართლებრივი დახმარება',
                'category'   => 'სამართლებრივი დახმარება',
            ],
            [
                'youtube_id' => 'N1ZuB-Oib2U',
                'title'      => 'სამართლებრივი კონსულტაცია',
                'category'   => 'სამართლებრივი დახმარება',
            ],
            [
                'youtube_id' => 'eqzCr1oCZkg',
                'title'      => 'სამართლებრივი დახმარება მოქალაქეებისთვის',
                'category'   => 'სამართლებრივი დახმარება',
            ],
            [
                'youtube_id' => 'RRbw7SEfi14',
                'title'      => 'სასამართლოში წარმომადგენლობა',
                'category'   => 'სასამართლო',
            ],
            [
                'youtube_id' => 'M5oAfraQyU0',
                'title'      => 'პარალეგალის მომსახურება',
                'category'   => 'პარალეგალი',
            ],
            [
                'youtube_id' => 'HFaBcJIcvcs',
                'title'      => 'კვალიფიციური სამართლებრივი დახმარება',
                'category'   => 'სამართლებრივი დახმარება',
            ],
            [
                'youtube_id' => 'TnCtl3KZJVI',
                'title'      => 'სამართლებრივი განათლება',
                'category'   => 'განათლება',
            ],
            [
                'youtube_id' => '_9nusxfxEk4',
                'title'      => 'ბიუროების მომსახურება',
                'category'   => 'სამსახურის შესახებ',
            ],
            [
                'youtube_id' => '6TLVqWIHcYs',
                'title'      => 'სამართლებრივი რეფორმა და სამსახური',
                'category'   => 'სამსახურის შესახებ',
            ],
        ];

        foreach ($videos as $i => $data) {
            Video::updateOrCreate(
                ['youtube_id' => $data['youtube_id']],
                array_merge($data, [
                    'youtube_url' => 'https://www.youtube.com/watch?v=' . $data['youtube_id'],
                    'thumbnail'   => 'https://img.youtube.com/vi/' . $data['youtube_id'] . '/maxresdefault.jpg',
                    'is_active'   => true,
                    'sort_order'  => $i + 1,
                ])
            );
        }
    }
}
