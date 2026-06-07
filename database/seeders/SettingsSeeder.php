<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'site_name',        'value' => 'იურიდიული დახმარების სამსახური',                  'type' => 'text',  'group' => 'general', 'label' => 'საიტის სახელი'],
            ['key' => 'site_tagline',     'value' => 'სამართლებრივი დახმარება ყველასთვის',              'type' => 'text',  'group' => 'general', 'label' => 'ტეგლაინი'],
            ['key' => 'site_description', 'value' => 'სსიპ — იურიდიული დახმარების სამსახური',           'type' => 'text',  'group' => 'general', 'label' => 'მოკლე აღწ.'],
            ['key' => 'maintenance_mode', 'value' => '0',                                                'type' => 'boolean','group' => 'general', 'label' => 'Maintenance Mode'],

            // Contact
            ['key' => 'phone_main',   'value' => '+995 (32) 2920055',           'type' => 'text', 'group' => 'contact', 'label' => 'მთ. ტელ.'],
            ['key' => 'phone_hotline','value' => '1485',                         'type' => 'text', 'group' => 'contact', 'label' => 'ცხ. ხ.'],
            ['key' => 'email',        'value' => 'info@legalaid.ge',             'type' => 'text', 'group' => 'contact', 'label' => 'ელ-ფ.'],
            ['key' => 'address',      'value' => 'თბილისი, 0114, კ. ხუცის 12', 'type' => 'text', 'group' => 'contact', 'label' => 'მისამ.'],
            ['key' => 'working_hours','value' => 'ორ–პარ: 9:00–18:00',          'type' => 'text', 'group' => 'contact', 'label' => 'სამ. სა.'],

            // Social
            ['key' => 'facebook_url',  'value' => 'https://www.facebook.com/legalaid.ge', 'type' => 'text', 'group' => 'social', 'label' => 'Facebook'],
            ['key' => 'youtube_url',   'value' => '',                                      'type' => 'text', 'group' => 'social', 'label' => 'YouTube'],
            ['key' => 'twitter_url',   'value' => '',                                      'type' => 'text', 'group' => 'social', 'label' => 'Twitter/X'],

            // SEO
            ['key' => 'seo_title',    'value' => 'იურიდიული დახმარების სამსახური',  'type' => 'text', 'group' => 'seo', 'label' => 'SEO სათ.'],
            ['key' => 'seo_keywords', 'value' => 'იურიდიული დახმარება, legal aid, saqartvelo', 'type' => 'text', 'group' => 'seo', 'label' => 'SEO Keywords'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(
                ['key' => $s['key']],
                [
                    'value' => $s['value'],
                    'type'  => $s['type'],
                    'group' => $s['group'],
                    'label' => $s['label'],
                ]
            );
        }

        $this->command->info('Settings seeded.');
    }
}
