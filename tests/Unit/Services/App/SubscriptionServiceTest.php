<?php

namespace Tests\Unit\Services\App;

use App\Models\Business;
use App\Models\Outlet;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Services\App\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected SubscriptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new SubscriptionService();
    }

    public function test_it_subscribes_business_to_plan()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $business = Business::first();

        // Create an existing active subscription
        $plan1 = SubscriptionPlan::first();
        $currentSubscription = Subscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan1->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'started_at' => Carbon::now()->subDays(10),
            'expired_at' => Carbon::now()->addDays(20),
        ]);

        $outlet1 = Outlet::create(['business_id' => $business->id, 'name' => 'Outlet 1', 'is_active' => true]);
        $outlet2 = Outlet::create(['business_id' => $business->id, 'name' => 'Outlet 2', 'is_active' => false]);

        $plan2 = SubscriptionPlan::create([
            'name' => 'Pro Plan',
            'code' => 'pro2',
            'price_per_outlet' => 100000,
            'yearly_discount_percent' => 10,
        ]);

        $now = Carbon::now()->startOfDay();
        Carbon::setTestNow($now);

        $newSubscription = $this->service->subscribe($business, $plan2, 'yearly');

        $this->assertInstanceOf(Subscription::class, $newSubscription);
        $this->assertEquals($plan2->id, $newSubscription->plan_id);
        $this->assertEquals('inactive', $newSubscription->status);
        $this->assertEquals('yearly', $newSubscription->billing_cycle);
        $this->assertEquals($now, $newSubscription->started_at);
        $this->assertEquals($now->copy()->addDays(365), $newSubscription->expired_at);

        $activeCount = $business->outlets()->where('is_active', true)->count();
        $this->assertCount($activeCount, $newSubscription->subscriptionOutlets);

        // Check old subscription cancelled
        $currentSubscription->refresh();
        $this->assertEquals('canceled', $currentSubscription->status);
        $this->assertEquals($now, $currentSubscription->canceled_at);
    }

    public function test_it_cancels_subscription()
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $business = Business::first();
        $plan = SubscriptionPlan::first();

        $subscription = Subscription::create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'billing_cycle' => 'monthly',
            'started_at' => Carbon::now()->subDays(10),
            'expired_at' => Carbon::now()->addDays(20),
        ]);

        $now = Carbon::now()->startOfDay();
        Carbon::setTestNow($now);

        $result = $this->service->cancel($subscription);

        $this->assertTrue($result);
        $subscription->refresh();
        $this->assertEquals('canceled', $subscription->status);
        $this->assertEquals($now, $subscription->canceled_at);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
}
