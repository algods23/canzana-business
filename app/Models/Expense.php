<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'building_id',
        'room_id',
        'category',
        'description',
        'recipient_name',
        'amount',
        'expense_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function buildingModel(): BelongsTo
    {
        return $this->belongsTo(Building::class, 'building_id');
    }

    public function roomModel(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Convenience accessor: building name.
     */
    public function getBuildingNameAttribute(): ?string
    {
        return $this->buildingModel?->name;
    }

    /**
     * Convenience accessor: room unit.
     */
    public function getRoomUnitAttribute(): ?string
    {
        return $this->roomModel?->unit;
    }
}
