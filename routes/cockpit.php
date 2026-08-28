<?php

use App\Http\Controllers\Cockpit\AuditController;
use App\Http\Controllers\Cockpit\AuthenticationController;
use App\Http\Controllers\Cockpit\BusinessController;
use App\Http\Controllers\Cockpit\ConfigController;
use App\Http\Controllers\Cockpit\DashboardController;
use App\Http\Controllers\Cockpit\InvoiceController;
use App\Http\Controllers\Cockpit\SubscriptionController;
use App\Http\Controllers\Cockpit\UomController;
use Illuminate\Support\Facades\Route;

Route::name('cockpit.')->group(function () {
    Route::middleware('guest:cockpit')->group(function () {
        Route::get('/login', [AuthenticationController::class, 'index'])->name('login');
        Route::post('/login', [AuthenticationController::class, 'store'])->name('login.attempt');
    });

    Route::middleware('auth:cockpit')->group(function () {
        Route::delete('/logout', [AuthenticationController::class, 'destroy'])->name('logout');

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/business', [BusinessController::class, 'index'])->name('merchants.index');
        Route::post('/business/{id}/toggle-status', [BusinessController::class, 'toggleStatus'])->name('merchants.toggle-status');
        Route::get('/business/{id}', [BusinessController::class, 'show'])->name('merchants.show');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{id}', [SubscriptionController::class, 'show'])->name('subscriptions.show');

        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');

        Route::get('/uoms', [UomController::class, 'index'])->name('uoms.index');

        Route::get('/config', [ConfigController::class, 'index'])->name('config.index');

        Route::get('/audit', [AuditController::class, 'index'])->name('audit.index');
    });
});
