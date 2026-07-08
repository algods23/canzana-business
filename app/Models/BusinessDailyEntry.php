<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessDailyEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_id',
        'entry_date',
        'sales_amount',
        'disbursement_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'sales_amount' => 'decimal:2',
            'disbursement_amount' => 'decimal:2',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
