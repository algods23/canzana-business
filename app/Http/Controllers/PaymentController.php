<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Payment;
use App\Models\Tenant;
use App\Support\Analytics;
use App\Support\TenantBalance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(Request $request): View
    {
        $payment = new Payment();
        
        if ($request->has('tenant_id') && $request->has('room_id')) {
            $payment->tenant_id = $request->input('tenant_id');
            $payment->room_id = $request->input('room_id');
            
            $tenant = Tenant::with('rooms')->find($payment->tenant_id);
            if ($tenant) {
                $room = $tenant->rooms->where('id', $payment->room_id)->first();
                if ($room && $room->lease_start) {
                    $roomBalance = TenantBalance::roomBreakdowns($tenant)
                        ->first(fn ($breakdown) => $breakdown['room']->id === (int) $room->id);
                    $payment->amount = max(0, $roomBalance['balance'] ?? (float) $room->rent);
                    $lastPayment = Payment::where('tenant_id', $payment->tenant_id)
                                          ->where('room_id', $payment->room_id)
                                          ->orderByDesc('due_date')
                                          ->first();
                    
                    if ($lastPayment && $lastPayment->due_date) {
                        $payment->due_date = \Carbon\Carbon::parse($lastPayment->due_date)->addMonth();
                    } else {
                        $payment->due_date = \Carbon\Carbon::parse($room->lease_start)->addMonth();
                    }
                }
            }
        }

        return view('payments.create', [
            'payment' => $payment,
            'tenants' => Tenant::query()->with(['rooms.buildingModel.propertyModel'])->orderBy('name')->get(),
            'properties' => Property::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'paid_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'paid', 'overdue'])],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'room_id' => ['nullable', 'exists:rooms,id'],
        ]);

        $tenant = Tenant::with('rooms.buildingModel')->findOrFail($validated['tenant_id']);
        
        $room = null;
        if (!empty($validated['room_id'])) {
            $room = $tenant->rooms->where('id', $validated['room_id'])->first();
        }
        if (!$room) {
            $room = $tenant->rooms->first();
        }

        $validated['property_id'] = $room ? $room->buildingModel->property_id : $tenant->property_id;
        $validated['room_id'] = $room ? $room->id : $tenant->room_id;

        // Determine status server-side from dates (overrides any client-submitted value)
        if (!empty($validated['paid_date'])) {
            $validated['status'] = 'paid';
        } elseif (!empty($validated['due_date']) && $validated['due_date'] < now()->toDateString()) {
            $validated['status'] = 'overdue';
        } else {
            $validated['status'] = 'pending';
        }

        Payment::create($validated);
        TenantBalance::sync($tenant->fresh(['rooms.payments']));

        return redirect()
            ->route('tenants.show', $validated['tenant_id'])
            ->with('success', 'Payment recorded.');
    }

    public function edit(Payment $payment): View
    {
        return view('payments.edit', [
            'payment' => $payment,
            'tenants' => Tenant::query()->with(['rooms.buildingModel.propertyModel'])->orderBy('name')->get(),
            'properties' => Property::query()->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['required', 'date'],
            'paid_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'paid', 'overdue'])],
            'method' => ['nullable', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'room_id' => ['nullable', 'exists:rooms,id'],
        ]);

        $tenant = Tenant::with('rooms.buildingModel')->findOrFail($validated['tenant_id']);
        
        $room = null;
        if (!empty($validated['room_id'])) {
            $room = $tenant->rooms->where('id', $validated['room_id'])->first();
        }
        if (!$room) {
            $room = $tenant->rooms->first();
        }

        $validated['property_id'] = $room ? $room->buildingModel->property_id : $tenant->property_id;
        $validated['room_id'] = $room ? $room->id : $tenant->room_id;

        // Determine status server-side from dates
        if (!empty($validated['paid_date'])) {
            $validated['status'] = 'paid';
        } elseif (!empty($validated['due_date']) && $validated['due_date'] < now()->toDateString()) {
            $validated['status'] = 'overdue';
        } else {
            $validated['status'] = 'pending';
        }

        $originalTenantId = $payment->tenant_id;

        $payment->update($validated);

        TenantBalance::sync($tenant->fresh(['rooms.payments']));
        if ((int) $originalTenantId !== (int) $tenant->id && $originalTenant = Tenant::find($originalTenantId)) {
            TenantBalance::sync($originalTenant->fresh(['rooms.payments']));
        }

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $tenant = $payment->tenant_id ? Tenant::find($payment->tenant_id) : null;

        $payment->delete();
        if ($tenant) {
            TenantBalance::sync($tenant->fresh(['rooms.payments']));
        }

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }

    public function markPaid(Request $request, Payment $payment): RedirectResponse
    {
        if ($payment->status === 'paid') {
            return back()->with('info', 'Payment is already marked as paid.');
        }

        $payment->update([
            'status'    => 'paid',
            'paid_date' => now()->toDateString(),
        ]);

        if ($payment->tenant_id && $tenant = Tenant::find($payment->tenant_id)) {
            TenantBalance::sync($tenant->fresh(['rooms.payments']));
        }

        $redirectTo = $request->query('redirect_tenant');
        if ($redirectTo) {
            return redirect()->route('tenants.show', $redirectTo)->with('success', 'Payment marked as paid.');
        }

        return redirect()->route('payments.index')->with('success', 'Payment marked as paid.');
    }

    public function index(Request $request)
    {
        $payments = Payment::query()->with(['tenantModel', 'roomModel', 'propertyModel']);

        if ($status = $request->get('status')) {
            $payments->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $payments->where(function ($query) use ($search): void {
                $query->whereHas('tenantModel', fn ($tenantQuery) => $tenantQuery->where('name', 'like', '%'.$search.'%'))
                    ->orWhereHas('roomModel', fn ($roomQuery) => $roomQuery->where('unit', 'like', '%'.$search.'%'));
            });
        }

        return view('payments.index', [
            'payments' => $payments->orderByDesc('due_date')->get(),
            'stats' => Analytics::paymentStats(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }
}
