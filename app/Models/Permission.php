<?php

namespace App\Models;

use App\Enums\PermissionEnum;
use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * @mixin IdeHelperPermission
 */
class Permission extends SpatiePermission
{
    protected $appends = [
        'label',
    ];

    public function getLabelAttribute(): string
    {
        return PermissionEnum::tryFrom($this->name)?->label()
            ?? $this->name;
    }
}
