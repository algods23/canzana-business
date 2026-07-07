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
        $existingTenants = Tenant::orderBy('name')->get(['id', 'name', 'email', 'phone', 'company', 'lease_start', 'lease_end', 'rent', 'status', 'property_id', 'room_id']);

        return view('tenants.create', [
            'tenant' => new Tenant([
                'property_id' => request('property_id'),
                'room_id' => request('room_id'),
            ]),
            'properties' => Property::query()->orderBy('name')->get(),
            'rooms' => Room::query()->with('buildingModel.propertyModel')->orderBy('unit')->get(),
            'existingTenants' => $existingTenants,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'existing_tenant_id' => ['nullable', 'exists:tenants,id'],
            'property_id' => ['required', 'exists:properties,id'],
            'room_id' => ['nullable', 'exists:rooms,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'lease_start' => ['nullable', 'date'],
            'lease_end' => ['nullable', 'date', 'after_or_equal:lease_start'],
            'rent' => ['required', 'numeric', 'min:0'],
            'balance' => ['required', 'numeric', 'min:0'],
            'status' => ['required', Rule::in(['active', 'overdue', 'inactive'])],
        ]);

        // If updating an existing tenant
        if (! empty($validated['existing_tenant_id'])) {
            $tenant = Tenant::findOrFail($validated['existing_tenant_id']);

            // Validate unique email ignoring this tenant
            $request->validate([
                'email' => [Rule::unique('tenants', 'email')->ignore($tenant->id)],
            ]);

            // If a room is selected, assign it (don't vacate old rooms — multi-room support)
            if (! empty($validated['room_id'])) {
                $room = Room::with('buildingModel')->findOrFail($validated['room_id']);
                abort_unless($room->buildingModel?->property_id === (int) $validated['property_id'], 422);
                $room->update(['tenant_id' => $tenant->id, 'status' => 'occupied']);
            }

            unset($validated['existing_tenant_id'], $validated['room_id']);
            $tenant->update($validated);

            return redirect()->route('tenants.show', $tenant)->with('success', 'Tenant updated and room assigned.');
        }

        // Creating a new tenant
        $request->validate([
            'email' => ['unique:tenants,email'],
        ]);

        $roomId = $validated['room_id'] ?? null;
        unset($validated['existing_tenant_id'], $validated['room_id']);
        $tenant = Tenant::create($validated);

        if ($roomId) {
            $room = Room::with('buildingModel')->findOrFail($roomId);
            abort_unless($room->buildingModel?->property_id === (int) $validated['property_id'], 422);
            $room->update(['tenant_id' => $tenant->id, 'status' => 'occupied']);
        }

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

        // If a new room is being explicitly assigned via the edit form, assign it
        if (! empty($validated['room_id'])) {
            $room = Room::with('buildingModel')->findOrFail($validated['room_id']);
            abort_unless($room->buildingModel?->property_id === (int) $validated['property_id'], 422);
            $room->update(['tenant_id' => $tenant->id, 'status' => 'occupied']);
        }

        unset($validated['room_id']);
        $tenant->update($validated);

        return redirect()->route('tenants.show', $tenant)->with('success', 'Tenant updated.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        // Vacate all rooms this tenant is assigned to
        $tenant->rooms()->update(['tenant_id' => null, 'status' => 'vacant']);

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

    public function uploadContract(Request $request, Tenant $tenant)
    {
        $request->validate([
            'contract' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'], // Max 10MB
        ]);

        $file = $request->file('contract');
        $path = $file->store('contracts', 'public');
        $name = $file->getClientOriginalName();

        if ($tenant->contract_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($tenant->contract_path);
        }

        $tenant->update([
            'contract_path' => $path,
            'contract_name' => $name,
        ]);

        return back()->with('success', 'Contract uploaded successfully.');
    }

    public function downloadContract(Tenant $tenant)
    {
        if (! $tenant->contract_path || ! \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->contract_path)) {
            abort(404, 'Contract file not found.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download(
            $tenant->contract_path,
            $tenant->contract_name ?? 'contract.pdf'
        );
    }
}
