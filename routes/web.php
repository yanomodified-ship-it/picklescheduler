<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCourtController;

// --- PUBLIC ROUTES ---
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::post('/bookings', [BookingController::class, 'store'])->name('booking.store');
Route::get('/confirmation/{booking_reference}', [BookingController::class, 'confirmation'])->name('bookings.confirmation');

// --- ADMIN SYSTEM ---
Route::prefix('admin')->name('admin.')->group(function () {
    
    // Admin Guest Routes (Login)
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Admin Protected Routes
    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminBookingController::class, 'dashboard'])->name('dashboard');
        
        // Receipts
        Route::get('/receipt/{booking}', [AdminBookingController::class, 'viewReceipt'])->name('receipt.show');

        // Booking Actions
        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approvePayment'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'rejectPayment'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirmBooking'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'deleteBooking'])->name('bookings.delete');

        // Court Actions
        Route::put('/courts/{court}', [AdminCourtController::class, 'update'])->name('courts.update');
    });
});