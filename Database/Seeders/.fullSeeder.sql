USE HontoriaPrintingDB;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE userProcessTasks;
TRUNCATE TABLE userRoles;
TRUNCATE TABLE roleProcessTasks;
TRUNCATE TABLE rolePermissions;
TRUNCATE TABLE permissions;
TRUNCATE TABLE roleManagementGovernance;
TRUNCATE TABLE roles;
TRUNCATE TABLE orderDesigns;
TRUNCATE TABLE orderProcess;
TRUNCATE TABLE orderGroups;
TRUNCATE TABLE orders;
TRUNCATE TABLE serviceProcess;
TRUNCATE TABLE processes;
TRUNCATE TABLE subserviceImages;
TRUNCATE TABLE subservices;
TRUNCATE TABLE services;
TRUNCATE TABLE users;
TRUNCATE TABLE userStats;
SET FOREIGN_KEY_CHECKS = 1;

-- Seed users first
INSERT INTO users (id, username, email, passwordHash, firstName, middleName, lastName, phone, createdAt, lastLoginAt) VALUES
(1, 'owner', 'juan.delacruz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Carlos', 'Dela Cruz', '09123456789', '2025-01-01 00:00:00', '2026-02-20 08:30:00'),
(2, 'ana', 'ana.santos@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Marie', 'Santos', '09123456780', '2025-01-05 00:00:00', '2026-02-21 09:00:00'),
(3, 'ben', 'ben.reyes@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ben', 'Joseph', 'Reyes', '09123456781', '2025-01-10 00:00:00', '2026-02-21 08:45:00'),
(4, 'carlo', 'carlo.gonzales@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlo', 'Antonio', 'Gonzales', '09123456782', '2025-01-15 00:00:00', '2026-02-21 10:15:00'),
(5, 'diana', 'diana.bautista@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diana', 'Lopez', 'Bautista', '09123456783', '2025-01-20 00:00:00', '2026-02-20 16:30:00'),
(6, 'edu', 'eduardo.torres@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eduardo', 'Martin', 'Torres', '09123456784', '2025-01-25 00:00:00', '2026-02-21 07:30:00'),
(7, 'fatima', 'fatima.gomez@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Cruz', 'Gomez', '09123456785', '2025-02-01 00:00:00', '2026-02-21 11:00:00'),
(8, 'gab', 'gabriel.diaz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gabriel', 'Santos', 'Diaz', '09123456786', '2025-02-05 00:00:00', '2026-02-20 14:20:00'),
(9, 'hannah', 'hannah.mendoza@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hannah', 'Rose', 'Mendoza', '09123456787', '2025-02-10 00:00:00', '2026-02-21 09:45:00'),
(10, 'ian', 'ian.cruz@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ian', 'Patrick', 'Cruz', '09123456788', '2025-02-15 00:00:00', '2026-02-21 08:00:00');

-- Seed user stats
INSERT INTO userStats (userID, tasksCompleted, tasksCompletedDuration) VALUES
(1, 2, 150.50),
(2, 0, 0),
(3, 21, 480.25),
(4, 21, 320.25),
(5, 21, 420.50),
(6, 21, 520.75),
(7, 0, 0),
(8, 0, 0),
(9, 0, 0),
(10, 0, 0);

-- Seed services and subservices next
INSERT INTO services (name) VALUES
('Sublimation'),
('Tarpaulin'),
('Sintra Board');

