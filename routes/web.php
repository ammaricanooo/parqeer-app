<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Debug route
Route::get('/debug/test-user', [DebugController::class, 'testUser']);

// Dashboard untuk attendant (default)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== ROUTES UNTUK attendant =====
Route::middleware(['auth', 'verified', 'role:attendant'])->prefix('attendant')->name('attendant.')->group(function () {
    // Transaction routes - attendant bisa input kendaraan masuk, lihat daftar, input keluar, cetak struk
    Route::get('/transaction', [TransactionController::class, 'index'])->name('transaction.index');
    Route::get('/transaction/create', [TransactionController::class, 'create'])->name('transaction.create');
    Route::post('/transaction', [TransactionController::class, 'store'])->name('transaction.store');
    Route::get('/transaction/{transaction}/exit', [TransactionController::class, 'showExit'])->name('transaction.exit');
    Route::post('/transaction/{transaction}/exit', [TransactionController::class, 'processExit'])->name('transaction.exit.process');
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
    Route::post('/transaction/{transaction}/pay', [TransactionController::class, 'pay'])->name('transaction.pay');
    Route::get('/transaction/search/vehicle', [TransactionController::class, 'searchVehicle'])->name('transaction.search-vehicle');
    Route::get('/transaction/get-rate', [TransactionController::class, 'getRate'])->name('transaction.get-rate');
    Route::get('/transaction/{transaction}/current-amount', [TransactionController::class, 'currentAmount'])->middleware(['auth','verified'])->name('transaction.current-amount');

    // Struk entry & QR
    Route::get('/transaction/{transaction}/entry-receipt', [TransactionController::class, 'entryReceipt'])->name('transaction.entry-receipt');
    Route::get('/transaction/scan', [TransactionController::class, 'scanQr'])->name('transaction.scan');

    // Vehicle routes - attendant bisa input kendaraan baru
    Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
    Route::get('/vehicle/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicle/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicle/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
});

// Dashboard admin
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');
Route::get('/admin/users', [UserController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users');
Route::get('/admin/users/create', [UserController::class, 'create'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.create');
Route::post('/admin/users', [UserController::class, 'store'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.store');
Route::get('/admin/users/import', [UserController::class, 'importForm'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.form');
Route::post('/admin/users/import', [UserController::class, 'importProcess'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.process');
Route::get('/admin/users/import/template', [UserController::class, 'importTemplate'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.template');
Route::get('/admin/users/edit/{user:id}', [UserController::class, 'edit'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.edit');
Route::put('/admin/users/edit/{user:id}', [UserController::class, 'update'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.update');
Route::delete('/admin/users/{user:id}', [UserController::class, 'destroy'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.destroy');
Route::get('/admin/users/export/excel', [UserController::class, 'exportExcel'])->name('admin.users.export');

Route::get('/admin/rates', [RateController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.index');
Route::get('/admin/rates/create', [RateController::class, 'create'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.create');
Route::post('/admin/rates', [RateController::class, 'store'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.store');
Route::get('/admin/rates/edit/{rate:id}', [RateController::class, 'edit'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.edit');
Route::put('/admin/rates/edit/{rate:id}', [RateController::class, 'update'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.update');
Route::delete('/admin/rates/{rate:id}', [RateController::class, 'destroy'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.rates.destroy');
Route::get('/admin/rates/export/excel', [RateController::class, 'exportExcel'])->name('admin.rates.export');

Route::get('/admin/areas', [AreaController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.index');
Route::get('/admin/areas/create', [AreaController::class, 'create'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.create');
Route::post('/admin/areas', [AreaController::class, 'store'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.store');
Route::get('/admin/areas/edit/{area:id}', [AreaController::class, 'edit'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.edit');
Route::put('/admin/areas/edit/{area:id}', [AreaController::class, 'update'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.update');
Route::delete('/admin/areas/{area:id}', [AreaController::class, 'destroy'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.areas.destroy');
Route::get('/admin/areas/export/excel', [AreaController::class, 'exportExcel'])->name('admin.areas.export');

Route::get('/admin/vehicles', [VehicleController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.vehicles.index');
Route::get('/admin/vehicles/{vehicle}', [VehicleController::class, 'show'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.vehicles.show');

Route::get('/admin/transaction/{transaction}/current-amount', [TransactionController::class, 'currentAmount'])->middleware(['auth','verified','role:admin'])->name('admin.transaction.current-amount');

// Dashboard owner
Route::middleware(['auth', 'verified', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', function () {
        return view('owner.dashboard');
    })->name('dashboard');

    // Owner: hanya menampilkan log aktivitas (list + detail)
    Route::get('/logs', [\App\Http\Controllers\LogActivityController::class, 'index'])->name('logs.index');
    Route::get('/logs/{logActivity}', [\App\Http\Controllers\LogActivityController::class, 'show'])->name('logs.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
