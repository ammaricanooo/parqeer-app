<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DebugController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LogActivityController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    return view('home');
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
    Route::get('/transaction/{transaction}/exit-vehicle', [TransactionController::class, 'showExitVehicle'])->name('transaction.exit-vehicle');
    Route::post('/transaction/{transaction}/exit-vehicle', [TransactionController::class, 'exitVehicle'])->name('transaction.exit-vehicle.process');
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
    Route::get('/transaction/{transaction}/receipt-pdf', [TransactionController::class, 'downloadExitReceipt'])->name('transaction.receipt-pdf');
    Route::get('/transaction/{transaction}/receipt-pdf', [TransactionController::class, 'downloadExitReceipt'])->name('transaction.receipt-pdf');
    Route::post('/transaction/{transaction}/pay', [TransactionController::class, 'pay'])->name('transaction.pay');
    Route::get('/transaction/{transaction}/payment-receipt', [TransactionController::class, 'downloadPaymentReceipt'])->name('transaction.payment-receipt');

    Route::get('/transaction/search/vehicle', [TransactionController::class, 'searchVehicle'])->name('transaction.search-vehicle');
    Route::get('/transaction/get-rate', [TransactionController::class, 'getRate'])->name('transaction.get-rate');
    Route::get('/transaction/{transaction}/current-amount', [TransactionController::class, 'currentAmount'])->middleware(['auth', 'verified'])->name('transaction.current-amount');

    // Struk entry & QR
    Route::get('/transaction/{transaction}/entry-receipt', [TransactionController::class, 'entryReceipt'])->name('transaction.entry-receipt');
    Route::get('/transaction/scan', [TransactionController::class, 'scanQr'])->name('transaction.scan');

    // Vehicle routes - attendant bisa input kendaraan baru
    Route::get('/vehicle/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicle/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicle/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
});

