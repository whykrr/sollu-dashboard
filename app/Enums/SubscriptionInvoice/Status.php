<?php

namespace App\Enums\SubscriptionInvoice;

enum Status: string
{
    case Unpaid = 'unpaid';
    case Open = 'open';
    case Draft = 'draft';
    case Payment = 'payment';
    case Paid = 'paid';
    case Expired = 'expired';
    case Canceled = 'canceled';
    case Void = 'void';

    public function label(): string
    {
        return match ($this) {
            self::Unpaid, self::Open => 'Menunggu Pembayaran',
            self::Draft => 'Draf',
            self::Payment => 'Proses Pembayaran',
            self::Paid => 'Lunas',
            self::Expired => 'Kedaluwarsa',
            self::Canceled, self::Void => 'Dibatalkan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Unpaid, self::Open => 'badge-warning',
            self::Draft => 'badge-gray',
            self::Payment => 'badge-info',
            self::Paid => 'badge-success',
            self::Expired, self::Canceled, self::Void => 'badge-danger',
        };
    }
}
