<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiAuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_auth_login_returns_token_for_valid_credentials(): void
    {
        /** @var User $user */
        $user = User::factory()->create([
            'email' => 'admin-login-test@legalaid.ge',
            'password' => Hash::make('LegalAid@2026!'),
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'LegalAid@2026!',
        ]);

        $response
            ->assertOk()
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'name', 'email'],
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 1);
    }

    public function test_api_auth_logout_revokes_the_current_token(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/auth/logout')
            ->assertOk()
            ->assertJson([
                'message' => 'Logged out.',
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_api_auth_login_rejects_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin-invalid-test@legalaid.ge',
            'password' => Hash::make('LegalAid@2026!'),
        ]);

        $this->postJson('/api/auth/login', [
            'email' => 'admin-invalid-test@legalaid.ge',
            'password' => 'wrong-password',
        ])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }
}