// Payment Gateway routes
Route::post('/attendant/payment/callback', [TransactionController::class, 'handlePaymentCallback'])->name('payment.callback');
Route::get('/attendant/payment/success', [TransactionController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/attendant/payment/failed', [TransactionController::class, 'paymentFailed'])->name('payment.failed');
Route::get('/attendant/transaction/{transaction}/payment-confirmed', [TransactionController::class, 'paymentConfirmed'])->name('attendant.transaction.payment-confirmed');

// Public gate scan ticket (dapat diakses tanpa login)
Route::get('/transaction/{transaction}/scan-ticket', [TransactionController::class, 'scanTicket'])->name('transaction.scan-ticket');

// Public payment page (dapat diakses tanpa login - untuk scan QR dari tiket)
Route::get('/payment/{transaction}', [TransactionController::class, 'publicPayment'])->name('payment.public');

// Dashboard admin (rekap cepat untuk admin)
// Dashboard admin (summary + area table + top transactions)
Route::get('/admin/dashboard', function () {
    $today = \Carbon\Carbon::today();

    // Revenue per area
    $revenuePerArea = \App\Models\Transaction::whereNotNull('exit_time')
        ->join('areas', 'transactions.area_id', '=', 'areas.id')
        ->selectRaw('areas.name as area_name, COUNT(*) as tx_count, SUM(transactions.amount) as total')
        ->groupBy('areas.name')
        ->orderByDesc('total')
        ->get();

    // Top transactions by amount
    $topTransactions = \App\Models\Transaction::whereNotNull('exit_time')
        ->with('vehicle', 'area')
        ->orderByDesc('amount')
        ->take(10)
        ->get();

    // Admin summary counts
    $userCount = \App\Models\User::count();
    $areaCount = \App\Models\Area::count();
    $carCount = \App\Models\Vehicle::all()->count();
    $areasWithRates = \App\Models\Area::with('rates')->get();

    return view('admin.dashboard', compact(
        'revenuePerArea',
        'topTransactions',
        'userCount',
        'areaCount',
        'carCount',
        'areasWithRates'
    ));
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

Route::get('/admin/users', [UserController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users');
Route::get('/admin/users/create', [UserController::class, 'create'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.create');
Route::post('/admin/users', [UserController::class, 'store'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.store');
Route::get('/admin/users/import', [UserController::class, 'importForm'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.form');
Route::post('/admin/users/import', [UserController::class, 'importProcess'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.process');
Route::get('/admin/users/import/template', [UserController::class, 'importTemplate'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.import.template');
Route::get('/admin/users/edit/{user:id}', [UserController::class, 'edit'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.users.edit');

// Owner laporan route (separate page)
Route::get('/laporan', function (\Illuminate\Http\Request $request) {
    $mode = $request->mode ?? 'single';
    $today = \Carbon\Carbon::today();
    $date = $request->date ?? $today->toDateString();
    $from = $request->from ?? $today->copy()->subDays(6)->toDateString();
    $to = $request->to ?? $today->toDateString();

    $baseQuery = \App\Models\Transaction::whereNotNull('exit_time');

    if ($mode === 'range') {
        $dailyTotal = (clone $baseQuery)
            ->whereBetween('exit_time', [$from, $to])
            ->sum('amount');
    } else {
        $dailyTotal = (clone $baseQuery)
            ->whereDate('exit_time', $date)
            ->sum('amount');
    }

    $weeklyTotal = (clone $baseQuery)
        ->whereBetween('exit_time', [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek()
        ])->sum('amount');

    $monthlyTotal = (clone $baseQuery)
        ->whereBetween('exit_time', [
            $today->copy()->startOfMonth(),
            $today
        ])->sum('amount');

    // Daily exit data - respects single vs range mode
    if ($mode === 'range') {
        $dailyData = (clone $baseQuery)
            ->whereBetween('exit_time', [$from, $to])
            ->selectRaw('DATE(exit_time) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    } else {
        $dailyData = (clone $baseQuery)
            ->whereDate('exit_time', $date)
            ->selectRaw('DATE(exit_time) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    $weeklyData = (clone $baseQuery)
        ->whereBetween('exit_time', [
            $today->copy()->startOfWeek(),
            $today->copy()->endOfWeek()
        ])
        ->selectRaw('DATE(exit_time) as date, SUM(amount) as total')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    $monthlyData = (clone $baseQuery)
        ->whereBetween('exit_time', [
            $today->copy()->startOfMonth(),
            $today
        ])
        ->selectRaw('WEEK(exit_time,1) as week, SUM(amount) as total')
        ->groupBy('week')
        ->orderBy('week')
        ->get();

    // vehicle recap - count by entry_time (when vehicles entered the area)
    if ($mode === 'range') {
        $vehicleRecap = \App\Models\Transaction::whereBetween('entry_time', [$from, $to])
            ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicles.type as vehicle_type, COUNT(*) as count')
            ->groupBy('vehicles.type')
            ->get();
    } else {
        $vehicleRecap = \App\Models\Transaction::whereDate('entry_time', $date)
            ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
            ->selectRaw('vehicles.type as vehicle_type, COUNT(*) as count')
            ->groupBy('vehicles.type')
            ->get();
    }

    // vehicles still parked (entered in range but not exited yet)
    if ($mode === 'range') {
        $stillParked = \App\Models\Transaction::whereBetween('entry_time', [$from, $to])
            ->whereNull('exit_time')
            ->count();
    } else {
        $stillParked = \App\Models\Transaction::whereDate('entry_time', $date)
            ->whereNull('exit_time')
            ->count();
    }

    return view('owner.laporan', compact(
        'dailyTotal',
        'weeklyTotal',
        'monthlyTotal',
        'dailyData',
        'weeklyData',
        'monthlyData',
        'mode',
        'date',
        'from',
        'to',
        'vehicleRecap',
        'stillParked'
    ));
})->name('owner.laporan');
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

Route::get('/admin/vehicles', [VehicleController::class, 'adminIndex'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.vehicles.index');
Route::get('/admin/vehicles/{vehicle}', [VehicleController::class, 'adminShow'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.vehicles.show');
Route::get('/admin/transaction/{transaction}/entry-receipt', [TransactionController::class, 'entryReceipt'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.transaction.entry-receipt');
Route::get('/admin/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.transaction.receipt');

Route::get('/admin/transaction/{transaction}/current-amount', [TransactionController::class, 'currentAmount'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.transaction.current-amount');

Route::get('/admin/logs', [LogActivityController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.logs.index');
Route::get('/logs/{logActivity}', [LogActivityController::class, 'show'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.logs.show');

Route::middleware(['auth', 'verified', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        Route::get('/dashboard', function (\Illuminate\Http\Request $request) {

            // Simplified owner dashboard metrics (cards + revenue per area + top transactions)
            $today = \Carbon\Carbon::today();

            $todayRevenue = \App\Models\Transaction::whereNotNull('exit_time')
                ->whereDate('exit_time', $today)
                ->sum('amount');

            $monthRevenue = \App\Models\Transaction::whereNotNull('exit_time')
                ->whereBetween('exit_time', [$today->copy()->startOfMonth(), $today])
                ->sum('amount');

            $currentlyParked = \App\Models\Transaction::whereNull('exit_time')->count();

            $totalRevenue = \App\Models\Transaction::whereNotNull('exit_time')->sum('amount');

            $revenuePerArea = \App\Models\Transaction::whereNotNull('exit_time')
                ->join('areas', 'transactions.area_id', '=', 'areas.id')
                ->selectRaw('areas.name as area_name, SUM(transactions.amount) as total, COUNT(*) as tx_count')
                ->groupBy('areas.name')
                ->orderByDesc('total')
                ->get();

            $topTransactions = \App\Models\Transaction::whereNotNull('exit_time')
                ->with('vehicle', 'area')
                ->orderByDesc('amount')
                ->take(3)
                ->get();

            return view('owner.dashboard', compact(
                'todayRevenue',
                'monthRevenue',
                'currentlyParked',
                'totalRevenue',
                'revenuePerArea',
                'topTransactions'
            ));
        })->name('dashboard');

        Route::get('/laporan/export', function (\Illuminate\Http\Request $request) {
            $mode = $request->mode ?? 'single';
            $today = \Carbon\Carbon::today();
            $date = $request->date ?? $today->toDateString();
            $from = $request->from ?? $today->copy()->subDays(6)->toDateString();
            $to = $request->to ?? $today->toDateString();

            $baseQuery = \App\Models\Transaction::whereNotNull('exit_time');

            if ($mode === 'range') {
                $transactions = (clone $baseQuery)
                    ->whereBetween('exit_time', [$from, $to])
                    ->with('vehicle', 'area')
                    ->orderBy('exit_time', 'desc')
                    ->get();
            } else {
                $transactions = (clone $baseQuery)
                    ->whereDate('exit_time', $date)
                    ->with('vehicle', 'area')
                    ->orderBy('exit_time', 'desc')
                    ->get();
            }

            return Excel::download(
                new \App\Exports\DashboardExport($transactions, $mode, $date, $from, $to),
                'rekap_parkir_' . now()->format('Y-m-d_His') . '.xlsx'
            );
        })->name('laporan.export');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
