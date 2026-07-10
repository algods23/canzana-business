<?php

namespace App\Http\Controllers;

use App\Models\Expense;
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
            'expensesByCategory' => Analytics::expensesByCategory(),
            'properties' => Property::query()->withCount([
                'rooms',
                'rooms as occupied_rooms' => fn ($query) => $query->where('rooms.status', 'occupied'),
            ])->withSum([
                'rooms as monthly_revenue' => fn ($query) => $query->where('rooms.status', 'occupied'),
            ], 'rent')->orderBy('name')->get(),
            'overduePayments' => Payment::query()->with(['tenantModel', 'roomModel', 'propertyModel'])->where('payments.status', 'overdue')->orderBy('due_date')->get(),
            'recentExpenses' => Expense::query()->with(['buildingModel.propertyModel', 'roomModel'])->latest('expense_date')->take(10)->get(),
        ]);
    }
}
