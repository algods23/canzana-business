<?php

namespace Tests\Feature;

use App\Models\Building;
use App\Models\Payment;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalMonitoringTest extends TestCase
{
    use RefreshDatabase;

    public function test_rental_monitoring_recent_transactions_include_paid_sales_payments(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $property = Property::create([
            'name' => 'Canzana Tower',
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

        $room = Room::create([
            'building_id' => $building->id,
            'unit' => '101',
            'floor' => 1,
            'type' => 'Studio',
            'size_sqm' => 25,
            'rent' => 1000,
            'status' => 'occupied',
        ]);

        $tenant = Tenant::create([
            'property_id' => $property->id,
            'room_id' => $room->id,
            'name' => 'Maria Santos',
            'email' => 'maria@example.test',
            'phone' => '123456789',
            'rent' => 1000,
            'balance' => 0,
            'status' => 'active',
        ]);

        Payment::create([
            'tenant_id' => $tenant->id,
            'property_id' => $property->id,
            'room_id' => $room->id,
            'amount' => 1500,
            'due_date' => '2026-07-01',
            'paid_date' => '2026-07-05',
            'status' => 'paid',
            'method' => 'bank transfer',
            'reference' => 'BANK-001',
        ]);

        Payment::create([
            'tenant_id' => $tenant->id,
            'property_id' => $property->id,
            'room_id' => $room->id,
            'amount' => 800,
            'due_date' => '2026-06-01',
            'paid_date' => '2026-06-05',
            'status' => 'paid',
            'method' => 'cash',
            'reference' => 'CASH-001',
        ]);

        Transaction::create([
            'account_type' => 'rental',
            'module_type' => 'income',
            'amount' => 250,
            'description' => 'Parking fee',
            'transaction_date' => '2026-07-06',
            'status' => 'completed',
        ]);

        $this->actingAs($admin)
            ->get(route('monitoring.rental', [
                'date_from' => '2026-07-01',
                'date_to' => '2026-07-31',
            ]))
            ->assertOk()
            ->assertSee('Property / Unit')
            ->assertSee('Received')
            ->assertSee('Maria Santos')
            ->assertSee('Canzana Tower / 101')
            ->assertSee('Parking fee')
            ->assertSee('Bank transfer')
            ->assertDontSee('Cash:');
    }
}
