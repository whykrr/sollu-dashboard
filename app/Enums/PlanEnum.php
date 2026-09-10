<?php

namespace App\Enums;

enum PlanEnum: string
{
    case MICRO = 'micro';
    case BASIC = 'basic';
    case PRO = 'pro';
    case ULTIMATE = 'ultimate';

    public function name(): string
    {
        return match ($this) {
            self::MICRO => 'Paket Mikro',
            self::BASIC => 'Paket Basic',
            self::PRO => 'Paket Pro',
            self::ULTIMATE => 'Paket Ultimate',
        };
    }

    public function pricePerOutlet(): int
    {
        return match ($this) {
            self::MICRO => 59000,
            self::BASIC => 129000,
            self::PRO => 299000,
            self::ULTIMATE => 499000,
        };
    }

    public function maxOutlet(): ?int
    {
        return match ($this) {
            self::MICRO => 3,
            self::BASIC => 10,
            self::PRO => 99,
            self::ULTIMATE => null,
        };
    }

    public function yearlyDiscountPercent(): int
    {
        return match ($this) {
            self::MICRO, self::BASIC, self::PRO => 20,
            self::ULTIMATE => 25,
        };
    }

    public function systemFeatures(): array
    {
        return match ($this) {
            self::MICRO => [
                FeatureEnum::POS_CASHIER,
                FeatureEnum::SHIFT_MANAGEMENT,
                FeatureEnum::CASH_DRAWER,
                FeatureEnum::PRODUCT_CATALOG,
                FeatureEnum::PRODUCT_CATEGORIES,
                FeatureEnum::CUSTOMER_MANAGEMENT,
                FeatureEnum::OPERATIONAL_HOURS,
                FeatureEnum::RECEIPT_CUSTOMIZATION,
                FeatureEnum::TAX_AND_SERVICE_CHARGE,
                FeatureEnum::CUSTOM_PAYMENT_METHODS,
                FeatureEnum::EMPLOYEE_MANAGEMENT,
                FeatureEnum::POS_DEVICE_SYNC,
                FeatureEnum::BASIC_REPORTS,
            ],
            self::BASIC => array_merge(self::MICRO->systemFeatures(), [
                FeatureEnum::SPLIT_PAYMENT,
                FeatureEnum::VOID_REFUND,
                FeatureEnum::PRODUCT_VARIANTS,
                FeatureEnum::PRODUCT_MODIFIERS,
                FeatureEnum::PRODUCT_IMPORT_EXPORT,
                FeatureEnum::INVENTORY_MANAGEMENT,
                FeatureEnum::RAW_MATERIALS,
                FeatureEnum::STOCK_MOVEMENTS,
                FeatureEnum::STOCK_ADJUSTMENTS,
                FeatureEnum::STOCK_OPNAME,
                FeatureEnum::SUPPLIER_MANAGEMENT,
                FeatureEnum::PURCHASE_ORDERS,
                FeatureEnum::INVENTORY_IMPORT_EXPORT,
                FeatureEnum::CUSTOMER_IMPORT_EXPORT,
                FeatureEnum::SALES_REPORTS,
                FeatureEnum::PRODUCT_REPORTS,
                FeatureEnum::REPORT_EXPORT,
                FeatureEnum::DEVICE_MANAGEMENT,
                FeatureEnum::ROLE_PERMISSIONS,
                FeatureEnum::PAYMENT_GATEWAY,
                FeatureEnum::MULTI_OUTLET,
            ]),
            self::PRO => array_merge(self::BASIC->systemFeatures(), [
                FeatureEnum::INVOICE_DEBT,
                FeatureEnum::PRODUCT_BUNDLES,
                FeatureEnum::STOCK_FREEZE,
                FeatureEnum::STOCK_TRANSFERS,
                FeatureEnum::PROMO_MANAGEMENT,
                FeatureEnum::DISCOUNT_VOUCHERS,
                FeatureEnum::CUSTOMER_LOYALTY,
                FeatureEnum::STOCK_REPORTS,
                FeatureEnum::CASHIER_REPORTS,
                FeatureEnum::PROMO_REPORTS,
                FeatureEnum::CUSTOMER_REPORTS,
                FeatureEnum::ADVANCED_REPORTS,
                FeatureEnum::AUDIT_LOGS,
            ]),
            self::ULTIMATE => array_merge(self::PRO->systemFeatures(), [
                FeatureEnum::RECIPE_MANAGEMENT,
                FeatureEnum::UNLIMITED_USERS,
                FeatureEnum::DEVELOPER_API,
            ]),
        };
    }

    public function uiFeatures(): array
    {
        $baseFeatures = [
            ['title' => 'Fitur Penjualan Lengkap', 'detail' => 'fitur lengkap dan lain lain'],
            ['title' => 'Laporan Penjualan', 'detail' => 'laporan penjualan lengkap'],
            ['title' => 'Manajemen Stok', 'detail' => 'manajemen stok mudah'],
            ['title' => 'Multi Outlet', 'detail' => 'kelola banyak outlet'],
            ['title' => 'Multi User', 'detail' => 'banyak user dalam 1 outlet'],
            ['title' => 'Support 24/7', 'detail' => 'bantuan kapan saja'],
        ];

        return match ($this) {
            self::MICRO, self::BASIC, self::PRO => $baseFeatures,
            self::ULTIMATE => array_merge($baseFeatures, [
                ['title' => 'Dedicated Account Manager', 'detail' => 'layanan prioritas khusus enterprise'],
            ]),
        };
    }

    public static function trialFeatures(): array
    {
        return self::MICRO->systemFeatures();
    }

    public static function freeFeatures(): array
    {
        return [
            FeatureEnum::BASIC_REPORTS,
            FeatureEnum::PRODUCT_CATALOG,
            FeatureEnum::PRODUCT_CATEGORIES,
        ];
    }
}
