<?php

namespace Tests\Feature;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\PostResource;
use App\Filament\Resources\VacancyResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_user_is_redirected_from_admin_root_to_vacancies(): void
    {
        /** @var User $hrUser */
        $hrUser = User::factory()->create([
            'email' => 'hr-test@legalaid.ge',
            'role' => 'hr',
            'is_active' => true,
        ]);

        $this->actingAs($hrUser)
            ->get('/admin')
            ->assertRedirect(VacancyResource::getUrl(panel: 'admin'));
    }

    public function test_hr_user_can_access_vacancies_but_not_other_admin_resources(): void
    {
        /** @var User $hrUser */
        $hrUser = User::factory()->create([
            'email' => 'hr-resource-test@legalaid.ge',
            'role' => 'hr',
            'is_active' => true,
        ]);

        $this->actingAs($hrUser)
            ->get(VacancyResource::getUrl(panel: 'admin'))
            ->assertOk();

        $this->actingAs($hrUser)
            ->get(Dashboard::getUrl(panel: 'admin'))
            ->assertForbidden();

        $this->actingAs($hrUser)
            ->get(PostResource::getUrl(panel: 'admin'))
            ->assertForbidden();
    }

    public function test_admin_user_is_redirected_from_admin_root_to_dashboard(): void
    {
        /** @var User $adminUser */
        $adminUser = User::factory()->create([
            'email' => 'admin-test@legalaid.ge',
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->actingAs($adminUser)
            ->get('/admin')
            ->assertRedirect(Dashboard::getUrl(panel: 'admin'));
    }
}