<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

// Rutas para Usuarios (Públicas o semi-públicas)
Route::prefix('booking')->name('booking.')->group(function () {
    Route::get('/', [AppointmentController::class, 'index'])->name('index');
    Route::post('/check-client', [AppointmentController::class, 'checkClient'])->name('check-client');
    Route::post('/store', [AppointmentController::class, 'store'])->name('store');
});

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas Administrativas
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('services', ServiceController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
