<?php

namespace App\Data;

class MockData
{
    public static function properties(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Canzana Tower Complex',
                'address' => '123 Ayala Avenue',
                'city' => 'Makati City',
                'type' => 'Mixed Use',
                'buildings_count' => 3,
                'rooms_count' => 48,
                'occupied_rooms' => 42,
                'occupancy_rate' => 87.5,
                'monthly_revenue' => 485000,
                'status' => 'active',
                'image' => null,
            ],
            [
                'id' => 2,
                'name' => 'Greenfield Residences',
                'address' => '45 Commonwealth Ave',
                'city' => 'Quezon City',
                'type' => 'Residential',
                'buildings_count' => 2,
                'rooms_count' => 32,
                'occupied_rooms' => 28,
                'occupancy_rate' => 87.5,
                'monthly_revenue' => 256000,
                'status' => 'active',
                'image' => null,
            ],
            [
                'id' => 3,
                'name' => 'Harbor Business Park',
                'address' => '88 Roxas Boulevard',
                'city' => 'Pasay City',
                'type' => 'Commercial',
                'buildings_count' => 4,
                'rooms_count' => 64,
                'occupied_rooms' => 51,
                'occupancy_rate' => 79.7,
                'monthly_revenue' => 720000,
                'status' => 'active',
                'image' => null,
            ],
            [
                'id' => 4,
                'name' => 'Sunrise Dormitory',
                'address' => '12 University Road',
                'city' => 'Manila',
                'type' => 'Residential',
                'buildings_count' => 1,
                'rooms_count' => 24,
                'occupied_rooms' => 22,
                'occupancy_rate' => 91.7,
                'monthly_revenue' => 96000,
                'status' => 'maintenance',
                'image' => null,
            ],
        ];
    }

    public static function buildings(int $propertyId): array
    {
        $buildings = [
            1 => [
                ['id' => 101, 'property_id' => 1, 'name' => 'Tower A', 'floors' => 12, 'rooms_count' => 20, 'occupied' => 18, 'status' => 'active'],
                ['id' => 102, 'property_id' => 1, 'name' => 'Tower B', 'floors' => 10, 'rooms_count' => 16, 'occupied' => 14, 'status' => 'active'],
                ['id' => 103, 'property_id' => 1, 'name' => 'Retail Podium', 'floors' => 3, 'rooms_count' => 12, 'occupied' => 10, 'status' => 'active'],
            ],
            2 => [
                ['id' => 201, 'property_id' => 2, 'name' => 'Block 1', 'floors' => 5, 'rooms_count' => 16, 'occupied' => 14, 'status' => 'active'],
                ['id' => 202, 'property_id' => 2, 'name' => 'Block 2', 'floors' => 5, 'rooms_count' => 16, 'occupied' => 14, 'status' => 'active'],
            ],
            3 => [
                ['id' => 301, 'property_id' => 3, 'name' => 'Office Wing North', 'floors' => 8, 'rooms_count' => 20, 'occupied' => 16, 'status' => 'active'],
                ['id' => 302, 'property_id' => 3, 'name' => 'Office Wing South', 'floors' => 8, 'rooms_count' => 20, 'occupied' => 17, 'status' => 'active'],
                ['id' => 303, 'property_id' => 3, 'name' => 'Warehouse A', 'floors' => 2, 'rooms_count' => 12, 'occupied' => 9, 'status' => 'active'],
                ['id' => 304, 'property_id' => 3, 'name' => 'Warehouse B', 'floors' => 2, 'rooms_count' => 12, 'occupied' => 9, 'status' => 'active'],
            ],
            4 => [
                ['id' => 401, 'property_id' => 4, 'name' => 'Main Building', 'floors' => 4, 'rooms_count' => 24, 'occupied' => 22, 'status' => 'maintenance'],
            ],
        ];

        return $buildings[$propertyId] ?? [];
    }

    public static function rooms(int $buildingId): array
    {
        $rooms = [
            101 => [
                ['id' => 1001, 'building_id' => 101, 'property_id' => 1, 'unit' => 'A-1201', 'floor' => 12, 'type' => 'Studio', 'size_sqm' => 28, 'rent' => 12500, 'status' => 'occupied', 'tenant' => 'Maria Santos'],
                ['id' => 1002, 'building_id' => 101, 'property_id' => 1, 'unit' => 'A-1102', 'floor' => 11, 'type' => '1BR', 'size_sqm' => 42, 'rent' => 18000, 'status' => 'occupied', 'tenant' => 'Juan Dela Cruz'],
                ['id' => 1003, 'building_id' => 101, 'property_id' => 1, 'unit' => 'A-1003', 'floor' => 10, 'type' => '2BR', 'size_sqm' => 58, 'rent' => 24000, 'status' => 'vacant', 'tenant' => null],
                ['id' => 1004, 'building_id' => 101, 'property_id' => 1, 'unit' => 'A-903', 'floor' => 9, 'type' => '1BR', 'size_sqm' => 40, 'rent' => 17500, 'status' => 'maintenance', 'tenant' => null],
                ['id' => 1005, 'building_id' => 101, 'property_id' => 1, 'unit' => 'A-804', 'floor' => 8, 'type' => 'Studio', 'size_sqm' => 26, 'rent' => 11500, 'status' => 'occupied', 'tenant' => 'Ana Reyes'],
            ],
        ];

        return $rooms[$buildingId] ?? [
            ['id' => 9001, 'building_id' => $buildingId, 'property_id' => 1, 'unit' => 'U-101', 'floor' => 1, 'type' => 'Office', 'size_sqm' => 65, 'rent' => 35000, 'status' => 'occupied', 'tenant' => 'Tech Solutions Inc.'],
            ['id' => 9002, 'building_id' => $buildingId, 'property_id' => 1, 'unit' => 'U-102', 'floor' => 1, 'type' => 'Office', 'size_sqm' => 45, 'rent' => 28000, 'status' => 'vacant', 'tenant' => null],
        ];
    }

    public static function tenants(): array
    {
        return [
            ['id' => 1, 'name' => 'Maria Santos', 'email' => 'maria.santos@email.com', 'phone' => '+63 917 123 4567', 'company' => null, 'property' => 'Canzana Tower Complex', 'unit' => 'A-1201', 'lease_start' => '2024-06-01', 'lease_end' => '2025-05-31', 'rent' => 12500, 'status' => 'active', 'balance' => 0],
            ['id' => 2, 'name' => 'Juan Dela Cruz', 'email' => 'juan.dc@email.com', 'phone' => '+63 918 234 5678', 'company' => null, 'property' => 'Canzana Tower Complex', 'unit' => 'A-1102', 'lease_start' => '2024-03-15', 'lease_end' => '2025-03-14', 'rent' => 18000, 'status' => 'active', 'balance' => 0],
            ['id' => 3, 'name' => 'Tech Solutions Inc.', 'email' => 'billing@techsolutions.ph', 'phone' => '+63 2 8123 4567', 'company' => 'Tech Solutions Inc.', 'property' => 'Harbor Business Park', 'unit' => 'N-801', 'lease_start' => '2023-01-01', 'lease_end' => '2025-12-31', 'rent' => 85000, 'status' => 'active', 'balance' => 0],
            ['id' => 4, 'name' => 'Ana Reyes', 'email' => 'ana.reyes@email.com', 'phone' => '+63 919 345 6789', 'company' => null, 'property' => 'Canzana Tower Complex', 'unit' => 'A-804', 'lease_start' => '2024-09-01', 'lease_end' => '2025-08-31', 'rent' => 11500, 'status' => 'active', 'balance' => 11500],
            ['id' => 5, 'name' => 'Carlos Mendoza', 'email' => 'carlos.m@email.com', 'phone' => '+63 920 456 7890', 'company' => 'Mendoza Trading', 'property' => 'Greenfield Residences', 'unit' => 'B1-305', 'lease_start' => '2024-01-01', 'lease_end' => '2024-12-31', 'status' => 'overdue', 'rent' => 22000, 'balance' => 44000],
            ['id' => 6, 'name' => 'Lisa Wong', 'email' => 'lisa.wong@email.com', 'phone' => '+63 921 567 8901', 'company' => null, 'property' => 'Sunrise Dormitory', 'unit' => 'M-204', 'lease_start' => '2024-08-15', 'lease_end' => '2025-06-14', 'rent' => 4500, 'status' => 'active', 'balance' => 0],
        ];
    }

    public static function payments(): array
    {
        return [
            ['id' => 1, 'tenant' => 'Maria Santos', 'unit' => 'A-1201', 'property' => 'Canzana Tower Complex', 'amount' => 12500, 'due_date' => '2025-07-05', 'paid_date' => '2025-07-03', 'status' => 'paid', 'method' => 'Bank Transfer'],
            ['id' => 2, 'tenant' => 'Juan Dela Cruz', 'unit' => 'A-1102', 'property' => 'Canzana Tower Complex', 'amount' => 18000, 'due_date' => '2025-07-05', 'paid_date' => null, 'status' => 'pending', 'method' => null],
            ['id' => 3, 'tenant' => 'Ana Reyes', 'unit' => 'A-804', 'property' => 'Canzana Tower Complex', 'amount' => 11500, 'due_date' => '2025-06-05', 'paid_date' => null, 'status' => 'overdue', 'method' => null],
            ['id' => 4, 'tenant' => 'Carlos Mendoza', 'unit' => 'B1-305', 'property' => 'Greenfield Residences', 'amount' => 22000, 'due_date' => '2025-05-05', 'paid_date' => null, 'status' => 'overdue', 'method' => null],
            ['id' => 5, 'tenant' => 'Tech Solutions Inc.', 'unit' => 'N-801', 'property' => 'Harbor Business Park', 'amount' => 85000, 'due_date' => '2025-07-01', 'paid_date' => '2025-07-01', 'status' => 'paid', 'method' => 'Check'],
            ['id' => 6, 'tenant' => 'Lisa Wong', 'unit' => 'M-204', 'property' => 'Sunrise Dormitory', 'amount' => 4500, 'due_date' => '2025-07-05', 'paid_date' => '2025-07-02', 'status' => 'paid', 'method' => 'GCash'],
            ['id' => 7, 'tenant' => 'Carlos Mendoza', 'unit' => 'B1-305', 'property' => 'Greenfield Residences', 'amount' => 22000, 'due_date' => '2025-06-05', 'paid_date' => null, 'status' => 'overdue', 'method' => null],
        ];
    }

    public static function activities(): array
    {
        return [
            ['id' => 1, 'type' => 'payment', 'icon' => 'payment', 'title' => 'Payment received', 'description' => 'Maria Santos paid ₱12,500 for Unit A-1201', 'user' => 'System', 'time' => '2 hours ago', 'entity' => 'Payment #1'],
            ['id' => 2, 'type' => 'tenant', 'icon' => 'user', 'title' => 'Lease renewed', 'description' => 'Tech Solutions Inc. lease extended to Dec 2025', 'user' => 'Admin User', 'time' => '5 hours ago', 'entity' => 'Tenant #3'],
            ['id' => 3, 'type' => 'room', 'icon' => 'room', 'title' => 'Unit marked vacant', 'description' => 'Unit A-1003 is now available for rent', 'user' => 'Property Manager', 'time' => '1 day ago', 'entity' => 'Room #1003'],
            ['id' => 4, 'type' => 'alert', 'icon' => 'alert', 'title' => 'Overdue payment flagged', 'description' => 'Carlos Mendoza — 2 months overdue (₱44,000)', 'user' => 'System', 'time' => '1 day ago', 'entity' => 'Tenant #5'],
            ['id' => 5, 'type' => 'maintenance', 'icon' => 'maintenance', 'title' => 'Maintenance scheduled', 'description' => 'Unit A-903 — plumbing repair on Jul 5', 'user' => 'Admin User', 'time' => '2 days ago', 'entity' => 'Room #1004'],
            ['id' => 6, 'type' => 'property', 'icon' => 'property', 'title' => 'Property updated', 'description' => 'Sunrise Dormitory status changed to Maintenance', 'user' => 'Admin User', 'time' => '3 days ago', 'entity' => 'Property #4'],
        ];
    }

    public static function dashboardStats(): array
    {
        return [
            'total_properties' => 4,
            'total_rooms' => 168,
            'occupied_rooms' => 143,
            'occupancy_rate' => 85.1,
            'monthly_revenue' => 1557000,
            'collected_this_month' => 1289500,
            'pending_payments' => 18000,
            'overdue_amount' => 77500,
            'overdue_count' => 3,
            'active_tenants' => 143,
        ];
    }

    public static function revenueChart(): array
    {
        return [
            ['month' => 'Feb', 'collected' => 1180000, 'expected' => 1420000],
            ['month' => 'Mar', 'collected' => 1350000, 'expected' => 1480000],
            ['month' => 'Apr', 'collected' => 1290000, 'expected' => 1500000],
            ['month' => 'May', 'collected' => 1410000, 'expected' => 1520000],
            ['month' => 'Jun', 'collected' => 1380000, 'expected' => 1540000],
            ['month' => 'Jul', 'collected' => 1289500, 'expected' => 1557000],
        ];
    }

    public static function occupancyByProperty(): array
    {
        return [
            ['name' => 'Canzana Tower', 'rate' => 87.5],
            ['name' => 'Greenfield', 'rate' => 87.5],
            ['name' => 'Harbor Park', 'rate' => 79.7],
            ['name' => 'Sunrise Dorm', 'rate' => 91.7],
        ];
    }

    public static function findProperty(int $id): ?array
    {
        foreach (self::properties() as $property) {
            if ($property['id'] === $id) {
                return $property;
            }
        }

        return null;
    }

    public static function findTenant(int $id): ?array
    {
        foreach (self::tenants() as $tenant) {
            if ($tenant['id'] === $id) {
                return $tenant;
            }
        }

        return null;
    }

    public static function findBuilding(int $propertyId, int $buildingId): ?array
    {
        foreach (self::buildings($propertyId) as $building) {
            if ($building['id'] === $buildingId) {
                return $building;
            }
        }

        return null;
    }

    public static function findRoom(int $roomId): ?array
    {
        foreach (self::buildings(1) as $building) {
            foreach (self::rooms($building['id']) as $room) {
                if ($room['id'] === $roomId) {
                    return $room;
                }
            }
        }

        return self::rooms(999)[0] ?? null;
    }
}
