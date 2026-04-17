<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HrUserSeeder extends Seeder
{
    public const EMAIL = 'hr@legalaid.ge';

    public const PASSWORD = 'HrVacancy@2026!';

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => self::EMAIL],
            [
                'name' => 'HR Admin',
                'password' => Hash::make(self::PASSWORD),
                'role' => 'hr',
                'is_active' => true,
            ],
        );
    }
}