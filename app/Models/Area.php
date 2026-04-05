<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Area extends Model
{
    /** @use HasFactory<\Database\Factories\AreaFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'occupied',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'occupied' => 'integer',
    ];

    /**
     * Relasi ke rates (satu area bisa punya multiple rates untuk car & motorcycle)
     */
    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }

    /**
     * Relasi ke transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get rate untuk vehicle type tertentu
     */
    public function getRateByVehicleType(string $vehicleType): ?Rate
    {
        return $this->rates()->where('vehicle_type', $vehicleType)->first();
    }

    /**
     * Update capacity occupied berdasarkan vehicles yang masih parkir
     */
    public function updateOccupancy(): void
    {
        $occupied = Transaction::where('area_id', $this->id)
            ->where('status', '!=', 'out')
            ->count();

        $this->occupied = $occupied;
        $this->save();
    }

    /**
     * Update occupancy untuk semua areas sekaligus (optimized)
     */
    public static function updateAllOccupancy(): void
    {
        $occupancyCounts = Transaction::select('area_id', DB::raw('count(*) as occupied'))
            ->where('status', '!=', 'out')
            ->groupBy('area_id')
            ->pluck('occupied', 'area_id');

        // Reset semua occupied ke 0 dulu
        self::query()->update(['occupied' => 0]);

        // Update occupied berdasarkan count
        foreach ($occupancyCounts as $areaId => $occupied) {
            self::where('id', $areaId)->update(['occupied' => $occupied]);
        }
    }

    /**
     * Cek apakah area masih punya slot
     */
    public function hasAvailableSlot(): bool
    {
        return $this->occupied < $this->capacity;
    }
}
