<?php

namespace App\Http\Controllers;

use App\Models\Business;
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
            // Auto-select the rental business for this user
            $rentalBusiness = $request->user()->businesses()->where('type', 'rental')->first();
            if ($rentalBusiness) {
                $request->session()->put('selected_business_id', $rentalBusiness->id);
            } else {
                return redirect()->route('businesses.select');
            }
        }

        $business = $request->user()
            ->businesses()
            ->whereKey($request->session()->get('selected_business_id'))
            ->first();

        if (! $business) {
            // Try to auto-select rental business as fallback
            $rentalBusiness = $request->user()->businesses()->where('type', 'rental')->first();
            if ($rentalBusiness) {
                $request->session()->put('selected_business_id', $rentalBusiness->id);
                $business = $rentalBusiness;
            } else {
                $request->session()->forget('selected_business_id');
                return redirect()->route('businesses.select');
            }
        }

        if ($business->type !== 'rental') {
            return view('businesses.dashboard', [
                'business' => $business,
                'todayEntry' => $business->dailyEntries()
                    ->whereDate('entry_date', now()->toDateString())
                    ->first(),
                'recentEntries' => $business->dailyEntries()
                    ->latest('entry_date')
                    ->take(10)
                    ->get(),
                'monthlySales' => (float) $business->dailyEntries()
                    ->whereBetween('entry_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                    ->sum('sales_amount'),
                'monthlyDisbursements' => (float) $business->dailyEntries()
                    ->whereBetween('entry_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                    ->sum('disbursement_amount'),
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

    public function storeDailyEntry(Request $request, Business $business): RedirectResponse
    {
        abort_unless((int) $business->user_id === (int) $request->user()->id, 404);
        abort_unless(in_array($business->type, ['fishpond', 'fruits'], true), 404);

        $validated = $request->validate([
            'entry_date' => ['required', 'date'],
            'sales_amount' => ['required', 'numeric', 'min:0'],
            'disbursement_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $business->dailyEntries()->updateOrCreate(
            ['entry_date' => $validated['entry_date']],
            [
                'user_id' => $request->user()->id,
                'sales_amount' => $validated['sales_amount'],
                'disbursement_amount' => $validated['disbursement_amount'],
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $request->session()->put('selected_business_id', $business->id);

        return redirect()->route('dashboard')->with('success', 'Daily entry saved.');
    }
}
