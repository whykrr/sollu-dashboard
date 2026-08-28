<?php

namespace Tests\Feature\API;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function test_api_health_check_returns_healthy_status(): void
    {
        $domain = config('domain.api', 'api.sollu.test');

        $response = $this->get("http://{$domain}/health");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'environment',
                'services' => [
                    'database' => [
                        'status',
                        'latency_ms',
                    ],
                    'cache' => [
                        'status',
                        'driver',
                    ],
                    'storage' => [
                        'status',
                    ],
                ],
            ])
            ->assertJson([
                'status' => 'healthy',
                'services' => [
                    'database' => [
                        'status' => 'healthy',
                    ],
                    'cache' => [
                        'status' => 'healthy',
                    ],
                    'storage' => [
                        'status' => 'healthy',
                    ],
                ],
            ]);
    }

    public function test_legacy_api_health_check_fallback_route_returns_healthy(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');

        $response = $this->get("http://{$appDomain}/api/health");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'healthy',
            ]);
    }
}
