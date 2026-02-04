<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $vehicles = Vehicle::latest()->paginate(15);
        return view('attendant.vehicles.index', compact('vehicles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('attendant.vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Vehicle::class);

        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number',
            'color' => 'required|string|max:30',
            'type' => 'required|string|in:motorcycle,car',
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
        return view('attendant.vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'color' => 'required|string|max:30',
            'type' => 'required|string|in:motorcycle,car',
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
        $vehicle->delete();
        return redirect()->route('attendant.vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}

