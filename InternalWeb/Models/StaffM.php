<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findSingleStaff($username) {
        $query = "SELECT id, username, passwordHash, firstName, middleName, lastName, phone, isActive, lastLoginAt
                  FROM users
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function authenticate($username, $password) {
        $user = $this->findSingleStaff($username);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['passwordHash'])) {
            return false;
        }

        $this->updateLastLogin($user['id']);
        return $user;
    }

    public function getAllStaff() {
        $query = "SELECT username, firstName, middleName, lastName, isActive, isOnline FROM users";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($userId) {
        $query = "UPDATE users SET lastLoginAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function updateOnlineStatus($userId) {
        $query = "UPDATE users SET isOnline = !isOnline WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function getfilteredStaff($search, $status) {
        $params = [];

        $where = "(CONCAT(firstName,' ',middleName,' ',lastName) LIKE :name1 OR " .
            "CONCAT(firstName,' ',middleName,' ',lastName) LIKE :name2)";
        $params['name1'] = $search . '%';
        $params['name2'] = '%' . $search . '%';

        if ($status !== '') {
            switch ($status) {
                case 'active':
                    $where .= ' AND isActive = 1 AND isOnline = 1';
                    break;
                case 'idle':
                    $where .= ' AND isActive = 0 AND isOnline = 1';
                    break;
                case 'offline':
                    $where .= ' AND isOnline = 0';
                    break;
            }
        }

        $sql = "SELECT username, firstName, middleName, lastName, isActive, isOnline
            FROM users
            WHERE {$where}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertAccount($username, $firstName, $middleName, $lastName, $phoneNumber, $emailAddress) {
        $user = $this->findSingleStaff($username);

        if ($user) {
            return false;
        }

        $query = "INSERT INTO users (username, email, passwordHash, firstName, middleName, lastName, phone) VALUES
            (:username, :email, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', :firstName, :middleName, :lastName, :phoneNumber);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':middleName', $middleName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':email', $emailAddress);
        return $stmt->execute();
    }
}
