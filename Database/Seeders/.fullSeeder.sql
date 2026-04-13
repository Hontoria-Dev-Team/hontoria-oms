USE HontoriaPrintingDB;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE userProcessTasks;
TRUNCATE TABLE userRoles;
TRUNCATE TABLE roleProcessTasks;
TRUNCATE TABLE rolePermissions;
TRUNCATE TABLE userPermissions;
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
SET FOREIGN_KEY_CHECKS = 1;

-- Seed users first
INSERT INTO users (id, username, email, passwordHash, firstName, middleName, lastName, phone, createdAt, lastLoginAt) VALUES
(1, 'owner', 'owner@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Juan', 'Carlos', 'Dela Cruz', '+639171234567', '2025-01-01 00:00:00', '2026-02-20 08:30:00'),
(2, 'ana', 'ana@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ana', 'Marie', 'Santos', '+639171111111', '2025-01-05 00:00:00', '2026-02-21 09:00:00'),
(3, 'ben', 'ben@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ben', 'Joseph', 'Reyes', '+639172222222', '2025-01-10 00:00:00', '2026-02-21 08:45:00'),
(4, 'carlo', 'carlo@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carlo', 'Antonio', 'Gonzales', '+639173333333', '2025-01-15 00:00:00', '2026-02-21 10:15:00'),
(5, 'diana', 'diana@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Diana', 'Lopez', 'Bautista', '+639174444444', '2025-01-20 00:00:00', '2026-02-20 16:30:00'),
(6, 'edu', 'edu@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Eduardo', 'Martin', 'Torres', '+639175555555', '2025-01-25 00:00:00', '2026-02-21 07:30:00'),
(7, 'fátima', 'fatima@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Fátima', 'Cruz', 'Gomez', '+639176666666', '2025-02-01 00:00:00', '2026-02-21 11:00:00'),
(8, 'gab', 'gab@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Gabriel', 'Santos', 'Diaz', '+639177777777', '2025-02-05 00:00:00', '2026-02-20 14:20:00'),
(9, 'hannah', 'hannah@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Hannah', 'Rose', 'Mendoza', '+639178888888', '2025-02-10 00:00:00', '2026-02-21 09:45:00'),
(10, 'ian', 'ian@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ian', 'Patrick', 'Cruz', '+639179999999', '2025-02-15 00:00:00', '2026-02-21 08:00:00'),
(11, 'jenny', 'jenny@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jenny', 'Lyn', 'Aquino', '+639180000000', '2025-02-20 00:00:00', '2026-02-21 12:30:00'),
(12, 'ken', 'ken@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Kenneth', 'Paul', 'Javier', '+639181111111', '2025-02-25 00:00:00', '2026-02-20 13:45:00'),
(13, 'lara', 'lara@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Lara', 'Kim', 'Ocampo', '+639182222222', '2025-03-01 00:00:00', '2026-02-21 10:00:00'),
(14, 'miguel', 'miguel@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Miguel', 'Angel', 'Perez', '+639183333333', '2025-03-05 00:00:00', '2026-02-21 07:15:00'),
(15, 'nina', 'nina@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Nina', 'Grace', 'Salazar', '+639184444444', '2025-03-10 00:00:00', '2026-02-21 11:30:00'),
(16, 'oliver', 'oliver@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Oliver', 'James', 'Villanueva', '+639185555555', '2025-03-15 00:00:00', '2026-02-20 15:45:00'),
(17, 'paula', 'paula@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Paula', 'May', 'Delgado', '+639186666666', '2025-03-20 00:00:00', '2026-02-21 09:20:00'),
(18, 'quinny', 'quinny@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Quinito', 'Ramos', 'Flores', '+639187777777', '2025-03-25 00:00:00', '2026-02-21 08:30:00'),
(19, 'rosa', 'rosa@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rosa', 'Mae', 'Alvarez', '+639188888888', '2025-03-30 00:00:00', '2026-02-21 10:45:00'),
(20, 'sam', 'sam@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Samuel', 'David', 'Castro', '+639189999999', '2025-04-01 00:00:00', '2026-02-20 17:00:00');

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
('owner'),
('admin'),
('artist'),
('print operator'),
('heat press operator'),
('sewist'),
('verifier');

INSERT INTO roleManagementGovernance (roleSubjectID, roleAgentID, canGrant, canRevoke, canAlter, canDelete) VALUES
(1, 1, 1, 0, 0, 0),
(1, 2, 0, 0, 0, 0),
(2, 1, 1, 1, 1, 1),
(2, 2, 1, 0, 0, 0);

INSERT INTO permissions (name) VALUES
('canViewStaffList'),
('isHiddenFromStaffList'),
('canCreateUserAccounts'),
('canDeleteUserAccounts'),
('canAlterAccountRoles'),

('canCreateRoles'),
('canDeleteRoles'),
('canAlterRoles'),

('canViewServiceList'),
('canCreateServices'),
('canDeleteServices'),
('canAlterServiceStatus'),
('canAlterServiceProcess'),
('canCreateServiceProcesses'),
('canCreateSubservices'),
('canDeleteSubservices'),
('canAlterSubservicePricing'),
('canAlterSubserviceDescription'),

('canViewOrders'),
('canCreateOrders'),
('canApplyDiscountToOrders'),
('canDeleteOrders'),
('canModifyOrders'),
('canAssignStaffToOrders'),
('canVerifyOrderCompletion');

INSERT INTO rolePermissions (roleID, permissionID) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 20),
(1, 21),
(1, 22),
(1, 23),
(1, 24),
(1, 25),

(2, 1),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(2, 9),
(2, 10),
(2, 11),
(2, 12),
(2, 13),
(2, 14),
(2, 15),
(2, 16),
(2, 17),
(2, 18),
(2, 19),
(2, 20),
(2, 21),
(2, 22),
(2, 23),
(2, 24),
(2, 25),

(3, 19),
(3, 20),

(4, 19),
(4, 20),

(5, 19),
(5, 20),

(6, 19),
(6, 20),

(7, 25);

INSERT INTO roleProcessTasks (roleID, processID) VALUES
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

-- Seed orders last, after subservices exist
INSERT INTO orders (subserviceID, customerName, messengerGCLink) VALUES
(1, 'John Marc', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c'),
(2, 'Adrian Newey', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c'),
(2, 'Allysa', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c'),
(2, 'Ben', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c'),
(4, 'Villanueve', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c'),
(4, 'Kilby Moro', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c');
