CREATE TABLE services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    isActive BOOLEAN DEFAULT FALSE,
    description TEXT
);

CREATE TABLE subservices (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serviceID INT UNSIGNED NOT NULL,
    name VARCHAR(50) NOT NULL,
    isActive BOOLEAN DEFAULT FALSE,
    description TEXT,
    pricePerUnit DECIMAL(10, 2) UNSIGNED NOT NULL,
    UNIQUE KEY uniqueName (serviceID, NAME),
    FOREIGN KEY (serviceID) REFERENCES services(id)
);
