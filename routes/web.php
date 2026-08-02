<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/clear-cache-xk92', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    return 'Cache cleared successfully';
});

Route::get('/run-migrations-xk92', function () {
    Artisan::call('migrate', ['--force' => true]);
    return Artisan::output();
});
