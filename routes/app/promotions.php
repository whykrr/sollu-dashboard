<?php

use App\Enums\FeatureEnum;
use App\Http\Controllers\App\PromotionController;
use Illuminate\Support\Facades\Route;

Route::prefix('promotions')
    ->middleware('plan.feature:'.FeatureEnum::PROMO_MANAGEMENT->value)
    ->name('promotions.')->group(function () {
        Route::post('/{promotion}/publish', [PromotionController::class, 'publish'])->name('publish');
        Route::post('/{promotion}/unpublish', [PromotionController::class, 'unpublish'])->name('unpublish');
    });

Route::resource('promotions', PromotionController::class)
    ->except(['create', 'edit', 'show'])
    ->middleware('plan.feature:'.FeatureEnum::PROMO_MANAGEMENT->value);
