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
                'rooms as occupied_rooms' => fn ($query) => $query->where('rooms.status', 'occupied'),
            ])
            ->withSum([
                'rooms as monthly_revenue' => fn ($query) => $query->where('rooms.status', 'occupied'),
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
            'rooms as occupied_rooms' => fn ($query) => $query->where('rooms.status', 'occupied'),
        ]);

        return view('properties.show', [
            'property' => $property,
            'buildings' => $property->buildings()->withCount([
                'rooms',
                'rooms as occupied' => fn ($query) => $query->where('rooms.status', 'occupied'),
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
            'floors' => ['required', 'integer', 'min:1', 'max:200'],
            'status' => ['required', 'in:active,maintenance'],
        ]);

        $property->buildings()->create($validated);

        return redirect()->route('properties.show', $property)->with('success', 'Building added.');
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

    public function room(Property $property, Building $building, Room $room)
    {
        abort_unless($building->property_id === $property->id && $room->building_id === $building->id, 404);

        $room->load(['currentTenant.payments']);

        $tenant = $room->currentTenant;

        return view('properties.room', [
            'property' => $property,
            'building' => $building,
            'room' => $room,
            'payments' => $tenant ? $tenant->payments()->latest('due_date')->take(4)->get() : collect(),
            'activities' => Analytics::recentActivities(3),
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
            'floor' => ['required', 'integer', 'min:1', 'max:200'],
            'type' => ['required', 'string', 'max:50'],
            'size_sqm' => ['required', 'numeric', 'min:1', 'max:10000'],
            'rent' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'status' => ['required', 'in:vacant,occupied,maintenance'],
        ]);

        $building->rooms()->create($validated);

        return redirect()->route('properties.building', [$property, $building])->with('success', 'Room added.');
    }
}
