<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperSubscriptionPlan
 */
class SubscriptionPlan extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'code',
        'name',
        'price_per_outlet',
        'max_outlet',
        'yearly_discount_percent',
        'features',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_outlet' => 'decimal:2',
            'yearly_discount_percent' => 'integer',
            'max_outlet' => 'integer',
            'features' => 'json',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'plan_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
