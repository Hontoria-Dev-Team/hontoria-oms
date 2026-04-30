<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function findSingleStaff($username) {
        $query = "SELECT id, username, email, passwordHash, firstName, middleName, lastName, phone, createdAt, lastActivityAt
                  FROM users
                  WHERE username = :username
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAccount($id) {
        $query = "SELECT username, email, passwordHash, firstName, middleName, lastName, phone, createdAt, lastActivityAt, note
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
        $query = "
            SELECT id, username, firstName, middleName, lastName, phone, email, createdAt, lastActivityAt, note
            FROM users
            ORDER BY
                CASE
                    WHEN lastActivityAt >= NOW() - INTERVAL 15 MINUTE THEN 1
                    ELSE 2
                END,
                firstName,
                lastName,
                lastActivityAt DESC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getfilteredStaff($search, $status) {
        $where = "(CONCAT(firstName,' ',middleName,' ',lastName) LIKE :query OR username LIKE :query)";

        if ($status !== '') {
            switch ($status) {
                case 'active':
                    $where .= " AND lastActivityAt >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
                    break;
                case 'inactive':
                    $where .= " AND (lastActivityAt IS NULL OR lastActivityAt < DATE_SUB(NOW(), INTERVAL 15 MINUTE))";
                    break;
            }
        }

        $sql = "
            SELECT id, username, firstName, middleName, lastName, phone, email, createdAt, lastActivityAt, note
            FROM users
            WHERE {$where}
            ORDER BY
                CASE
                    WHEN lastActivityAt >= NOW() - INTERVAL 15 MINUTE THEN 1
                    ELSE 2
                END,
                firstName,
                lastName,
                lastActivityAt DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':query', $search . '%');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // public function updateLastLogin($userId) {
    //     $query = "UPDATE users SET lastActivityAt = NOW() WHERE id = :id";
    //     $stmt = $this->pdo->prepare($query);
    //     $stmt->bindParam(':id', $userId);
    //     return $stmt->execute();
    // }

    // public function updateOnlineStatus($userId, $status) {
    //     // The isOnline field was removed from the users table. Use lastActivityAt updates instead.
    //     return false;
    // }

    public function updateLastActiveAt() {
        $query = "UPDATE users SET lastActivityAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['id']);
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
        $query = "DELETE FROM userRoles WHERE userID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

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

    public function updateUsernote($id, $note) {
        $note = trim($note);

        if (strlen($note) > 30) {
            return 'Error: Note must be 30 characters or fewer.';
        }

        if ($note === '') {
            $query = "UPDATE users SET note = NULL WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
        } else {
            $query = "UPDATE users SET note = :note WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':note', $note);
            $stmt->bindParam(':id', $id);
        }

        $stmt->execute();

        return 'Success: User note updated.';
    }

    public function getUserPermissions($id) {
        $query = "SELECT DISTINCT permissions.id, permissions.name
                      FROM userRoles
                      JOIN rolePermissions ON rolePermissions.roleID = userRoles.roleID
                      JOIN permissions ON permissions.id = rolePermissions.permissionID
                      WHERE userRoles.userID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPermissions() {
        $query = "SELECT id, name FROM permissions ORDER BY id ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRolePermissions() {
        $query = "SELECT rolePermissions.roleID, rolePermissions.permissionID, permissions.name FROM rolePermissions
                  JOIN permissions ON permissions.id = rolePermissions.permissionID
                  ORDER BY rolePermissions.roleID ASC, rolePermissions.permissionID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRolePermissions($roleID) {
        $query = "SELECT permissions.id, permissions.name FROM rolePermissions
                  JOIN permissions ON permissions.id = rolePermissions.permissionID WHERE rolePermissions.roleID = :roleID
                  ORDER BY rolePermissions.roleID ASC, rolePermissions.permissionID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    public function getAllRoles() {
        $query = "SELECT id, name FROM roles";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserRoles() {
        $query = "SELECT userRoles.userID, userRoles.roleID, roles.name FROM userRoles JOIN roles ON roles.id = userRoles.roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRoles($userID) {
        $query = "SELECT roleID FROM userRoles WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    public function getAllRolesTally($actorID) {
        $unalterableRoles = [];
        $params = [];

        foreach ($this->getRoleManagementGovernance($this->getUserRoles($actorID)) as $rule) {
            if (!$rule['canAlter']) {
                $unalterableRoles[] = $rule['roleSubjectID'];
            }
        }

        $query = "SELECT roles.id, roles.name, COUNT(userRoles.userID) AS count FROM roles LEFT JOIN userRoles ON roles.id = userRoles.roleID";

        if (!empty($unalterableRoles)) {
            $placeholders = implode(',', array_fill(0, count($unalterableRoles), '?'));
            $query .= " WHERE roles.id NOT IN ($placeholders)";
            $params = $unalterableRoles;
        }

        $query .= " GROUP BY roles.id, roles.name
            ORDER BY COUNT(userRoles.userID) DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRoleManagementGovernance() {
        $query = "SELECT * FROM roleManagementGovernance ORDER BY roleAgentID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRoleManagementGovernance($roles) {
        $placeholders = implode(',', array_fill(0, count($roles), '?'));

        $query = "SELECT roleSubjectID, MAX(canGrant)  AS canGrant, MAX(canRevoke) AS canRevoke, MAX(canAlter)  AS canAlter, MAX(canDelete) AS canDelete
                  FROM roleManagementGovernance WHERE roleAgentID IN ($placeholders) GROUP BY roleSubjectID";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($roles);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGovernanceRulesBetweenUsers($subjectID, $actorID) {
        $subjectRoles = $this->getUserRoles($subjectID);

        $roleGovernance = $this->getRoleManagementGovernance($this->getUserRoles($actorID));

        $governances = array_filter($roleGovernance, function ($gov) use ($subjectRoles) {
            return in_array((int)$gov['roleSubjectID'], $subjectRoles);
        });

        if (empty($subjectRoles) || empty($governances)) {
            return [
                'canGrant'  => 1,
                'canRevoke' => 1,
                'canAlter'  => 1,
                'canDelete' => 1
            ];
        }

        $governanceRules = [
            'canGrant'  => 0,
            'canRevoke' => 0,
            'canAlter'  => 0,
            'canDelete' => 0
        ];

        foreach ($governances as $gov) {
            if ((int)$gov['canGrant'] === 1)  $governanceRules['canGrant'] = 1;
            if ((int)$gov['canRevoke'] === 1) $governanceRules['canRevoke'] = 1;
            if ((int)$gov['canAlter'] === 1)  $governanceRules['canAlter'] = 1;
            if ((int)$gov['canDelete'] === 1) $governanceRules['canDelete'] = 1;
        }

        return $governanceRules;
    }

    public function clearUserRoles($userID) {
        $query = "DELETE FROM userRoles WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        return $stmt->execute();
    }

    public function updateUserRoles($userID, $userRoles) {
        $this->clearUserRoles($userID);

        if (!empty($userRoles)) {
            $query = "INSERT INTO userRoles (userID, roleID) VALUES (:userID, :roleID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($userRoles); $i++) {
                $stmt->execute([
                    ':userID' => $userID,
                    ':roleID' => $userRoles[$i]
                ]);
            }
        }
    }

    public function clearRolePermissions($roleID) {
        $query = "DELETE FROM rolePermissions WHERE roleID = :roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        return $stmt->execute();
    }

    public function updateRolePermissions($roleID, $permissions) {
        $this->clearRolePermissions($roleID);

        if (!empty($permissions)) {
            $query = "INSERT INTO rolePermissions (roleID, permissionID) VALUES (:roleID, :permissionID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($permissions); $i++) {
                $stmt->execute([
                    ':roleID' => $roleID,
                    ':permissionID' => $permissions[$i]
                ]);
            }
        }
    }

    public function clearRoleManagementGovernance($roleID) {
        $query = "DELETE FROM roleManagementGovernance WHERE roleAgentID = :roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        return $stmt->execute();
    }

    public function updateRoleManagementGovernance($roleID, $rules) {
        $this->clearRoleManagementGovernance($roleID);

        if (!empty($rules)) {
            $query = "INSERT INTO roleManagementGovernance (roleSubjectID, roleAgentID, canGrant, canRevoke, canAlter, canDelete) VALUES
                      (:roleSubjectID, :roleAgentID, :canGrant, :canRevoke, :canAlter, :canDelete)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($rules); $i++) {
                $stmt->execute([
                    ':roleSubjectID' => $rules[$i]['roleSubjectID'],
                    ':roleAgentID' => $roleID,
                    ':canGrant' => $rules[$i]['canGrant'],
                    ':canRevoke' => $rules[$i]['canRevoke'],
                    ':canAlter' => $rules[$i]['canAlter'],
                    ':canDelete' => $rules[$i]['canDelete'],
                ]);
            }
        }
    }

    public function getRoleByName($name) {
        $query = "SELECT id FROM roles WHERE name = :name LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertRole($name) {
        $role = $this->getRoleByName($name);

        if ($role) {
            return false;
        }

        $query = "INSERT INTO roles (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function removeRole($id) {
        $query = "DELETE FROM rolePermissions WHERE roleID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM roleProcessTasks WHERE roleID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM roleManagementGovernance WHERE roleSubjectID = :id OR roleAgentID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM userRoles WHERE roleID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllRoleProcessTasks() {
        $query = "SELECT roleProcessTasks.roleID, roleProcessTasks.processID, processes.name FROM roleProcessTasks
                  JOIN processes ON roleProcessTasks.processID = processes.id
                  ORDER BY roleProcessTasks.roleID ASC, processes.name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserAssignableProcessTasks() {
        $query = "
            SELECT
                users.id AS userID,
                users.firstName,
                users.middleName,
                users.lastName,
                processes.id AS processID,
                processes.name AS processName,
                GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ', ') AS roles
            FROM users
            JOIN userRoles ON users.id = userRoles.userID
            JOIN roles ON userRoles.roleID = roles.id
            JOIN roleProcessTasks ON userRoles.roleID = roleProcessTasks.roleID
            JOIN processes ON roleProcessTasks.processID = processes.id
            GROUP BY
                users.id,
                users.firstName,
                users.middleName,
                users.lastName,
                processes.id,
                processes.name
            ORDER BY processes.id ASC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserProcessTasks() {
        $query = "
            SELECT
                users.id AS userID,
                users.firstName,
                users.middleName,
                users.lastName,
                userProcessTasks.orderProcessID,
                userProcessTasks.status,
                userProcessTasks.assignedAt,
                (
                    SELECT GROUP_CONCAT(DISTINCT roles.name ORDER BY roles.name SEPARATOR ', ')
                    FROM userRoles
                    JOIN roles ON userRoles.roleID = roles.id
                    JOIN roleProcessTasks ON userRoles.roleID = roleProcessTasks.roleID
                    JOIN processes ON roleProcessTasks.processID = processes.id
                    WHERE userRoles.userID = users.id
                    AND processes.id = (
                        SELECT processes.id
                        FROM orderProcess
                        JOIN orders ON orderProcess.orderID = orders.id
                        JOIN subservices ON orders.subserviceID = subservices.id
                        JOIN serviceProcess ON subservices.serviceID = serviceProcess.serviceID AND orderProcess.phase = serviceProcess.phase
                        JOIN processes ON serviceProcess.processesID = processes.id
                        WHERE orderProcess.id = userProcessTasks.orderProcessID
                        LIMIT 1
                    )
                ) AS roles
            FROM userProcessTasks
            JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
            JOIN users ON userProcessTasks.userID = users.id
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess ON subservices.serviceID = serviceProcess.serviceID AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserProcessTasksDetailed() {
        $query = "
            SELECT
                userProcessTasks.userID,
                userProcessTasks.orderProcessID,
                userProcessTasks.status,
                userProcessTasks.assignedAt,
                orders.id AS orderID,
                orders.customerName,
                subservices.name AS subserviceName,
                services.name AS serviceName,
                processes.name AS processName
            FROM userProcessTasks
            JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            JOIN serviceProcess ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRoleProcessTasks($id) {
        $roles = $this->getUserRoles($id);

        $placeholders = implode(',', array_fill(0, count($roles), '?'));

        $query = "SELECT roleProcessTasks.roleID, roleProcessTasks.processID, processes.name FROM roleProcessTasks
                  JOIN processes ON roleProcessTasks.processID = processes.id WHERE roleProcessTasks.roleID IN ($placeholders)
                  ORDER BY roleProcessTasks.roleID ASC, processes.name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($roles);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearRoleProcessTasks($roleID) {
        $query = "DELETE FROM roleProcessTasks WHERE roleID = :roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        return $stmt->execute();
    }

    public function updateRoleProcessTasks($roleID, $processes) {
        $this->clearRoleProcessTasks($roleID);

        if (!empty($processes)) {
            $query = "INSERT INTO roleProcessTasks (roleID, processID) VALUES (:roleID, :processID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($processes); $i++) {
                $stmt->execute([
                    ':roleID' => $roleID,
                    ':processID' => $processes[$i],
                ]);
            }
        }
    }

    public function getAllUsersTaskCount() {
        $query = "SELECT userID, COUNT(userID) AS taskCount FROM userProcessTasks GROUP BY userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findSingleAccountImageByID($userID) {
        $query = "SELECT imageName FROM userImages WHERE userID = :userID LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAccountImage($userID, $imageFile) {
        $storageDir = __DIR__ . '/../../Storage/AccountImages/';

        // Ensure storage directory exists
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        // Validate file extension
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowed)) {
            return "Error: Invalid file format.";
        }

        // Check for existing image
        $existing = $this->findSingleAccountImageByID($userID);

        // Generate unique filename
        $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '.' . $fileExtension;
        $targetPath = $storageDir . $newFileName;

        // Move uploaded file
        if (!move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
            return "Error: Upload failed.";
        }

        try {
            // Insert or update database record
            if ($existing) {
                $query = "UPDATE userImages SET imageName = :imageName WHERE userID = :userID";
            } else {
                $query = "INSERT INTO userImages (userID, imageName) VALUES (:userID, :imageName)";
            }
            $stmt = $this->pdo->prepare($query);
            $success = $stmt->execute([
                ':userID'   => $userID,
                ':imageName' => $newFileName
            ]);

            if (!$success) {
                // DB failed – delete the newly uploaded file
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                return "Error: Database operation failed.";
            }

            // If updating, delete the old image file
            if ($existing && !empty($existing['imageName'])) {
                $oldFilePath = $storageDir . $existing['imageName'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            return "Success: Upload successful.";
        } catch (PDOException $e) {
            // On exception, clean up the newly uploaded file
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            return "Error: " . $e->getMessage();
        }
    }

    public function getAccountImage($userID) {
        $query = "SELECT imageName FROM userImages WHERE userID = :userID LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['imageName'] : null;
    }

    public function getAllAccountImages() {
        $query = "SELECT * FROM userImages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAccountImagesMapped() {
        $map = [];

        foreach ($this->getAllAccountImages() as $item) {
            $map[$item['userID']] = $item['imageName'];
        }

        return $map;
    }

    public function getAllUserStats() {
        $query = "SELECT * FROM userStats";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUserActivityLogs() {
        $query = "SELECT * FROM userActivityLog ORDER BY loggedAt DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllMiscellaneousTasks() {
        $query = "SELECT * FROM miscellaneousTasks";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMiscellaneousTaskByUserID($userID) {
        $query = "SELECT * FROM miscellaneousTasks WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertMiscellaneousTask($assigneeID, $description) {
        // Get assignee name
        $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $assigneeID);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $middleInitial = $user['middleName'] ? substr($user['middleName'], 0, 1) . '. ' : '';
        $userFullName = $user['firstName'] . ' ' . $middleInitial . $user['lastName'];

        // Get current user name
        $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();
        $_user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_middleInitial = $_user['middleName'] ? substr($_user['middleName'], 0, 1) . '. ' : '';
        $_userFullName = $_user['firstName'] . ' ' . $_middleInitial . $_user['lastName'];

        // Insert the task
        $query = "INSERT INTO miscellaneousTasks (userID, description) VALUES (:userID, :description)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $assigneeID);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        // Log for the assignee
        $this->insertUserActivityLog(
            $assigneeID,
            'task assignment',
            'Assigned to a miscellaneous task described as: "' . $description . '" by ' . $_userFullName . '.',
            'yellow'
        );

        // Log for the assigner
        $this->insertUserActivityLog(
            $_SESSION['id'],
            'task assigning',
            'Assigned ' . $userFullName . ' to a miscellaneous task described as: "' . $description . '".',
            'yellow'
        );

        return "Success: Miscellaneous task assigned.";
    }

    public function completeMiscellaneousTask($userID) {
        $query = "SELECT description, assignedAt FROM miscellaneousTasks WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        $description = $task ? $task['description'] : 'Unknown';

        if ($task) {
            $durationInMinutes = abs(time() - strtotime($task['assignedAt'])) / 60;

            // Log for the assignee
            $this->insertUserActivityLog(
                $userID,
                'task completion',
                'Completed a miscellaneous task described as: "' . $description . '" in ' . number_format($durationInMinutes, 2) . ' minutes.',
                'green'
            );

            // Log for the current user who marked it complete
            $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
            $stmt2 = $this->pdo->prepare($query);
            $stmt2->bindParam(':id', $_SESSION['id']);
            $stmt2->execute();
            $_user = $stmt2->fetch(PDO::FETCH_ASSOC);
            $_middleInitial = $_user['middleName'] ? substr($_user['middleName'], 0, 1) . '. ' : '';
            $_userFullName = $_user['firstName'] . ' ' . $_middleInitial . $_user['lastName'];

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'task completion',
                'Marked a miscellaneous task described as: "' . $description . '" for ' . $_userFullName . ' as complete.',
                'green'
            );

            $query = "
            UPDATE userStats
            SET
                tasksCompleted = tasksCompleted + 1,
                tasksCompletedDuration = tasksCompletedDuration + :taskCompletedDuration
            WHERE userID = :id
        ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $userID,
                ':taskCompletedDuration' => $durationInMinutes
            ]);
        }

        $query = "DELETE FROM miscellaneousTasks WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        return "Success: Miscellaneous task completed.";
    }

    public function unassignMiscellaneousTask($userID) {
        $query = "SELECT description FROM miscellaneousTasks WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        $task = $stmt->fetch(PDO::FETCH_ASSOC);
        $description = $task ? $task['description'] : 'Unknown';

        $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userID);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $middleInitial = $user['middleName'] ? substr($user['middleName'], 0, 1) . '. ' : '';
        $userFullName = $user['firstName'] . ' ' . $middleInitial . $user['lastName'];

        $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['id']);
        $stmt->execute();
        $_user = $stmt->fetch(PDO::FETCH_ASSOC);
        $_middleInitial = $_user['middleName'] ? substr($_user['middleName'], 0, 1) . '. ' : '';
        $_userFullName = $_user['firstName'] . ' ' . $_middleInitial . $_user['lastName'];

        $query = "DELETE FROM miscellaneousTasks WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();

        $this->insertUserActivityLog(
            $userID,
            'task unassignment',
            'Unassigned from a miscellaneous task described as: "' . $description . '" by ' . $_userFullName . '.',
            'red'
        );

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'task unassigning',
            'Unassigned ' . $userFullName . ' from a miscellaneous task described as: "' . $description . '".',
            'red'
        );

        return "Success: Miscellaneous task unassigned.";
    }

    public function insertUserActivityLog($userID, $head, $log, $color) {
        $query = "INSERT INTO userActivityLog (userID, head, log, color) VALUES (:userID, :head, :log, :color)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':userID' => $userID,
            ':head' => strtolower($head),
            ':log' => $log,
            ':color' => strtolower($color)
        ]);
    }
}
