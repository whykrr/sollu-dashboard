<?php

namespace App\Listeners\Inventory;

use App\Events\Transaction\TransactionCompleted;
use App\Services\App\Transaction\InventoryDeductionService;

class DeductStockOnTransactionListener
{
    public function __construct(protected InventoryDeductionService $stockDeductionService) {}

    public function handle(TransactionCompleted $event): void
    {
        $this->stockDeductionService->deductFromTransaction($event->transaction);
    }
}
