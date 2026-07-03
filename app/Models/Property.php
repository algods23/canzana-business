<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'type',
        'status',
        'image',
    ];

    public function buildings(): HasMany
    {
        return $this->hasMany(Building::class);
    }

    public function rooms(): HasManyThrough
    {
        return $this->hasManyThrough(Room::class, Building::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getOccupancyRateAttribute(): float
    {
        $rooms = (int) ($this->attributes['rooms_count'] ?? $this->rooms()->count());

        if ($rooms === 0) {
            return 0.0;
        }

        return round(((int) $this->occupied_rooms / $rooms) * 100, 1);
    }

    public function getBuildingsCountAttribute(): int
    {
        return (int) ($this->attributes['buildings_count'] ?? $this->buildings()->count());
    }

    public function getRoomsCountAttribute(): int
    {
        return (int) ($this->attributes['rooms_count'] ?? $this->rooms()->count());
    }

    public function getOccupiedRoomsAttribute(): int
    {
        return (int) ($this->attributes['occupied_rooms'] ?? $this->rooms()->where('status', 'occupied')->count());
    }

    public function getMonthlyRevenueAttribute(): float
    {
        return (float) ($this->attributes['monthly_revenue'] ?? $this->rooms()->where('status', 'occupied')->sum('rent'));
    }
}