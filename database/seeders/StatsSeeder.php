<?php

namespace Database\Seeders;

use App\Models\Stat;
use Illuminate\Database\Seeder;

class StatsSeeder extends Seeder
{
    public function run(): void
    {
        $stats = [
            [
                'label'      => 'ადამიანი ისარგებლა',
                'value'      => '127,000+',
                'suffix'     => null,
                'icon'       => 'heroicon-o-users',
                'color'      => '#8B2635',
                'group'      => 'homepage',
                'sort_order' => 1,
            ],
            [
                'label'      => 'უფასო კონსულტაცია',
                'value'      => '85,000+',
                'suffix'     => null,
                'icon'       => 'heroicon-o-chat-bubble-left-right',
                'color'      => '#1d4ed8',
                'group'      => 'homepage',
                'sort_order' => 2,
            ],
            [
                'label'      => 'სამართლებრივი დახმარება',
                'value'      => '42,000+',
                'suffix'     => null,
                'icon'       => 'heroicon-o-scale',
                'color'      => '#059669',
                'group'      => 'homepage',
                'sort_order' => 3,
            ],
            [
                'label'      => 'ოფისი საქართველოში',
                'value'      => '68',
                'suffix'     => '+',
                'icon'       => 'heroicon-o-building-office',
                'color'      => '#7c3aed',
                'group'      => 'homepage',
                'sort_order' => 4,
            ],
        ];

        foreach ($stats as $stat) {
            Stat::updateOrCreate(
                ['label' => $stat['label'], 'group' => $stat['group']],
                array_merge($stat, ['is_active' => true])
            );
        }

        $this->command->info('Stats seeded.');
    }
}
