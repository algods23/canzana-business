<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Property;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_main_pages(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $property = Property::create([
            'name' => 'Test Property',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'type' => 'Apartment',
            'status' => 'active',
        ]);

        $building = Building::create([
            'property_id' => $property->id,
            'name' => 'Main Building',
            'floors' => 1,
            'status' => 'active',
        ]);

        Room::create([
            'building_id' => $building->id,
            'unit' => '101',
            'floor' => 1,
            'type' => 'Studio',
            'size_sqm' => 25,
            'rent' => 1000,
            'status' => 'occupied',
        ]);

        foreach (['dashboard', 'properties.index', 'reports.index'] as $route) {
            $this->actingAs($admin)
                ->get(route($route))
                ->assertOk();
        }

        $this->actingAs($admin)
            ->get(route('properties.show', $property))
            ->assertOk();
    }
}
