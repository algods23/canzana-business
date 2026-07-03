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
}
