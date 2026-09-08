<?php

namespace Tests\Feature\Console;

use App\Models\Business;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Notifications\SubscriptionRenewalNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SendSubscriptionRenewalNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_sends_notification_for_expiring_subscriptions()
    {
        Notification::fake();

        $plan = SubscriptionPlan::factory()->create();
        $business = Business::factory()->create();
        
        $owner = User::factory()->create();
        $owner->business_id = $business->id;
        $owner->save();

        // Expiring in 7 days
        $subscription = Subscription::factory()->create([
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'expired_at' => Carbon::now()->addDays(7)->startOfDay(),
        ]);

        $this->artisan('subscription:renewal-notification')->assertSuccessful();

        Notification::assertSentTo(
            $owner,
            SubscriptionRenewalNotification::class,
            function ($notification) use ($business, $plan) {
                return $notification->business->id === $business->id &&
                       $notification->plan->id === $plan->id &&
                       $notification->daysRemaining === 7;
            }
        );
    }
}
