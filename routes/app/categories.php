<?php

use App\Http\Controllers\App\Dashboard\CategoryController;
use Illuminate\Support\Facades\Route;

Route::delete('categories/{category}/force', [CategoryController::class, 'forceDelete'])
    ->name('categories.force-delete');

Route::resource('categories', CategoryController::class);
