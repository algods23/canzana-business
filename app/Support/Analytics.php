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
        $occupiedRooms = Room::where('status', 'occupied')->count();

        return [
            'total_properties' => Property::count(),
            'total_rooms' => $totalRooms,
            'occupied_rooms' => $occupiedRooms,
            'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0.0,
            'monthly_revenue' => (float) Room::where('status', 'occupied')->sum('rent'),
            'collected_this_month' => (float) Payment::where('status', 'paid')
                ->whereMonth('paid_date', now()->month)
                ->whereYear('paid_date', now()->year)
                ->sum('amount'),
            'pending_payments' => (float) Payment::where('status', 'pending')->sum('amount'),
            'overdue_amount' => (float) Payment::where('status', 'overdue')->sum('amount'),
            'overdue_count' => Payment::where('status', 'overdue')->count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
        ];
    }

    public static function paymentStats(): array
    {
        $baseQuery = Payment::query();

        return [
            'total' => (clone $baseQuery)->count(),
            'paid' => (clone $baseQuery)->where('status', 'paid')->count(),
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'overdue' => (clone $baseQuery)->where('status', 'overdue')->count(),
            'collected' => (float) (clone $baseQuery)->where('status', 'paid')->sum('amount'),
            'outstanding' => (float) (clone $baseQuery)->whereIn('status', ['pending', 'overdue'])->sum('amount'),
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

            $collected = Payment::where('status', 'paid')
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
                'rooms as occupied_rooms' => fn ($query) => $query->where('status', 'occupied'),
            ])
            ->orderBy('name')
            ->get()
            ->map(fn (Property $property) => [
                'name' => $property->name,
                'rate' => $property->occupancy_rate,
            ])
            ->all();
    }

    public static function recentActivities(int $limit = 5): array
    {
        $items = collect();

        $items = $items->merge(
            Payment::query()
                ->with(['tenantModel', 'propertyModel', 'roomModel'])
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
            Tenant::query()
                ->with(['propertyModel', 'roomModel'])
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

        return $items->take($limit)->values()->all();
    }
}