INSERT INTO roles (name) VALUES
('owner'),
('admin'),
('artist'),
('print operator'),
('heat press operator'),
('sewist'),
('verifier');

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
