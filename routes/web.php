<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return inertia('Dashboard/Index');
});
Route::get('/blank', function () {
    return inertia('Blank');
})->name('blank');
