<?php

use Illuminate\Support\Facades\Route;

// Landing pages (public marketing site)
Route::get('/', function () {
    return view('app', ['app' => 'landing']);
})->name('landing');

Route::get('/features', function () {
    return view('app', ['app' => 'landing']);
});

Route::get('/pricing', function () {
    return view('app', ['app' => 'landing']);
});

// Dashboard app (requires auth)
Route::get('/app{any?}', function () {
    return view('app', ['app' => 'dashboard']);
})->where('any', '.*');

// Public booking pages
Route::get('/book/{any?}', function () {
    return view('app', ['app' => 'booking']);
})->where('any', '.*');
