<?php

namespace App\Support;

use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TenantBalance
{
    public static function roomBreakdowns(Tenant $tenant): Collection
    {
        $tenant->loadMissing(['rooms.payments']);

        return $tenant->rooms->map(function ($room): array {
            $rent = (float) $room->rent;
            $downpaymentMonths = (int) ($room->downpayment_months ?? 0);
            $monthlyDueDates = self::monthlyDueDates($room->lease_start, $room->lease_end);
            $generatedDueKeys = $monthlyDueDates->map(fn (Carbon $date) => $date->toDateString())->all();

            $paidAmount = (float) $room->payments
                ->where('tenant_id', $room->tenant_id)
                ->where('status', 'paid')
                ->sum('amount');

            $extraUnpaidAmount = (float) $room->payments
                ->where('tenant_id', $room->tenant_id)
                ->whereIn('status', ['pending', 'overdue'])
                ->reject(fn ($payment) => $payment->due_date && in_array($payment->due_date->toDateString(), $generatedDueKeys, true))
                ->sum('amount');

            $downpaymentAmount = $rent * $downpaymentMonths;
            $monthlyAmount = $rent * $monthlyDueDates->count();
            $totalPayable = $downpaymentAmount + $monthlyAmount + $extraUnpaidAmount;
            $balance = $totalPayable - $paidAmount;

            return [
                'room' => $room,
                'rent' => $rent,
                'downpayment_months' => $downpaymentMonths,
                'downpayment_amount' => $downpaymentAmount,
                'monthly_months_due' => $monthlyDueDates->count(),
                'monthly_amount_due' => $monthlyAmount,
                'pending_amount' => $extraUnpaidAmount,
                'paid_amount' => $paidAmount,
                'balance' => $balance,
                'advance' => max(0, -$balance),
                'months_behind' => $rent > 0 ? (int) ceil(max(0, $balance) / $rent) : 0,
                'last_due_date' => $monthlyDueDates->last(),
                'next_due_date' => self::nextDueDate($room->lease_start, $room->lease_end),
            ];
        });
    }

    public static function totalBalance(Tenant $tenant): float
    {
        if ($tenant->rooms->isEmpty()) {
            return (float) $tenant->balance;
        }

        return (float) self::roomBreakdowns($tenant)->sum('balance');
    }

    public static function sync(Tenant $tenant): void
    {
        $tenant->loadMissing(['rooms.payments']);

        if ($tenant->rooms->isEmpty()) {
            return;
        }

        $balance = self::totalBalance($tenant);

        $tenant->forceFill([
            'balance' => $balance,
            'status' => $balance > 0 ? 'overdue' : 'active',
        ])->save();
    }

    private static function monthlyDueDates($leaseStart, $leaseEnd): Collection
    {
        if (! $leaseStart) {
            return collect();
        }

        $due = Carbon::parse($leaseStart)->addMonthNoOverflow()->startOfDay();
        $today = Carbon::today();
        $lastDate = $leaseEnd ? Carbon::parse($leaseEnd)->startOfDay()->min($today) : $today;
        $dates = collect();

        while ($due->lte($lastDate) && $dates->count() < 600) {
            $dates->push($due->copy());
            $due->addMonthNoOverflow();
        }

        return $dates;
    }

    private static function nextDueDate($leaseStart, $leaseEnd): ?Carbon
    {
        if (! $leaseStart) {
            return null;
        }

        $due = Carbon::parse($leaseStart)->addMonthNoOverflow()->startOfDay();
        $today = Carbon::today();
        $leaseEndDate = $leaseEnd ? Carbon::parse($leaseEnd)->startOfDay() : null;

        while ($due->lte($today) && (! $leaseEndDate || $due->lte($leaseEndDate))) {
            $due->addMonthNoOverflow();
        }

        return $leaseEndDate && $due->gt($leaseEndDate) ? null : $due;
    }
}
