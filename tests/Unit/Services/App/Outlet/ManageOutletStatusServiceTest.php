<?php

namespace Tests\Unit\Services\App\Outlet;

use App\Models\Business;
use App\Models\Invoice;
use App\Models\Outlet;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\App\Outlet\ManageOutletStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ManageOutletStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ManageOutletStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ManageOutletStatusService();
    }

    public function test_it_toggles_status_to_active()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Outlet Test',
            'is_active' => false,
        ]);

        // Mock subscription
        $plan = SubscriptionPlan::first();
        $subscription = $business->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $this->actingAs($user);

        $result = $this->service->toggleStatus($outlet, true, $user);

        $this->assertTrue($result->is_active);
        
        $this->assertDatabaseHas('subscription_outlets', [
            'subscription_id' => $subscription->id,
            'outlet_id' => $outlet->id,
            'deactivated_at' => null,
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'action' => 'enabled',
        ]);
    }

    public function test_it_fails_to_activate_if_unpaid_invoice()
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Outlet Test',
            'is_active' => false,
        ]);

        $invoice = Invoice::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-123',
            'status' => 'open',
            'subtotal' => 1000,
            'tax_amount' => 0,
            'total_amount' => 1000,
            'due_date' => now()->addDays(3),
        ]);

        $invoice->items()->create([
            'item_type' => 'outlet_addition',
            'description' => 'Outlet addition',
            'quantity' => 1,
            'unit_price' => 1000,
            'total_price' => 1000,
            'metadata' => ['outlet_id' => $outlet->id],
        ]);

        $this->actingAs($user);

        $this->service->toggleStatus($outlet, true, $user);
    }

    public function test_it_toggles_status_to_inactive()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $business = $user->business;
        $outlet = Outlet::create([
            'business_id' => $business->id,
            'name' => 'Outlet Test',
            'is_active' => true,
        ]);

        $plan = SubscriptionPlan::first();
        $subscription = $business->subscriptions()->create([
            'plan_id' => $plan->id,
            'status' => 'active',
            'current_period_start' => now(),
            'current_period_end' => now()->addMonth(),
        ]);

        $subscription->subscriptionOutlets()->create([
            'outlet_id' => $outlet->id,
            'activated_at' => now()->subDay(),
        ]);

        $this->actingAs($user);

        $result = $this->service->toggleStatus($outlet, false, $user);

        $this->assertFalse($result->is_active);
        
        $this->assertDatabaseMissing('subscription_outlets', [
            'subscription_id' => $subscription->id,
            'outlet_id' => $outlet->id,
            'deactivated_at' => null,
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'action' => 'disabled',
        ]);
    }

    public function test_it_deletes_outlet()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Delete',
        ]);

        $this->actingAs($user);

        $this->service->delete($outlet, $user);

        $this->assertSoftDeleted('outlets', ['id' => $outlet->id]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'action' => 'deleted',
        ]);
    }

    public function test_it_restores_outlet()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $user = User::first();
        $outlet = Outlet::create([
            'business_id' => $user->business_id,
            'name' => 'Outlet Restore',
        ]);

        $outlet->delete();

        $this->actingAs($user);

        $result = $this->service->restore($outlet->id, $user);

        $this->assertEquals($outlet->id, $result->id);
        $this->assertDatabaseHas('outlets', [
            'id' => $outlet->id,
            'deleted_at' => null,
        ]);

        $this->assertDatabaseHas('outlet_audit_logs', [
            'outlet_id' => $outlet->id,
            'action' => 'restored',
        ]);
    }
}
