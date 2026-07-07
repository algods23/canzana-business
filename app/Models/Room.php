<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'tenant_id',
        'unit',
        'floor',
        'type',
        'size_sqm',
        'rent',
        'status',
        'lease_start',
        'lease_end',
    ];

    protected function casts(): array
    {
        return [
            'size_sqm' => 'decimal:2',
            'rent' => 'decimal:2',
            'lease_start' => 'date',
            'lease_end' => 'date',
        ];
    }

    public function buildingModel(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    /**
     * The tenant directly assigned to this room (via rooms.tenant_id).
     * A tenant can be assigned to many rooms; each room holds its own tenant_id.
     */
    public function currentTenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getTenantAttribute(): ?string
    {
        return $this->currentTenant?->name;
    }
}
