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

        if (request('search')) {
            $query->where('vehicle_type', 'like', '%' . request('search') . '%')
                ->orWhereHas('area', function ($q) {
                    $q->where('name', 'like', '%' . request('search') . '%');
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
        $areas = Area::all();
        return view('admin.rates.create', compact('areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateRequest $request)
    {
        // dd($request->all());
        $request->validate([
            'area_id' => 'required|exists:areas,id',
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
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'vehicle_type' => 'required|string|max:50',
            'amount' => 'required|integer|min:0',
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
     * Update the specified resource in storage.
     */
    public function update(UpdateRateRequest $request, Rate $rate)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'vehicle_type' => 'required|string|max:50',
            'amount' => 'required|integer|min:0',
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
