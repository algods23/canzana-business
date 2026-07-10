<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_type',
        'module_type',
        'amount',
        'description',
        'transaction_date',
        'reference_type',
        'reference_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    /**
     * Scope to filter by account type
     */
    public function scopeByAccount($query, string $accountType)
    {
        return $query->where('account_type', $accountType);
    }

    /**
     * Scope to filter by module type
     */
    public function scopeByModule($query, string $moduleType)
    {
        return $query->where('module_type', $moduleType);
    }

    /**
     * Get balance for a specific account
     */
    public static function getBalance(string $accountType): float
    {
        $income = self::byAccount($accountType)
            ->where('module_type', 'income')
            ->where('status', 'completed')
            ->sum('amount');

        $expense = self::byAccount($accountType)
            ->where('module_type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        return (float) ($income - $expense);
    }

    /**
     * Get total income for a specific account
     */
    public static function getTotalIncome(string $accountType): float
    {
        return (float) self::byAccount($accountType)
            ->where('module_type', 'income')
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get total expenses for a specific account
     */
    public static function getTotalExpenses(string $accountType): float
    {
        return (float) self::byAccount($accountType)
            ->where('module_type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get net income for a specific account
     */
    public static function getNetIncome(string $accountType): float
    {
        return self::getTotalIncome($accountType) - self::getTotalExpenses($accountType);
    }
}
