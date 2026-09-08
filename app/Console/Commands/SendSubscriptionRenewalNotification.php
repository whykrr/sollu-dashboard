<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Notifications\SubscriptionRenewalNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendSubscriptionRenewalNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:renewal-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send subscription renewal notifications based on expired_at (H-14, H-7, H-3, H-1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting subscription renewal notification process...');
        
        $intervals = [14, 7, 3, 1];
        $count = 0;

        foreach ($intervals as $days) {
            $targetDate = Carbon::now()->addDays($days)->startOfDay();

            $subscriptions = Subscription::with(['business.users', 'plan'])
                ->where('status', 'active')
                ->whereDate('expired_at', $targetDate)
                ->get();

            foreach ($subscriptions as $subscription) {
                if (!$subscription->business || !$subscription->plan) {
                    continue;
                }

                $owner = $subscription->business->users()->first();
                if ($owner) {
                    try {
                        $expiredAtFormatted = $subscription->expired_at->translatedFormat('d F Y');
                        $owner->notify(new SubscriptionRenewalNotification(
                            $subscription->business,
                            $subscription->plan,
                            $expiredAtFormatted,
                            $days
                        ));
                        $this->info("Notification sent for business: {$subscription->business->name} (Days left: {$days})");
                        $count++;
                    } catch (\Exception $e) {
                        Log::error('Failed to send renewal notification: ' . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Successfully sent {$count} renewal notifications.");
    }
}
