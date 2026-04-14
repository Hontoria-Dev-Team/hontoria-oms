DROP DATABASE IF EXISTS HontoriaPrintingDB;
CREATE DATABASE HontoriaPrintingDB;

USE HontoriaPrintingDB;

DROP TABLE IF EXISTS userProcessTasks;
DROP TABLE IF EXISTS userRoles;
DROP TABLE IF EXISTS roleProcessTasks;
DROP TABLE IF EXISTS rolePermissions;
DROP TABLE IF EXISTS userPermissions;
DROP TABLE IF EXISTS permissions;
DROP TABLE IF EXISTS roleManagementGovernance;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS orderDesigns;
DROP TABLE IF EXISTS orderProcess;
DROP TABLE IF EXISTS orderGroups;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS serviceProcess;
DROP TABLE IF EXISTS processes;
DROP TABLE IF EXISTS subserviceImages;
DROP TABLE IF EXISTS subservices;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS orderTasksAssignmentArchive;
DROP TABLE IF EXISTS orderDesignArchive;
DROP TABLE IF EXISTS orderGroupArchive;
DROP TABLE IF EXISTS orderArchive;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL,
    passwordHash VARCHAR(255) NOT NULL,
    firstName VARCHAR(100) NOT NULL,
    middleName VARCHAR(100) NULL,
    lastName VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    isActive BOOLEAN DEFAULT FALSE,
    isOnline BOOLEAN DEFAULT FALSE,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lastLoginAt TIMESTAMP NULL
);

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

CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    subserviceID INT UNSIGNED NOT NULL,
    customerName VARCHAR(100) NOT NULL,
    messengerGCLink VARCHAR(255) NOT NULL,
    priceTotal DECIMAL(10, 2) NOT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deadlineAt TIMESTAMP NULL,
    FOREIGN KEY (subserviceID) REFERENCES subservices(id),
    UNIQUE KEY uniqueName (subserviceID, customerName, createdAt)
);

CREATE TABLE orderGroups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orderID BIGINT UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(id)
);

CREATE TABLE orderProcess (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orderID BIGINT UNSIGNED NOT NULL,
    phase TINYINT UNSIGNED NOT NULL,
    minAssign  TINYINT NOT NULL DEFAULT 1,
    maxAssign TINYINT NOT NULL DEFAULT 10,
    status VARCHAR(20) NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(id)
);

CREATE TABLE orderDesigns (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    orderID BIGINT UNSIGNED UNIQUE NOT NULL,
    imageName VARCHAR(255) NOT NULL,
    approved BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (orderID) REFERENCES orders(id)
);

CREATE TABLE roles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL,
    description TEXT
);

CREATE TABLE roleManagementGovernance (
    roleSubjectID INT UNSIGNED NOT NULL,
    roleAgentID INT UNSIGNED NOT NULL,
    canGrant BOOLEAN DEFAULT FALSE,
    canRevoke BOOLEAN DEFAULT FALSE,
    canAlter BOOLEAN DEFAULT FALSE,
    canDelete BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (roleSubjectID, roleAgentID),
    FOREIGN KEY (roleSubjectID) REFERENCES roles(id),
    FOREIGN KEY (roleAgentID) REFERENCES roles(id)
);

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

CREATE TABLE userProcessTasks (
    userID BIGINT UNSIGNED NOT NULL,
    orderProcessID BIGINT UNSIGNED NOT NULL,
    status VARCHAR(20) DEFAULT 'pending',
    assignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (userID, orderProcessID),
    FOREIGN KEY (userID) REFERENCES users(id),
    FOREIGN KEY (orderProcessID) REFERENCES orderProcess(id)
);

CREATE TABLE orderArchive (
    id BIGINT UNSIGNED PRIMARY KEY,
    serviceName VARCHAR(50) NOT NULL,
    subserviceName VARCHAR(50) NOT NULL,
    customerName VARCHAR(100) NOT NULL,
    messengerGCLink VARCHAR(255) NOT NULL,
    priceTotal DECIMAL(10, 2) NOT NULL,
    createdAt TIMESTAMP NULL,
    deadlineAt TIMESTAMP NULL,
    archivedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    isCompleted BOOLEAN DEFAULT FALSE
);

CREATE TABLE orderTasksAssignmentArchive (
    orderArchiveID BIGINT UNSIGNED NOT NULL,
    userFirstName VARCHAR(100) NOT NULL,
    userMiddleName VARCHAR(100) NULL,
    userLastName VARCHAR(100) NOT NULL,
    processName VARCHAR(50) NOT NULL,
    processPhase VARCHAR(50) NOT NULL,
    assignedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (orderArchiveID) REFERENCES orderArchive(id)
);

CREATE TABLE orderDesignArchive (
    orderArchiveID BIGINT UNSIGNED NOT NULL,
    imageName VARCHAR(255) NOT NULL,
    PRIMARY KEY (orderArchiveID, imageName),
    FOREIGN KEY (orderArchiveID) REFERENCES orderArchive(id)
);

CREATE TABLE orderGroupArchive (
    orderArchiveID BIGINT UNSIGNED NOT NULL,
    DESCRIPTION VARCHAR(255) NOT NULL,
    units INT UNSIGNED NOT NULL,
    PRIMARY KEY (orderArchiveID, DESCRIPTION),
    FOREIGN KEY (orderArchiveID) REFERENCES orderArchive(id)
);
