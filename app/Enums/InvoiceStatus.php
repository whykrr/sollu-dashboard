<?php

namespace App\Enums;

enum InvoiceStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Sent = 'sent';
    case Paid = 'paid';
    case Cancelled = 'cancel'; // 'canceled' in some places? Let's use 'cancel' based on TransactionService
    case Void = 'void';
    case Overdue = 'overdue';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draf',
            self::Open => 'Menunggu Pembayaran',
            self::Sent => 'Terkirim',
            self::Paid => 'Lunas',
            self::Cancelled, self::Void => 'Dibatalkan',
            self::Overdue => 'Jatuh Tempo',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'badge-gray',
            self::Open => 'badge-warning',
            self::Sent => 'badge-info',
            self::Paid => 'badge-success',
            self::Cancelled, self::Void => 'badge-danger',
            self::Overdue => 'badge-warning',
        };
    }
}
