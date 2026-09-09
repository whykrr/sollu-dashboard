<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperFeatureFlag
 */
class FeatureFlag extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected static function booted()
    {
        static::saved(function ($flag) {
            if (is_null($flag->business_id) && $flag->feature_name === 'midtrans_payment_enabled') {
                \Illuminate\Support\Facades\Cache::forget('global_feature_midtrans_payment_enabled');
            }
        });

        static::deleted(function ($flag) {
            if (is_null($flag->business_id) && $flag->feature_name === 'midtrans_payment_enabled') {
                \Illuminate\Support\Facades\Cache::forget('global_feature_midtrans_payment_enabled');
            }
        });
    }

    public static function isMidtransEnabled(): bool
    {
        return \Illuminate\Support\Facades\Cache::remember('global_feature_midtrans_payment_enabled', 86400, function () {
            return self::whereNull('business_id')->where('feature_name', 'midtrans_payment_enabled')->first()?->enabled ?? false;
        });
    }
}
