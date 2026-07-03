<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Support\Analytics;

class DashboardController extends Controller
{
    public function index()
    {
        $properties = Property::query()
            ->withCount([
                'buildings',
                'rooms',
                'rooms as occupied_rooms' => fn ($query) => $query->where('rooms.status', 'occupied'),
            ])
            ->withSum([
                'rooms as monthly_revenue' => fn ($query) => $query->where('rooms.status', 'occupied'),
            ], 'rent')
            ->orderBy('name')
            ->get();

        return view('dashboard.index', [
            'stats' => Analytics::dashboardStats(),
            'revenueChart' => Analytics::revenueChart(),
            'occupancy' => Analytics::occupancyByProperty(),
            'activities' => Analytics::recentActivities(5),
            'overduePayments' => Payment::query()->with(['tenantModel', 'propertyModel', 'roomModel'])->where('payments.status', 'overdue')->orderBy('due_date')->get(),
            'properties' => $properties,
        ]);
    }
}
