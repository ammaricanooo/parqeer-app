<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Area::query();

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        $areas = $query->paginate(10);

        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAreaRequest $request)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:areas,name',
            'capacity' => 'required|integer|min:0',
            'occupied' => 'required|integer|min:0|lte:capacity',
        ]);

        Area::create([
            'name'     => $request->name,
            'capacity' => $request->capacity,
            'occupied' => $request->occupied,
        ]);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Data area berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Area $area)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Area $area)
    {
        return view('admin.areas.edit', compact('area'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAreaRequest $request, Area $area)
    {
        $request->validate([
            'name'     => 'required|string|max:100|unique:areas,name,' . $area->id,
            'capacity' => 'required|integer|min:0',
            'occupied' => 'required|integer|min:0|lte:capacity',
        ]);

        $area->update([
            'name'     => $request->name,
            'capacity' => $request->capacity,
            'occupied' => $request->occupied,
        ]);

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Data area berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Area $area)
    {
        // Cek apakah masih ada transaksi aktif (kendaraan yang sedang parkir)
        $activeTransactions = $area->transactions()->where('status', 'in')->count();

        if ($activeTransactions > 0) {
            return redirect()
                ->route('admin.areas.index')
                ->with('error', "Area '{$area->name}' tidak dapat dihapus karena masih ada {$activeTransactions} kendaraan yang sedang parkir!");
        }

        // Cek apakah masih ada transaksi historis
        $totalTransactions = $area->transactions()->count();

        if ($totalTransactions > 0) {
            return redirect()
                ->route('admin.areas.index')
                ->with('error', "Area '{$area->name}' tidak dapat dihapus karena masih memiliki {$totalTransactions} data transaksi historis. Data ini diperlukan untuk laporan dan audit.");
        }

        $area->delete();

        return redirect()
            ->route('admin.areas.index')
            ->with('success', 'Data area berhasil dihapus!');
    }
}
