<?php

namespace App\Http\Controllers;

use App\Models\LogActivity;
use App\Http\Requests\StoreLogActivityRequest;
use App\Http\Requests\UpdateLogActivityRequest;

class LogActivityController extends Controller
{
    /**
     * Display a listing of the resource for owner (read-only).
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $this->authorize('viewAny', LogActivity::class);

        $query = LogActivity::with(['vehicle', 'user', 'transaction'])->orderBy('created_at', 'desc');

        if ($q = $request->get('q')) {
            $query->where(function ($sub) use ($q) {
                $sub->where('plate_number', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($u) use ($q) {
                        $u->where('name', 'like', "%{$q}%");
                    });
            });
        }

        if ($activity = $request->get('activity')) {
            $query->where('activity', $activity);
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('owner.logs.index', compact('logs'));
    }

    /**
     * Display the specified resource detail.
     */
    public function show(LogActivity $logActivity)
    {
        $this->authorize('view', $logActivity);

        $logActivity->load(['vehicle', 'user', 'transaction']);

        return view('owner.logs.show', compact('logActivity'));
    }

    // other methods remain intentionally empty to keep read-only for owner
}
