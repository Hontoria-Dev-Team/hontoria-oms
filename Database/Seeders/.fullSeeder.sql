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
INSERT INTO users (id, username, email, passwordHash, firstName, middleName, lastName, phone, createdAt, lastActivityAt) VALUES
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
INSERT INTO services (name, hasDesign, hasVariableList, isActive) VALUES
('Sublimation', 1, 1, 1),
('Tarpaulin', 1, 0, 0),
('Sintra Board', 1, 0, 0);

INSERT INTO subservices (serviceID, name, isActive, pricePerUnit, description) VALUES
(1, 'Jersey', 1, 300, 'High-quality full sublimation printing on jerseys. Perfect for sports teams, events, and uniforms. Fade-resistant and durable.'),
(1, 'T-shirt', 1, 250, 'Custom sublimation printed t-shirts in any design. Great for organizations, teams, and personal use.'),
(1, 'Short', 1, 200, 'Vibrant sublimation printed shorts. Matched perfectly with our jerseys for a complete team uniform.'),
(1, 'Warmer', 1, 400, 'Sublimation warmers for players and athletes. Keeps you warm while looking professional on and off the court.'),
(1, 'Jogging Pants', 1, 400, 'Full sublimation jogging pants with any design. Comfortable, durable, and eye-catching for any team or individual.');

INSERT INTO subserviceImages (subserviceID, imageName) VALUES
-- Jersey (1-16)
(1, 'jerseyPicture1.jpg'),
(1, 'jerseyPicture2.jpg'),
(1, 'jerseyPicture3.jpg'),
(1, 'jerseyPicture4.jpg'),
(1, 'jerseyPicture5.jpg'),
(1, 'jerseyPicture6.jpg'),
(1, 'jerseyPicture7.jpg'),
(1, 'jerseyPicture8.jpg'),
(1, 'jerseyPicture9.jpg'),
(1, 'jerseyPicture10.jpg'),
(1, 'jerseyPicture11.jpg'),
(1, 'jerseyPicture12.jpg'),
(1, 'jerseyPicture13.jpg'),
(1, 'jerseyPicture14.jpg'),
(1, 'jerseyPicture15.jpg'),
(1, 'jerseyPicture16.jpg'),
-- T-shirt (1-24)
(2, 'tshirtPicture1.jpg'),
(2, 'tshirtPicture2.jpg'),
(2, 'tshirtPicture3.jpg'),
(2, 'tshirtPicture4.jpg'),
(2, 'tshirtPicture5.jpg'),
(2, 'tshirtPicture6.jpg'),
(2, 'tshirtPicture7.jpg'),
(2, 'tshirtPicture8.jpg'),
(2, 'tshirtPicture9.jpg'),
(2, 'tshirtPicture10.jpg'),
(2, 'tshirtPicture11.jpg'),
(2, 'tshirtPicture12.jpg'),
(2, 'tshirtPicture13.jpg'),
(2, 'tshirtPicture14.jpg'),
(2, 'tshirtPicture15.jpg'),
(2, 'tshirtPicture16.jpg'),
(2, 'tshirtPicture17.jpg'),
(2, 'tshirtPicture18.jpg'),
(2, 'tshirtPicture19.jpg'),
(2, 'tshirtPicture20.jpg'),
(2, 'tshirtPicture21.jpg'),
(2, 'tshirtPicture22.jpg'),
(2, 'tshirtPicture23.jpg'),
(2, 'tshirtPicture24.jpg'),
-- Short (1-10)
(3, 'short1.jpg'),
(3, 'short2.jpg'),
(3, 'short3.jpg'),
(3, 'short4.jpg'),
(3, 'short5.jpg'),
(3, 'short6.jpg'),
(3, 'short7.jpg'),
(3, 'short8.jpg'),
(3, 'short9.jpg'),
(3, 'short10.jpg'),
-- Warmer (1-11)
(4, 'warmer1.jpg'),
(4, 'warmer2.jpg'),
(4, 'warmer3.jpg'),
(4, 'warmer4.jpg'),
(4, 'warmer5.jpg'),
(4, 'warmer6.jpg'),
(4, 'warmer7.jpg'),
(4, 'warmer8.jpg'),
(4, 'warmer9.jpg'),
(4, 'warmer10.jpg'),
(4, 'warmer11.jpg'),
-- Jogging Pants (1-5)
(5, 'pants1.jpg'),
(5, 'pants2.jpg'),
(5, 'pants3.jpg'),
(5, 'pants4.jpg'),
(5, 'pants5.jpg');

