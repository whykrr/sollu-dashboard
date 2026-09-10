<?php

namespace Tests\Feature\Settings;

use App\Enums\PermissionEnum;
use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use Carbon\Carbon;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['inertia.testing.page_paths' => [resource_path('js/Pages/App')]]);
        $this->seed(DatabaseSeeder::class);
    }

    public function test_unauthenticated_user_cannot_access_billing_page(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $response = $this->get("http://{$appDomain}/settings/billing");

        $response->assertStatus(302);
    }

    public function test_user_without_permission_cannot_access_billing_page(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $user = User::first();
        $user->syncPermissions([]);
        $user->syncRoles([]);

        $response = $this->actingAs($user, 'business')->get("http://{$appDomain}/settings/billing");

        $response->assertRedirect();
        $response->assertSessionHas('failed');
    }

    public function test_authorized_user_can_access_billing_page_with_expected_props(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $user = User::first();
        $user->givePermissionTo(PermissionEnum::BUSINESS_BILLING->value);

        $business = $user->business;
        $plan = SubscriptionPlan::first();

        $now = Carbon::now();
        Subscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'started_at' => $now->copy()->subDays(5),
            'expired_at' => $now->copy()->addDays(25),
        ]);

        $pendingInvoice = Invoice::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-TEST-001',
            'status' => 'open',
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total_amount' => 111000,
            'due_date' => $now->copy()->addDays(3),
        ]);

        $response = $this->actingAs($user, 'business')->get("http://{$appDomain}/settings/billing");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Settings/Billing/Index')
            ->has('subscription')
            ->has('pendingInvoice')
            ->has('maxOutlets')
            ->has('invoices')
            ->where('pendingInvoice.id', $pendingInvoice->id)
            ->where('maxOutlets', $plan->max_outlet)
        );
    }

    public function test_authorized_user_can_access_billing_page_with_void_invoices(): void
    {
        $appDomain = config('domain.app', 'app.sollu.test');
        $user = User::first();
        $user->givePermissionTo(PermissionEnum::BUSINESS_BILLING->value);

        $business = $user->business;

        Invoice::create([
            'business_id' => $business->id,
            'invoice_number' => 'INV-TEST-VOID-001',
            'status' => 'void',
            'subtotal' => 100000,
            'tax_amount' => 0,
            'total_amount' => 100000,
            'due_date' => Carbon::now()->addDays(3),
        ]);

        $response = $this->actingAs($user, 'business')->get("http://{$appDomain}/settings/billing");

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Settings/Billing/Index')
            ->has('invoices.data', 1)
            ->where('invoices.data.0.status', 'void')
        );
    }
}
