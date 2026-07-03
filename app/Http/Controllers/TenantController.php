<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Support\Analytics;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function create(): View
    {
        return view('tenants.create', [
            'tenant' => new Tenant(),
            'properties' => Property::query()->orderBy('name')->get(),
            'rooms' => Room::query()->with('buildingModel.propertyModel')->orderBy('unit')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:tenants,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'lease_start' => ['nullable', 'date'],
            'lease_end' => ['nullable', 'date', 'after_or_equal:lease_start'],
            'rent' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'overdue', 'inactive'])],
        ]);

        if (! empty($validated['room_id'])) {
            $room = Room::with('buildingModel')->findOrFail($validated['room_id']);
            abort_unless($room->buildingModel?->property_id === (int) $validated['property_id'], 422);
            $room->update(['status' => 'occupied']);
        }

        $tenant = Tenant::create($validated);

        return redirect()->route('tenants.show', $tenant)->with('success', 'Tenant created.');
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', [
            'tenant' => $tenant,
            'properties' => Property::query()->orderBy('name')->get(),
            'rooms' => Room::query()->with('buildingModel.propertyModel')->orderBy('unit')->get(),
        ]);
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'property_id' => ['required', 'exists:properties,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('tenants', 'email')->ignore($tenant->id)],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'lease_start' => ['nullable', 'date'],
            'lease_end' => ['nullable', 'date', 'after_or_equal:lease_start'],
            'rent' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'overdue', 'inactive'])],
        ]);

        if ($tenant->room_id && $tenant->room_id !== (int) ($validated['room_id'] ?? 0)) {
            Room::whereKey($tenant->room_id)->update(['status' => 'vacant']);
        }

        if (! empty($validated['room_id'])) {
            $room = Room::with('buildingModel')->findOrFail($validated['room_id']);
            abort_unless($room->buildingModel?->property_id === (int) $validated['property_id'], 422);
            $room->update(['status' => 'occupied']);
        }

        $tenant->update($validated);

        return redirect()->route('tenants.show', $tenant)->with('success', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        if ($tenant->room_id) {
            Room::whereKey($tenant->room_id)->update(['status' => 'vacant']);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'Tenant deleted.');
    }

    public function index(Request $request)
    {
        $tenants = Tenant::query()->with(['propertyModel', 'roomModel']);

        if ($search = $request->get('search')) {
            $tenants->where(function ($query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('roomModel', fn ($roomQuery) => $roomQuery->where('unit', 'like', '%'.$search.'%'));
            });
        }

        if ($status = $request->get('status')) {
            $tenants->where('status', $status);
        }

        return view('tenants.index', [
            'tenants' => $tenants->orderBy('name')->get(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(Tenant $tenant)
    {
        $tenant->load(['propertyModel', 'roomModel', 'payments' => fn ($query) => $query->latest('due_date')->take(4)]);

        return view('tenants.show', [
            'tenant' => $tenant,
            'payments' => $tenant->payments,
            'activities' => Analytics::recentActivities(4),
        ]);
    }
}
