<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia(
        'Dashboard/Index'
    );
})->name('dashboard');
Route::get('/colors', function () {
    return inertia('Colors/Index');
})->name('colors');
Route::get('/typography', function () {
    return inertia('Colors/Index');
})->name('typography');

// Base
Route::prefix('base')
    ->name('base.')
    ->group(function () {
        Route::get('/cards', function () {
            return inertia('Colors/Index');
        })->name('cards');
        Route::get('/navigation', function () {
            return inertia('Colors/Index');
        })->name('navigation');
        Route::get('/placeholders', function () {
            return inertia('Colors/Index');
        })->name('placeholders');
        Route::get('/spinners', function () {
            return inertia('Colors/Index');
        })->name('spinners');
        Route::get('/tables', function () {
            return inertia('Colors/Index');
        })->name('tables');
    });

// Buttons
Route::prefix('buttons')
    ->name('buttons.')
    ->group(function () {
        Route::get('/buttons', function () {
            return inertia('Colors/Index');
        })->name('buttons');
        Route::get('/button-group', function () {
            return inertia('Colors/Index');
        })->name('button-group');
    });

Route::get('/charts', function () {
    return inertia('Colors/Index');
})->name('charts');

// Forms
Route::prefix('forms')
    ->name('forms.')
    ->group(function () {
        Route::get('/form-control', function () {
            return inertia('Colors/Index');
        })->name('form-control');
        Route::get('/select', function () {
            return inertia('Colors/Index');
        })->name('select');
        Route::get('/radio', function () {
            return inertia('Colors/Index');
        })->name('radio');
        Route::get('/check', function () {
            return inertia('Colors/Index');
        })->name('check');
        Route::get('/input-group', function () {
            return inertia('Colors/Index');
        })->name('input-group');
        Route::get('/floating-label', function () {
            return inertia('Colors/Index');
        })->name('floating-label');
        Route::get('/layout', function () {
            return inertia('Colors/Index');
        })->name('layout');
        Route::get('/validation', function () {
            return inertia('Colors/Index');
        })->name('validation');
    });

// Notification
Route::prefix('notification')
    ->name('notification.')
    ->group(function () {
        Route::get('/alert', function () {
            return inertia('Colors/Index');
        })->name('alert');
        Route::get('/modal', function () {
            return inertia('Colors/Index');
        })->name('modal');
        Route::get('/badge', function () {
            return inertia('Colors/Index');
        })->name('badge');
        Route::get('/toast', function () {
            return inertia('Colors/Index');
        })->name('toast');
    });

Route::get('/widgets', function () {
    return inertia('Colors/Index');
})->name('widgets');

// Pages
Route::prefix('pages')
    ->name('pages.')
    ->group(function () {
        Route::get('/login', function () {
            return inertia('Colors/Index');
        })->name('login');
        Route::get('/register', function () {
            return inertia('Colors/Index');
        })->name('register');
    });

// Error Pages
Route::prefix('error-pages')
    ->name('error-pages.')
    ->group(function () {
        Route::get('/error-404', function () {
            return inertia('Colors/Index');
        })->name('error-404');
        Route::get('/error-500', function () {
            return inertia('Colors/Index');
        })->name('error-500');
    });
