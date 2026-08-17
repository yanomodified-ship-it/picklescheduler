<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', [BookingController::class, 'index'])->name('home');
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
Route::get('/confirmation/{booking_reference}', [BookingController::class, 'confirmation'])->name('bookings.confirmation');