CREATE TABLE processes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    minAssignDefault TINYINT NOT NULL DEFAULT 1,
    maxAssignDefault TINYINT NOT NULL DEFAULT 10,
    hasGCAccess BOOLEAN DEFAULT FALSE,
    designAccess VARCHAR(20) DEFAULT 'no access',
    variableListAccess VARCHAR(20) DEFAULT 'no access',
    description TEXT
);

CREATE TABLE serviceProcess (
    serviceID INT UNSIGNED NOT NULL,
    processesID INT UNSIGNED NOT NULL,
    phase TINYINT UNSIGNED NOT NULL,
    PRIMARY KEY (serviceID, processesID),
    FOREIGN KEY (serviceID) REFERENCES services(id),
    FOREIGN KEY (processesID) REFERENCES processes(id)
);
