<?php

namespace App\Services\App;

use App\Models\Business;
use App\Models\Subscription;
use App\Models\SubscriptionPlan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Subscribe business to a plan
     */
    public function subscribe(Business $business, SubscriptionPlan $plan, string $billing_cycle = 'monthly'): Subscription
    {
        /** @var Subscription */
        $subscription = DB::transaction(function () use ($business, $plan, $billing_cycle) {
            // We no longer cancel the current active subscription here.
            // It will be canceled only when the new subscription invoice is paid,
            // so the user does not lose their current subscription while waiting for payment.

            $durationDays = $billing_cycle === 'yearly' ? 365 : 30;

            $subscription = Subscription::create([
                'business_id' => $business->id,
                'plan_id' => $plan->id,
                'status' => 'inactive',
                'billing_cycle' => $billing_cycle,
                'started_at' => Carbon::now(),
                'expired_at' => Carbon::now()->addDays($durationDays),
            ]);

            // Sync all active outlets to the new subscription
            $activeOutlets = $business->outlets()->where('is_active', true)->get();
            foreach ($activeOutlets as $outlet) {
                $subscription->subscriptionOutlets()->create([
                    'outlet_id' => $outlet->id,
                    'activated_at' => Carbon::now(),
                ]);
            }

            return $subscription;
        });

        return $subscription;
    }

    /**
     * Cancel a subscription
     */
    public function cancel(Subscription $subscription): bool
    {
        return $subscription->update([
            'status' => 'canceled',
            'canceled_at' => Carbon::now(),
        ]);
    }
}
