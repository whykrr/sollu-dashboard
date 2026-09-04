<?php

use App\Http\Controllers\Cockpit\AuditController;
use App\Http\Controllers\Cockpit\Auth\ProfileController;
use App\Http\Controllers\Cockpit\AuthenticationController;
use App\Http\Controllers\Cockpit\BusinessController;
use App\Http\Controllers\Cockpit\ConfigController;
use App\Http\Controllers\Cockpit\DashboardController;
use App\Http\Controllers\Cockpit\InvoiceController;
use App\Http\Controllers\Cockpit\SubscriptionPlanController;
use App\Http\Controllers\Cockpit\UomController;
use Illuminate\Support\Facades\Route;

Route::name('cockpit.')->group(function () {
    Route::middleware('guest:cockpit')->group(function () {
        Route::get('/login', [AuthenticationController::class, 'index'])->name('login');
        Route::post('/login', [AuthenticationController::class, 'store'])->name('login.attempt');
    });

    Route::middleware('auth:cockpit')->group(function () {
        Route::delete('/logout', [AuthenticationController::class, 'destroy'])->name('logout');

        Route::patch('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/business', [BusinessController::class, 'index'])->name('merchants.index');
        Route::post('/business/{id}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('merchants.toggle-status');
        Route::get('/business/{id}', [BusinessController::class, 'show'])->name('merchants.show');
        Route::get('/business/{id}/impersonate/{userId}', [BusinessController::class, 'impersonate'])->name('merchants.impersonate');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices/{invoice}/approve', [InvoiceController::class, 'approve'])->name('invoices.approve');
        Route::post('/invoices/{invoice}/reject', [InvoiceController::class, 'reject'])->name('invoices.reject');

        Route::get('/subscription-plans', [SubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('/subscription-plans/{id}', [SubscriptionPlanController::class, 'show'])->name('subscription-plans.show');
        Route::put('/subscription-plans/{id}', [SubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
        Route::post('/subscription-plans/{id}/toggle-status', [SubscriptionPlanController::class, 'toggleStatus'])->name('subscription-plans.toggle-status');

        Route::get('/uoms', [UomController::class, 'index'])->name('uoms.index');

        Route::get('/config', [ConfigController::class, 'index'])->name('config.index');

        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
});
