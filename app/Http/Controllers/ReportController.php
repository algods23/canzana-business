<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Support\Analytics;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'stats' => Analytics::dashboardStats(),
            'revenueChart' => Analytics::revenueChart(),
            'occupancy' => Analytics::occupancyByProperty(),
            'properties' => Property::query()->withCount([
                'rooms',
                'rooms as occupied_rooms' => fn ($query) => $query->where('status', 'occupied'),
            ])->withSum([
                'rooms as monthly_revenue' => fn ($query) => $query->where('status', 'occupied'),
            ], 'rent')->orderBy('name')->get(),
            'overduePayments' => Payment::query()->with(['tenantModel', 'roomModel', 'propertyModel'])->where('status', 'overdue')->orderBy('due_date')->get(),
        ]);
    }
}
