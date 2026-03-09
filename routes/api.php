<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/login', [App\Http\Controllers\AuthController::class, 'mobileLogin']);
Route::post('/auth/logout', [App\Http\Controllers\AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// Mobile API Group - All authenticated
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

    /** ===== TRANSACTION MANAGEMENT (Entry/Exit/Payment) ===== */

    // Create transaction (entry)
    Route::post('/transactions', [App\Http\Controllers\MobileController::class, 'storeTransaction'])
        ->name('api.transactions.store');

    // Get transaction details
    Route::get('/transactions/{id}', [App\Http\Controllers\MobileController::class, 'showTransaction'])
        ->name('api.transactions.show');

    // List attendant's transactions (with filters)
    Route::get('/transactions', [App\Http\Controllers\MobileController::class, 'listTransactions'])
        ->name('api.transactions.index');

    // Record exit (update transaction with exit_time)
    Route::post('/transactions/{id}/exit', [App\Http\Controllers\MobileController::class, 'recordExit'])
        ->name('api.transactions.exit');

    // Record payment
    Route::post('/transactions/{id}/payment', [App\Http\Controllers\MobileController::class, 'recordPayment'])
        ->name('api.transactions.payment');

    // Batch sync (for offline mode)
    Route::post('/transactions/batch', [App\Http\Controllers\MobileController::class, 'batchSync'])
        ->name('api.transactions.batch');

    /** ===== DASHBOARD & SUMMARY ===== */

    // Attendant dashboard (today's stats)
    Route::get('/attendant/dashboard', [App\Http\Controllers\MobileController::class, 'dashboard'])
        ->name('api.attendant.dashboard');

    // Daily summary (with date filter)
    Route::get('/attendant/daily-summary', [App\Http\Controllers\MobileController::class, 'dailySummary'])
        ->name('api.attendant.daily-summary');

    // Active transactions list
    Route::get('/attendant/active-transactions', [App\Http\Controllers\MobileController::class, 'activeTransactions'])
        ->name('api.attendant.active-transactions');

    /** ===== AREA & RATE DATA ===== */

    // Get all areas with rates (for dropdown/selection)
    Route::get('/areas', [App\Http\Controllers\MobileController::class, 'getAreas'])
        ->name('api.areas.index');

    // Get area occupancy (real-time status)
    Route::get('/areas/occupancy', [App\Http\Controllers\MobileController::class, 'occupancy'])
        ->name('api.areas.occupancy');

    // Get single area details with rates
    Route::get('/areas/{id}', [App\Http\Controllers\MobileController::class, 'showArea'])
        ->name('api.areas.show');

    /** ===== VEHICLE VALIDATION ===== */

    // Validate/check vehicle by plate number
    Route::post('/vehicles/validate', [App\Http\Controllers\MobileController::class, 'validateVehicle'])
        ->name('api.vehicles.validate');

    /** ===== DEVICE & USER MANAGEMENT ===== */

    // Register device for push notifications
    Route::post('/mobile/register', [App\Http\Controllers\MobileController::class, 'registerDevice'])
        ->name('api.mobile.register');

    // Get current user profile
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    })->name('api.user');

    // Update user profile (optional)
    Route::put('/user', [App\Http\Controllers\MobileController::class, 'updateProfile'])
        ->name('api.user.update');
});
