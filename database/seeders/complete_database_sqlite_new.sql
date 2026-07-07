-- Complete Database Export for Canzana Business Property Management System
-- SQLite-compatible version
-- Execute this file on your SQLite database

-- ============================================
-- DROP TABLES (in reverse order of dependencies)
-- ============================================
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS tenants;
DROP TABLE IF EXISTS rooms;
DROP TABLE IF EXISTS buildings;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS users;

-- ============================================
-- CREATE TABLES
-- ============================================

-- Users Table
CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    email_verified_at TEXT,
    password TEXT NOT NULL,
    remember_token TEXT,
    created_at TEXT,
    updated_at TEXT
);

-- Password Reset Tokens Table
CREATE TABLE password_reset_tokens (
    email TEXT PRIMARY KEY,
    token TEXT NOT NULL,
    created_at TEXT
);

-- Sessions Table
CREATE TABLE sessions (
    id TEXT PRIMARY KEY,
    user_id INTEGER,
    ip_address TEXT,
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER
);

CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

-- Properties Table
CREATE TABLE properties (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    address TEXT NOT NULL,
    city TEXT NOT NULL,
    type TEXT NOT NULL,
    status TEXT NOT NULL DEFAULT 'active',
    image TEXT,
    created_at TEXT,
    updated_at TEXT
);

-- Buildings Table
CREATE TABLE buildings (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER NOT NULL,
    name TEXT NOT NULL,
    floors INTEGER NOT NULL DEFAULT 1,
    status TEXT NOT NULL DEFAULT 'active',
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
);

-- Rooms Table
CREATE TABLE rooms (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    building_id INTEGER NOT NULL,
    unit TEXT NOT NULL,
    floor INTEGER NOT NULL,
    type TEXT NOT NULL,
    size_sqm REAL NOT NULL,
    rent REAL NOT NULL,
    status TEXT NOT NULL DEFAULT 'vacant',
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY (building_id) REFERENCES buildings(id) ON DELETE CASCADE
);

-- Tenants Table
CREATE TABLE tenants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    property_id INTEGER NOT NULL,
    room_id INTEGER,
    name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE,
    phone TEXT,
    company TEXT,
    lease_start TEXT,
    lease_end TEXT,
    rent REAL NOT NULL DEFAULT 0,
    balance REAL NOT NULL DEFAULT 0,
    status TEXT NOT NULL DEFAULT 'active',
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- Payments Table
CREATE TABLE payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    property_id INTEGER NOT NULL,
    room_id INTEGER,
    amount REAL NOT NULL,
    due_date TEXT NOT NULL,
    paid_date TEXT,
    status TEXT NOT NULL DEFAULT 'pending',
    method TEXT,
    reference TEXT,
    created_at TEXT,
    updated_at TEXT,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL
);

-- ============================================
-- INSERT DATA
-- ============================================

-- Users
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Admin User', 'admin@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', datetime('now'), datetime('now')),
('Manager John', 'john@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', datetime('now'), datetime('now')),
('Agent Sarah', 'sarah@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', datetime('now'), datetime('now'));

