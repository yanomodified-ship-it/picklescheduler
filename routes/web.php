<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

Route::get('/', function () {
    return view('welcome');
});

// Route for form submissions
Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');