<?php

namespace App\Http\Controllers\API\Midtrans;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        Log::info('midtrans_notification', $request->all());

        $serverKey = config('midtrans.server_key');

        $signature = hash(
            'sha512',
            $request->order_id.
            $request->status_code.
            $request->gross_amount.
            $serverKey
        );

        if (! hash_equals($signature, $request->signature_key)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('payment_reference', $request->order_id)->first();

        if (! $payment) {
            return response()->json(['message' => 'Payment record not found'], 404);
        }

        if ($payment->status === 'success') {
            return response()->json(['message' => 'Payment already processed'], 200);
        }

        $payment->payment_method = $request->payment_type ?? 'midtrans';
        $payment->json_respond = $request->all();

        $transactionStatus = $request->transaction_status;

        DB::beginTransaction();
        try {
            $invoice = $payment->invoice;

            if (
                $transactionStatus === \App\Enums\SubscriptionPayment\Status::Settlement->value ||
                ($transactionStatus === \App\Enums\SubscriptionPayment\Status::Capture->value && $request->fraud_status === 'accept')
            ) {
                $payment->status = 'success';
                $payment->paid_at = Carbon::now();

                $completeService = app(\App\Services\App\Invoice\CompleteInvoiceService::class);
                $completeService->execute($invoice);
            } elseif (in_array($transactionStatus, [
                \App\Enums\SubscriptionPayment\Status::Deny->value,
                \App\Enums\SubscriptionPayment\Status::Cancel->value,
                \App\Enums\SubscriptionPayment\Status::Expire->value,
                \App\Enums\SubscriptionPayment\Status::Failure->value,
            ])) {
                $payment->status = 'failed';
            } else {
                $payment->status = 'pending';
            }

            $payment->save();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        return response()->json(['message' => 'Successfully processed notification'], 200);
    }
}
