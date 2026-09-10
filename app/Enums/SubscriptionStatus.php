<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Active = 'active';
    case Suspended = 'suspended';
    case Canceled = 'canceled';
    case Pending = 'pending'; // In case it's used somewhere

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Aktif',
            self::Suspended => 'Ditangguhkan',
            self::Canceled => 'Dibatalkan',
            self::Pending => 'Menunggu',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'badge-success',
            self::Suspended, self::Canceled => 'badge-danger',
            self::Pending => 'badge-warning',
        };
    }
}
