<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/up');
        $response->assertStatus(200);
    }

    public function test_api_services_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/services');
        $response->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_api_posts_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/posts');
        $response->assertStatus(200)->assertJsonStructure(['data']);
    }

    public function test_api_stats_endpoint_returns_json(): void
    {
        $response = $this->getJson('/api/v1/stats');
        $response->assertStatus(200)->assertJsonStructure(['data']);
    }
}
