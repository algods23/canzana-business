<?php

namespace App\Support;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Carbon\Carbon;

class Analytics
{
    public static function dashboardStats(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::whereHas('currentTenant')->count();

        return [
            'total_properties' => Property::count(),
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0.0,
            'monthly_revenue' => (float) Room::whereHas('currentTenant')->sum('rent'),
            'collected_this_month' => (float) Payment::where('payments.status', 'paid')
                ->whereMonth('paid_date', now()->month)
                ->whereYear('paid_date', now()->year)
                ->sum('amount'),
            'pending_payments' => (float) Payment::where('payments.status', 'pending')->sum('amount'),
            'overdue_amount' => (float) Payment::where('payments.status', 'overdue')->sum('amount'),
            'overdue_count' => Payment::where('payments.status', 'overdue')->count(),
            'active_tenants' => Tenant::where('tenants.status', 'active')->count(),
        ];
    }

    public static function paymentStats(): array
    {
        $baseQuery = Payment::query();

        return [
            'total' => (clone $baseQuery)->count(),
            'paid' => (clone $baseQuery)->where('payments.status', 'paid')->count(),
            'pending' => (clone $baseQuery)->where('payments.status', 'pending')->count(),
            'overdue' => (clone $baseQuery)->where('payments.status', 'overdue')->count(),
            'collected' => (float) (clone $baseQuery)->where('payments.status', 'paid')->sum('amount'),
            'outstanding' => (float) (clone $baseQuery)->whereIn('payments.status', ['pending', 'overdue'])->sum('amount'),
        ];
    }

    public static function revenueChart(int $months = 6): array
    {
        $start = now()->startOfMonth()->subMonths($months - 1);
        $chart = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonths($i);

            $expected = Payment::whereMonth('due_date', $month->month)
                ->whereYear('due_date', $month->year)
                ->sum('amount');

            $collected = Payment::where('payments.status', 'paid')
                ->whereMonth('paid_date', $month->month)
                ->whereYear('paid_date', $month->year)
                ->sum('amount');

            $chart[] = [
                'month' => $month->format('M'),
                'collected' => (float) $collected,
                'expected' => (float) $expected,
            ];
        }

        return $chart;
    }

    public static function occupancyByProperty(): array
    {
        return Property::query()
            ->withCount([
                'rooms',
                'rooms as occupied_rooms' => fn ($query) => $query->whereHas('currentTenant'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Property $property) => [
                'name' => $property->name,
                'rate' => $property->occupancy_rate,
            ])
            ->all();
    }

    public static function recentActivities(int $limit = 5, ?int $roomId = null): array
    {
        $items = collect();

        $paymentQuery = Payment::query()->with(['tenantModel', 'propertyModel', 'roomModel']);
        $tenantQuery = Tenant::query()->with(['propertyModel', 'roomModel']);

        if ($roomId) {
            $paymentQuery->where('room_id', $roomId);
            $tenantQuery->where('room_id', $roomId);
        }

        $items = $items->merge(
            $paymentQuery
                ->latest('updated_at')
                ->take($limit)
                ->get()
                ->map(function (Payment $payment) {
                    $type = match ($payment->status) {
                        'paid' => 'payment',
                        'overdue' => 'alert',
                        default => 'payment',
                    };

                    return [
                        'type' => $type,
                        'icon' => 'payment',
                        'title' => $payment->status === 'paid' ? 'Payment received' : 'Payment updated',
                        'description' => trim(sprintf('%s for %s %s', $payment->tenant ?? 'Tenant', $payment->property ?? 'property', $payment->unit ? 'Unit '.$payment->unit : '')),
                        'user' => 'System',
                        'time' => $payment->updated_at?->diffForHumans() ?? 'just now',
                        'entity' => 'Payment #'.$payment->id,
                    ];
                })
        );

        $items = $items->merge(
            $tenantQuery
                ->latest('updated_at')
                ->take($limit)
                ->get()
                ->map(function (Tenant $tenant) {
                    return [
                        'type' => 'tenant',
                        'icon' => 'user',
                        'title' => 'Tenant updated',
                        'description' => $tenant->name.' lease is '.($tenant->status === 'active' ? 'active' : strtolower($tenant->status)),
                        'user' => 'System',
                        'time' => $tenant->updated_at?->diffForHumans() ?? 'just now',
                        'entity' => 'Tenant #'.$tenant->id,
                    ];
                })
        );

        return $items->sortByDesc('time')->take($limit)->values()->all();
    }
}
