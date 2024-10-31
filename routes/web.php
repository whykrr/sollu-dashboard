<?php

use App\Models\User;
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
    return inertia('Typography/Index');
})->name('typography');

// Base
Route::prefix('base')
    ->name('base.')
    ->group(function () {
        Route::get('/cards', function () {
            return inertia('Base/Cards');
        })->name('cards');
        Route::get('/navigation', function () {
            return inertia('Base/Navigation');
        })->name('navigation');
        Route::get('/placeholders', function () {
            return inertia('Base/Placeholders');
        })->name('placeholders');
        Route::get('/spinners', function () {
            return inertia('ComingSoon');
        })->name('spinners');
        Route::get('/tables', function () {
            return inertia('Base/Tables');
        })->name('tables');
        Route::get('/pagination', function () {
            return inertia('Base/Pagination', ['users' => User::paginate(10)]);
        })->name('pagination');
    });

// Buttons
Route::prefix('buttons')
    ->name('buttons.')
    ->group(function () {
        Route::get('/buttons', function () {
            return inertia('Buttons/Buttons');
        })->name('buttons');
        Route::get('/button-group', function () {
            return inertia('Buttons/ButtonGroup');
        })->name('button-group');
    });

Route::get('/charts', function () {
    return inertia('ComingSoon');
})->name('charts');

// Forms
Route::prefix('forms')
    ->name('forms.')
    ->group(function () {
        Route::get('/form-control', function () {
            return inertia('ComingSoon');
        })->name('form-control');
        Route::get('/select', function () {
            return inertia('ComingSoon');
        })->name('select');
        Route::get('/radio', function () {
            return inertia('ComingSoon');
        })->name('radio');
        Route::get('/check', function () {
            return inertia('ComingSoon');
        })->name('check');
        Route::get('/input-group', function () {
            return inertia('ComingSoon');
        })->name('input-group');
        Route::get('/floating-label', function () {
            return inertia('ComingSoon');
        })->name('floating-label');
        Route::get('/layout', function () {
            return inertia('ComingSoon');
        })->name('layout');
        Route::get('/validation', function () {
            return inertia('ComingSoon');
        })->name('validation');
    });

// Notification
Route::prefix('notification')
    ->name('notification.')
    ->group(function () {
        Route::get('/alert', function () {
            return inertia('ComingSoon');
        })->name('alert');
        Route::get('/modal', function () {
            return inertia('ComingSoon');
        })->name('modal');
        Route::get('/badge', function () {
            return inertia('ComingSoon');
        })->name('badge');
        Route::get('/toast', function () {
            return inertia('ComingSoon');
        })->name('toast');
    });

Route::get('/widgets', function () {
    return inertia('ComingSoon');
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