-- Seed processes before serviceProcess because of the foreign key dependency
INSERT INTO processes (name, minAssignDefault, maxAssignDefault, hasGCAccess, designAccess, variableListAccess) VALUES
('Designing', 1, 3, 1, 'view & update', 'view & update'),
('Printing', 1, 3, 0, 'view only', 'view only'),
('Heat Press', 1, 3, 0, 'view only', 'view only'),
('Sewing', 3, 10, 0, 'view only', 'no access');

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
('canSelfUnassignToTasks'),          -- 28
('canAssignMiscTasksToStaff'),       -- 29
('canUnassignMiscTasksToStaff'),     -- 30
('canSelfAssignToMiscTasks'),        -- 31
('canSelfUnassignToMiscTasks'),      -- 32

-- Inventory Related Permissions
('canViewInventory'),                -- 33
('canCreateItems'),                  -- 34
('canDeleteItems'),                  -- 35
('canModifyItems'),                  -- 36
('canUpdateItemQuantity'),           -- 37

-- Sales Related Permissions
('canViewSales'),                    -- 38
('canManageSalesRecords');           -- 39


INSERT INTO rolePermissions (roleID, permissionID) VALUES
-- Owner permissions has all permissions
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
(1, 29),  -- canAssignMiscTasksToStaff
(1, 30),  -- canUnassignMiscTasksToStaff
(1, 31),  -- canSelfAssignToMiscTasks
(1, 32),  -- canSelfUnassignToMiscTasks
(1, 33),  -- canViewInventory
(1, 34),  -- canCreateItems
(1, 35),  -- canDeleteItems
(1, 36),  -- canModifyItems
(1, 37),  -- canUpdateItemQuantity
(1, 38),  -- canViewSales
(1, 39),  -- canManageSalesRecords

-- Admin permissions add Task Related, Inventory Related, and Sales Related permissions
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
(2, 29),  -- canAssignMiscTasksToStaff
(2, 30),  -- canUnassignMiscTasksToStaff
(2, 31),  -- canSelfAssignToMiscTasks
(2, 32),  -- canSelfUnassignToMiscTasks
(2, 33),  -- canViewInventory
(2, 34),  -- canCreateItems
(2, 35),  -- canDeleteItems
(2, 36),  -- canModifyItems
(2, 37),  -- canUpdateItemQuantity
(2, 38),  -- canViewSales
(2, 39),  -- canManageSalesRecords

-- Artist permissions
(3, 27),  -- canSelfAssignToTasks

-- Print Operator permissions
(4, 27),  -- canSelfAssignToTasks

-- Heat Press Operator permissions
(5, 27),  -- canSelfAssignToTasks

-- Sewist permissions
(6, 27),  -- canSelfAssignToTasks

-- Verifier permissions
(7, 19),  -- canViewOrdersPage
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

INSERT INTO inventory (name, minQuantity, maxAvgConsumption) VALUES
('paper', 20, 10),
('cloth', 10, 5);

