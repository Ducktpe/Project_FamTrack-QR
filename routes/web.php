<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', function () {
    return view('homepage'); // matches resources/views/homepage.blade.php
})->name('homepage');

// Admin Login (placeholder for now)
Route::get('/login', function () {
    return "Login page coming soon!";
})->name('login');

// Households
Route::get('/households/create', function () {
    return "Household registration page coming soon!";
})->name('households.create');

Route::get('/households', function () {
    return "View all households page coming soon!";
})->name('households.index');

// QR Code
Route::get('/qr/generate', function () {
    return "Generate QR code page coming soon!";
})->name('qr.generate');

// Ayuda Distribution
Route::get('/ayuda', function () {
    return "Ayuda distribution page coming soon!";
})->name('ayuda.index');
