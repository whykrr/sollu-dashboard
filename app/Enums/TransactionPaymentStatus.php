<?php

namespace App\Enums;

enum TransactionPaymentStatus: string
{
    case Paid = 'paid';
    case Draft = 'draft';
    case Unpaid = 'unpaid';
    case Partial = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Lunas',
            self::Draft => 'Draf',
            self::Unpaid => 'Belum Dibayar',
            self::Partial => 'Dibayar Sebagian',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Paid => 'badge-success',
            self::Draft => 'badge-gray',
            self::Unpaid => 'badge-danger',
            self::Partial => 'badge-warning',
        };
    }
}
