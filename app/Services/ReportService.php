<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

/**
 * Service untuk menangani query dan logika laporan parkir
 */
class ReportService
{
    /**
     * Get owner laporan data
     */
    public function getOwnerLaporan(array $params): array
    {
        $mode = $params['mode'] ?? 'single';
        $today = Carbon::today();
        $date = $params['date'] ?? $today->toDateString();
        $from = $params['from'] ?? $today->copy()->subDays(6)->toDateString();
        $to = $params['to'] ?? $today->toDateString();

        $baseQuery = Transaction::whereNotNull('exit_time');

        // Summary totals
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
            $vehicleRecap = Transaction::whereBetween('entry_time', [$from, $to])
                ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
                ->selectRaw('vehicles.type as vehicle_type, COUNT(*) as count')
                ->groupBy('vehicles.type')
                ->get();
        } else {
            $vehicleRecap = Transaction::whereDate('entry_time', $date)
                ->join('vehicles', 'transactions.vehicle_id', '=', 'vehicles.id')
                ->selectRaw('vehicles.type as vehicle_type, COUNT(*) as count')
                ->groupBy('vehicles.type')
                ->get();
        }

        // vehicles still parked (entered in range but not exited yet)
        if ($mode === 'range') {
            $stillParked = Transaction::whereBetween('entry_time', [$from, $to])
                ->whereNull('exit_time')
                ->count();
        } else {
            $stillParked = Transaction::whereDate('entry_time', $date)
                ->whereNull('exit_time')
                ->count();
        }

        return compact(
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
            'stillParked'
        );
    }
}
