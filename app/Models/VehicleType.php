<?php

namespace App\Models;

use App\Models\Rate;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
    ];

    protected $casts = [
        'key' => 'string',
        'name' => 'string',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class, 'vehicle_type', 'key');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class, 'type', 'key');
    }
}
