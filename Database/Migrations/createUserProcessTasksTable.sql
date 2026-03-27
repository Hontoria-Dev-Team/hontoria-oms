CREATE TABLE userProcessTasks (
    userID BIGINT UNSIGNED NOT NULL,
    orderProcessID BIGINT UNSIGNED NOT NULL,
    assignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userID, orderProcessID),
    FOREIGN KEY (userID) REFERENCES users(id),
    FOREIGN KEY (orderProcessID) REFERENCES orderProcess(id)
);
