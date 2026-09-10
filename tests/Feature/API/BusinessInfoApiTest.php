<?php

namespace Tests\Feature\API;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessInfoApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_business_info(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $response = $this->get("http://{$appDomain}/api/internal/business-info");

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_retrieve_business_info(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $user = User::first();

        $response = $this->actingAs($user, 'business')
            ->get("http://{$appDomain}/api/internal/business-info");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'business_type',
                'plan_name',
                'expired_at',
                'outlet_count',
            ]);

        $data = $response->json();
        $this->assertIsString($data['business_type']);
        $this->assertIsString($data['plan_name']);
        $this->assertIsInt($data['outlet_count']);
    }
}
