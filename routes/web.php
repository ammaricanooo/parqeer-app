<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\LogActivityController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Transaction;
use App\Models\Area;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Illuminate\Http\Request;
use Carbon\Carbon;

Route::get('/', function () {
    return view('home');
});

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
    /*
    Route::get('/transaction/{transaction}/receipt', [TransactionController::class, 'receipt'])->name('transaction.receipt');
    */
    Route::get('/transaction/{transaction}/receipt-pdf', [TransactionController::class, 'downloadExitReceipt'])->name('transaction.receipt-pdf');

    Route::get('/transaction/search/vehicle', [TransactionController::class, 'searchVehicle'])->name('transaction.search-vehicle');
    Route::get('/transaction/get-rate', [TransactionController::class, 'getRate'])->name('transaction.get-rate');
    Route::get('/transaction/{transaction}/current-amount', [TransactionController::class, 'currentAmount'])->middleware(['auth', 'verified'])->name('transaction.current-amount');

    // Struk entry & QR
    Route::get('/transaction/{transaction}/entry-receipt', [TransactionController::class, 'entryReceipt'])->name('transaction.entry-receipt');
    Route::get('/transaction/{transaction}/scan-ticket', [TransactionController::class, 'scanTicket'])->name('transaction.scan-ticket');
    Route::post('/transaction/{transaction}/pay-and-exit', [TransactionController::class, 'payAndExit'])->name('transaction.pay-and-exit');
    Route::get('/transaction/scan', [TransactionController::class, 'scanQr'])->name('transaction.scan');

    // Vehicle routes - attendant bisa input kendaraan baru
    Route::get('/vehicle/create', [VehicleController::class, 'create'])->name('vehicles.create');
    Route::post('/vehicle', [VehicleController::class, 'store'])->name('vehicles.store');
    Route::get('/vehicle/{vehicle}', [VehicleController::class, 'show'])->name('vehicles.show');
    Route::get('/vehicle/{vehicle}/edit', [VehicleController::class, 'edit'])->name('vehicles.edit');
    Route::put('/vehicle/{vehicle}', [VehicleController::class, 'update'])->name('vehicles.update');
    Route::delete('/vehicle/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
});

// Payment confirmation route (attendant-only)
Route::get('/attendant/transaction/{transaction}/payment-confirmed', [TransactionController::class, 'paymentConfirmed'])->name('attendant.transaction.payment-confirmed');

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

    // Area occupancy - current parked vehicles per area
    $areaOccupancy = \App\Models\Area::with('transactions')->get()->map(function ($area) {
        $occupied = $area->transactions->where('status', 'in')->count();
        $percentage = $area->capacity > 0 ? round(($occupied / $area->capacity) * 100, 1) : 0;
        return [
            'name' => $area->name,
            'capacity' => $area->capacity,
            'occupied' => $occupied,
            'percentage' => $percentage,
            'status' => $percentage >= 90 ? 'full' : ($percentage >= 70 ? 'warning' : 'available')
        ];
    });

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
        'stillParked',
        'areaOccupancy'
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

