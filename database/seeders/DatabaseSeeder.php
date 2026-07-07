<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        \DB::table('users')->insert([
            ['name' => 'Admin User', 'email' => 'admin@canzana.com', 'password' => bcrypt('password'), 'role' => 'admin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager John', 'email' => 'john@canzana.com', 'password' => bcrypt('password'), 'role' => 'manager', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Agent Sarah', 'email' => 'sarah@canzana.com', 'password' => bcrypt('password'), 'role' => 'agent', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create properties
        \DB::table('properties')->insert([
            ['name' => 'Canzana Tower', 'address' => '123 Business District Ave', 'city' => 'Manila', 'type' => 'Commercial', 'status' => 'active', 'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Green Valley Residences', 'address' => '456 Suburban Lane', 'city' => 'Quezon City', 'type' => 'Residential', 'status' => 'active', 'image' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Metro Industrial Park', 'address' => '789 Industrial Zone', 'city' => 'Makati', 'type' => 'Industrial', 'status' => 'active', 'image' => 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sunset Heights', 'address' => '321 Hillside Drive', 'city' => 'Taguig', 'type' => 'Residential', 'status' => 'active', 'image' => 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create buildings
        \DB::table('buildings')->insert([
            ['property_id' => 1, 'name' => 'Main Tower', 'floors' => 15, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 1, 'name' => 'Annex Building', 'floors' => 8, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 2, 'name' => 'Building A', 'floors' => 5, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 2, 'name' => 'Building B', 'floors' => 6, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 2, 'name' => 'Building C', 'floors' => 4, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 3, 'name' => 'Warehouse 1', 'floors' => 2, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 3, 'name' => 'Warehouse 2', 'floors' => 3, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 4, 'name' => 'Tower 1', 'floors' => 12, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 4, 'name' => 'Tower 2', 'floors' => 10, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create rooms
        \DB::table('rooms')->insert([
            ['building_id' => 1, 'unit' => '101', 'floor' => 1, 'type' => 'Office', 'size_sqm' => 45.50, 'rent' => 25000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 1, 'unit' => '102', 'floor' => 1, 'type' => 'Office', 'size_sqm' => 52.00, 'rent' => 28000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 1, 'unit' => '103', 'floor' => 1, 'type' => 'Office', 'size_sqm' => 48.00, 'rent' => 26000.00, 'status' => 'vacant', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 1, 'unit' => '201', 'floor' => 2, 'type' => 'Office', 'size_sqm' => 60.00, 'rent' => 32000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 1, 'unit' => '202', 'floor' => 2, 'type' => 'Office', 'size_sqm' => 55.00, 'rent' => 30000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 3, 'unit' => 'A-101', 'floor' => 1, 'type' => 'Studio', 'size_sqm' => 25.00, 'rent' => 15000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 3, 'unit' => 'A-102', 'floor' => 1, 'type' => 'Studio', 'size_sqm' => 28.00, 'rent' => 16000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 3, 'unit' => 'A-201', 'floor' => 2, 'type' => '1-Bedroom', 'size_sqm' => 45.00, 'rent' => 22000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 3, 'unit' => 'A-202', 'floor' => 2, 'type' => '1-Bedroom', 'size_sqm' => 48.00, 'rent' => 23000.00, 'status' => 'vacant', 'created_at' => now(), 'updated_at' => now()],
            ['building_id' => 3, 'unit' => 'A-301', 'floor' => 3, 'type' => '2-Bedroom', 'size_sqm' => 65.00, 'rent' => 32000.00, 'status' => 'occupied', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create tenants
        \DB::table('tenants')->insert([
            ['property_id' => 1, 'room_id' => 1, 'name' => 'TechStart Solutions Inc.', 'email' => 'contact@techstart.com', 'phone' => '0917-123-4567', 'company' => 'TechStart Solutions Inc.', 'lease_start' => '2024-01-01', 'lease_end' => '2025-12-31', 'rent' => 25000.00, 'balance' => 0.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 1, 'room_id' => 2, 'name' => 'Digital Marketing Pro', 'email' => 'info@digitalpro.com', 'phone' => '0918-234-5678', 'company' => 'Digital Marketing Pro', 'lease_start' => '2024-02-01', 'lease_end' => '2025-01-31', 'rent' => 28000.00, 'balance' => 5000.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 1, 'room_id' => 4, 'name' => 'Innovate Corp', 'email' => 'hello@innovate.com', 'phone' => '0919-345-6789', 'company' => 'Innovate Corp', 'lease_start' => '2024-03-01', 'lease_end' => '2025-02-28', 'rent' => 32000.00, 'balance' => 0.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 2, 'room_id' => 6, 'name' => 'Maria Santos', 'email' => 'maria.santos@email.com', 'phone' => '0925-901-2345', 'company' => null, 'lease_start' => '2024-01-01', 'lease_end' => '2025-12-31', 'rent' => 15000.00, 'balance' => 0.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
            ['property_id' => 2, 'room_id' => 7, 'name' => 'Juan Cruz', 'email' => 'juan.cruz@email.com', 'phone' => '0926-123-4567', 'company' => null, 'lease_start' => '2024-02-01', 'lease_end' => '2025-01-31', 'rent' => 16000.00, 'balance' => 1500.00, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create payments
        \DB::table('payments')->insert([
            ['tenant_id' => 1, 'property_id' => 1, 'room_id' => 1, 'amount' => 25000.00, 'due_date' => '2024-01-01', 'paid_date' => '2024-01-05', 'status' => 'paid', 'method' => 'bank_transfer', 'reference' => 'BANK-001', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'property_id' => 1, 'room_id' => 1, 'amount' => 25000.00, 'due_date' => '2024-02-01', 'paid_date' => '2024-02-03', 'status' => 'paid', 'method' => 'bank_transfer', 'reference' => 'BANK-002', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'property_id' => 1, 'room_id' => 1, 'amount' => 25000.00, 'due_date' => '2024-03-01', 'paid_date' => '2024-03-02', 'status' => 'paid', 'method' => 'bank_transfer', 'reference' => 'BANK-003', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'property_id' => 1, 'room_id' => 1, 'amount' => 25000.00, 'due_date' => '2024-04-01', 'paid_date' => '2024-04-05', 'status' => 'paid', 'method' => 'bank_transfer', 'reference' => 'BANK-004', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 1, 'property_id' => 1, 'room_id' => 1, 'amount' => 25000.00, 'due_date' => '2024-05-01', 'paid_date' => '2024-05-03', 'status' => 'paid', 'method' => 'bank_transfer', 'reference' => 'BANK-005', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'property_id' => 1, 'room_id' => 2, 'amount' => 28000.00, 'due_date' => '2024-02-01', 'paid_date' => '2024-02-05', 'status' => 'paid', 'method' => 'gcash', 'reference' => 'GCASH-001', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'property_id' => 1, 'room_id' => 2, 'amount' => 28000.00, 'due_date' => '2024-03-01', 'paid_date' => '2024-03-03', 'status' => 'paid', 'method' => 'gcash', 'reference' => 'GCASH-002', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'property_id' => 1, 'room_id' => 2, 'amount' => 28000.00, 'due_date' => '2024-04-01', 'paid_date' => '2024-04-06', 'status' => 'paid', 'method' => 'gcash', 'reference' => 'GCASH-003', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'property_id' => 1, 'room_id' => 2, 'amount' => 28000.00, 'due_date' => '2024-05-01', 'paid_date' => '2024-05-04', 'status' => 'paid', 'method' => 'gcash', 'reference' => 'GCASH-004', 'created_at' => now(), 'updated_at' => now()],
            ['tenant_id' => 2, 'property_id' => 1, 'room_id' => 2, 'amount' => 28000.00, 'due_date' => '2024-06-01', 'paid_date' => '2024-06-05', 'status' => 'paid', 'method' => 'gcash', 'reference' => 'GCASH-005', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
