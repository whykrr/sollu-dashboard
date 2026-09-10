<?php

namespace App\Services\App\Role;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\Business;
use App\Models\Role;

class RoleProvisioningService
{
    /**
     * Provision default roles for a specific business.
     */
    public function provision(Business $business): void
    {
        setPermissionsTeamId($business->id);

        // Owner
        $owner = Role::firstOrCreate(
            ['name' => RoleEnum::OWNER->value, 'guard_name' => 'business', 'business_id' => $business->id],
            ['label' => RoleEnum::OWNER->label(), 'is_default' => true]
        );
        $owner->syncPermissions(PermissionEnum::values());

        // Manager
        $manager = Role::firstOrCreate(
            ['name' => RoleEnum::MANAGER->value, 'guard_name' => 'business', 'business_id' => $business->id],
            ['label' => RoleEnum::MANAGER->label(), 'is_default' => true]
        );
        $manager->syncPermissions([
            PermissionEnum::OUTLET_VIEW->value,
            PermissionEnum::TRANSACTION_ALL->value,
            PermissionEnum::PRODUCT_VIEW->value,
            PermissionEnum::PRODUCT_UPDATE->value,
            PermissionEnum::CATEGORY_VIEW->value,
            PermissionEnum::INVENTORY_VIEW->value,
            PermissionEnum::INVENTORY_ADJUSTMENT_READ->value,
            PermissionEnum::INVENTORY_ADJUSTMENT_CREATE->value,
            PermissionEnum::INVENTORY_ADJUSTMENT_APPROVE->value,
            PermissionEnum::INVENTORY_ADJUSTMENT_EXPORT->value,
            PermissionEnum::INVENTORY_ADJUSTMENT_FREEZE->value,
            PermissionEnum::INVENTORY_TRANSFER_READ->value,
            PermissionEnum::INVENTORY_TRANSFER_CREATE->value,
            PermissionEnum::INVENTORY_TRANSFER_UPDATE->value,
            PermissionEnum::INVENTORY_TRANSFER_APPROVE->value,
            PermissionEnum::INVENTORY_TRANSFER_SHIP->value,
            PermissionEnum::INVENTORY_TRANSFER_RECEIVE->value,
            PermissionEnum::INVENTORY_STOCK_OPNAME->value,
            PermissionEnum::PROMO_VIEW->value,
            PermissionEnum::CUSTOMER_VIEW->value,
            PermissionEnum::REPORT_SALES->value,
            PermissionEnum::REPORT_INVENTORY->value,
            PermissionEnum::REPORT_SHIFT->value,
            PermissionEnum::REPORT_PRODUCT->value,
        ]);

        // Cashier
        $cashier = Role::firstOrCreate(
            ['name' => RoleEnum::CASHIER->value, 'guard_name' => 'business', 'business_id' => $business->id],
            ['label' => RoleEnum::CASHIER->label(), 'is_default' => true]
        );
        $cashier->syncPermissions([
            PermissionEnum::TRANSACTION_VIEW->value,
            PermissionEnum::TRANSACTION_CREATE->value,
            PermissionEnum::TRANSACTION_HOLD->value,
            PermissionEnum::TRANSACTION_REPRINT->value,
            PermissionEnum::TRANSACTION_ISSUE_INVOICE->value,
            PermissionEnum::TRANSACTION_RECORD_PAYMENT->value,
            PermissionEnum::PRODUCT_VIEW->value,
            PermissionEnum::CUSTOMER_VIEW->value,
            PermissionEnum::CUSTOMER_CREATE->value,
        ]);
    }
}
