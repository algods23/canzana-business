<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Business;
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

        $business = Business::create([
            'user_id' => $admin->id,
            'name' => 'Rental',
            'slug' => 'rental',
            'type' => 'rental',
            'description' => 'Properties, tenants, leases, and payments',
            'status' => 'active',
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
                ->withSession(['selected_business_id' => $business->id])
                ->get(route($route))
                ->assertOk();
        }

        $this->actingAs($admin)
            ->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee(route('properties.buildings.create', $property), false);

        $this->actingAs($admin)
            ->get(route('properties.buildings.create', $property))
            ->assertOk();

        $this->actingAs($admin)
            ->post(route('properties.buildings.store', $property), [
                'name' => 'Annex Building',
                'floors' => 3,
                'status' => 'active',
            ])
            ->assertRedirect(route('properties.show', $property));

        $this->assertDatabaseHas('buildings', [
            'property_id' => $property->id,
            'name' => 'Annex Building',
            'floors' => 3,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_select_a_business(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('businesses.select'))
            ->assertOk()
            ->assertSee('Rental')
            ->assertSee('Fishpond')
            ->assertSee('Fruits')
            ->assertSee('Add Business');

        $business = Business::where('user_id', $admin->id)->where('slug', 'rental')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('businesses.open', $business))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('selected_business_id', $business->id);
    }
}
