CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subserviceID INT UNSIGNED NOT NULL,
    customerName VARCHAR(100) NOT NULL,
    messengerGCLink VARCHAR(255) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deadlineAt TIMESTAMP NULL,
    FOREIGN KEY (subserviceID) REFERENCES subservices(id),
    UNIQUE KEY uniqueName (subserviceID, customerName, createdAt)
);
