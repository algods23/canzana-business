<?php

namespace App\Http\Controllers;

use App\Data\MockData;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'stats' => MockData::dashboardStats(),
            'revenueChart' => MockData::revenueChart(),
            'occupancy' => MockData::occupancyByProperty(),
            'activities' => array_slice(MockData::activities(), 0, 5),
            'overduePayments' => array_filter(MockData::payments(), fn ($p) => $p['status'] === 'overdue'),
            'properties' => MockData::properties(),
        ]);
    }
}
