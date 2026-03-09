<?php

/**
 * Mobile API Controller for Parqeer Mobile App
 * 
 * Add to: app/Http/Controllers/MobileController.php
 * 
 * Routes in routes/api.php:
 * Route::middleware(['auth:sanctum'])->group(function () {
 *     Route::post('/transactions/batch', [MobileController::class, 'batchSync']);
 *     Route::get('/areas/occupancy', [MobileController::class, 'occupancy']);
 *     Route::post('/mobile/register', [MobileController::class, 'registerDevice']);
 *     Route::get('/attendant/daily-summary', [MobileController::class, 'dailySummary']);
 * });
 */

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Area;
use App\Models\Vehicle;
use App\Models\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MobileController extends Controller
{
    /**
     * Batch sync offline transactions (entry, exit, payment)
     * 
     * POST /api/transactions/batch
     * Body: {
     *   "transactions": [
     *     {
     *       "type": "entry",
     *       "plate_number": "B 1234 ABC",
     *       "vehicle_color": "Merah",
     *       "area_id": 1,
     *       "entry_time": "2026-02-16 10:30:00"
     *     },
     *     {
     *       "type": "exit",
     *       "id": 123,
     *       "exit_time": "2026-02-16 11:45:00"
     *     },
     *     {
     *       "type": "payment",
     *       "id": 123,
     *       "paid_amount": 50000
     *     }
     *   ]
     * }
     */
    public function batchSync(Request $request)
    {
        $validated = $request->validate([
            'transactions' => 'required|array|min:1',
            'transactions.*.type' => 'required|in:entry,exit,payment',
            'transactions.*.id' => 'nullable|integer',
            'transactions.*.plate_number' => 'nullable|string',
            'transactions.*.vehicle_color' => 'nullable|string',
            'transactions.*.area_id' => 'nullable|integer',
            'transactions.*.entry_time' => 'nullable|date',
            'transactions.*.exit_time' => 'nullable|date',
            'transactions.*.paid_amount' => 'nullable|numeric',
        ]);

        $results = [
            'synced' => 0,
            'failed' => 0,
            'errors' => [],
            'transactions' => [],
        ];

        foreach ($validated['transactions'] as $tx) {
            try {
                if ($tx['type'] === 'entry') {
                    $transaction = DB::transaction(function () use ($tx) {
                        // Create vehicle if not exists
                        $vehicle = \App\Models\Vehicle::firstOrCreate(
                            ['plate_number' => $tx['plate_number']],
                            ['color' => $tx['vehicle_color'], 'type' => 'car']
                        );

                        // Get area and rate
                        $area = Area::with('rates')->findOrFail($tx['area_id']);
                        if ($area->rates->count() !== 1) {
                            throw new \Exception('Area must have exactly 1 rate');
                        }
                        $rate = $area->rates->first();

                        // Create transaction
                        $transaction = Transaction::create([
                            'vehicle_id' => $vehicle->id,
                            'user_id' => Auth::id(),
                            'area_id' => $area->id,
                            'rate_id' => $rate->id,
                            'plate_number' => $tx['plate_number'],
                            'vehicle_color' => $tx['vehicle_color'],
                            'entry_time' => Carbon::parse($tx['entry_time']),
                            'status' => 'in',
                        ]);

                        // Update occupancy
                        $area->updateOccupancy();

                        // Log activity
                        LogActivity::create([
                            'transaction_id' => $transaction->id,
                            'vehicle_id' => $vehicle->id,
                            'user_id' => Auth::id(),
                            'activity' => 'entry',
                            'plate_number' => $tx['plate_number'],
                            'vehicle_color' => $tx['vehicle_color'],
                            'description' => 'Entry via mobile app',
                        ]);

                        return $transaction;
                    });

                    $results['synced']++;
                    $results['transactions'][] = [
                        'type' => 'entry',
                        'transaction_id' => $transaction->id,
                        'status' => 'success',
                    ];
                } elseif ($tx['type'] === 'exit') {
                    $transaction = Transaction::findOrFail($tx['id']);

                    if ($transaction->exit_time) {
                        throw new \Exception('Transaction already exited');
                    }

                    DB::transaction(function () use ($transaction, $tx) {
                        // Process exit
                        $transaction->processExit(Carbon::parse($tx['exit_time']));

                        // Update occupancy
                        $transaction->area->updateOccupancy();

                        // Log activity
                        LogActivity::create([
                            'transaction_id' => $transaction->id,
                            'vehicle_id' => $transaction->vehicle_id,
                            'user_id' => Auth::id(),
                            'activity' => 'exit',
                            'plate_number' => $transaction->plate_number,
                            'vehicle_color' => $transaction->vehicle_color,
                            'description' => 'Exit via mobile app',
                        ]);
                    });

                    $results['synced']++;
                    $results['transactions'][] = [
                        'type' => 'exit',
                        'transaction_id' => $transaction->id,
                        'status' => 'success',
                    ];
                } elseif ($tx['type'] === 'payment') {
                    $transaction = Transaction::findOrFail($tx['id']);

                    if ($transaction->status === 'paid') {
                        throw new \Exception('Transaction already paid');
                    }

                    DB::transaction(function () use ($transaction, $tx) {
                        $paidAmount = (float) $tx['paid_amount'];
                        $change = $paidAmount - (float) $transaction->amount;

                        $transaction->paid_amount = $paidAmount;
                        $transaction->change = $change;
                        $transaction->status = 'paid';
                        $transaction->save();

                        // Log activity
                        LogActivity::create([
                            'transaction_id' => $transaction->id,
                            'vehicle_id' => $transaction->vehicle_id,
                            'user_id' => Auth::id(),
                            'activity' => 'payment',
                            'plate_number' => $transaction->plate_number,
                            'vehicle_color' => $transaction->vehicle_color,
                            'description' => "Payment via mobile: Rp " . number_format($transaction->amount, 2) . " | Paid: Rp " . number_format($paidAmount, 2),
                        ]);
                    });

                    $results['synced']++;
                    $results['transactions'][] = [
                        'type' => 'payment',
                        'transaction_id' => $transaction->id,
                        'status' => 'success',
                    ];
                }
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'type' => $tx['type'],
                    'id' => $tx['id'] ?? null,
                    'message' => $e->getMessage(),
                ];
                $results['transactions'][] = [
                    'type' => $tx['type'],
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return response()->json($results);
    }

    /**
     * Get real-time area occupancy
     * 
     * GET /api/areas/occupancy
     * 
     * Response: {
     *   "areas": [
     *     {
     *       "id": 1,
     *       "name": "Basement 1",
     *       "capacity": 100,
     *       "occupied": 45,
     *       "available": 55,
     *       "percentage": 45,
     *       "status": "available"  // available, crowded, full
     *     }
     *   ]
     * }
     */
    public function occupancy()
    {
        $areas = Area::with(['rates' => function ($q) {
            $q->select('id', 'area_id', 'vehicle_type', 'amount');
        }])
            ->select('id', 'name', 'capacity', 'occupied')
            ->get()
            ->map(function ($area) {
                $percentage = round(($area->occupied / $area->capacity) * 100);

                if ($percentage >= 90) {
                    $status = 'full';
                } elseif ($percentage >= 70) {
                    $status = 'crowded';
                } else {
                    $status = 'available';
                }

                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'capacity' => $area->capacity,
                    'occupied' => $area->occupied,
                    'available' => $area->capacity - $area->occupied,
                    'percentage' => $percentage,
                    'status' => $status,
                    'rates' => $area->rates->map(fn($r) => [
                        'vehicle_type' => $r->vehicle_type,
                        'amount' => $r->amount,
                    ])->toArray(),
                ];
            });

        return response()->json([
            'areas' => $areas,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Register mobile device for push notifications
     * 
     * POST /api/mobile/register
     * Body: {
     *   "device_token": "xxxxxxxxxxx",
     *   "platform": "android",
     *   "app_version": "1.0.0"
     * }
     */
    public function registerDevice(Request $request)
    {
        $validated = $request->validate([
            'device_token' => 'required|string',
            'platform' => 'required|in:android,ios',
            'app_version' => 'required|string',
        ]);

        // Store in database or cache for push notifications
        $user = Auth::user();
        $user->update([
            'device_token' => $validated['device_token'],
            'last_device_platform' => $validated['platform'],
            'app_version' => $validated['app_version'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Device registered successfully',
        ]);
    }

    /**
     * Get attendant daily summary
     * 
     * GET /api/attendant/daily-summary
     * 
     * Response: {
     *   "date": "2026-02-16",
     *   "transactions_count": 45,
     *   "revenue": 450000,
     *   "vehicles_entered": 25,
     *   "vehicles_exited": 20,
     *   "vehicles_paid": 18,
     *   "average_duration": 125,
     *   "average_payment": 9500
     * }
     */
    public function dailySummary(Request $request)
    {
        $date = $request->get('date', today()->toDateString());
        $userId = Auth::id();

        $transactions = Transaction::where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->get();

        $entered = $transactions->count();
        $exited = $transactions->whereNotNull('exit_time')->count();
        $paid = $transactions->where('status', 'paid')->count();

        $revenue = $transactions
            ->where('status', 'paid')
            ->sum('amount');

        $avgDuration = $transactions
            ->whereNotNull('duration_minutes')
            ->avg('duration_minutes');

        $avgPayment = $paid > 0 ? $revenue / $paid : 0;

        return response()->json([
            'date' => $date,
            'transactions_count' => $entered,
            'revenue' => (float) $revenue,
            'vehicles_entered' => $entered,
            'vehicles_exited' => $exited,
            'vehicles_paid' => $paid,
            'average_duration' => (int) ceil($avgDuration ?? 0),
            'average_payment' => (float) $avgPayment,
        ]);
    }

    /**
     * Get current user active transactions (for mobile dashboard)
     * 
     * GET /api/attendant/active-transactions
     */
    public function activeTransactions()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->whereNull('exit_time')
            ->with(['vehicle', 'area', 'rate'])
            ->select('id', 'vehicle_id', 'area_id', 'rate_id', 'plate_number', 'vehicle_color', 'entry_time', 'amount')
            ->orderBy('entry_time', 'desc')
            ->get()
            ->map(function ($tx) {
                $now = Carbon::now();
                $minutes = $tx->entry_time->diffInMinutes($now);
                $hours = ceil($minutes / 60);
                $estimatedCost = ($tx->rate->amount ?? 0) * $hours;

                return [
                    'id' => $tx->id,
                    'plate_number' => $tx->plate_number,
                    'vehicle_color' => $tx->vehicle_color,
                    'area' => $tx->area->name,
                    'entry_time' => $tx->entry_time->toIso8601String(),
                    'duration_minutes' => $minutes,
                    'duration_hours' => $hours,
                    'estimated_cost' => (float) $estimatedCost,
                ];
            });

        return response()->json([
            'count' => $transactions->count(),
            'transactions' => $transactions,
        ]);
    }

    /**
     * Create new transaction (vehicle entry)
     * 
     * POST /api/transactions
     * Body: {
     *   "plate_number": "B 1234 ABC",
     *   "vehicle_color": "Merah",
     *   "area_id": 1
     * }
     * 
     * Note: vehicle_type ditentukan otomatis dari area yang dipilih
     * (area A-1 hanya untuk motor, B-1 hanya untuk mobil, dst)
     */
    public function storeTransaction(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
            'vehicle_color' => 'nullable|string|max:30',
            'area_id' => 'required|integer|exists:areas,id',
        ]);

        try {
            $transaction = DB::transaction(function () use ($validated) {
                // Get area dengan rates
                $area = Area::with('rates')->findOrFail($validated['area_id']);

                // Validasi: area harus memiliki tepat satu rate (menentukan vehicle type)
                if ($area->rates->count() === 0) {
                    throw new \Exception("Area '{$area->name}' tidak memiliki tarif terkonfigurasi");
                }

                if ($area->rates->count() > 1) {
                    throw new \Exception("Area '{$area->name}' memiliki lebih dari satu tarif. Hubungi admin untuk perbaiki konfigurasi.");
                }

                // Get rate (car atau motorcycle ditentukan oleh area)
                $rate = $area->rates->first();
                $vehicleType = $rate->vehicle_type;
                $vehicleColor = $validated['vehicle_color'] ?? 'Unknown';

                // Get or create vehicle
                $vehicle = Vehicle::where('plate_number', $validated['plate_number'])->first();

                if ($vehicle) {
                    // Kendaraan sudah ada - update warna jika berbeda
                    if ($validated['vehicle_color'] && $vehicle->color !== $validated['vehicle_color']) {
                        $vehicle->update(['color' => $validated['vehicle_color']]);
                    }
                    $vehicleColor = $vehicle->color;
                } else {
                    // Kendaraan baru - set type dari area/rate
                    $vehicle = Vehicle::create([
                        'plate_number' => $validated['plate_number'],
                        'color' => $vehicleColor,
                        'type' => $vehicleType,
                    ]);
                }

                // Validasi area masih punya slot
                if ($area->occupied >= $area->capacity) {
                    throw new \Exception("Area '{$area->name}' sudah penuh (kapasitas: {$area->capacity})");
                }

                // Create transaction
                $transaction = Transaction::create([
                    'vehicle_id' => $vehicle->id,
                    'user_id' => Auth::id(),
                    'area_id' => $area->id,
                    'rate_id' => $rate->id,
                    'plate_number' => $validated['plate_number'],
                    'vehicle_color' => $vehicleColor,
                    'entry_time' => now(),
                    'status' => 'in',
                ]);

                // Update occupancy
                $area->updateOccupancy();

                // Log activity
                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $vehicle->id,
                    'user_id' => Auth::id(),
                    'activity' => 'entry',
                    'plate_number' => $validated['plate_number'],
                    'vehicle_color' => $vehicleColor,
                    'description' => "Entry di {$area->name} ({$vehicleType}) via mobile API",
                ]);

                return $transaction;
            });

            return response()->json([
                'success' => true,
                'message' => 'Vehicle entry recorded successfully',
                'data' => [
                    'id' => $transaction->id,
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'vehicle_type' => $transaction->rate->vehicle_type,
                    'area' => $transaction->area->name,
                    'rate_per_hour' => (float) $transaction->rate->amount,
                    'entry_time' => $transaction->entry_time->toIso8601String(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get single transaction details
     * 
     * GET /api/transactions/{id}
     */
    public function showTransaction($id)
    {
        $transaction = Transaction::with(['vehicle', 'area', 'rate'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        $duration = $transaction->exit_time
            ? $transaction->entry_time->diffInMinutes($transaction->exit_time)
            : now()->diffInMinutes($transaction->entry_time);

        return response()->json([
            'id' => $transaction->id,
            'plate_number' => $transaction->plate_number,
            'vehicle_color' => $transaction->vehicle_color,
            'vehicle_type' => $transaction->vehicle->type,
            'area' => $transaction->area->name,
            'entry_time' => $transaction->entry_time->toIso8601String(),
            'exit_time' => $transaction->exit_time?->toIso8601String(),
            'duration_minutes' => $duration,
            'duration_hours' => ceil($duration / 60),
            'amount' => (float) $transaction->amount,
            'paid_amount' => (float) $transaction->paid_amount,
            'change' => (float) $transaction->change,
            'status' => $transaction->status,
        ]);
    }

    /**
     * List attendant's transactions with optional filters
     * 
     * GET /api/transactions?status=paid&date=2026-03-08
     */
    public function listTransactions(Request $request)
    {
        $query = Transaction::where('user_id', Auth::id())
            ->with(['vehicle', 'area'])
            ->orderBy('entry_time', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('entry_time', $request->date);
        }

        // Filter by area_id
        if ($request->has('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        // Pagination
        $transactions = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'data' => $transactions->items(),
            'pagination' => [
                'current_page' => $transactions->currentPage(),
                'per_page' => $transactions->perPage(),
                'total' => $transactions->total(),
                'last_page' => $transactions->lastPage(),
            ],
        ]);
    }

    /**
     * Record vehicle exit
     * 
     * POST /api/transactions/{id}/exit
     */
    public function recordExit($id)
    {
        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        if ($transaction->exit_time) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle already exited',
            ], 422);
        }

        try {
            DB::transaction(function () use ($transaction) {
                $transaction->processExit(now());
                $transaction->area->updateOccupancy();

                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'exit',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => 'Exit via mobile API',
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Vehicle exit recorded',
                'data' => [
                    'id' => $transaction->id,
                    'exit_time' => $transaction->exit_time->toIso8601String(),
                    'duration_hours' => ceil($transaction->duration_minutes / 60),
                    'amount' => (float) $transaction->amount,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Record payment
     * 
     * POST /api/transactions/{id}/payment
     * Body: {
     *   "paid_amount": 50000
     * }
     */
    public function recordPayment($id, Request $request)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0',
        ]);

        $transaction = Transaction::where('user_id', Auth::id())->findOrFail($id);

        if ($transaction->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Already paid',
            ], 422);
        }

        if (!$transaction->exit_time) {
            return response()->json([
                'success' => false,
                'message' => 'Vehicle must exit first before payment',
            ], 422);
        }

        if ($validated['paid_amount'] < $transaction->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount is less than required',
                'required' => (float) $transaction->amount,
                'paid' => (float) $validated['paid_amount'],
            ], 422);
        }

        try {
            DB::transaction(function () use ($transaction, $validated) {
                $transaction->paid_amount = $validated['paid_amount'];
                $transaction->change = $validated['paid_amount'] - $transaction->amount;
                $transaction->status = 'paid';
                $transaction->save();

                LogActivity::create([
                    'transaction_id' => $transaction->id,
                    'vehicle_id' => $transaction->vehicle_id,
                    'user_id' => Auth::id(),
                    'activity' => 'payment',
                    'plate_number' => $transaction->plate_number,
                    'vehicle_color' => $transaction->vehicle_color,
                    'description' => "Payment: Rp " . number_format($transaction->amount, 2),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => [
                    'id' => $transaction->id,
                    'amount' => (float) $transaction->amount,
                    'paid_amount' => (float) $transaction->paid_amount,
                    'change' => (float) $transaction->change,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Dashboard stats for attendant
     * 
     * GET /api/attendant/dashboard
     */
    public function dashboard()
    {
        $today = today();
        $userId = Auth::id();

        $todayTransactions = Transaction::where('user_id', $userId)
            ->whereDate('entry_time', $today)
            ->get();

        $thisMonth = Transaction::where('user_id', $userId)
            ->whereMonth('entry_time', $today->month)
            ->whereYear('entry_time', $today->year)
            ->get();

        $activeCount = Transaction::where('user_id', $userId)
            ->whereNull('exit_time')
            ->count();

        $todayRevenue = $todayTransactions
            ->where('status', 'paid')
            ->sum('amount');

        $monthRevenue = $thisMonth
            ->where('status', 'paid')
            ->sum('amount');

        return response()->json([
            'today' => [
                'transactions_count' => $todayTransactions->count(),
                'revenue' => (float) $todayRevenue,
                'vehicles_entered' => $todayTransactions->count(),
                'vehicles_exited' => $todayTransactions->whereNotNull('exit_time')->count(),
                'vehicles_paid' => $todayTransactions->where('status', 'paid')->count(),
            ],
            'month' => [
                'transactions_count' => $thisMonth->count(),
                'revenue' => (float) $monthRevenue,
                'vehicles_paid' => $thisMonth->where('status', 'paid')->count(),
            ],
            'active' => [
                'count' => $activeCount,
            ],
        ]);
    }

    /**
     * Get all areas with their rates
     * 
     * GET /api/areas
     */
    public function getAreas()
    {
        $areas = Area::with(['rates' => function ($q) {
            $q->select('id', 'area_id', 'vehicle_type', 'amount');
        }])
            ->select('id', 'name', 'capacity', 'occupied')
            ->get()
            ->map(function ($area) {
                $percentage = round(($area->occupied / $area->capacity) * 100);

                return [
                    'id' => $area->id,
                    'name' => $area->name,
                    'capacity' => $area->capacity,
                    'occupied' => $area->occupied,
                    'available' => $area->capacity - $area->occupied,
                    'percentage' => $percentage,
                    'rates' => $area->rates->map(fn($r) => [
                        'id' => $r->id,
                        'vehicle_type' => $r->vehicle_type,
                        'amount' => (float) $r->amount,
                    ])->toArray(),
                ];
            });

        return response()->json([
            'data' => $areas,
            'count' => $areas->count(),
        ]);
    }

    /**
     * Get single area details
     * 
     * GET /api/areas/{id}
     */
    public function showArea($id)
    {
        $area = Area::with('rates')->findOrFail($id);

        $percentage = round(($area->occupied / $area->capacity) * 100);

        return response()->json([
            'id' => $area->id,
            'name' => $area->name,
            'capacity' => $area->capacity,
            'occupied' => $area->occupied,
            'available' => $area->capacity - $area->occupied,
            'percentage' => $percentage,
            'rates' => $area->rates->map(fn($r) => [
                'id' => $r->id,
                'vehicle_type' => $r->vehicle_type,
                'amount' => (float) $r->amount,
            ]),
        ]);
    }

    /**
     * Validate vehicle by plate number
     * 
     * POST /api/vehicles/validate
     * Body: {
     *   "plate_number": "B 1234 ABC"
     * }
     */
    public function validateVehicle(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20',
        ]);

        $vehicle = Vehicle::where('plate_number', $validated['plate_number'])->first();

        if ($vehicle) {
            return response()->json([
                'exists' => true,
                'vehicle' => [
                    'id' => $vehicle->id,
                    'plate_number' => $vehicle->plate_number,
                    'color' => $vehicle->color,
                    'type' => $vehicle->type,
                ],
            ]);
        }

        return response()->json([
            'exists' => false,
            'message' => 'Vehicle not found',
        ]);
    }

    /**
     * Update user profile
     * 
     * PUT /api/user
     * Body: {
     *   "name": "Budi Susanto",
     *   "photo": "base64_image_data" (optional)
     * }
     */
    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'photo' => 'sometimes|nullable|base64image|max:500',
        ]);

        $user = Auth::user();

        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }

        if (isset($validated['photo'])) {
            // Handle base64 image storage if needed
            // For now, skip photo handling
        }

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ]);
    }
}
