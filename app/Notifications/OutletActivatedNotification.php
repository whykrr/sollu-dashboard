<?php

namespace App\Notifications;

use App\Mail\OutletActivatedMail;
use App\Models\Outlet;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class OutletActivatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Outlet $outlet) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new OutletActivatedMail($this->outlet))->to($notifiable->email);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'outlet_activated',
            'title' => 'Penambahan Outlet Berhasil',
            'message' => 'Pembayaran telah dikonfirmasi dan Outlet "'.$this->outlet->name.'" telah berhasil diaktifkan.',
            'business_id' => $this->outlet->business_id,
            'outlet_id' => $this->outlet->id,
        ];
    }
}
