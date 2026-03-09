<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findSingleStaff($username) {
        $query = "SELECT id, username, email, passwordHash, firstName, middleName, lastName, phone, isActive, lastLoginAt
                  FROM users
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccount($id) {
        $query = "SELECT username, email, passwordHash, firstName, middleName, lastName, phone, isActive, lastLoginAt
                  FROM users
                  WHERE id = :id
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
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

        return $user;
    }

    public function getStaffList() {
        $query = "SELECT id, username, firstName, middleName, lastName, isActive, isOnline
                  FROM users
                  ORDER BY
                  CASE
                      WHEN isActive = 1 AND isOnline = 1 THEN 1
                      WHEN isActive = 0 AND isOnline = 1 THEN 2
                      ELSE 3
                  END,
                  firstName, lastName";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $sql = "SELECT id, username, firstName, middleName, lastName, isActive, isOnline
            FROM users
            WHERE {$where}
            ORDER BY
                CASE
                    WHEN isActive = 1 AND isOnline = 1 THEN 1
                    WHEN isActive = 0 AND isOnline = 1 THEN 2
                    ELSE 3
                END,
                firstName, lastName";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateLastLogin($userId) {
        $query = "UPDATE users SET lastLoginAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    public function updateOnlineStatus($userId, $status) {
        $query = "UPDATE users SET isOnline = :onlineStatus WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->bindParam(':onlineStatus', $status);
        return $stmt->execute();
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

    public function removeAccount($id) {
        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateUsername($id, $username) {
        $user = $this->findSingleStaff($username);

        if ($user) {
            return false;
        }

        $user = $this->getAccount($id);
        $query = "UPDATE users SET username = :username WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateContacts($id, $phoneNumber, $email) {
        $query = "UPDATE users SET phone = :phone, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':phone', $phoneNumber);
        $stmt->bindParam(':email', $email);
        return $stmt->execute();
    }

    public function updatePassword($id, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET passwordHash = :passwordHash WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':passwordHash', $hash);
        return $stmt->execute();
    }

    public function getUserPermissions($id) {
        $query = "SELECT perms.name
                  FROM permissions perms
                  JOIN userPermissions userPerms ON perms.id = userPerms.permissionID
                  WHERE userPerms.userID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function grantPermissions($id, $permissions) {
        $query = "DELETE FROM userPermissions WHERE userID = :id;";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        foreach ($permissions as $permission) {
            $query = "INSERT INTO userPermissions (userID, permissionID) VALUES (:id, :permission);";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':permission', $permission);
            $stmt->execute();
        }
    }
}
