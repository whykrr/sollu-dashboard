<?php

namespace App\Events\Transaction;

use App\Models\Sales\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionReversed
{
    use Dispatchable, SerializesModels;

    /**
     * @param  Transaction  $transaction  The reversed transaction
     * @param  bool  $wasUnpaidOrPartial  Whether the transaction was previously unpaid/partial and requires stock restoration
     */
    public function __construct(public readonly Transaction $transaction, public readonly bool $requiresStockRestoration = true) {}
}
