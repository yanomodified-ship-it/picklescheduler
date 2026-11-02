<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\AdminCourtController;
use App\Http\Controllers\Admin\AdminOpenPlayController;
use App\Http\Controllers\OpenPlayController;

// --- PUBLIC ROUTES ---
Route::get('/', [BookingController::class, 'index'])->name('home');
Route::get('/booking', [BookingController::class, 'create'])->name('booking.create');
Route::get('/open-play', [OpenPlayController::class, 'index'])->middleware('throttle:60,1')->name('open-play.index');
Route::get('/open-play/confirmation/{reference}', [OpenPlayController::class, 'confirmation'])->middleware('throttle:30,1')->name('open-play.confirmation');
Route::get('/open-play/{openPlaySession}', [OpenPlayController::class, 'show'])->whereNumber('openPlaySession')->middleware('throttle:60,1')->name('open-play.show');
Route::get('/open-play/{openPlaySession}/join', [OpenPlayController::class, 'join'])->middleware('throttle:60,1')->name('open-play.join');
Route::post('/open-play/{openPlaySession}/join', [OpenPlayController::class, 'store'])->middleware('throttle:10,1')->name('open-play.store');
Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:10,1')->name('booking.store');
Route::get('/confirmation/{booking_reference}', [BookingController::class, 'confirmation'])->middleware('throttle:30,1')->name('bookings.confirmation');

// --- KEEP ALIVE ROUTE ---
Route::get('/api/keep-alive', function () {
    \Illuminate\Support\Facades\DB::select('SELECT 1');
    return response()->json(['status' => 'alive']);
})->middleware('throttle:12,1');

// --- AUTH REDIRECT BRIDGE ---
// to protected pages redirect properly instead of erroring.
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');

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

                // Open Play
        Route::get('/open-play', [AdminOpenPlayController::class, 'index'])->name('open-play.index');
        Route::post('/open-play', [AdminOpenPlayController::class, 'store'])->name('open-play.store');
        Route::patch('/open-play/{openPlaySession}/cancel', [AdminOpenPlayController::class, 'cancel'])->name('open-play.cancel');
                Route::delete('/open-play/past', [AdminOpenPlayController::class, 'deletePast'])->name('open-play.delete-past');

                Route::patch('/open-play/entries/{openPlayEntry}/verify', [AdminOpenPlayController::class, 'verifyEntry'])->name('open-play.entries.verify');
        Route::patch('/open-play/entries/{openPlayEntry}/reject', [AdminOpenPlayController::class, 'rejectEntry'])->name('open-play.entries.reject');
        Route::patch('/open-play/entries/{openPlayEntry}/cancel', [AdminOpenPlayController::class, 'cancelEntry'])->name('open-play.entries.cancel');
        
        // Court Actions
        Route::post('/courts', [AdminCourtController::class, 'store'])->name('courts.store');
        Route::put('/courts/{court}', [AdminCourtController::class, 'update'])->name('courts.update');
        Route::delete('/courts/{court}', [AdminCourtController::class, 'destroy'])->name('courts.destroy');
    });
});