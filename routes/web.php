<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
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

// Ruta "secreta" para el Monitor de TV / Sala de espera
Route::get('/pantalla-turnos-v1', [AppointmentController::class, 'monitor'])->name('monitor');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas Administrativas
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('services', ServiceController::class);
    Route::get('appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::patch('appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.update-status');
    Route::delete('appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
