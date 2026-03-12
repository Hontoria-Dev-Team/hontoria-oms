CREATE TABLE permissions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE userPermissions (
    userID BIGINT UNSIGNED NOT NULL,
    permissionID INT UNSIGNED NOT NULL,
    PRIMARY KEY (userID, permissionID),
    FOREIGN KEY (userID) REFERENCES users(id),
    FOREIGN KEY (permissionID) REFERENCES permissions(id)
);