Route::get('/admin/logs', [LogActivityController::class, 'index'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.logs.index');
Route::get('/logs/{logActivity}', [LogActivityController::class, 'show'])->middleware(['auth', 'verified', 'role:admin'])->name('admin.logs.show');

Route::middleware(['auth', 'verified', 'role:owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {

        Route::get('/dashboard', function (Request $request) {
            $mode = $request->mode ?? 'single';
            $today = Carbon::today();
            $date = $request->date ?? $today->toDateString();
            $from = $request->from ?? $today->copy()->subDays(6)->toDateString();
            $to = $request->to ?? $today->toDateString();

            $baseQuery = Transaction::whereNotNull('exit_time');

            // Filter Utama
            $filteredData = (clone $baseQuery)
                ->when(
                    $mode === 'range',
                    fn($q) => $q->whereBetween('exit_time', [$from . ' 00:00:00', $to . ' 23:59:59']),
                    fn($q) => $q->whereDate('exit_time', $date)
                );

            // METRICS UNTUK CARD
            $filteredTotalRevenue = (clone $filteredData)->sum('amount');
            $filteredTotalCount = (clone $filteredData)->count(); // Total unit keluar di periode ini
            $monthRevenue = (clone $baseQuery)
                ->whereBetween('exit_time', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])
                ->sum('amount');
            $currentlyParked = Transaction::whereNull('exit_time')->count();

            $chartData = (clone $filteredData)
                ->selectRaw('DATE(exit_time) as date, SUM(amount) as total')
                ->groupBy('date')->orderBy('date')->get();

            $vehicleRecap = Transaction::when(
                $mode === 'range',
                fn($q) => $q->whereBetween('entry_time', [$from . ' 00:00:00', $to . ' 23:59:59']),
                fn($q) => $q->whereDate('entry_time', $date)
            )
                ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
                ->selectRaw('vehicles.type, COUNT(*) as count')
                ->groupBy('vehicles.type')->get();

            $areaOccupancy = Area::withCount(['transactions as occupied' => function ($q) {
                $q->whereNull('exit_time');
            }])->get()->map(function ($area) {
                $percentage = $area->capacity > 0 ? round(($area->occupied / $area->capacity) * 100, 1) : 0;
                return [
                    'name' => $area->name,
                    'capacity' => $area->capacity,
                    'occupied' => $area->occupied,
                    'percentage' => $percentage,
                    'status' => $percentage >= 90 ? 'full' : ($percentage >= 70 ? 'warning' : 'available')
                ];
            });

            return view('owner.dashboard', compact(
                'filteredTotalRevenue',
                'filteredTotalCount',
                'monthRevenue',
                'currentlyParked',
                'chartData',
                'vehicleRecap',
                'areaOccupancy',
                'mode',
                'date',
                'from',
                'to'
            ));
        })->name('dashboard');

        // Route Export
        Route::get('/export-excel', function (Request $request) {
            $mode = $request->mode ?? 'single';
            $today = Carbon::today();
            $date = $request->date ?? $today->toDateString();
            $from = $request->from ?? $today->copy()->subDays(6)->toDateString();
            $to = $request->to ?? $today->toDateString();

            $baseQuery = Transaction::whereNotNull('exit_time');

            // Filter data sesuai mode
            $transactions = (clone $baseQuery)
                ->when(
                    $mode === 'range',
                    fn($q) => $q->whereBetween('exit_time', [$from . ' 00:00:00', $to . ' 23:59:59']),
                    fn($q) => $q->whereDate('exit_time', $date)
                )
                ->with('vehicle', 'area')
                ->orderBy('exit_time', 'desc')
                ->get();

            $filename = $mode === 'range'
                ? "Laporan_Parkir_{$from}_to_{$to}.xlsx"
                : "Laporan_Parkir_{$date}.xlsx";

            return Excel::download(new \App\Exports\DashboardExport($transactions, $mode, $date, $from, $to), $filename);
        })->name('export.excel');

        Route::get('/export-pdf', function (Request $request) {
            $mode = $request->mode ?? 'single';
            $today = Carbon::today();
            $date = $request->date ?? $today->toDateString();
            $from = $request->from ?? $today->copy()->subDays(6)->toDateString();
            $to = $request->to ?? $today->toDateString();

            $baseQuery = Transaction::whereNotNull('exit_time');

            $transactions = (clone $baseQuery)
                ->when(
                    $mode === 'range',
                    fn($q) => $q->whereBetween('exit_time', [$from . ' 00:00:00', $to . ' 23:59:59']),
                    fn($q) => $q->whereDate('exit_time', $date)
                )
                ->with('vehicle', 'area')
                ->orderBy('exit_time', 'desc')
                ->get();

            $totalRevenue = $transactions->sum('amount');
            $totalCount = $transactions->count();
            $avgRevenue = $totalCount > 0 ? $totalRevenue / $totalCount : 0;

            $pdf = DomPDF::loadView('pdfs.dashboard', [
                'transactions' => $transactions,
                'mode' => $mode,
                'date' => $date,
                'from' => $from,
                'to' => $to,
                'totalRevenue' => $totalRevenue,
                'totalCount' => $totalCount,
                'avgRevenue' => $avgRevenue,
            ]);

            $filename = $mode === 'range'
                ? "Laporan_Parkir_{$from}_to_{$to}.pdf"
                : "Laporan_Parkir_{$date}.pdf";

            return $pdf->download($filename);
        })->name('export.pdf');
    });
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