INSERT INTO inventoryRecords (date, inventoryID, quantity, consumption, added) VALUES
--  For Paper
('2026-03-20', 1, 63, 7, 70),
('2026-03-21', 1, 58, 5, 0),
('2026-03-23', 1, 53, 5, 0),
('2026-03-24', 1, 44, 9, 0),
('2026-03-25', 1, 38, 6, 0),
('2026-03-26', 1, 31, 7, 0),
('2026-03-27', 1, 27, 4, 0),
('2026-03-28', 1, 71, 6, 50),
('2026-03-30', 1, 60, 11, 0),
('2026-03-31', 1, 53, 7, 0),
('2026-04-01', 1, 48, 5, 0),
('2026-04-02', 1, 43, 5, 0),
('2026-04-03', 1, 88, 5, 50),
('2026-04-04', 1, 80, 8, 0),
('2026-04-06', 1, 73, 7, 0),
('2026-04-07', 1, 65, 8, 0),
('2026-04-08', 1, 53, 12, 0),
('2026-04-09', 1, 44, 9, 0),
('2026-04-10', 1, 89, 15, 60),
('2026-04-11', 1, 79, 10, 0),
('2026-04-13', 1, 72, 7, 0),
('2026-04-14', 1, 67, 5, 0),
('2026-04-15', 1, 62, 5, 0),
('2026-04-16', 1, 54, 8, 0),
('2026-04-17', 1, 78, 6, 30),
('2026-04-18', 1, 70, 8, 0),
('2026-04-20', 1, 62, 8, 0),
('2026-04-21', 1, 53, 9, 0),
('2026-04-22', 1, 47, 6, 0),
('2026-04-23', 1, 34, 13, 0),
('2026-04-24', 1, 77, 7, 50),

-- For Cloth
('2026-03-20', 2, 26, 4, 30),
('2026-03-21', 2, 23, 3, 0),
('2026-03-23', 2, 19, 4, 0),
('2026-03-24', 2, 16, 3, 0),
('2026-03-25', 2, 12, 4, 0),
('2026-03-26', 2, 9, 3, 0),
('2026-03-27', 2, 5, 4, 0),
('2026-03-28', 2, 32, 3, 30),
('2026-03-30', 2, 28, 4, 0),
('2026-03-31', 2, 25, 3, 0),
('2026-04-01', 2, 21, 4, 0),
('2026-04-02', 2, 18, 3, 0),
('2026-04-03', 2, 43, 5, 30),
('2026-04-04', 2, 39, 4, 0),
('2026-04-06', 2, 36, 3, 0),
('2026-04-07', 2, 32, 4, 0),
('2026-04-08', 2, 29, 3, 0),
('2026-04-09', 2, 22, 7, 0),
('2026-04-10', 2, 48, 4, 30),
('2026-04-11', 2, 45, 3, 0),
('2026-04-13', 2, 41, 4, 0),
('2026-04-14', 2, 38, 3, 0),
('2026-04-15', 2, 34, 4, 0),
('2026-04-16', 2, 31, 3, 0),
('2026-04-17', 2, 57, 4, 30),
('2026-04-18', 2, 54, 3, 0),
('2026-04-20', 2, 50, 4, 0),
('2026-04-21', 2, 47, 3, 0),
('2026-04-22', 2, 43, 4, 0),
('2026-04-23', 2, 35, 8, 0),
('2026-04-24', 2, 61, 4, 30);

INSERT INTO salesRecords (date, isInflow, type, description, value) VALUES
('2026-04-02', 1, 'Stickers & Decals Car', 'Order #5 Payment', 3000),
('2026-04-03', 0, 'Inventory Expense', 'Paper Expense', 500),
('2026-04-03', 0, 'Inventory Expense', 'Cloth Expense', 600),
('2026-04-08', 1, 'Sublimation Shorts', 'Order #2 Payment', 1000),
('2026-04-10', 0, 'Inventory Expense', 'Paper Expense', 600),
('2026-04-10', 1, 'Stickers & Decals Motorcycle', 'Order #5 Payment', 3000),
('2026-04-10', 0, 'Inventory Expense', 'Cloth Expense', 600),
('2026-04-15', 1, 'Sublimation Jersey', 'Order #1 Payment', 1000),
('2026-04-16', 1, 'Tarpaulin Birthday', 'Order #4 Payment', 8000),
('2026-04-17', 0, 'Inventory Expense', 'Paper Expense', 300),
('2026-04-17', 0, 'Inventory Expense', 'Cloth Expense', 600),
('2026-04-21', 1, 'Sublimation Jersey', 'Order #7 Payment', 2500),
('2026-04-23', 0, 'Service Bills', 'Electricity Bill', 12000),
('2026-04-24', 0, 'Inventory Expense', 'Paper Expense', 300),
('2026-04-24', 0, 'Inventory Expense', 'Cloth Expense', 600);

