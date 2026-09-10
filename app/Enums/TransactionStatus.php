<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Hold = 'hold';
    case Draft = 'draft';
    case Completed = 'completed';
    case Void = 'void';
    case Cancel = 'cancel';

    // Some logic in TransactionService.php also checks for 'unpaid', 'partial', 'paid', but in some places it says:
    // in_array($transaction->status, ['completed', 'paid']) -> Maybe 'paid' is sometimes used instead of 'completed'?
    // Let's add them just to be safe and match the current codebase.
    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Partial = 'partial';

    public function label(): string
    {
        return match ($this) {
            self::Hold => 'Ditahan',
            self::Draft => 'Draf',
            self::Completed => 'Selesai',
            self::Void => 'Dibatalkan (Void)',
            self::Cancel => 'Batal',
            self::Paid => 'Lunas',
            self::Unpaid => 'Belum Dibayar',
            self::Partial => 'Dibayar Sebagian',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Hold => 'badge-warning',
            self::Draft => 'badge-gray',
            self::Completed, self::Paid => 'badge-success',
            self::Void, self::Cancel => 'badge-danger',
            self::Unpaid => 'badge-gray',
            self::Partial => 'badge-info',
        };
    }
}
