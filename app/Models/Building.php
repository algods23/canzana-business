<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Building extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'type',
        'name',
        'floors',
        'status',
    ];

    public function propertyModel(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(\App\Models\Expense::class);
    }

    public function getRoomsCountAttribute(): int
    {
        return (int) ($this->attributes['rooms_count'] ?? $this->rooms()->count());
    }

    public function getOccupiedAttribute(): int
    {
        return (int) ($this->attributes['occupied'] ?? $this->rooms()->where('rooms.status', 'occupied')->count());
    }

    public function getOccupancyRateAttribute(): float
    {
        $rooms = $this->rooms_count;

        if ($rooms === 0) {
            return 0.0;
        }

        return round(($this->occupied / $rooms) * 100, 1);
    }
}
