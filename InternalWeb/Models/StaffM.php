<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Find staff by username
    public function findByUsername($username) {
        $query = "SELECT id, username, passwordHash, firstName, middleName, lastName, phone, isActive, lastLoginAt
                  FROM users
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Update last login timestamp
    public function updateLastLogin($userId) {
        $query = "UPDATE users SET lastLoginAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }
}
