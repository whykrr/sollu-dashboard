<?php

namespace App\Notifications;

use App\Mail\PaymentRejectedMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class InvoicePaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Invoice $invoice,
        public string $rejectionReason
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable)
    {
        return (new PaymentRejectedMail($this->invoice, $this->rejectionReason))
            ->to($notifiable->email);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'rejection_reason' => $this->rejectionReason,
            'message' => 'Pembayaran untuk invoice '.$this->invoice->invoice_number.' ditolak.',
        ];
    }
}
