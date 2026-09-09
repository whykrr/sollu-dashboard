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
                FeatureEnum::BASIC_REPORTS,
            ],
            self::BASIC => array_merge(self::MICRO->systemFeatures(), [
                FeatureEnum::INVENTORY_MANAGEMENT,
                FeatureEnum::MULTI_OUTLET,
            ]),
            self::PRO => array_merge(self::BASIC->systemFeatures(), [
                FeatureEnum::ADVANCED_REPORTS,
                FeatureEnum::PROMO_MANAGEMENT,
                FeatureEnum::CUSTOMER_LOYALTY,
            ]),
            self::ULTIMATE => array_merge(self::PRO->systemFeatures(), [
                FeatureEnum::RECIPE_MANAGEMENT,
                FeatureEnum::UNLIMITED_USERS,
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
        // By default, trial users get the ULTIMATE experience
        return self::MICRO->systemFeatures();
    }

    public static function freeFeatures(): array
    {
        // Free users get minimal access
        return [
            FeatureEnum::BASIC_REPORTS,
        ];
    }
}
