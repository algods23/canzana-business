<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'property_id',
        'room_id',
        'amount',
        'due_date',
        'paid_date',
        'status',
        'method',
        'reference',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function tenantModel(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function propertyModel(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function roomModel(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function getTenantAttribute(): ?string
    {
        return $this->tenantModel?->name;
    }

    public function getPropertyAttribute(): ?string
    {
        return $this->propertyModel?->name;
    }

    public function getUnitAttribute(): ?string
    {
        return $this->roomModel?->unit;
    }
}