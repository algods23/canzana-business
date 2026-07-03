<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Payment;
use App\Models\Tenant;
use App\Support\Analytics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function create(): View
    {
        return view('payments.create', [
            'payment' => new Payment(),
            'tenants' => Tenant::query()->with(['propertyModel', 'roomModel'])->orderBy('name')->get(),
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
        ]);

        $tenant = Tenant::with(['propertyModel', 'roomModel'])->findOrFail($validated['tenant_id']);

        $validated['property_id'] = $tenant->property_id;
        $validated['room_id'] = $tenant->room_id;
        $validated['paid_date'] = $validated['status'] === 'paid' && empty($validated['paid_date']) ? now()->toDateString() : ($validated['paid_date'] ?? null);

        Payment::create($validated);

        return redirect()->route('payments.index')->with('success', 'Payment recorded.');
    }

    public function edit(Payment $payment): View
    {
        return view('payments.edit', [
            'payment' => $payment,
            'tenants' => Tenant::query()->with(['propertyModel', 'roomModel'])->orderBy('name')->get(),
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
        ]);

        $tenant = Tenant::with(['propertyModel', 'roomModel'])->findOrFail($validated['tenant_id']);

        $validated['property_id'] = $tenant->property_id;
        $validated['room_id'] = $tenant->room_id;
        $validated['paid_date'] = $validated['status'] === 'paid' && empty($validated['paid_date']) ? now()->toDateString() : ($validated['paid_date'] ?? null);

        $payment->update($validated);

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $payment->delete();

        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
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
