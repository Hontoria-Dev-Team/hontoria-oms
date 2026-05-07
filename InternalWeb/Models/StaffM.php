<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    //  USER LOOKUP & AUTHENTICATION
    // ================================================================

    // Look up a single user by their username.
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

    // Get full account details for a given user id.
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

    // Verify username and password. Returns user array on success, false otherwise.
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

    // ================================================================
    //  ACCOUNT LOCKOUT / BRUTE-FORCE PROTECTION
    // ================================================================

    // Check whether a user is currently locked out.
    public function isUserLocked($userId) {
        $query = "SELECT lockedUntil FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !$user['lockedUntil']) {
            return false;
        }

        // Lock period has expired?
        if (strtotime($user['lockedUntil']) <= time()) {
            return false;
        }

        return true;
    }

    // Return a human-readable string for the remaining lock time (days, hours, minutes, seconds).
    public function getFormattedLockTimeRemaining($userId) {
        $remainingSeconds = $this->getLockTimeRemaining($userId);

        if ($remainingSeconds <= 0) {
            return "0 seconds";
        }

        $days = intdiv($remainingSeconds, 86400);
        $remainingSeconds %= 86400;

        $hours = intdiv($remainingSeconds, 3600);
        $remainingSeconds %= 3600;

        $minutes = intdiv($remainingSeconds, 60);
        $seconds = $remainingSeconds % 60;

        $parts = [];
        if ($days > 0) $parts[] = $days . " day" . ($days !== 1 ? "s" : "");
        if ($hours > 0) $parts[] = $hours . " hour" . ($hours !== 1 ? "s" : "");
        if ($minutes > 0) $parts[] = $minutes . " minute" . ($minutes !== 1 ? "s" : "");
        if ($seconds > 0) $parts[] = $seconds . " second" . ($seconds !== 1 ? "s" : "");

        return implode(", ", $parts);
    }

    // Get remaining lock time in seconds (0 if not locked).
    public function getLockTimeRemaining($userId) {
        $query = "SELECT lockedUntil FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !$user['lockedUntil']) {
            return 0;
        }

        $remaining = strtotime($user['lockedUntil']) - time();
        return max(0, $remaining);
    }

    // Increment failed login attempts and apply exponential lockout on multiples of 5 (5,10,15…).
    public function incrementFailedAttempts($userId) {
        $query = "SELECT failedAttempts FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentAttempts = $user['failedAttempts'] ?? 0;
        $newAttempts = $currentAttempts + 1;

        // Lock only when attempts reach a multiple of 5
        if ($newAttempts % 5 === 0) {
            // Determine which tier (5→tier1, 10→tier2, …)
            $tier = $newAttempts / 5;
            // Exponentially increasing timeout: 30s * 2^(tier-1)
            $timeoutSeconds = 30 * pow(2, $tier - 1);
            $lockedUntil = date('Y-m-d H:i:s', time() + $timeoutSeconds);
        } else {
            $lockedUntil = null;
        }

        // Update failed attempts and lock time
        $query = "UPDATE users SET failedAttempts = :attempts, lockedUntil = :lockedUntil WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':attempts', $newAttempts);
        $stmt->bindParam(':lockedUntil', $lockedUntil);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        // Log every 5 failed attempts milestone
        if ($newAttempts % 5 === 0) {
            $this->insertUserActivityLog($userId, "Login Failure", "Login Failure {$newAttempts} times", "red");
        }

        return $newAttempts;
    }

    // Reset failed login counter after successful login.
    public function resetFailedAttempts($userId) {
        $query = "UPDATE users SET failedAttempts = 0, lockedUntil = NULL WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $userId);
        return $stmt->execute();
    }

    // ================================================================
    //  STAFF LISTS
    // ================================================================

    // Get all staff ordered by online activity and busy/idle status.
    public function getStaffList() {
        $query = "
            SELECT id, username, firstName, middleName, lastName, phone, email, createdAt, lastActivityAt, note
            FROM users
            ORDER BY
                CASE
                    WHEN lastActivityAt >= DATE_SUB(NOW(), INTERVAL 15 MINUTE) THEN
                        CASE
                            WHEN (SELECT COUNT(*) FROM userProcessTasks WHERE userProcessTasks.userID = users.id) > 0 OR (SELECT COUNT(*) FROM miscellaneousTasks WHERE miscellaneousTasks.userID = users.id) > 0 THEN 1
                            ELSE 2
                        END
                    ELSE
                        CASE
                            WHEN (SELECT COUNT(*) FROM userProcessTasks WHERE userProcessTasks.userID = users.id) > 0 OR (SELECT COUNT(*) FROM miscellaneousTasks WHERE miscellaneousTasks.userID = users.id) > 0 THEN 3
                            ELSE 4
                        END
                END,
                firstName ASC,
                lastName ASC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Filter staff by search term, online status, activity status and/or role.
    public function getfilteredStaff($search, $onlineStatus = '', $activityStatus = '', $roleId = '') {
        $where = "(CONCAT(firstName,' ',middleName,' ',lastName) LIKE :query OR username LIKE :query)";
        $params = [':query' => $search . '%'];

        // Online status filter: active = seen in last 15 minutes, offline = not seen
        if ($onlineStatus === 'active') {
            $where .= " AND lastActivityAt >= DATE_SUB(NOW(), INTERVAL 15 MINUTE)";
        } elseif ($onlineStatus === 'offline') {
            $where .= " AND (lastActivityAt IS NULL OR lastActivityAt < DATE_SUB(NOW(), INTERVAL 15 MINUTE))";
        }

        // Activity status: busy = has tasks, idle = no tasks
        if ($activityStatus === 'busy') {
            $where .= " AND (SELECT COUNT(*) FROM userProcessTasks WHERE userProcessTasks.userID = users.id) > 0 OR (SELECT COUNT(*) FROM miscellaneousTasks WHERE miscellaneousTasks.userID = users.id) > 0";
        } elseif ($activityStatus === 'idle') {
            $where .= " AND (SELECT COUNT(*) FROM userProcessTasks WHERE userProcessTasks.userID = users.id) = 0 AND (SELECT COUNT(*) FROM miscellaneousTasks WHERE miscellaneousTasks.userID = users.id) = 0";
        }

        // Role filter (requires LEFT JOIN)
        $roleJoin = '';
        if ($roleId !== '' && $roleId > 0) {
            $roleJoin = "LEFT JOIN userRoles ON users.id = userRoles.userID";
            $where .= " AND userRoles.roleID = :roleId";
            $params[':roleId'] = $roleId;
        }

        $sql = "
            SELECT DISTINCT users.id, users.username, users.firstName, users.middleName, users.lastName, users.phone, users.email, users.createdAt, users.lastActivityAt, users.note
            FROM users
            {$roleJoin}
            WHERE {$where}
            ORDER BY users.firstName ASC, users.lastName ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update the current user's last-activity timestamp.
    public function updateLastActiveAt() {
        $query = "UPDATE users SET lastActivityAt = NOW() WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $_SESSION['id']);
        return $stmt->execute();
    }

    // ================================================================
    //  ACCOUNT CRUD (CREATE, READ, UPDATE, DELETE)
    // ================================================================

    // Create a new user account (password defaults to "password").
    public function insertAccount($username, $firstName, $middleName, $lastName, $phoneNumber, $emailAddress) {
        $user = $this->findSingleStaff($username);

        if ($user) {
            return false;
        }

        $query = "INSERT INTO users (username, email, passwordHash, firstName, middleName, lastName, phone) VALUES
            (:username, :email, :password, :firstName, :middleName, :lastName, :phoneNumber);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':middleName', $middleName);
        $stmt->bindParam(':lastName', $lastName);
        $stmt->bindParam(':phoneNumber', $phoneNumber);
        $stmt->bindParam(':email', $emailAddress);
        $stmt->bindValue(':password', '$2a$12$GneJMn6XG0CpMy.q4hjvRO2REhToNFFIh2k3ulHN6K2ztX1mhPyCm');
        $result = $stmt->execute();

        if ($result) {
            $this->insertUserActivityLog($_SESSION['id'], "Account Creation", "Created a new account named {$username}", "yellow");
        }

        return $result;
    }

    // Delete a user account and all associated data (roles, permissions, tasks, etc.).
    public function removeAccount($id) {
        try {
            $this->pdo->beginTransaction();

            $account = $this->getAccount($id);
            if (!$account) {
                $this->pdo->rollBack();
                return "Error: Account not found.";
            }

            $username = $account['username'];

            // Remove physical account image file first, if present
            $storageDir = __DIR__ . '/../../Storage/AccountImages/';
            $existingImage = $this->findSingleAccountImageByID($id);

            if ($existingImage && !empty($existingImage['imageName'])) {
                $imagePath = $storageDir . $existingImage['imageName'];
                if (file_exists($imagePath) && !unlink($imagePath)) {
                    $this->pdo->rollBack();
                    return "Error: Failed to delete account image file.";
                }
            }

            $dependentTables = [
                'userRoles',
                'userPermissions',
                'userStats',
                'userActivityLog',
                'userProcessTasks',
                'miscellaneousTasks'
            ];

            foreach ($dependentTables as $table) {
                $query = "DELETE FROM {$table} WHERE userID = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // Delete image record after removing the physical file
            $query = "DELETE FROM userImages WHERE userID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $query = "DELETE FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            if ($result) {
                $this->pdo->commit();
                $this->insertUserActivityLog($_SESSION['id'], "Account Deletion", "Deleted an account named {$username}.", "red");
                return "Success: Account '{$username}' deleted successfully.";
            }

            $this->pdo->rollBack();
            return "Error: Failed to delete account.";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            error_log($e->getMessage());
            return "An error occurred. Please try again.";
        }
    }

    // Change a user's username (must be unique).
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
        $result = $stmt->execute();

        if ($result) {
            $this->insertUserActivityLog($id, "Account Update", "Changed username to {$username}", "yellow");
        }

        return $result;
    }

    // Update phone and email for a user.
    public function updateContacts($id, $phoneNumber, $email) {
        $query = "UPDATE users SET phone = :phone, email = :email WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':phone', $phoneNumber);
        $stmt->bindParam(':email', $email);
        $result = $stmt->execute();

        if ($result) {
            $changes = [];
            $changes[] = "phone to {$phoneNumber}";
            $changes[] = "email to {$email}";
            $changeLog = implode(" and ", $changes);
            $this->insertUserActivityLog($id, "Account Update", "Updated {$changeLog}", "yellow");
        }

        return $result;
    }

    // Change a user's password (hashes automatically).
    public function updatePassword($id, $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE users SET passwordHash = :passwordHash WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':passwordHash', $hash);
        $result = $stmt->execute();

        if ($result) {
            $this->insertUserActivityLog($id, "Account Security", "Password changed successfully", "yellow");
        }

        return $result;
    }

    // Set or clear the user's note (max 30 characters).
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

    // ================================================================
    //  PERMISSIONS
    // ================================================================

    // Get all distinct permissions granted to a user through their roles.
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

    // Get all permissions defined in the system.
    public function getAllPermissions() {
        $query = "SELECT id, name FROM permissions ORDER BY id ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all role-permission assignments.
    public function getAllRolePermissions() {
        $query = "SELECT rolePermissions.roleID, rolePermissions.permissionID, permissions.name FROM rolePermissions
                  JOIN permissions ON permissions.id = rolePermissions.permissionID
                  ORDER BY rolePermissions.roleID ASC, rolePermissions.permissionID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the permissions assigned to a specific role.
    public function getRolePermissions($roleID) {
        $query = "SELECT permissions.id, permissions.name FROM rolePermissions
                  JOIN permissions ON permissions.id = rolePermissions.permissionID WHERE rolePermissions.roleID = :roleID
                  ORDER BY rolePermissions.roleID ASC, rolePermissions.permissionID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    //  ROLES
    // ================================================================

    // Get all roles.
    public function getAllRoles() {
        $query = "SELECT id, name FROM roles";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all user-role assignments.
    public function getAllUserRoles() {
        $query = "SELECT userRoles.userID, userRoles.roleID, roles.name FROM userRoles JOIN roles ON roles.id = userRoles.roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the role names for a specific user.
    public function getAllUserRolesByUserID($userID) {
        $query = "SELECT roles.name FROM userRoles JOIN roles ON roles.id = userRoles.roleID WHERE userRoles.userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the role IDs assigned to a user.
    public function getUserRoles($userID) {
        $query = "SELECT roleID FROM userRoles WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // Get roles (with user counts) that the current actor can alter.
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

    // Find a role by its name (used for duplicate checks).
    public function getRoleByName($name) {
        $query = "SELECT id FROM roles WHERE name = :name LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Find a role by its id
    public function getRoleByID($id) {
        $query = "SELECT name FROM roles WHERE id = :id LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Create a new role.
    public function insertRole($name) {
        $role = $this->getRoleByName($name);

        if ($role) {
            return false;
        }

        $query = "INSERT INTO roles (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $result = $stmt->execute();

        if ($result) {
            $this->insertUserActivityLog($_SESSION['id'], 'Role Creation', "Created a new role named {$name}.", 'yellow');
        }

        return $result;
    }

    // Delete a role and all its dependencies (permissions, process tasks, governance, user assignments).
    public function removeRole($id) {
        try {
            $this->pdo->beginTransaction();

            // Fetch role name for logging before deletion
            $role = $this->getRoleByID($id);

            if (!$role) {
                return "Error: Cannot find role";
            }

            $roleName = $role ? $role['name'] : 'Unknown';

            // Delete dependent records
            $tables = [
                'rolePermissions'         => 'roleID',
                'roleProcessTasks'        => 'roleID',
                'roleManagementGovernance' => 'roleSubjectID = :id OR roleAgentID',
                'userRoles'               => 'roleID'
            ];

            foreach ($tables as $table => $condition) {
                // For the governance table, condition uses OR
                if ($table === 'roleManagementGovernance') {
                    $query = "DELETE FROM {$table} WHERE roleSubjectID = :id OR roleAgentID = :id";
                } else {
                    $query = "DELETE FROM {$table} WHERE {$condition} = :id";
                }
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // Delete the role itself
            $query = "DELETE FROM roles WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $this->pdo->commit();

            // Log the deletion
            $this->insertUserActivityLog($_SESSION['id'], 'Role Deletion', "Deleted a role named {$roleName}.", 'red');

            return "Success: Role '{$roleName}' deleted successfully.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log($e->getMessage());
            return "An error occurred. Please try again.";
        }
    }

    // ================================================================
    //  ROLE GOVERNANCE
    // ================================================================

    // Get all governance rules.
    public function getAllRoleManagementGovernance() {
        $query = "SELECT * FROM roleManagementGovernance ORDER BY roleAgentID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get governance rules for a set of agent roles.
    public function getRoleManagementGovernance($roles) {
        $placeholders = implode(',', array_fill(0, count($roles), '?'));

        $query = "SELECT roleSubjectID, MAX(canGrant)  AS canGrant, MAX(canRevoke) AS canRevoke, MAX(canAlter)  AS canAlter, MAX(canDelete) AS canDelete
                  FROM roleManagementGovernance WHERE roleAgentID IN ($placeholders) GROUP BY roleSubjectID";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($roles);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Compute the effective governance rights one user (actor) has over another (subject).
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

    // ================================================================
    //  ROLE & PERMISSION MUTATIONS (clear & update pairs)
    // ================================================================

    public function clearUserRoles($userID) {
        $query = "DELETE FROM userRoles WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        return $stmt->execute();
    }

    public function updateUserRoles($userID, $userRoles) {
        $this->clearUserRoles($userID);
        $userFullName = 'Unknown';

        if (!empty($userRoles)) {
            $query = "INSERT INTO userRoles (userID, roleID) VALUES (:userID, :roleID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($userRoles); $i++) {
                $stmt->execute([
                    ':userID' => $userID,
                    ':roleID' => $userRoles[$i]
                ]);
            }

            $user = $this->getAccount($userID);
            if ($user) {
                $userFullName = $user['firstName'];
                if (!empty($user['middleName'])) {
                    $userFullName .= ' ' . strtoupper($user['middleName'][0]) . '.';
                }
                $userFullName .= ' ' . $user['lastName'];
            } else {
                $userFullName = 'Unknown';
            }

            // Log to admin/actor who made the change
            $this->insertUserActivityLog($_SESSION['id'], 'Account Update', "Updated the roles for {$userFullName}.", 'yellow');

            // Log to the user whose roles were updated
            $actorName = $this->getAccount($_SESSION['id']);
            $actorFullName = $actorName ? $actorName['firstName'] : 'Unknown';
            if ($actorName && !empty($actorName['middleName'])) {
                $actorFullName .= ' ' . strtoupper($actorName['middleName'][0]) . '.';
            }
            if ($actorName) {
                $actorFullName .= ' ' . $actorName['lastName'];
            }
            $this->insertUserActivityLog($userID, 'Account Update', "Roles were updated by {$actorFullName}.", 'yellow');
        }

        return "Success: Updated the role of {$userFullName}.";
    }

    public function clearRolePermissions($roleID) {
        $query = "DELETE FROM rolePermissions WHERE roleID = :roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        return $stmt->execute();
    }

    public function updateRolePermissions($roleID, $permissions) {
        $this->clearRolePermissions($roleID);
        $roleName = 'Unknown';

        if (!empty($permissions)) {
            $query = "INSERT INTO rolePermissions (roleID, permissionID) VALUES (:roleID, :permissionID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($permissions); $i++) {
                $stmt->execute([
                    ':roleID' => $roleID,
                    ':permissionID' => $permissions[$i]
                ]);
            }

            // Get role name for logging
            $role = $this->getRoleByID($roleID);
            $roleName = $role ? $role['name'] : 'Unknown';

            // Get permission names for logging
            $query = "SELECT GROUP_CONCAT(permissions.name SEPARATOR ', ') AS permissionNames FROM permissions WHERE id IN (" . implode(',', $permissions) . ")";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $permissionNames = $result['permissionNames'] ?? 'No permissions assigned';

            // Log that we updated the role permissions with its name in yellow color
            $this->insertUserActivityLog($_SESSION['id'], 'Role Update', "Updated permissions for role '{$roleName}'.", 'yellow');
        }

        return "Success: Updated permissions for role '{$roleName}'.";
    }

    public function clearRoleManagementGovernance($roleID) {
        $query = "DELETE FROM roleManagementGovernance WHERE roleAgentID = :roleID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':roleID', $roleID);
        return $stmt->execute();
    }

    public function updateRoleManagementGovernance($roleID, $rules) {
        $this->clearRoleManagementGovernance($roleID);
        $roleName = 'Unknown';

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

            // Get role name for logging
            $role = $this->getRoleByID($roleID);
            $roleName = $role ? $role['name'] : 'Unknown';

            // Log that we updated the role management governance of this role with its name (don't include the changes themselves)
            $this->insertUserActivityLog($_SESSION['id'], 'Role Update', "Updated governance rules for role '{$roleName}'.", 'yellow');
        }

        return "Success: Updated governance rules for role '{$roleName}'.";
    }

    // ================================================================
    //  PROCESS TASKS
    // ================================================================

    // Get all role-process-task assignments.
    public function getAllRoleProcessTasks() {
        $query = "SELECT roleProcessTasks.roleID, roleProcessTasks.processID, processes.name FROM roleProcessTasks
                  JOIN processes ON roleProcessTasks.processID = processes.id
                  ORDER BY roleProcessTasks.roleID ASC, processes.name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all users and the processes they can be assigned to (via their roles).
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

    // Get all user process tasks with roles.
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

    // Get all user process tasks with detailed order/process info.
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

    // Get the process tasks available to a user through their roles.
    public function getUserRoleProcessTasks($id) {
        $roles = $this->getUserRoles($id);

        if (empty($roles)) {
            return [];
        }

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
        $roleName = 'Unknown';

        if (!empty($processes)) {
            $query = "INSERT INTO roleProcessTasks (roleID, processID) VALUES (:roleID, :processID)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($processes); $i++) {
                $stmt->execute([
                    ':roleID' => $roleID,
                    ':processID' => $processes[$i],
                ]);
            }

            // Get role name for logging
            $role = $this->getRoleByID($roleID);
            $roleName = $role ? $role['name'] : 'Unknown';

            // Log that we updated the process tasks for this role with its name in yellow color
            $this->insertUserActivityLog($_SESSION['id'], 'Role Update', "Updated process tasks for role '{$roleName}'.", 'yellow');
        }

        return "Success: Updated process tasks for role '{$roleName}'.";
    }

    // Get task counts grouped by user.
    public function getAllUsersTaskCount() {
        $query = "SELECT userID, COUNT(userID) AS taskCount FROM userProcessTasks GROUP BY userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    //  ACCOUNT IMAGES
    // ================================================================

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

            $this->insertUserActivityLog($userID, "Account Update", "Uploaded new profile photo", "yellow");
            return "Success: Upload successful.";
        } catch (PDOException $e) {
            // On exception, clean up the newly uploaded file
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            error_log($e->getMessage());
            return "An error occurred. Please try again.";
        }
    }

    // Get a user's account image filename (null if none).
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

    // Return an associative array mapping userID to image filename.
    public function getAllAccountImagesMapped() {
        $map = [];

        foreach ($this->getAllAccountImages() as $item) {
            $map[$item['userID']] = $item['imageName'];
        }

        return $map;
    }

    // ================================================================
    //  USER STATS & ACTIVITY LOGS
    // ================================================================

    public function getAllUserStats() {
        $query = "SELECT * FROM userStats";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserStatsByID($userID) {
        $query = "SELECT * FROM userStats WHERE userID = :userID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUserActivityLogs() {
        $query = "SELECT * FROM userActivityLog ORDER BY loggedAt DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserActivityLogsByID($userID) {
        $query = "SELECT * FROM userActivityLog WHERE userID = :userID ORDER BY loggedAt DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    //  MISCELLANEOUS TASKS
    // ================================================================

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

    // Assign a miscellaneous task to a user and log for both parties.
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

    // Mark a miscellaneous task as complete, update user stats and log.
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

    // Unassign a miscellaneous task from a user and log.
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

    // ================================================================
    //  ACTIVITY LOGGING (utility used throughout the model)
    // ================================================================

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
