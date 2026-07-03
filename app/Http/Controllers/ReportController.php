<?php

namespace App\Http\Controllers;

use App\Data\MockData;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index', [
            'stats' => MockData::dashboardStats(),
            'revenueChart' => MockData::revenueChart(),
            'occupancy' => MockData::occupancyByProperty(),
            'properties' => MockData::properties(),
            'overduePayments' => array_filter(MockData::payments(), fn ($p) => $p['status'] === 'overdue'),
        ]);
    }
}
