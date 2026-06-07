<?php

namespace Tests\Feature;

use App\Support\InstallState;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    protected bool $hadMarker = false;

    protected ?string $markerContents = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->hadMarker = InstallState::isInstalled();
        $this->markerContents = $this->hadMarker
            ? file_get_contents(InstallState::markerPath())
            : null;

        InstallState::clear();
        config(['installer.enabled' => true]);
    }

    protected function tearDown(): void
    {
        InstallState::clear();

        if ($this->hadMarker && $this->markerContents !== null) {
            InstallState::ensureDirectoryExists();
            file_put_contents(InstallState::markerPath(), $this->markerContents);
        }

        parent::tearDown();
    }

    public function test_uninstalled_browser_requests_redirect_to_installer(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect(route('install.show'));
    }

    public function test_uninstalled_api_requests_return_install_required_response(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response
            ->assertStatus(503)
            ->assertJson([
                'message' => 'Application is not installed yet.',
                'status' => 'install_required',
            ]);
    }

    public function test_install_screen_is_accessible_before_installation(): void
    {
        $response = $this->get('/install');

        $response
            ->assertOk()
            ->assertSee('Legal Aid installer')
            ->assertSee('Install application');
    }
}