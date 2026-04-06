<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create default admin user (CHANGE PASSWORD IMMEDIATELY after setup!)
        User::firstOrCreate(
            ['email' => 'admin@legalaid.ge'],
            [
                'name'      => 'Admin',
                'password'  => Hash::make('LegalAid@2026!'),
                'role'      => 'admin',
                'is_active' => true,
            ]
        );

        $this->call([
            SettingsSeeder::class,
            StatsSeeder::class,
            ServicesSeeder::class,
            LegalQuestionSeeder::class,
            DocumentSeeder::class,
            PostSeeder::class,
            PageContentSeeder::class,
            OfficeSeeder::class,
            VacancySeeder::class,
            JournalSeeder::class,
            ProjectSeeder::class,
            VideoSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeded successfully!');
        $this->command->newLine();
        $this->command->warn('⚠️  SECURITY: Change the admin password IMMEDIATELY at /admin!');
        $this->command->line('   Email   : admin@legalaid.ge');
        $this->command->line('   Password: LegalAid@2026!');
        $this->command->newLine();
    }
}
