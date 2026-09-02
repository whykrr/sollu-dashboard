<?php

namespace App\Services\App\Invoice;

use App\Models\Invoice;
use App\Notifications\SubscriptionActivatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompleteInvoiceService
{
    /**
     * Completes an invoice payment, setting its status to paid
     * and activating the latest associated subscription if applicable.
     */
    public function execute(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $now = Carbon::now();

            // Mark invoice as paid
            $invoice->update([
                'status' => 'paid',
                'paid_at' => $now,
            ]);

            $business = $invoice->business;

            // Check if this invoice is an outlet addition
            $isOutletAddition = false;
            if ($invoice->items()->where('item_type', 'outlet_addition')->exists()) {
                $isOutletAddition = true;
            }

            // Only activate the subscription if it is a recurring plan, OR if the subscription isn't active yet.
            // Generally, we just ensure the latest subscription is set to active.
            $subscription = $business->subscriptions()->latest()->first();

            if ($subscription && $subscription->status !== 'active' && ! $isOutletAddition) {
                $subscription->update([
                    'status' => 'active',
                ]);

                // Send notification to the first user (business owner)
                $owner = $business->users()->first();
                if ($owner) {
                    try {
                        $expiredAt = $subscription->expired_at
                            ? $subscription->expired_at->translatedFormat('d F Y')
                            : 'Lifetime';

                        $owner->notify(new SubscriptionActivatedNotification(
                            $business,
                            $subscription->plan,
                            $expiredAt
                        ));
                    } catch (\Exception $e) {
                        Log::error('Gagal mengirim notifikasi aktivasi langganan: '.$e->getMessage());
                    }
                }
            }

            if ($isOutletAddition) {
                $outletAdditionItem = $invoice->items()->where('item_type', 'outlet_addition')->first();
                if ($outletAdditionItem && isset($outletAdditionItem->metadata['outlet_id'])) {
                    $outlet = \App\Models\Outlet::find($outletAdditionItem->metadata['outlet_id']);
                    if ($outlet && ! $outlet->is_active) {
                        $owner = $business->users()->first();
                        app(\App\Services\App\Outlet\ManageOutletStatusService::class)->toggleStatus($outlet, true, $owner);

                        if ($owner) {
                            try {
                                $owner->notify(new \App\Notifications\OutletActivatedNotification($outlet));
                            } catch (\Exception $e) {
                                Log::error('Gagal mengirim notifikasi aktivasi outlet: '.$e->getMessage());
                            }
                        }
                    }
                }
            }

            return $invoice;
        });
    }
}
