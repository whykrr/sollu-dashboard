<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\OutletAuditLog;
use App\Models\SubscriptionInvoice;
use App\Models\User;
use App\Services\App\BillingEngine;
use App\Services\App\Outlet\CreateOutletService;
use App\Services\App\Outlet\OutletProvisioningService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class CreateOutletServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BillingEngine $billingEngineMock;
    protected OutletProvisioningService $provisioningServiceMock;
    protected CreateOutletService $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->billingEngineMock = Mockery::mock(BillingEngine::class);
        $this->provisioningServiceMock = Mockery::mock(OutletProvisioningService::class);
        $this->provisioningServiceMock->shouldReceive('provisionAll')->andReturnNull();

        $this->service = new CreateOutletService(
            $this->billingEngineMock,
            $this->provisioningServiceMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_it_creates_outlet_without_active_subscription()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first(); // First user is usually root user from seeder
        
        $data = [
            'name' => 'New Outlet',
            'address' => 'Test Address',
            'phone' => '12345678',
            'email' => 'outlet@example.com',
        ];

        // Simulate request user for SummaryUser::cacheDelete()
        $this->actingAs($user);

        $result = $this->service->execute($data, $user);

        $this->assertIsArray($result);
        $this->assertInstanceOf(Outlet::class, $result['outlet']);
        $this->assertNull($result['invoice']);

        $this->assertEquals('New Outlet', $result['outlet']->name);
        $this->assertFalse($result['outlet']->is_active);

        // Assert it was attached to root user
        $this->assertTrue($user->outlets->contains($result['outlet']->id));

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $result['outlet']->id,
            'user_id' => $user->id,
            'action' => 'created',
        ]);
    }

    public function test_it_creates_outlet_and_generates_invoice_for_active_subscription()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        
        // Mock active subscription
        $plan = \App\Models\SubscriptionPlan::first();
        
        $subscription = $business->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $mockInvoice = new \App\Models\Invoice(['id' => \Illuminate\Support\Str::uuid()]);
        
        $this->billingEngineMock->shouldReceive('generateOutletProratedInvoice')
            ->once()
            ->andReturn($mockInvoice);

        $data = [
            'name' => 'New Outlet With Invoice',
        ];

        $this->actingAs($user);

        $result = $this->service->execute($data, $user);

        $this->assertInstanceOf(Outlet::class, $result['outlet']);
        $this->assertEquals($mockInvoice, $result['invoice']);
    }
}
