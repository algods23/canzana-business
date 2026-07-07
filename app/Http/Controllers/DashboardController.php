<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Support\Analytics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('selected_business_id')) {
            return redirect()->route('businesses.select');
        }

        $business = $request->user()
            ->businesses()
            ->whereKey($request->session()->get('selected_business_id'))
            ->first();

        if (! $business) {
            $request->session()->forget('selected_business_id');

            return redirect()->route('businesses.select');
        }

        if ($business->type !== 'rental') {
            return view('businesses.dashboard', [
                'business' => $business,
            ]);
        }

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
            'business' => $business,
            'stats' => Analytics::dashboardStats(),
            'revenueChart' => Analytics::revenueChart(),
            'occupancy' => Analytics::occupancyByProperty(),
            'activities' => Analytics::recentActivities(5),
            'overduePayments' => Payment::query()->with(['tenantModel', 'propertyModel', 'roomModel'])->where('payments.status', 'overdue')->orderBy('due_date')->get(),
            'properties' => $properties,
        ]);
    }
}
