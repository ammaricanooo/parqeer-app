<?php

namespace App\Http\Controllers;

use App\Models\VehicleType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class VehicleTypeController extends Controller
{
    public function index()
    {
        $vehicleTypes = VehicleType::orderBy('name')->paginate(10);
        return view('admin.vehicle-types.index', compact('vehicleTypes'));
    }

    public function create()
    {
        return view('admin.vehicle-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'key' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('vehicle_types', 'key')],
        ]);

        VehicleType::create($validated);

        return redirect()
            ->route('admin.vehicle-types.index')
            ->with('success', 'Tipe kendaraan berhasil ditambahkan.');
    }

    public function edit(VehicleType $vehicleType)
    {
        return view('admin.vehicle-types.edit', compact('vehicleType'));
    }

    public function update(Request $request, VehicleType $vehicleType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'key' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('vehicle_types', 'key')->ignore($vehicleType->id)],
        ]);

        $vehicleType->update($validated);

        return redirect()
            ->route('admin.vehicle-types.index')
            ->with('success', 'Tipe kendaraan berhasil diperbarui.');
    }

    public function destroy(VehicleType $vehicleType)
    {
        $rateCount = $vehicleType->rates()->count();
        $vehicleCount = $vehicleType->vehicles()->count();

        if ($rateCount > 0 || $vehicleCount > 0) {
            return redirect()
                ->route('admin.vehicle-types.index')
                ->with('error', "Tipe kendaraan tidak dapat dihapus karena sudah digunakan di {$rateCount} tarif dan {$vehicleCount} kendaraan.");
        }

        $vehicleType->delete();

        return redirect()
            ->route('admin.vehicle-types.index')
            ->with('success', 'Tipe kendaraan berhasil dihapus.');
    }
}
