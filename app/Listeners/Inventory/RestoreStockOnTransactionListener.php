<?php

namespace App\Listeners\Inventory;

use App\Events\Transaction\TransactionReversed;
use App\Services\App\Transaction\InventoryDeductionService;

class RestoreStockOnTransactionListener
{
    public function __construct(protected InventoryDeductionService $stockDeductionService) {}

    public function handle(TransactionReversed $event): void
    {
        if ($event->requiresStockRestoration) {
            $this->stockDeductionService->restoreFromTransaction($event->transaction);
        }
    }
}
