<?php

namespace App\Http\Controllers;

use App\Models\Rate;
use App\Models\Area;
use Illuminate\Http\Request;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;

class RateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Rate::with('area');

        if ($search = request('search')) {

            $vehicleMap = [
                'mobil' => 'car',
                'motor' => 'motorcycle',
                'car' => 'car',
                'motorcycle' => 'motorcycle',
            ];

            $query->where(function ($q) use ($search, $vehicleMap) {

                // search vehicle_type
                if (isset($vehicleMap[strtolower($search)])) {
                    $q->where('vehicle_type', $vehicleMap[strtolower($search)]);
                } else {
                    $q->where('vehicle_type', 'like', "%{$search}%");
                }

                // search area name
                $q->orWhereHas('area', function ($area) use ($search) {
                    $area->where('name', 'like', "%{$search}%");
                });
            });
        }

        $rates = $query->paginate(10);

        return view('admin.rates.index', compact('rates'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $areas = Area::whereNotIn('id', function ($query) {
            $query->select('area_id')->from('rates');
        })->get();
        return view('admin.rates.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateRequest $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id|unique:rates,area_id',
            'vehicle_type' => 'required|string|max:50',
            'amount' => 'required|integer|min:0',
        ]);

        Rate::create([
            'area_id'      => $request->area_id,
            'vehicle_type' => $request->vehicle_type,
            'amount'       => $request->amount,
        ]);

        return redirect()
            ->route('admin.rates.index')
            ->with('success', 'Data rate berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Rate $rate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Rate $rate, Request $request)
    {
        $areas = Area::whereNotIn('id', function ($query) use ($rate) {
            $query->select('area_id')->from('rates')->where('id', '!=', $rate->id);
        })->get();
        return view('admin.rates.edit', compact('rate', 'areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRateRequest $request, Rate $rate)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'vehicle_type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
        ]);

        $rate->update([
            'area_id'      => $request->area_id,
            'vehicle_type' => $request->vehicle_type,
            'amount'       => $request->amount,
        ]);

        return redirect()
            ->route('admin.rates.index')
            ->with('success', 'Data rate berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rate $rate)
    {
        $rate->delete();

        return redirect()
            ->route('admin.rates.index')
            ->with('success', 'Data rate berhasil dihapus');
    }
}
