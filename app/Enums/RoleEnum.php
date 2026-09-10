<?php

namespace App\Enums;

enum RoleEnum: string
{
    // Management
    case OWNER = 'owner';
    case MANAGER = 'manager';

    // Operational
    case CASHIER = 'cashier';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Pemilik Usaha',
            self::MANAGER => 'Manajer',
            self::CASHIER => 'Kasir',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $role) => [
                $role->value => $role->label(),
            ])
            ->toArray();
    }
}
