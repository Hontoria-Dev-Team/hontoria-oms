INSERT INTO permissions (NAME, DESCRIPTION) VALUES
('canManageStaff', 'Can add and delete staff members, and also grant or revoke permissions.');

INSERT INTO userPermissions (userID, permissionID) VALUES
(1, 1);
