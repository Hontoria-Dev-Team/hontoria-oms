CREATE TABLE services (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    hasDesign BOOLEAN DEFAULT FALSE,
    hasVariableList BOOLEAN DEFAULT FALSE,
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

CREATE TABLE subserviceImages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subserviceID INT UNSIGNED NOT NULL,
    imageName VARCHAR(255) NOT NULL,
    FOREIGN KEY (subserviceID) REFERENCES subservices(id)
);
