<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'unit',
        'floor',
        'type',
        'size_sqm',
        'rent',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'size_sqm' => 'decimal:2',
            'rent' => 'decimal:2',
        ];
    }

    public function buildingModel(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function currentTenant(): HasOne
    {
        return $this->hasOne(Tenant::class)->where('status', 'active');
    }

    public function getTenantAttribute(): ?string
    {
        return $this->currentTenant?->name;
    }
}