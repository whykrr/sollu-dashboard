<?php

namespace App\Notifications;

use App\Mail\SubscriptionActivatedMail;
use App\Models\Business;
use App\Models\SubscriptionPlan;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SubscriptionActivatedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Business $business,
        public SubscriptionPlan $plan,
        public string $expiredAt
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new SubscriptionActivatedMail($this->business, $this->plan, $this->expiredAt))
            ->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'subscription_activated',
            'title' => 'Langganan Aktif',
            'message' => 'Langganan paket '.$this->plan->name.' telah diaktifkan sampai '.$this->expiredAt.'.',
            'business_id' => $this->business->id,
            'plan_id' => $this->plan->id,
        ];
    }
}
