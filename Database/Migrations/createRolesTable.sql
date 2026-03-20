CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE rolePermissions (
    roleID INT UNSIGNED NOT NULL,
    permissionID INT UNSIGNED NOT NULL,
    PRIMARY KEY (roleID, permissionID),
    FOREIGN KEY (roleID) REFERENCES roles(id),
    FOREIGN KEY (permissionID) REFERENCES permissions(id)
);

CREATE TABLE roleProcessTasks (
    roleID INT UNSIGNED NOT NULL,
    processID INT UNSIGNED NOT NULL,
    PRIMARY KEY (roleID, processID),
    FOREIGN KEY (roleID) REFERENCES roles(id),
    FOREIGN KEY (processID) REFERENCES processes(id)
);

CREATE TABLE userRoles (
    userID BIGINT UNSIGNED NOT NULL,
    roleID INT UNSIGNED NOT NULL,
    PRIMARY KEY (userID, roleID),
    FOREIGN KEY (userID) REFERENCES users(id),
    FOREIGN KEY (roleID) REFERENCES roles(id)
);
