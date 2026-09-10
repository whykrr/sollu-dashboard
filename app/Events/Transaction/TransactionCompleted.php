<?php

namespace App\Events\Transaction;

use App\Models\Sales\Transaction;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCompleted
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Transaction $transaction) {}
}