INSERT INTO subservices (serviceID, name, pricePerUnit, description) VALUES
(1, 'Jersey', 300, 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.'),
(1, 'T-shirt', 250, 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.'),
(1, 'Short', 200, 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.'),
(1, 'Warmer', 400, 'Sublimation warmers for players and athletes. Keeps you warm while looking professional on and off the court.'),
(1, 'Jogging Pants', 400, 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching for any team or individual.');

-- Seed processes before serviceProcess because of the foreign key dependency
INSERT INTO processes (name) VALUES
('Designing'),
('Printing'),
('Heat Press'),
('Sewing');

INSERT INTO serviceProcess (serviceID, processesID, phase) VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 3),
(1, 4, 4);

-- Seed roles, permissions, and related mappings
INSERT INTO roles (name) VALUES
('owner'),                 -- 1
('admin'),                 -- 2
('artist'),                -- 3
('print operator'),        -- 4
('heat press operator'),   -- 5
('sewist'),                -- 6
('verifier');              -- 7

INSERT INTO roleManagementGovernance (roleSubjectID, roleAgentID, canGrant, canRevoke, canAlter, canDelete) VALUES
(1, 1, 1, 0, 1, 0), -- Owner to Owner
(1, 2, 0, 0, 0, 0), -- Admin to Owner
(2, 1, 1, 1, 1, 1), -- Owner to Admin
(2, 2, 1, 0, 0, 0); -- Admin to Admin

INSERT INTO permissions (name) VALUES
-- Staff Management Permissions
('canViewStaffPage'),                -- 1
('canCreateUserAccounts'),           -- 2
('canDeleteUserAccounts'),           -- 3
('canAlterAccountRoles'),            -- 4

-- Role Management Permissions
('canViewRoleManagementPage'),       -- 5
('canCreateRoles'),                  -- 6
('canDeleteRoles'),                  -- 7
('canAlterRoles'),                   -- 8

-- Service & Subservice Management Permissions
('canViewServicesPage'),             -- 9
('canCreateServices'),               -- 10
('canDeleteServices'),               -- 11
('canAlterServices'),                -- 12
('canAlterServiceStatus'),           -- 13
('canManageServiceProcesses'),       -- 14
('canCreateSubservices'),            -- 15
('canDeleteSubservices'),            -- 16
('canAlterSubservices'),             -- 17
('canAlterSubserviceStatus'),        -- 18

-- Order Management Permissions
('canViewOrdersPage'),               -- 19
('canCreateOrders'),                 -- 20
('canApplyDiscountToOrders'),        -- 21
('canDeleteOrders'),                 -- 22
('canAlterOrders'),                  -- 23
('canAssignStaffToOrders'),          -- 24
('canUnassignStaffToOrders'),        -- 25
('canVerifyOrderCompletion'),        -- 26

-- Task Related Permissions
('canSelfAssignToTasks'),            -- 27
('canSelfUnassignToTasks');          -- 28

INSERT INTO rolePermissions (roleID, permissionID) VALUES
-- Owner permissions
(1, 1),   -- canViewStaffPage
(1, 2),   -- canCreateUserAccounts
(1, 3),   -- canDeleteUserAccounts
(1, 4),   -- canAlterAccountRoles
(1, 5),   -- canViewRoleManagementPage
(1, 6),   -- canCreateRoles
(1, 7),   -- canDeleteRoles
(1, 8),   -- canAlterRoles
(1, 9),   -- canViewServicesPage
(1, 10),  -- canCreateServices
(1, 11),  -- canDeleteServices
(1, 12),  -- canAlterServices
(1, 13),  -- canAlterServiceStatus
(1, 14),  -- canManageServiceProcesses
(1, 15),  -- canCreateSubservices
(1, 16),  -- canDeleteSubservices
(1, 17),  -- canAlterSubservices
(1, 18),  -- canAlterSubserviceStatus
(1, 19),  -- canViewOrdersPage
(1, 20),  -- canCreateOrders
(1, 21),  -- canApplyDiscountToOrders
(1, 22),  -- canDeleteOrders
(1, 23),  -- canAlterOrders
(1, 24),  -- canAssignStaffToOrders
(1, 25),  -- canUnassignStaffToOrders
(1, 26),  -- canVerifyOrderCompletion
(1, 27),  -- canSelfAssignToTasks
(1, 28),  -- canSelfUnassignToTasks

-- Admin permissions
(2, 1),   -- canViewStaffPage
(2, 2),   -- canCreateUserAccounts
(2, 3),   -- canDeleteUserAccounts
(2, 4),   -- canAlterAccountRoles
(2, 5),   -- canViewRoleManagementPage
(2, 8),   -- canAlterRoles
(2, 9),   -- canViewServicesPage
(2, 13),  -- canAlterServiceStatus
(2, 17),  -- canAlterSubservices
(2, 18),  -- canAlterSubserviceStatus
(2, 19),  -- canViewOrdersPage
(2, 20),  -- canCreateOrders
(2, 21),  -- canApplyDiscountToOrders
(2, 22),  -- canDeleteOrders
(2, 23),  -- canAlterOrders
(2, 24),  -- canAssignStaffToOrders
(2, 25),  -- canUnassignStaffToOrders
(2, 27),  -- canSelfAssignToTasks
(2, 28),  -- canSelfUnassignToTasks

-- Artist permissions
(3, 27),  -- canSelfAssignToTasks

-- Print Operator permissions
(4, 27),  -- canSelfAssignToTasks

-- Heat Press Operator permissions
(5, 27),  -- canSelfAssignToTasks

-- Sewist permissions
(6, 27),  -- canSelfAssignToTasks

-- Verifier permissions
(7, 26);  -- canVerifyOrderCompletion

INSERT INTO roleProcessTasks (roleID, processID) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(3, 1),
(4, 2),
(5, 3),
(6, 4);

INSERT INTO userRoles (userID, roleID) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(6, 7);
