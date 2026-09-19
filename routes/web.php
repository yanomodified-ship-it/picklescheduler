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

// --- KEEP ALIVE ROUTE ---
Route::get('/api/keep-alive', function () {
    \Illuminate\Support\Facades\DB::select('SELECT 1');
    return response()->json(['status' => 'alive']);
});

// --- ADMIN SYSTEM ---
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminBookingController::class, 'dashboard'])->name('dashboard');

        // Booking Actions
        Route::post('/bookings', [AdminBookingController::class, 'store'])->name('bookings.store'); // NEW WALK-IN ROUTE
        Route::patch('/bookings/{booking}/approve', [AdminBookingController::class, 'approvePayment'])->name('bookings.approve');
        Route::patch('/bookings/{booking}/reject', [AdminBookingController::class, 'rejectPayment'])->name('bookings.reject');
        Route::patch('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirmBooking'])->name('bookings.confirm');
        Route::patch('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancelBooking'])->name('bookings.cancel');
        Route::delete('/bookings/{booking}', [AdminBookingController::class, 'deleteBooking'])->name('bookings.delete');
        Route::delete('/bookings', [AdminBookingController::class, 'deleteAllBookings'])->name('bookings.delete-all');

        // Bulk actions grouped by customer
        Route::patch('/customers/{customer}/verify-all', [AdminBookingController::class, 'verifyAllForCustomer'])->name('customers.verify-all');
        Route::patch('/customers/{customer}/reject-all', [AdminBookingController::class, 'rejectAllForCustomer'])->name('customers.reject-all');

        // Court Actions
        Route::post('/courts', [AdminCourtController::class, 'store'])->name('courts.store');
        Route::put('/courts/{court}', [AdminCourtController::class, 'update'])->name('courts.update');
        Route::delete('/courts/{court}', [AdminCourtController::class, 'destroy'])->name('courts.destroy');
    });
});