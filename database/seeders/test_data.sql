-- Test Data for Canzana Business Property Management System
-- Execute this in Navicat/Laragon MySQL database
-- This file contains test data with 10+ records per main entity

SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing test data (optional)
DELETE FROM payments WHERE id > 0;
DELETE FROM tenants WHERE id > 0;
DELETE FROM rooms WHERE id > 0;
DELETE FROM buildings WHERE id > 0;
DELETE FROM properties WHERE id > 0;
DELETE FROM users WHERE id > 0;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- USERS (15 records)
-- ============================================
INSERT INTO users (name, email, password, created_at, updated_at) VALUES
('Admin User', 'admin@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Manager John', 'manager@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Agent Sarah', 'sarah@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Agent Mike', 'mike@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Agent Lisa', 'lisa@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Accountant Tom', 'tom@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Supervisor Jane', 'jane@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff David', 'david@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Emma', 'emma@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Chris', 'chris@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Amy', 'amy@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Robert', 'robert@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Jennifer', 'jennifer@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff William', 'william@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW()),
('Staff Olivia', 'olivia@canzana.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());

-- ============================================
-- PROPERTIES (20 records)
-- ============================================
INSERT INTO properties (name, address, city, type, status, image, created_at, updated_at) VALUES
('Canzana Tower', '123 Business District Ave', 'Manila', 'Commercial', 'active', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', NOW(), NOW()),
('Green Valley Residences', '456 Suburban Lane', 'Quezon City', 'Residential', 'active', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800', NOW(), NOW()),
('Metro Industrial Park', '789 Industrial Zone', 'Makati', 'Industrial', 'active', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', NOW(), NOW()),
('Sunset Heights', '321 Hillside Drive', 'Taguig', 'Residential', 'active', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800', NOW(), NOW()),
('Ocean View Apartments', '555 Coastal Road', 'Manila', 'Residential', 'active', 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800', NOW(), NOW()),
('Tech Hub Center', '888 Innovation Street', 'Pasig', 'Commercial', 'active', 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800', NOW(), NOW()),
('Mountain View Condos', '999 Highland Avenue', 'Antipolo', 'Residential', 'active', 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=800', NOW(), NOW()),
('City Plaza Mall', '777 Commercial Blvd', 'Mandaluyong', 'Commercial', 'active', 'https://images.unsplash.com/photo-1565514020176-8c40944b1d9f?w=800', NOW(), NOW()),
('Riverside Gardens', '222 Riverbank Drive', 'Marikina', 'Residential', 'active', 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=800', NOW(), NOW()),
('Industrial Complex A', '111 Factory Road', 'Valenzuela', 'Industrial', 'active', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', NOW(), NOW()),
('Business Center Prime', '444 Executive Ave', 'Ortigas', 'Commercial', 'active', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', NOW(), NOW()),
('Suburban Homes', '333 Peaceful Lane', 'Cavite', 'Residential', 'active', 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800', NOW(), NOW()),
('Grand Central Tower', '666 Central Avenue', 'Manila', 'Commercial', 'active', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', NOW(), NOW()),
('Lakeview Residences', '777 Lakeshore Drive', 'Laguna', 'Residential', 'active', 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800', NOW(), NOW()),
('Industrial Zone B', '888 Manufacturing St', 'Bulacan', 'Industrial', 'active', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', NOW(), NOW()),
('Skyline Corporate', '999 Skyline Blvd', 'BGC', 'Commercial', 'active', 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800', NOW(), NOW()),
('Garden Heights', '111 Garden Road', 'San Juan', 'Residential', 'active', 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800', NOW(), NOW()),
('Logistics Park Central', '222 Logistics Way', 'Pampanga', 'Industrial', 'active', 'https://images.unsplash.com/photo-1587293852726-70cdb56c2866?w=800', NOW(), NOW()),
('Metropolis Tower', '333 Metro Ave', 'Pasay', 'Commercial', 'active', 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800', NOW(), NOW()),
('Harbor View Condos', '444 Harbor Street', 'Paranaque', 'Residential', 'active', 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800', NOW(), NOW());

-- ============================================
-- BUILDINGS (25 records)
-- ============================================
INSERT INTO buildings (property_id, name, floors, status, created_at, updated_at) VALUES
(1, 'Main Tower', 15, 'active', NOW(), NOW()),
(1, 'Annex Building', 8, 'active', NOW(), NOW()),
(2, 'Building A', 5, 'active', NOW(), NOW()),
(2, 'Building B', 6, 'active', NOW(), NOW()),
(2, 'Building C', 4, 'active', NOW(), NOW()),
(3, 'Warehouse 1', 2, 'active', NOW(), NOW()),
(3, 'Warehouse 2', 3, 'active', NOW(), NOW()),
(4, 'Tower 1', 12, 'active', NOW(), NOW()),
(4, 'Tower 2', 10, 'active', NOW(), NOW()),
(5, 'Ocean Tower', 8, 'active', NOW(), NOW()),
(6, 'Tech Hub Main', 6, 'active', NOW(), NOW()),
(7, 'Mountain View A', 5, 'active', NOW(), NOW()),
(8, 'Mall Building 1', 3, 'active', NOW(), NOW()),
(9, 'Riverside Block A', 4, 'active', NOW(), NOW()),
(10, 'Factory Building 1', 2, 'active', NOW(), NOW()),
(11, 'Business Center A', 8, 'active', NOW(), NOW()),
(12, 'Suburban Block A', 3, 'active', NOW(), NOW()),
(13, 'Grand Central Main', 20, 'active', NOW(), NOW()),
(14, 'Lakeview Tower A', 6, 'active', NOW(), NOW()),
(15, 'Industrial Zone B1', 3, 'active', NOW(), NOW()),
(16, 'Skyline Corporate A', 12, 'active', NOW(), NOW()),
(17, 'Garden Heights A', 4, 'active', NOW(), NOW()),
(18, 'Logistics Park Main', 4, 'active', NOW(), NOW()),
(19, 'Metropolis Tower A', 15, 'active', NOW(), NOW()),
(20, 'Harbor View Tower A', 8, 'active', NOW(), NOW());

-- ============================================
-- ROOMS (40 records)
-- ============================================
INSERT INTO rooms (building_id, unit, floor, type, size_sqm, rent, status, created_at, updated_at) VALUES
-- Canzana Tower - Main Tower
(1, '101', 1, 'Office', 45.50, 25000.00, 'occupied', NOW(), NOW()),
(1, '102', 1, 'Office', 52.00, 28000.00, 'occupied', NOW(), NOW()),
(1, '103', 1, 'Office', 48.00, 26000.00, 'vacant', NOW(), NOW()),
(1, '201', 2, 'Office', 60.00, 32000.00, 'occupied', NOW(), NOW()),
(1, '202', 2, 'Office', 55.00, 30000.00, 'occupied', NOW(), NOW()),
-- Canzana Tower - Annex Building
(2, 'A101', 1, 'Retail', 35.00, 20000.00, 'occupied', NOW(), NOW()),
(2, 'A102', 1, 'Retail', 40.00, 22000.00, 'occupied', NOW(), NOW()),
(2, 'A201', 2, 'Office', 50.00, 27000.00, 'vacant', NOW(), NOW()),
-- Green Valley Residences - Building A
(3, 'A-101', 1, 'Studio', 25.00, 15000.00, 'occupied', NOW(), NOW()),
(3, 'A-102', 1, 'Studio', 28.00, 16000.00, 'occupied', NOW(), NOW()),
(3, 'A-201', 2, '1-Bedroom', 45.00, 22000.00, 'occupied', NOW(), NOW()),
(3, 'A-202', 2, '1-Bedroom', 48.00, 23000.00, 'vacant', NOW(), NOW()),
(3, 'A-301', 3, '2-Bedroom', 65.00, 32000.00, 'occupied', NOW(), NOW()),
-- Green Valley Residences - Building B
(4, 'B-101', 1, 'Studio', 26.00, 15500.00, 'occupied', NOW(), NOW()),
(4, 'B-102', 1, 'Studio', 27.00, 15800.00, 'vacant', NOW(), NOW()),
(4, 'B-201', 2, '1-Bedroom', 46.00, 22500.00, 'occupied', NOW(), NOW()),
-- Metro Industrial Park - Warehouse 1
(6, 'W1-A', 1, 'Warehouse', 200.00, 80000.00, 'occupied', NOW(), NOW()),
(6, 'W1-B', 1, 'Warehouse', 250.00, 100000.00, 'occupied', NOW(), NOW()),
(6, 'W1-C', 2, 'Warehouse', 180.00, 75000.00, 'vacant', NOW(), NOW()),
-- Metro Industrial Park - Warehouse 2
(7, 'W2-A', 1, 'Warehouse', 220.00, 85000.00, 'occupied', NOW(), NOW()),
(7, 'W2-B', 2, 'Warehouse', 280.00, 110000.00, 'occupied', NOW(), NOW()),
-- Sunset Heights - Tower 1
(8, 'T1-501', 5, '1-Bedroom', 42.00, 28000.00, 'occupied', NOW(), NOW()),
(8, 'T1-502', 5, '1-Bedroom', 45.00, 29500.00, 'occupied', NOW(), NOW()),
(8, 'T1-601', 6, '2-Bedroom', 62.00, 38000.00, 'occupied', NOW(), NOW()),
(8, 'T1-602', 6, '2-Bedroom', 65.00, 39500.00, 'vacant', NOW(), NOW()),
-- Ocean View Apartments
(10, 'OV-101', 1, 'Studio', 30.00, 18000.00, 'occupied', NOW(), NOW()),
(10, 'OV-102', 1, '1-Bedroom', 50.00, 25000.00, 'occupied', NOW(), NOW()),
(10, 'OV-201', 2, '2-Bedroom', 70.00, 35000.00, 'vacant', NOW(), NOW()),
-- Tech Hub Center
(11, 'TH-101', 1, 'Office', 40.00, 22000.00, 'occupied', NOW(), NOW()),
(11, 'TH-102', 1, 'Office', 45.00, 24000.00, 'occupied', NOW(), NOW()),
(11, 'TH-201', 2, 'Office', 55.00, 28000.00, 'vacant', NOW(), NOW()),
-- Mountain View Condos
(12, 'MV-101', 1, '1-Bedroom', 48.00, 26000.00, 'occupied', NOW(), NOW()),
(12, 'MV-201', 2, '2-Bedroom', 72.00, 38000.00, 'occupied', NOW(), NOW()),
-- Grand Central Tower
(13, 'GC-101', 1, 'Office', 55.00, 30000.00, 'occupied', NOW(), NOW()),
(13, 'GC-102', 1, 'Office', 60.00, 32000.00, 'occupied', NOW(), NOW()),
(13, 'GC-201', 2, 'Office', 70.00, 38000.00, 'vacant', NOW(), NOW()),
-- Lakeview Residences
(14, 'LV-101', 1, '1-Bedroom', 45.00, 24000.00, 'occupied', NOW(), NOW()),
(14, 'LV-102', 1, '2-Bedroom', 68.00, 35000.00, 'occupied', NOW(), NOW()),
-- Skyline Corporate
(16, 'SK-101', 1, 'Office', 50.00, 27000.00, 'occupied', NOW(), NOW()),
(16, 'SK-102', 1, 'Office', 55.00, 29000.00, 'vacant', NOW(), NOW()),
-- Metropolis Tower
(19, 'MT-101', 1, 'Office', 65.00, 35000.00, 'occupied', NOW(), NOW()),
(19, 'MT-102', 1, 'Office', 70.00, 38000.00, 'occupied', NOW(), NOW()),
(19, 'MT-201', 2, 'Office', 80.00, 42000.00, 'vacant', NOW(), NOW());

-- ============================================
-- TENANTS (20 records)
-- ============================================
INSERT INTO tenants (property_id, room_id, name, email, phone, company, lease_start, lease_end, rent, balance, status, created_at, updated_at) VALUES
(1, 1, 'TechStart Solutions Inc.', 'contact@techstart.com', '0917-123-4567', 'TechStart Solutions Inc.', '2024-01-01', '2025-12-31', 25000.00, 0.00, 'active', NOW(), NOW()),
(1, 2, 'Digital Marketing Pro', 'info@digitalpro.com', '0918-234-5678', 'Digital Marketing Pro', '2024-02-01', '2025-01-31', 28000.00, 5000.00, 'active', NOW(), NOW()),
(1, 4, 'Innovate Corp', 'hello@innovate.com', '0919-345-6789', 'Innovate Corp', '2024-03-01', '2025-02-28', 32000.00, 0.00, 'active', NOW(), NOW()),
(1, 5, 'Global Ventures Ltd', 'admin@globalventures.com', '0920-456-7890', 'Global Ventures Ltd', '2024-04-01', '2025-03-31', 30000.00, 10000.00, 'active', NOW(), NOW()),
(2, 6, 'Maria Santos', 'maria.santos@email.com', '0925-901-2345', NULL, '2024-01-01', '2025-12-31', 15000.00, 0.00, 'active', NOW(), NOW()),
(2, 7, 'Juan Cruz', 'juan.cruz@email.com', '0926-123-4567', NULL, '2024-02-01', '2025-01-31', 16000.00, 1500.00, 'active', NOW(), NOW()),
(2, 8, 'Ana Reyes', 'ana.reyes@email.com', '0927-234-5678', NULL, '2024-03-01', '2025-02-28', 22000.00, 0.00, 'active', NOW(), NOW()),
(2, 10, 'Carlos Mendoza', 'carlos.mendoza@email.com', '0928-345-6789', NULL, '2024-04-01', '2025-03-31', 32000.00, 3000.00, 'active', NOW(), NOW()),
(3, 11, 'Manufacturing Co A', 'operations@mfg-a.com', '0938-345-6789', 'Manufacturing Co A', '2024-01-01', '2025-12-31', 80000.00, 0.00, 'active', NOW(), NOW()),
(3, 12, 'Logistics Hub Inc', 'info@logistics.com', '0939-456-7890', 'Logistics Hub Inc', '2024-02-01', '2025-01-31', 100000.00, 15000.00, 'active', NOW(), NOW()),
(4, 14, 'Diego Flores', 'diego.flores@email.com', '0942-789-0123', NULL, '2024-01-01', '2025-12-31', 28000.00, 0.00, 'active', NOW(), NOW()),
(4, 15, 'Victoria Silva', 'victoria.silva@email.com', '0943-890-1234', NULL, '2024-02-01', '2025-01-31', 29500.00, 2500.00, 'active', NOW(), NOW()),
(4, 16, 'Ricardo Medina', 'ricardo.medina@email.com', '0944-901-2345', NULL, '2024-03-01', '2025-02-28', 38000.00, 0.00, 'active', NOW(), NOW()),
(5, 18, 'Patricia Herrera', 'patricia.herrera@email.com', '0945-012-3456', NULL, '2024-01-01', '2025-12-31', 18000.00, 0.00, 'active', NOW(), NOW()),
(5, 19, 'Javier Ortiz', 'javier.ortiz@email.com', '0946-123-4567', NULL, '2024-02-01', '2025-01-31', 25000.00, 2000.00, 'active', NOW(), NOW()),
(6, 21, 'Monica Delgado', 'monica.delgado@email.com', '0947-234-5678', 'Tech Startup XYZ', '2024-01-01', '2025-12-31', 22000.00, 0.00, 'active', NOW(), NOW()),
(6, 22, 'Fernando Castro', 'fernando.castro@email.com', '0948-345-6789', 'Web Design Co', '2024-02-01', '2025-01-31', 24000.00, 1000.00, 'active', NOW(), NOW()),
(7, 23, 'Laura Vargas', 'laura.vargas@email.com', '0949-456-7890', NULL, '2024-01-01', '2025-12-31', 26000.00, 0.00, 'active', NOW(), NOW()),
(7, 24, 'Andres Jimenez', 'andres.jimenez@email.com', '0950-567-8901', NULL, '2024-02-01', '2025-01-31', 38000.00, 3000.00, 'active', NOW(), NOW()),
(1, 3, 'Former Tenant A', 'former.a@email.com', '0951-678-9012', 'Former Company A', '2023-01-01', '2023-12-31', 26000.00, 0.00, 'inactive', NOW(), NOW());

-- ============================================
-- PAYMENTS (50 records)
-- ============================================
INSERT INTO payments (tenant_id, property_id, room_id, amount, due_date, paid_date, status, method, reference, created_at, updated_at) VALUES
-- TechStart Solutions (Tenant ID 1) - All paid
(1, 1, 1, 25000.00, '2024-01-01', '2024-01-05', 'paid', 'bank_transfer', 'BANK-001', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-02-01', '2024-02-03', 'paid', 'bank_transfer', 'BANK-002', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-03-01', '2024-03-02', 'paid', 'bank_transfer', 'BANK-003', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-04-01', '2024-04-05', 'paid', 'bank_transfer', 'BANK-004', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-05-01', '2024-05-03', 'paid', 'bank_transfer', 'BANK-005', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-06-01', '2024-06-04', 'paid', 'bank_transfer', 'BANK-006', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-07-01', '2024-07-02', 'paid', 'bank_transfer', 'BANK-007', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-08-01', '2024-08-05', 'paid', 'bank_transfer', 'BANK-008', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-09-01', '2024-09-03', 'paid', 'bank_transfer', 'BANK-009', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-10-01', '2024-10-04', 'paid', 'bank_transfer', 'BANK-010', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-11-01', '2024-11-02', 'paid', 'bank_transfer', 'BANK-011', NOW(), NOW()),
(1, 1, 1, 25000.00, '2024-12-01', '2024-12-05', 'paid', 'bank_transfer', 'BANK-012', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-01-01', '2025-01-03', 'paid', 'bank_transfer', 'BANK-013', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-02-01', '2025-02-04', 'paid', 'bank_transfer', 'BANK-014', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-03-01', '2025-03-02', 'paid', 'bank_transfer', 'BANK-015', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-04-01', '2025-04-05', 'paid', 'bank_transfer', 'BANK-016', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-05-01', '2025-05-03', 'paid', 'bank_transfer', 'BANK-017', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-06-01', '2025-06-04', 'paid', 'bank_transfer', 'BANK-018', NOW(), NOW()),
(1, 1, 1, 25000.00, '2025-07-01', NULL, 'pending', NULL, NULL, NOW(), NOW()),
-- Digital Marketing Pro (Tenant ID 2) - Mixed payments
(2, 1, 2, 28000.00, '2024-02-01', '2024-02-05', 'paid', 'gcash', 'GCASH-001', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-03-01', '2024-03-03', 'paid', 'gcash', 'GCASH-002', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-04-01', '2024-04-06', 'paid', 'gcash', 'GCASH-003', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-05-01', '2024-05-04', 'paid', 'gcash', 'GCASH-004', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-06-01', '2024-06-05', 'paid', 'gcash', 'GCASH-005', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-07-01', '2024-07-03', 'paid', 'gcash', 'GCASH-006', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-08-01', '2024-08-06', 'paid', 'gcash', 'GCASH-007', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-09-01', '2024-09-04', 'paid', 'gcash', 'GCASH-008', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-10-01', '2024-10-05', 'paid', 'gcash', 'GCASH-009', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-11-01', '2024-11-03', 'paid', 'gcash', 'GCASH-010', NOW(), NOW()),
(2, 1, 2, 28000.00, '2024-12-01', '2024-12-06', 'paid', 'gcash', 'GCASH-011', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-01-01', '2025-01-04', 'paid', 'gcash', 'GCASH-012', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-02-01', '2025-02-05', 'paid', 'gcash', 'GCASH-013', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-03-01', '2025-03-03', 'paid', 'gcash', 'GCASH-014', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-04-01', '2025-04-06', 'paid', 'gcash', 'GCASH-015', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-05-01', '2025-05-04', 'paid', 'gcash', 'GCASH-016', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-06-01', '2025-06-05', 'paid', 'gcash', 'GCASH-017', NOW(), NOW()),
(2, 1, 2, 28000.00, '2025-07-01', NULL, 'pending', NULL, NULL, NOW(), NOW()),
-- Global Ventures Ltd (Tenant ID 4) - With overdue
(4, 1, 5, 30000.00, '2024-04-01', '2024-04-05', 'paid', 'bank_transfer', 'BANK-101', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-05-01', '2024-05-03', 'paid', 'bank_transfer', 'BANK-102', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-06-01', '2024-06-06', 'paid', 'bank_transfer', 'BANK-103', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-07-01', '2024-07-04', 'paid', 'bank_transfer', 'BANK-104', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-08-01', '2024-08-05', 'paid', 'bank_transfer', 'BANK-105', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-09-01', '2024-09-03', 'paid', 'bank_transfer', 'BANK-106', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-10-01', '2024-10-06', 'paid', 'bank_transfer', 'BANK-107', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-11-01', '2024-11-04', 'paid', 'bank_transfer', 'BANK-108', NOW(), NOW()),
(4, 1, 5, 30000.00, '2024-12-01', '2024-12-05', 'paid', 'bank_transfer', 'BANK-109', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-01-01', '2025-01-03', 'paid', 'bank_transfer', 'BANK-110', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-02-01', '2025-02-06', 'paid', 'bank_transfer', 'BANK-111', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-03-01', '2025-03-04', 'paid', 'bank_transfer', 'BANK-112', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-04-01', '2025-04-05', 'paid', 'bank_transfer', 'BANK-113', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-05-01', '2025-05-03', 'paid', 'bank_transfer', 'BANK-114', NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-06-01', NULL, 'overdue', NULL, NULL, NOW(), NOW()),
(4, 1, 5, 30000.00, '2025-07-01', NULL, 'pending', NULL, NULL, NOW(), NOW()),
-- Maria Santos (Tenant ID 5) - Residential
(5, 2, 6, 15000.00, '2024-01-01', '2024-01-05', 'paid', 'gcash', 'GCASH-201', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-02-01', '2024-02-03', 'paid', 'gcash', 'GCASH-202', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-03-01', '2024-03-06', 'paid', 'gcash', 'GCASH-203', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-04-01', '2024-04-04', 'paid', 'gcash', 'GCASH-204', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-05-01', '2024-05-05', 'paid', 'gcash', 'GCASH-205', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-06-01', '2024-06-03', 'paid', 'gcash', 'GCASH-206', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-07-01', '2024-07-06', 'paid', 'gcash', 'GCASH-207', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-08-01', '2024-08-04', 'paid', 'gcash', 'GCASH-208', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-09-01', '2024-09-05', 'paid', 'gcash', 'GCASH-209', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-10-01', '2024-10-03', 'paid', 'gcash', 'GCASH-210', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-11-01', '2024-11-06', 'paid', 'gcash', 'GCASH-211', NOW(), NOW()),
(5, 2, 6, 15000.00, '2024-12-01', '2024-12-04', 'paid', 'gcash', 'GCASH-212', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-01-01', '2025-01-05', 'paid', 'gcash', 'GCASH-213', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-02-01', '2025-02-03', 'paid', 'gcash', 'GCASH-214', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-03-01', '2025-03-06', 'paid', 'gcash', 'GCASH-215', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-04-01', '2025-04-04', 'paid', 'gcash', 'GCASH-216', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-05-01', '2025-05-05', 'paid', 'gcash', 'GCASH-217', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-06-01', '2025-06-03', 'paid', 'gcash', 'GCASH-218', NOW(), NOW()),
(5, 2, 6, 15000.00, '2025-07-01', NULL, 'pending', NULL, NULL, NOW(), NOW());

-- ============================================
-- TEST DATA SUMMARY
-- ============================================
-- Users: 15 records
-- Properties: 20 records (Commercial, Residential, Industrial)
-- Buildings: 25 records
-- Rooms: 40 records (Office, Studio, 1-Bedroom, 2-Bedroom, Warehouse, Retail)
-- Tenants: 20 records (19 active, 1 inactive)
-- Payments: 50 records (paid, pending, overdue)
-- 
-- Payment Methods: bank_transfer, gcash
-- Payment Statuses: paid, pending, overdue
-- Room Statuses: occupied, vacant
-- Tenant Statuses: active, inactive