-- Properties
INSERT INTO properties (name, address, city, type, status, image, created_at, updated_at) VALUES
('Canzana Tower', '123 Business District Ave', 'Manila', 'Commercial', 'active', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', datetime('now'), datetime('now')),
('Green Valley Residences', '456 Suburban Lane', 'Quezon City', 'Residential', 'active', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800', datetime('now'), datetime('now')),
('Metro Industrial Park', '789 Industrial Zone', 'Makati', 'Industrial', 'active', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', datetime('now'), datetime('now')),
('Sunset Heights', '321 Hillside Drive', 'Taguig', 'Residential', 'active', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800', datetime('now'), datetime('now'));

-- Buildings
INSERT INTO buildings (property_id, name, floors, status, created_at, updated_at) VALUES
(1, 'Main Tower', 15, 'active', datetime('now'), datetime('now')),
(1, 'Annex Building', 8, 'active', datetime('now'), datetime('now')),
(2, 'Building A', 5, 'active', datetime('now'), datetime('now')),
(2, 'Building B', 6, 'active', datetime('now'), datetime('now')),
(2, 'Building C', 4, 'active', datetime('now'), datetime('now')),
(3, 'Warehouse 1', 2, 'active', datetime('now'), datetime('now')),
(3, 'Warehouse 2', 3, 'active', datetime('now'), datetime('now')),
(4, 'Tower 1', 12, 'active', datetime('now'), datetime('now')),
(4, 'Tower 2', 10, 'active', datetime('now'), datetime('now'));

-- Rooms
INSERT INTO rooms (building_id, unit, floor, type, size_sqm, rent, status, created_at, updated_at) VALUES
(1, '101', 1, 'Office', 45.50, 25000.00, 'occupied', datetime('now'), datetime('now')),
(1, '102', 1, 'Office', 52.00, 28000.00, 'occupied', datetime('now'), datetime('now')),
(1, '103', 1, 'Office', 48.00, 26000.00, 'vacant', datetime('now'), datetime('now')),
(1, '201', 2, 'Office', 60.00, 32000.00, 'occupied', datetime('now'), datetime('now')),
(1, '202', 2, 'Office', 55.00, 30000.00, 'occupied', datetime('now'), datetime('now')),
(1, '203', 2, 'Office', 65.00, 35000.00, 'vacant', datetime('now'), datetime('now')),
(1, '301', 3, 'Office', 70.00, 38000.00, 'occupied', datetime('now'), datetime('now')),
(1, '302', 3, 'Office', 75.00, 40000.00, 'maintenance', datetime('now'), datetime('now')),
(2, 'A101', 1, 'Retail', 35.00, 20000.00, 'occupied', datetime('now'), datetime('now')),
(2, 'A102', 1, 'Retail', 40.00, 22000.00, 'occupied', datetime('now'), datetime('now')),
(2, 'A201', 2, 'Office', 50.00, 27000.00, 'vacant', datetime('now'), datetime('now')),
(2, 'A202', 2, 'Office', 55.00, 29000.00, 'occupied', datetime('now'), datetime('now')),
(3, 'A-101', 1, 'Studio', 25.00, 15000.00, 'occupied', datetime('now'), datetime('now')),
(3, 'A-102', 1, 'Studio', 28.00, 16000.00, 'occupied', datetime('now'), datetime('now')),
(3, 'A-201', 2, '1-Bedroom', 45.00, 22000.00, 'occupied', datetime('now'), datetime('now')),
(3, 'A-202', 2, '1-Bedroom', 48.00, 23000.00, 'vacant', datetime('now'), datetime('now')),
(3, 'A-301', 3, '2-Bedroom', 65.00, 32000.00, 'occupied', datetime('now'), datetime('now')),
(3, 'A-302', 3, '2-Bedroom', 70.00, 34000.00, 'occupied', datetime('now'), datetime('now')),
(4, 'B-101', 1, 'Studio', 26.00, 15500.00, 'occupied', datetime('now'), datetime('now')),
(4, 'B-102', 1, 'Studio', 27.00, 15800.00, 'vacant', datetime('now'), datetime('now')),
(4, 'B-201', 2, '1-Bedroom', 46.00, 22500.00, 'occupied', datetime('now'), datetime('now')),
(4, 'B-202', 2, '1-Bedroom', 50.00, 24000.00, 'occupied', datetime('now'), datetime('now')),
(4, 'B-301', 3, '2-Bedroom', 68.00, 33000.00, 'occupied', datetime('now'), datetime('now')),
(4, 'B-302', 3, '2-Bedroom', 72.00, 35000.00, 'vacant', datetime('now'), datetime('now')),
(4, 'B-401', 4, 'Penthouse', 120.00, 65000.00, 'occupied', datetime('now'), datetime('now')),
(5, 'C-101', 1, 'Studio', 24.00, 14500.00, 'occupied', datetime('now'), datetime('now')),
(5, 'C-102', 1, 'Studio', 25.00, 14800.00, 'occupied', datetime('now'), datetime('now')),
(5, 'C-201', 2, '1-Bedroom', 44.00, 21500.00, 'vacant', datetime('now'), datetime('now')),
(5, 'C-202', 2, '1-Bedroom', 47.00, 22800.00, 'occupied', datetime('now'), datetime('now')),
(6, 'W1-A', 1, 'Warehouse', 200.00, 80000.00, 'occupied', datetime('now'), datetime('now')),
(6, 'W1-B', 1, 'Warehouse', 250.00, 100000.00, 'occupied', datetime('now'), datetime('now')),
(6, 'W1-C', 2, 'Warehouse', 180.00, 75000.00, 'vacant', datetime('now'), datetime('now')),
(7, 'W2-A', 1, 'Warehouse', 220.00, 85000.00, 'occupied', datetime('now'), datetime('now')),
(7, 'W2-B', 2, 'Warehouse', 280.00, 110000.00, 'occupied', datetime('now'), datetime('now')),
(7, 'W2-C', 3, 'Warehouse', 300.00, 120000.00, 'vacant', datetime('now'), datetime('now')),
(8, 'T1-501', 5, '1-Bedroom', 42.00, 28000.00, 'occupied', datetime('now'), datetime('now')),
(8, 'T1-502', 5, '1-Bedroom', 45.00, 29500.00, 'occupied', datetime('now'), datetime('now')),
(8, 'T1-601', 6, '2-Bedroom', 62.00, 38000.00, 'occupied', datetime('now'), datetime('now')),
(8, 'T1-602', 6, '2-Bedroom', 65.00, 39500.00, 'vacant', datetime('now'), datetime('now')),
(8, 'T1-701', 7, '2-Bedroom', 68.00, 41000.00, 'occupied', datetime('now'), datetime('now')),
(8, 'T1-801', 8, '3-Bedroom', 85.00, 52000.00, 'occupied', datetime('now'), datetime('now')),
(9, 'T2-401', 4, '1-Bedroom', 40.00, 27000.00, 'occupied', datetime('now'), datetime('now')),
(9, 'T2-402', 4, '1-Bedroom', 43.00, 28500.00, 'vacant', datetime('now'), datetime('now')),
(9, 'T2-501', 5, '2-Bedroom', 60.00, 37000.00, 'occupied', datetime('now'), datetime('now')),
(9, 'T2-502', 5, '2-Bedroom', 64.00, 39000.00, 'occupied', datetime('now'), datetime('now')),
(9, 'T2-601', 6, '3-Bedroom', 82.00, 50000.00, 'occupied', datetime('now'), datetime('now'));

-- Tenants
INSERT INTO tenants (property_id, room_id, name, email, phone, company, lease_start, lease_end, rent, balance, status, created_at, updated_at) VALUES
(1, 1, 'TechStart Solutions Inc.', 'contact@techstart.com', '0917-123-4567', 'TechStart Solutions Inc.', '2024-01-01', '2025-12-31', 25000.00, 0.00, 'active', datetime('now'), datetime('now')),
(1, 2, 'Digital Marketing Pro', 'info@digitalpro.com', '0918-234-5678', 'Digital Marketing Pro', '2024-02-01', '2025-01-31', 28000.00, 5000.00, 'active', datetime('now'), datetime('now')),
(1, 4, 'Innovate Corp', 'hello@innovate.com', '0919-345-6789', 'Innovate Corp', '2024-03-01', '2025-02-28', 32000.00, 0.00, 'active', datetime('now'), datetime('now')),
(1, 5, 'Global Ventures Ltd', 'admin@globalventures.com', '0920-456-7890', 'Global Ventures Ltd', '2024-04-01', '2025-03-31', 30000.00, 10000.00, 'active', datetime('now'), datetime('now')),
(1, 7, 'NextGen Technologies', 'support@nextgen.com', '0921-567-8901', 'NextGen Technologies', '2024-05-01', '2025-04-30', 38000.00, 0.00, 'active', datetime('now'), datetime('now')),
(1, 10, 'Retail Store ABC', 'store@retailabc.com', '0922-678-9012', 'Retail Store ABC', '2024-01-15', '2025-01-14', 20000.00, 0.00, 'active', datetime('now'), datetime('now')),
(1, 11, 'Fashion Boutique', 'fashion@boutique.com', '0923-789-0123', 'Fashion Boutique', '2024-02-15', '2025-02-14', 22000.00, 2000.00, 'active', datetime('now'), datetime('now')),
(1, 13, 'Consulting Group', 'info@consulting.com', '0924-890-1234', 'Consulting Group', '2024-03-15', '2025-03-14', 29000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 14, 'Maria Santos', 'maria.santos@email.com', '0925-901-2345', NULL, '2024-01-01', '2025-12-31', 15000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 15, 'Juan Cruz', 'juan.cruz@email.com', '0926-123-4567', NULL, '2024-02-01', '2025-01-31', 16000.00, 1500.00, 'active', datetime('now'), datetime('now')),
(2, 16, 'Ana Reyes', 'ana.reyes@email.com', '0927-234-5678', NULL, '2024-03-01', '2025-02-28', 22000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 18, 'Carlos Mendoza', 'carlos.mendoza@email.com', '0928-345-6789', NULL, '2024-04-01', '2025-03-31', 32000.00, 3000.00, 'active', datetime('now'), datetime('now')),
(2, 19, 'Sofia Garcia', 'sofia.garcia@email.com', '0929-456-7890', NULL, '2024-05-01', '2025-04-30', 34000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 20, 'Pedro Rodriguez', 'pedro.rodriguez@email.com', '0930-567-8901', NULL, '2024-06-01', '2025-05-31', 15500.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 22, 'Luisa Fernandez', 'luisa.fernandez@email.com', '0931-678-9012', NULL, '2024-07-01', '2025-06-30', 22500.00, 1000.00, 'active', datetime('now'), datetime('now')),
(2, 23, 'Miguel Torres', 'miguel.torres@email.com', '0932-789-0123', NULL, '2024-08-01', '2025-07-31', 24000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 24, 'Elena Ramos', 'elena.ramos@email.com', '0933-890-1234', NULL, '2024-09-01', '2025-08-31', 33000.00, 2000.00, 'active', datetime('now'), datetime('now')),
(2, 26, 'Roberto Castillo', 'roberto.castillo@email.com', '0934-901-2345', NULL, '2024-10-01', '2025-09-30', 35000.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 27, 'Carmen Lopez', 'carmen.lopez@email.com', '0935-012-3456', NULL, '2024-11-01', '2025-10-31', 14500.00, 500.00, 'active', datetime('now'), datetime('now')),
(2, 28, 'Antonio Rivera', 'antonio.rivera@email.com', '0936-123-4567', NULL, '2024-12-01', '2025-11-30', 14800.00, 0.00, 'active', datetime('now'), datetime('now')),
(2, 30, 'Isabella Morales', 'isabella.morales@email.com', '0937-234-5678', NULL, '2024-01-15', '2025-01-14', 22800.00, 0.00, 'active', datetime('now'), datetime('now')),
(3, 31, 'Manufacturing Co A', 'operations@mfg-a.com', '0938-345-6789', 'Manufacturing Co A', '2024-01-01', '2025-12-31', 80000.00, 0.00, 'active', datetime('now'), datetime('now')),
(3, 32, 'Logistics Hub Inc', 'info@logistics.com', '0939-456-7890', 'Logistics Hub Inc', '2024-02-01', '2025-01-31', 100000.00, 15000.00, 'active', datetime('now'), datetime('now')),
(3, 34, 'Distribution Center', 'contact@distcenter.com', '0940-567-8901', 'Distribution Center', '2024-03-01', '2025-02-28', 85000.00, 0.00, 'active', datetime('now'), datetime('now')),
(3, 35, 'Storage Solutions', 'admin@storage.com', '0941-678-9012', 'Storage Solutions', '2024-04-01', '2025-03-31', 110000.00, 20000.00, 'active', datetime('now'), datetime('now')),
(4, 38, 'Diego Flores', 'diego.flores@email.com', '0942-789-0123', NULL, '2024-01-01', '2025-12-31', 28000.00, 0.00, 'active', datetime('now'), datetime('now')),
(4, 39, 'Victoria Silva', 'victoria.silva@email.com', '0943-890-1234', NULL, '2024-02-01', '2025-01-31', 29500.00, 2500.00, 'active', datetime('now'), datetime('now')),
(4, 40, 'Ricardo Medina', 'ricardo.medina@email.com', '0944-901-2345', NULL, '2024-03-01', '2025-02-28', 38000.00, 0.00, 'active', datetime('now'), datetime('now')),
(4, 42, 'Patricia Herrera', 'patricia.herrera@email.com', '0945-012-3456', NULL, '2024-04-01', '2025-03-31', 41000.00, 3000.00, 'active', datetime('now'), datetime('now')),
(4, 43, 'Javier Ortiz', 'javier.ortiz@email.com', '0946-123-4567', NULL, '2024-05-01', '2025-04-30', 52000.00, 0.00, 'active', datetime('now'), datetime('now')),
(4, 44, 'Monica Delgado', 'monica.delgado@email.com', '0947-234-5678', NULL, '2024-06-01', '2025-05-31', 27000.00, 0.00, 'active', datetime('now'), datetime('now')),
(4, 46, 'Fernando Castro', 'fernando.castro@email.com', '0948-345-6789', NULL, '2024-07-01', '2025-06-30', 37000.00, 1500.00, 'active', datetime('now'), datetime('now')),
(4, 47, 'Laura Vargas', 'laura.vargas@email.com', '0949-456-7890', NULL, '2024-08-01', '2025-07-31', 39000.00, 0.00, 'active', datetime('now'), datetime('now')),
(4, 48, 'Andres Jimenez', 'andres.jimenez@email.com', '0950-567-8901', NULL, '2024-09-01', '2025-08-31', 50000.00, 4000.00, 'active', datetime('now'), datetime('now')),
(1, 3, 'Former Tenant A', 'former.a@email.com', '0951-678-9012', 'Former Company A', '2023-01-01', '2023-12-31', 26000.00, 0.00, 'inactive', datetime('now'), datetime('now')),
(2, 17, 'Former Resident B', 'former.b@email.com', '0952-789-0123', NULL, '2023-06-01', '2024-05-31', 23000.00, 0.00, 'inactive', datetime('now'), datetime('now'));

-- Payments
INSERT INTO payments (tenant_id, property_id, room_id, amount, due_date, paid_date, status, method, reference, created_at, updated_at) VALUES
(1, 1, 1, 25000.00, '2024-01-01', '2024-01-05', "paid", 'bank_transfer', 'BANK-2024-001', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-02-01', '2024-02-03', "paid", 'bank_transfer', 'BANK-2024-002', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-03-01', '2024-03-02', "paid", 'bank_transfer', 'BANK-2024-003', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-04-01', '2024-04-05', "paid", 'bank_transfer', 'BANK-2024-004', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-05-01', '2024-05-03', "paid", 'bank_transfer', 'BANK-2024-005', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-06-01', '2024-06-04', "paid", 'bank_transfer', 'BANK-2024-006', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-07-01', '2024-07-02', "paid", 'bank_transfer', 'BANK-2024-007', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-08-01', '2024-08-05', "paid", 'bank_transfer', 'BANK-2024-008', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-09-01', '2024-09-03', "paid", 'bank_transfer', 'BANK-2024-009', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-10-01', '2024-10-04', "paid", 'bank_transfer', 'BANK-2024-010', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-11-01', '2024-11-02', "paid", 'bank_transfer', 'BANK-2024-011', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2024-12-01', '2024-12-05', "paid", 'bank_transfer', 'BANK-2024-012', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-01-01', '2025-01-03', "paid", 'bank_transfer', 'BANK-2025-001', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-02-01', '2025-02-04', "paid", 'bank_transfer', 'BANK-2025-002', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-03-01', '2025-03-02', "paid", 'bank_transfer', 'BANK-2025-003', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-04-01', '2025-04-05', "paid", 'bank_transfer', 'BANK-2025-004', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-05-01', '2025-05-03', "paid", 'bank_transfer', 'BANK-2025-005', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-06-01', '2025-06-04', "paid", 'bank_transfer', 'BANK-2025-006', datetime('now'), datetime('now')),
(1, 1, 1, 25000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-02-01', '2024-02-05', "paid", 'gcash', 'GCASH-2024-001', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-03-01', '2024-03-03', "paid", 'gcash', 'GCASH-2024-002', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-04-01', '2024-04-06', "paid", 'gcash', 'GCASH-2024-003', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-05-01', '2024-05-04', "paid", 'gcash', 'GCASH-2024-004', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-06-01', '2024-06-05', "paid", 'gcash', 'GCASH-2024-005', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-07-01', '2024-07-03', "paid", 'gcash', 'GCASH-2024-006', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-08-01', '2024-08-06', "paid", 'gcash', 'GCASH-2024-007', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-09-01', '2024-09-04', "paid", 'gcash', 'GCASH-2024-008', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-10-01', '2024-10-05', "paid", 'gcash', 'GCASH-2024-009', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-11-01', '2024-11-03', "paid", 'gcash', 'GCASH-2024-010', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2024-12-01', '2024-12-06', "paid", 'gcash', 'GCASH-2024-011', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-01-01',   '2025-01-04', "paid", 'gcash', 'GCASH-2025-001', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-02-01', '2025-02-05', "paid", 'gcash', 'GCASH-2025-002', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-03-01', '2025-03-03', "paid", 'gcash', 'GCASH-2025-003', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-04-01', '2025-04-06', "paid", 'gcash', 'GCASH-2025-004', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-05-01', '2025-05-04', "paid", 'gcash', 'GCASH-2025-005', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-06-01', '2025-06-05', "paid", 'gcash', 'GCASH-2025-006', datetime('now'), datetime('now')),
(2, 1, 2, 28000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-03-01', '2024-03-05', "paid", 'bank_transfer', 'BANK-2024-101', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-04-01', '2024-04-03', "paid", 'bank_transfer', 'BANK-2024-102', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-05-01', '2024-05-06', "paid", 'bank_transfer', 'BANK-2024-103', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-06-01', '2024-06-04', "paid", 'bank_transfer', 'BANK-2024-104', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-07-01', '2024-07-05', "paid", 'bank_transfer', 'BANK-2024-105', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-08-01', '2024-08-03', "paid", 'bank_transfer', 'BANK-2024-106', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-09-01', '2024-09-06', "paid", 'bank_transfer', 'BANK-2024-107', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-10-01', '2024-10-04', "paid", 'bank_transfer', 'BANK-2024-108', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-11-01', '2024-11-05', "paid", 'bank_transfer', 'BANK-2024-109', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2024-12-01', '2024-12-03', "paid", 'bank_transfer', 'BANK-2024-110', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-01-01', '2025-01-06', "paid", 'bank_transfer', 'BANK-2025-101', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-02-01', '2025-02-04', "paid", 'bank_transfer', 'BANK-2025-102', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-03-01', '2025-03-05', "paid", 'bank_transfer', 'BANK-2025-103', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-04-01', '2025-04-03', "paid", 'bank_transfer', 'BANK-2025-104', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-05-01', '2025-05-06', "paid", 'bank_transfer', 'BANK-2025-105', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-06-01', '2025-06-04', "paid", 'bank_transfer', 'BANK-2025-106', datetime('now'), datetime('now')),
(3, 1, 4, 32000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-04-01', '2024-04-05', "paid", 'bank_transfer', 'BANK-2024-201', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-05-01', '2024-05-03', "paid", 'bank_transfer', 'BANK-2024-202', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-06-01', '2024-06-06', "paid", 'bank_transfer', 'BANK-2024-203', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-07-01', '2024-07-04', "paid", 'bank_transfer', 'BANK-2024-204', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-08-01', '2024-08-05', "paid", 'bank_transfer', 'BANK-2024-205', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-09-01', '2024-09-03', "paid", 'bank_transfer', 'BANK-2024-206', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-10-01', '2024-10-06', "paid", 'bank_transfer', 'BANK-2024-207', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-11-01', '2024-11-04', "paid", 'bank_transfer', 'BANK-2024-208', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2024-12-01', '2024-12-05', "paid", 'bank_transfer', 'BANK-2024-209', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-01-01', '2025-01-03', "paid", 'bank_transfer', 'BANK-2025-201', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-02-01', '2025-02-06', "paid", 'bank_transfer', 'BANK-2025-202', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-03-01', '2025-03-04', "paid", 'bank_transfer', 'BANK-2025-203', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-04-01', '2025-04-05', "paid", 'bank_transfer', 'BANK-2025-204', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-05-01', '2025-05-03', "paid", 'bank_transfer', 'BANK-2025-205', datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-06-01', NULL, 'overdue', NULL, NULL, datetime('now'), datetime('now')),
(4, 1, 5, 30000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-01-01', '2024-01-05', "paid", 'gcash', 'GCASH-2024-301', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-02-01', '2024-02-03', "paid", 'gcash', 'GCASH-2024-302', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-03-01', '2024-03-06', "paid", 'gcash', 'GCASH-2024-303', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-04-01', '2024-04-04', "paid", 'gcash', 'GCASH-2024-304', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-05-01', '2024-05-05', "paid", 'gcash', 'GCASH-2024-305', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-06-01', '2024-06-03', "paid", 'gcash', 'GCASH-2024-306', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-07-01', '2024-07-06', "paid", 'gcash', 'GCASH-2024-307', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-08-01', '2024-08-04', "paid", 'gcash', 'GCASH-2024-308', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-09-01', '2024-09-05', "paid", 'gcash', 'GCASH-2024-309', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-10-01', '2024-10-03', "paid", 'gcash', 'GCASH-2024-310', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-11-01', '2024-11-06', "paid", 'gcash', 'GCASH-2024-311', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2024-12-01', '2024-12-04', "paid", 'gcash', 'GCASH-2024-312', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-01-01', '2025-01-05', "paid", 'gcash', 'GCASH-2025-301', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-02-01', '2025-02-03', "paid", 'gcash', 'GCASH-2025-302', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-03-01', '2025-03-06', "paid", 'gcash', 'GCASH-2025-303', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-04-01', '2025-04-04', "paid", 'gcash', 'GCASH-2025-304', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-05-01', '2025-05-05', "paid", 'gcash', 'GCASH-2025-305', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-06-01', '2025-06-03', "paid", 'gcash', 'GCASH-2025-306', datetime('now'), datetime('now')),
(14, 2, 14, 15000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-02-01', '2024-02-05', "paid", 'cash', 'CASH-2024-401', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-03-01', '2024-03-03', "paid", 'cash', 'CASH-2024-402', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-04-01', '2024-04-06', "paid", 'cash', 'CASH-2024-403', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-05-01', '2024-05-04', "paid", 'cash', 'CASH-2024-404', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-06-01', '2024-06-05', "paid", 'cash', 'CASH-2024-405', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-07-01', '2024-07-03', "paid", 'cash', 'CASH-2024-406', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-08-01', '2024-08-06', "paid", 'cash', 'CASH-2024-407', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-09-01', '2024-09-04', "paid", 'cash', 'CASH-2024-408', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-10-01', '2024-10-05', "paid", 'cash', 'CASH-2024-409', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-11-01', '2024-11-03', "paid", 'cash', 'CASH-2024-410', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2024-12-01', '2024-12-06', "paid", 'cash', 'CASH-2024-411', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-01-01',   '2025-01-04', "paid", 'cash', 'CASH-2025-401', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-02-01', '2025-02-05', "paid", 'cash', 'CASH-2025-402', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-03-01', '2025-03-03', "paid", 'cash', 'CASH-2025-403', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-04-01', '2025-04-06', "paid", 'cash', 'CASH-2025-404', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-05-01', '2025-05-04', "paid", 'cash', 'CASH-2025-405', datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-06-01', NULL, 'overdue', NULL, NULL, datetime('now'), datetime('now')),
(15, 2, 15, 16000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-01-01', '2024-01-05', "paid", 'bank_transfer', 'BANK-2024-501', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-02-01', '2024-02-03', "paid", 'bank_transfer', 'BANK-2024-502', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-03-01', '2024-03-06', "paid", 'bank_transfer', 'BANK-2024-503', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-04-01', '2024-04-04', "paid", 'bank_transfer', 'BANK-2024-504', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-05-01', '2024-05-05', "paid", 'bank_transfer', 'BANK-2024-505', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-06-01', '2024-06-03', "paid", 'bank_transfer', 'BANK-2024-506', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-07-01', '2024-07-06', "paid", 'bank_transfer', 'BANK-2024-507', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-08-01', '2024-08-04', "paid", 'bank_transfer', 'BANK-2024-508', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-09-01', '2024-09-05', "paid", 'bank_transfer', 'BANK-2024-509', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-10-01', '2024-10-03', "paid", 'bank_transfer', 'BANK-2024-510', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-11-01', '2024-11-06', "paid", 'bank_transfer', 'BANK-2024-511', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2024-12-01', '2024-12-04', "paid", 'bank_transfer', 'BANK-2024-512', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-01-01', '2025-01-05', "paid", 'bank_transfer', 'BANK-2025-501', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-02-01', '2025-02-03', "paid", 'bank_transfer', 'BANK-2025-502', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-03-01', '2025-03-06', "paid", 'bank_transfer', 'BANK-2025-503', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-04-01', '2025-04-04', "paid", 'bank_transfer', 'BANK-2025-504', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-05-01', '2025-05-05', "paid", 'bank_transfer', 'BANK-2025-505', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-06-01', '2025-06-03', "paid", 'bank_transfer', 'BANK-2025-506', datetime('now'), datetime('now')),
(31, 3, 31, 80000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-02-01', '2024-02-05', "paid", 'bank_transfer', 'BANK-2024-601', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-03-01', '2024-03-03', "paid", 'bank_transfer', 'BANK-2024-602', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-04-01', '2024-04-06', "paid", 'bank_transfer', 'BANK-2024-603', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-05-01', '2024-05-04', "paid", 'bank_transfer', 'BANK-2024-604', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-06-01', '2024-06-05', "paid", 'bank_transfer', 'BANK-2024-605', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-07-01', '2024-07-03', "paid", 'bank_transfer', 'BANK-2024-606', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-08-01', '2024-08-06', "paid", 'bank_transfer', 'BANK-2024-607', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-09-01', '2024-09-04', "paid", 'bank_transfer', 'BANK-2024-608', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-10-01', '2024-10-05', "paid", 'bank_transfer', 'BANK-2024-609', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-11-01', '2024-11-03', "paid", 'bank_transfer', 'BANK-2024-610', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2024-12-01', '2024-12-06', "paid", 'bank_transfer', 'BANK-2024-611', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-01-01', '2025-01-04’, "paid", 'bank_transfer', 'BANK-2025-601', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-02-01', '2025-02-05', "paid", 'bank_transfer', 'BANK-2025-602', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-03-01', '2025-03-03', "paid", 'bank_transfer', 'BANK-2025-603', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-04-01', '2025-04-06', "paid", 'bank_transfer', 'BANK-2025-604', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-05-01', '2025-05-04', "paid", 'bank_transfer', 'BANK-2025-605', datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-06-01', NULL, 'overdue', NULL, NULL, datetime('now'), datetime('now')),
(32, 3, 32, 100000.00, '2025-07-01', NULL, 'pending', NULL, NULL, datetime('now'), datetime('now'));

