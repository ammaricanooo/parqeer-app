<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $query = Vehicle::query();
        if (request('search')) {
            $query->where('plate_number', 'like', '%' . request('search') . '%')
                ->orWhere('color', 'like', '%' . request('search') . '%');
        }
        $vehicles = $query->paginate(10);

        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $vehicleTypes = VehicleType::orderBy('name')->get();
        return view('attendant.vehicles.create', compact('vehicleTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Vehicle::class);

        $availableTypes = VehicleType::pluck('key')->toArray();

        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'color' => 'required|string|max:30',
            'type' => ['required', 'string', Rule::in($availableTypes)],
        ]);

        Vehicle::create($validated);

        return redirect()->route('attendant.vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Vehicle $vehicle): View
    {
        $vehicle->load(['transactions']);
        return view('attendant.vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle): View
    {
        $vehicleTypes = VehicleType::orderBy('name')->get();
        return view('attendant.vehicles.edit', compact('vehicle', 'vehicleTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $availableTypes = VehicleType::pluck('key')->toArray();

        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'color' => 'required|string|max:30',
            'type' => ['required', 'string', Rule::in($availableTypes)],
        ]);

        $vehicle->update($validated);

        return redirect()->route('attendant.vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $activeTransactions = $vehicle->transactions()->where('status', 'in')->count();
        $totalTransactions = $vehicle->transactions()->count();

        if ($activeTransactions > 0) {
            return redirect()->route('attendant.vehicles.index')
                ->with('error', "Kendaraan tidak dapat dihapus karena masih ada {$activeTransactions} transaksi aktif (kendaraan masih parkir). Data parkir harus tetap ada.");
        }

        if ($totalTransactions > 0) {
            return redirect()->route('attendant.vehicles.index')
                ->with('error', "Kendaraan tidak dapat dihapus karena masih memiliki {$totalTransactions} transaksi historis. Data transaksi penting untuk laporan dan audit.");
        }

        $vehicle->delete();
        return redirect()->route('attendant.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }

    /**
     * Admin: list vehicles
     */
    public function adminIndex(): View
    {
        $this->authorize('viewAny', Vehicle::class);
        $vehicles = Vehicle::with(['transactions'])->latest()->paginate(15);
        return view('admin.vehicles.index', compact('vehicles'));
    }

    /**
     * Admin: show vehicle detail with transactions/receipts
     */
    public function adminShow(Vehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);
        $vehicle->load(['transactions' => fn($q) => $q->orderBy('created_at', 'desc')->with(['area', 'rate'])]);
        return view('admin.vehicles.show', compact('vehicle'));
    }

    /**
     * Owner: list vehicles
     */
    public function ownerIndex(): View
    {
        $vehicles = Vehicle::with(['transactions'])->latest()->paginate(15);
        return view('owner.vehicles.index', compact('vehicles'));
    }

    /**
     * Owner: show vehicle detail with transactions/receipts
     */
    public function ownerShow(Vehicle $vehicle): View
    {
        $vehicle->load(['transactions' => fn($q) => $q->orderBy('created_at', 'desc')->with(['area', 'rate'])]);
        return view('owner.vehicles.show', compact('vehicle'));
    }
}
