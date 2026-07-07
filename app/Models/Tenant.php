<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'room_id',
        'name',
        'email',
        'phone',
        'company',
        'lease_start',
        'lease_end',
        'rent',
        'balance',
        'status',
        'contract_path',
        'contract_name',
    ];

    protected function casts(): array
    {
        return [
            'lease_start' => 'date',
            'lease_end' => 'date',
            'rent' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function propertyModel(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function roomModel(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
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