-- Seeder for Order #1 (T-shirt by Adrian Rojer B. Lambas)

-- 1. Insert the order and capture its ID
INSERT INTO orders (subserviceID, customerName, messengerGCLink, priceTotal, createdAt, deadlineAt) VALUES
(2, 'Adrian Rojer B. Lambas', 'https://m.me/j/AbbjSDFfpAcqXGez/?send_source=gc%3Acopy_invite_link_c', 20000, NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY));

SET @order_id = LAST_INSERT_ID();

-- 2. Order Groups (S:10, M:12, L:7, XL:2)
INSERT INTO orderGroups (orderID, description, quantity) VALUES
(@order_id, 'Small', 10),
(@order_id, 'Medium', 12),
(@order_id, 'Large', 7),
(@order_id, 'Extra Large', 2);

-- 3. Order Process (serviceID=1, phases 1-4, minAssign=1, maxAssign=5)
INSERT INTO orderProcess (orderID, phase, minAssign, maxAssign, status) VALUES
(@order_id, 1, 1, 5, 'active'),
(@order_id, 2, 1, 5, 'pending'),
(@order_id, 3, 1, 5, 'pending'),
(@order_id, 4, 1, 5, 'pending');

-- 4. Order Design
INSERT INTO orderDesigns (orderID, imageName) VALUES
(@order_id, 'exampleOrderDesign.jpg');

-- 5. Variable List
INSERT INTO variableLists (orderID) VALUES (@order_id);

INSERT INTO variableListColumns (orderID, columnName, displayOrder) VALUES
(@order_id, 'group', 1),
(@order_id, 'last name', 2);

SET @col_group_id = (SELECT id FROM variableListColumns WHERE orderID = @order_id AND columnName = 'group');
SET @col_lname_id = (SELECT id FROM variableListColumns WHERE orderID = @order_id AND columnName = 'last name');

-- Generate 31 row numbers (1..31)
INSERT INTO variableListValues (orderID, rowNumber, columnID, valueText)
SELECT @order_id, r.rn, c.col_id,
       CASE WHEN c.col_id = @col_group_id THEN
            CASE WHEN r.rn <= 10 THEN 'small'
                 WHEN r.rn <= 22 THEN 'medium'
                 WHEN r.rn <= 29 THEN 'large'
                 ELSE 'extra large'
            END
            ELSE
            ELT(FLOOR(1 + RAND() * 31),
                'Dela Cruz','Reyes','Santos','Gonzales','Bautista','Torres','Ramos',
                'Aquino','Fernandez','Villanueva','Mendoza','Aguirre','Castro','Velasco',
                'Marquez','Salvador','Garcia','Cruz','Rivera','Panganiban','De Leon',
                'Cortez','Mercado','Ocampo','Suarez','Javier','Sarmiento','Montemayor',
                'Abad','Reyes','De Guzman')
       END
FROM (
    SELECT n AS rn FROM (
        SELECT a.N + b.N * 10 + 1 AS n
        FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
              UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a
        CROSS JOIN (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) b
        WHERE a.N + b.N * 10 < 31
    ) nums
) r
CROSS JOIN (SELECT @col_group_id AS col_id UNION SELECT @col_lname_id) c
ORDER BY r.rn, c.col_id;

-- Row checks for all 31 rows (unchecked)
INSERT INTO variableListRowChecks (orderID, rowNumber, isChecked)
SELECT @order_id, r.rn, FALSE
FROM (
    SELECT n AS rn FROM (
        SELECT a.N + b.N * 10 + 1 AS n
        FROM (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4
              UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) a
        CROSS JOIN (SELECT 0 AS N UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) b
        WHERE a.N + b.N * 10 < 31
    ) nums
) r
ORDER BY r.rn;

INSERT INTO publicOrderPages (orderCode, orderID) VALUES
('a1b9e4d7c3f8a2b6d0e51714558145', @order_id);
