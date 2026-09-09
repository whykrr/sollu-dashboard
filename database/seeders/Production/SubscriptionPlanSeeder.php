<?php

namespace Database\Seeders\Production;

use App\Enums\PlanEnum;
use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (PlanEnum::cases() as $plan) {
            SubscriptionPlan::updateOrCreate(
                [
                    'code' => $plan->value,
                ],
                [
                    'name' => $plan->name(),
                    'price_per_outlet' => $plan->pricePerOutlet(),
                    'max_outlet' => $plan->maxOutlet(),
                    'yearly_discount_percent' => $plan->yearlyDiscountPercent(),
                    'features' => $plan->uiFeatures(),
                    'is_active' => true,
                ]
            );
        }
    }
}
