<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use App\Support\Analytics;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function create(): View
    {
        return view('properties.create', [
            'property' => new Property(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,maintenance'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        $property = Property::create($validated);

        return redirect()->route('properties.show', $property)->with('success', 'Property created.');
    }

    public function edit(Property $property): View
    {
        return view('properties.edit', compact('property'));
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:active,maintenance'],
            'image' => ['nullable', 'string', 'max:255'],
        ]);

        $property->update($validated);

        return redirect()->route('properties.show', $property)->with('success', 'Property updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Property deleted.');
    }

    public function index(Request $request)
    {
        $properties = Property::query()
            ->withCount([
                'buildings',
                'rooms',
                'rooms as occupied_rooms' => fn ($query) => $query->whereHas('currentTenant'),
            ])
            ->withSum([
                'rooms as monthly_revenue' => fn ($query) => $query->whereHas('currentTenant'),
            ], 'rent');

        if ($search = $request->get('search')) {
            $properties->where(function ($query) use ($search): void {
                $query->where('properties.name', 'like', '%'.$search.'%')
                    ->orWhere('properties.city', 'like', '%'.$search.'%');
            });
        }

        if ($type = $request->get('type')) {
            $properties->where('properties.type', $type);
        }

        if ($status = $request->get('status')) {
            $properties->where('properties.status', $status);
        }

        return view('properties.index', [
            'properties' => $properties->orderBy('properties.name')->get(),
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function show(Property $property)
    {
        $property->loadCount([
            'buildings',
            'rooms',
            'rooms as occupied_rooms' => fn ($query) => $query->whereHas('currentTenant'),
        ]);

        return view('properties.show', [
            'property' => $property,
            'buildings' => $property->buildings()->withCount([
                'rooms',
                'rooms as occupied' => fn ($query) => $query->whereHas('currentTenant'),
            ])->orderBy('name')->get(),
        ]);
    }

    public function createBuilding(Property $property): View
    {
        return view('properties.buildings.create', [
            'property' => $property,
            'building' => new Building([
                'floors' => 1,
                'status' => 'active',
            ]),
        ]);
    }

    public function storeBuilding(Request $request, Property $property): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:building,house'],
            'floors' => ['required', 'integer', 'min:1', 'max:200'],
            'status' => ['required', 'in:active,maintenance'],
            'rental_mode' => ['nullable', 'in:rooms,whole'],
            'rent' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $building = $property->buildings()->create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'floors' => $validated['floors'],
            'status' => $validated['status'],
        ]);

        if (($validated['rental_mode'] ?? null) === 'whole') {
            $building->rooms()->create([
                'unit' => 'Whole Property',
                'floor' => 1,
                'type' => $building->type === 'house' ? 'house' : 'building',
                'size_sqm' => 100, // placeholder
                'rent' => $validated['rent'] ?? 0,
                'status' => 'vacant',
            ]);
            
            return redirect()->route('properties.show', $property)->with('success', 'Property added and ready to be rented as a whole.');
        }

        return redirect()->route('properties.show', $property)->with('success', 'Building added. You can now add individual units.');
    }

    public function building(Property $property, Building $building)
    {
        abort_unless($building->property_id === $property->id, 404);

        return view('properties.building', [
            'property' => $property,
            'building' => $building,
            'rooms' => $building->rooms()->with('currentTenant')->orderBy('floor', 'desc')->orderBy('unit')->get(),
        ]);
    }

    public function editBuilding(Property $property, Building $building): View
    {
        abort_unless($building->property_id === $property->id, 404);

        return view('properties.buildings.edit', [
            'property' => $property,
            'building' => $building,
        ]);
    }

    public function updateBuilding(Request $request, Property $property, Building $building): RedirectResponse
    {
        abort_unless($building->property_id === $property->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:building,house'],
            'floors' => ['required', 'integer', 'min:1', 'max:200'],
            'status' => ['required', 'in:active,maintenance'],
        ]);

        $building->update($validated);

        return redirect()->route('properties.building', [$property, $building])->with('success', 'Building updated.');
    }

    public function destroyBuilding(Property $property, Building $building): RedirectResponse
    {
        abort_unless($building->property_id === $property->id, 404);

        $building->delete();

        return redirect()->route('properties.show', $property)->with('success', 'Building deleted.');
    }

    public function room(Property $property, Building $building, Room $room)
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $room->load(['currentTenant.payments']);

        $tenant = $room->currentTenant;

        return view('properties.room', [
            'property' => $property,
            'building' => $building,
            'room' => $room,
            'tenant' => $tenant,
            'payments' => $tenant ? $tenant->payments()->latest('due_date')->take(4)->get() : collect(),
            'roomExpenses' => $room->expenses()->latest('expense_date')->get(),
            'activities' => Analytics::recentActivities(3, $room->id),
        ]);
    }

    public function createRoom(Property $property, Building $building): View
    {
        abort_unless($building->property_id === $property->id, 404);

        return view('properties.rooms.create', [
            'property' => $property,
            'building' => $building,
            'room' => new Room([
                'floor' => 1,
                'status' => 'vacant',
            ]),
        ]);
    }

    public function storeRoom(Request $request, Property $property, Building $building): RedirectResponse
    {
        abort_unless($building->property_id === $property->id, 404);

        $validated = $request->validate([
            'unit' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'integer', 'min:1', 'max:'.$building->floors],
            'type' => ['required', 'string', 'max:50'],
            'size_sqm' => ['required', 'numeric', 'min:1', 'max:10000'],
            'rent' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:vacant,occupied,maintenance'],
        ]);

        $building->rooms()->create($validated);

        return redirect()->route('properties.building', [$property, $building])->with('success', 'Room added.');
    }

    public function editRoom(Property $property, Building $building, Room $room): View
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        return view('properties.rooms.edit', [
            'property' => $property,
            'building' => $building,
            'room' => $room,
        ]);
    }

    public function updateRoom(Request $request, Property $property, Building $building, Room $room): RedirectResponse
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $validated = $request->validate([
            'unit' => ['required', 'string', 'max:50'],
            'floor' => ['required', 'integer', 'min:1', 'max:'.$building->floors],
            'type' => ['required', 'string', 'max:50'],
            'size_sqm' => ['required', 'numeric', 'min:1', 'max:10000'],
            'rent' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:vacant,occupied,maintenance'],
        ]);

        $room->update($validated);

        return redirect()->route('properties.room', [$property, $building, $room])->with('success', 'Room updated.');
    }

    public function destroyRoom(Property $property, Building $building, Room $room): RedirectResponse
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $room->delete();

        return redirect()->route('properties.building', [$property, $building])->with('success', 'Room deleted.');
    }
    public function assignTenant(Request $request, Property $property, Building $building, Room $room): RedirectResponse
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $validated = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
        ]);

        $tenant = \App\Models\Tenant::findOrFail($validated['tenant_id']);

        // Assign this room to the tenant (multi-room: don't vacate other rooms)
        $room->update(['tenant_id' => $tenant->id, 'status' => 'occupied']);

        return redirect()->route('properties.room', [$property, $building, $room])->with('success', 'Tenant assigned to unit.');
    }
    public function vacateTenant(Request $request, Property $property, Building $building, Room $room): RedirectResponse
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $room->update([
            'tenant_id' => null,
            'status' => 'vacant',
            'lease_start' => null,
            'lease_end' => null,
        ]);

        return redirect()->route('properties.room', [$property, $building, $room])->with('success', 'Tenant removed from unit.');
    }

    public function toggleMaintenance(Request $request, Property $property, Building $building, Room $room): RedirectResponse
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        if ($room->status === 'maintenance') {
            // Restore to occupied or vacant based on tenant assignment
            $room->update([
                'status' => $room->tenant_id ? 'occupied' : 'vacant',
            ]);
            $message = 'Room restored from maintenance.';
        } else {
            $room->update([
                'status' => 'maintenance',
            ]);
            $message = 'Room set to under maintenance.';
        }

        return redirect()->route('properties.room', [$property, $building, $room])->with('success', $message);
    }
}
