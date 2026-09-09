<?php

namespace App\Notifications;

use App\Mail\SubscriptionRenewalMail;
use App\Models\Business;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Business $business,
        public SubscriptionPlan $plan,
        public string $expiredAt,
        public int $daysRemaining
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new SubscriptionRenewalMail($this->business, $this->plan, $this->expiredAt, $this->daysRemaining))
            ->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'subscription_renewal',
            'title' => 'Pengingat Perpanjangan Langganan',
            'message' => 'Langganan paket '.$this->plan->name.' Anda akan berakhir pada '.$this->expiredAt.'. Segera lakukan perpanjangan agar layanan tidak terhenti.',
            'action_url' => route('settings.billing.checkout', ['plan_id' => $this->plan->id]).'?is_renewal=1',
            'business_id' => $this->business->id,
            'plan_id' => $this->plan->id,
        ];
    }
}
