<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Vehicle;
use App\Models\Area;
use App\Models\Rate;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Menampilkan form input kendaraan masuk (create transaction)
     */
    public function create(): View
    {
        $areas = Area::all();
        
        return view('attendant.transaction.create', compact('areas'));
    }

    /**
     * Simpan transaksi baru (kendaraan masuk)
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'plate_number' => 'required|string|max:20',
            'vehicle_color' => 'required|string|max:30',
            'vehicle_type' => 'required|string|in:motorcycle,car,truck,bus',
            'area_id' => 'required|exists:areas,id',
            'entry_time' => 'required|date_format:Y-m-d H:i',
        ]);

        // Get area untuk fetch rate berdasarkan vehicle_type
        $area = Area::findOrFail($validated['area_id']);
        $rate = $area->getRateByVehicleType($validated['vehicle_type']);

        if (!$rate) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Tidak ada tarif untuk tipe kendaraan ini di area tersebut.');
        }

        // Jika vehicle_id tidak ada, cari berdasarkan plate_number atau buat yang baru
        if (!$validated['vehicle_id']) {
            $vehicle = Vehicle::where('plate_number', $validated['plate_number'])->first();
            if (!$vehicle) {
                $vehicle = Vehicle::create([
                    'plate_number' => $validated['plate_number'],
                    'color' => $validated['vehicle_color'],
                    'type' => $validated['vehicle_type'],
                ]);
            }
            $validated['vehicle_id'] = $vehicle->id;
        }

        // Tambahkan user_id dan rate_id
        $validated['user_id'] = Auth::id();
        $validated['rate_id'] = $rate->id;
        $validated['status'] = 'masuk';
        
        // Konversi entry_time string ke datetime
        $validated['entry_time'] = Carbon::createFromFormat('Y-m-d H:i', $validated['entry_time']);

        // Buat transaksi baru
        $transaction = Transaction::create($validated);

        return redirect()->route('petugas.transaction.index')
            ->with('success', 'Kendaraan masuk dicatat. ID Transaksi: ' . $transaction->id);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Ambil transaksi yang masih aktif (exit_time null)
        $activeTransactions = Transaction::whereNull('exit_time')
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil transaksi yang sudah selesai (hari ini)
        $completedTransactions = Transaction::whereNotNull('exit_time')
            ->whereDate('exit_time', today())
            ->with(['vehicle', 'area', 'rate'])
            ->orderBy('exit_time', 'desc')
            ->get();

        return view('attendant.transaction.index', compact('activeTransactions', 'completedTransactions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function showExit(Transaction $transaction): View
    {
        if ($transaction->exit_time) {
            return abort(403, 'Transaksi ini sudah selesai.');
        }

        return view('attendant.transaction.exit', compact('transaction'));
    }

    /**
     * Proses kendaraan keluar dan hitung tarif per jam
     */
    public function processExit(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->exit_time) {
            return redirect()->back()->with('error', 'Transaksi sudah selesai.');
        }

        $validated = $request->validate([
            'exit_time' => 'required|date_format:Y-m-d H:i|after_or_equal:' . $transaction->entry_time->format('Y-m-d H:i'),
        ]);

        // Update transaksi dengan exit time
        $exitTime = Carbon::createFromFormat('Y-m-d H:i', $validated['exit_time']);
        $transaction->processExit($exitTime);

        return redirect()->route('petugas.transaction.receipt', $transaction->id)
            ->with('success', 'Kendaraan keluar dicatat. Silahkan cetak struk.');
    }

    /**
     * Menampilkan struk/receipt untuk dicetak
     */
    public function receipt(Transaction $transaction): View|RedirectResponse
    {
        if (!$transaction->exit_time) {
            return redirect()->route('petugas.transaction.index')
                ->with('error', 'Transaksi belum selesai.');
        }

        return view('attendant.transaction.receipt', compact('transaction'));
    }

    /**
     * Proses pembayaran transaksi
     */
    public function pay(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->status === 'paid') {
            return redirect()->back()->with('info', 'Transaksi sudah dibayar.');
        }

        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:' . $transaction->amount,
        ]);

        // Update status transaksi
        $transaction->status = 'paid';
        $transaction->save();

        return redirect()->route('petugas.transaction.index')
            ->with('success', 'Pembayaran berhasil. Struk telah dicetak.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
    /**
     * Search kendaraan berdasarkan plate number (untuk autocomplete)
     */
    public function searchVehicle(Request $request)
    {
        $search = $request->get('q');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $vehicles = Vehicle::where('plate_number', 'like', '%' . $search . '%')
            ->limit(10)
            ->get()
            ->map(fn($v) => [
                'id' => $v->id,
                'label' => $v->plate_number . ' (' . $v->color . ')',
                'plate_number' => $v->plate_number,
                'color' => $v->color,
            ]);

        return response()->json($vehicles);
    }

    /**
     * Get rate berdasarkan area dan vehicle type (API untuk frontend)
     */
    public function getRate(Request $request)
    {
        $areaId = $request->get('area_id');
        $vehicleType = $request->get('vehicle_type');

        if (!$areaId || !$vehicleType) {
            return response()->json(['success' => false]);
        }

        $area = Area::find($areaId);
        if (!$area) {
            return response()->json(['success' => false]);
        }

        $rate = $area->getRateByVehicleType($vehicleType);
        if (!$rate) {
            return response()->json(['success' => false]);
        }

        return response()->json(['success' => true, 'rate' => $rate]);
    }
}
