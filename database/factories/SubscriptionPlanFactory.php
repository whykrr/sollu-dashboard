<?php

namespace Database\Factories;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubscriptionPlanFactory extends Factory
{
    protected $model = SubscriptionPlan::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word.' Plan',
            'code' => $this->faker->unique()->word,
            'price_per_outlet' => 100000,
            'yearly_discount_percent' => 0,
            'features' => [],
            'is_active' => true,
        ];
    }
}
