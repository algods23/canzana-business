<?php

namespace App\Http\Controllers;

use App\Data\MockData;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = MockData::properties();

        if ($search = $request->get('search')) {
            $properties = array_filter($properties, fn ($p) =>
                str_contains(strtolower($p['name']), strtolower($search)) ||
                str_contains(strtolower($p['city']), strtolower($search))
            );
        }

        if ($type = $request->get('type')) {
            $properties = array_filter($properties, fn ($p) => strtolower($p['type']) === strtolower($type));
        }

        return view('properties.index', [
            'properties' => array_values($properties),
            'filters' => $request->only(['search', 'type', 'status']),
        ]);
    }

    public function show(int $id)
    {
        $property = MockData::findProperty($id);

        abort_unless($property, 404);

        return view('properties.show', [
            'property' => $property,
            'buildings' => MockData::buildings($id),
        ]);
    }

    public function building(int $propertyId, int $buildingId)
    {
        $property = MockData::findProperty($propertyId);
        $building = MockData::findBuilding($propertyId, $buildingId);

        abort_unless($property && $building, 404);

        return view('properties.building', [
            'property' => $property,
            'building' => $building,
            'rooms' => MockData::rooms($buildingId),
        ]);
    }

    public function room(int $propertyId, int $buildingId, int $roomId)
    {
        $property = MockData::findProperty($propertyId);
        $building = MockData::findBuilding($propertyId, $buildingId);
        $rooms = MockData::rooms($buildingId);
        $room = collect($rooms)->firstWhere('id', $roomId);

        abort_unless($property && $building && $room, 404);

        return view('properties.room', [
            'property' => $property,
            'building' => $building,
            'room' => $room,
            'payments' => array_slice(MockData::payments(), 0, 4),
            'activities' => array_slice(MockData::activities(), 0, 3),
        ]);
    }
}
