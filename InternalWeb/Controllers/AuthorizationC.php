<?php

/**
 * Authorization Controller
 *
 * Handles user authentication, account management, role and permission management,
 * and staff-related operations for the internal web application.
 */
class AuthorizationC {
    /** @var StaffM Staff model instance */
    private $staffModel;

    /** @var ServicesM Services model instance */
    private $servicesModel;

    /**
     * Constructor
     *
     * Initializes the controller with required model dependencies.
     *
     * @param PDO $pdo Database connection instance
     */
    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->staffModel = new StaffM($pdo);
        $this->servicesModel = new ServicesM($pdo);
    }

    // ==================== AUTHENTICATION METHODS ====================

    /**
     * Display the login page
     *
     * Renders the login view with necessary variables.
     */
    public function showLogin() {
        $page = "login";
        $error = null;
        require __DIR__ . '/../Views/Login/Page.php';
    }

    /**
     * Handle user login
     *
     * Processes login credentials, handles authentication, failed attempts,
     * account locking, and session setup.
     */
    public function login() {
        $username = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';

        $userRecord = $this->staffModel->findSingleStaff($username);
        $error = null;

        // Check if user exists and is locked
        if ($userRecord && $this->staffModel->isUserLocked($userRecord['id'])) {
            $formattedTime = $this->staffModel->getFormattedLockTimeRemaining($userRecord['id']);
            $_SESSION['message'] = "Error: Account locked due to too many failed login attempts. Please try again in {$formattedTime}.";
            require __DIR__ . '/../Views/Login/Page.php';
            return;
        }

        $user = $this->staffModel->authenticate($username, $password);

        if ($user) {
            // Reset failed attempts on successful login
            $this->staffModel->resetFailedAttempts($user['id']);

            // $this->staffModel->updateOnlineStatus($user['id'], true);

            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['phoneNumber'] = $user['phone'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['firstName'] . ' ' . $user['lastName'];
            $_SESSION['logged_in'] = true;

            $this->getPermissions();
            // $this->staffModel->updateLastLogin($user['id']);
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            // Handle failed login attempt
            if ($userRecord) {
                $failedCount = $this->staffModel->incrementFailedAttempts($userRecord['id']);

                // Check if there's an active lockout with remaining time
                if ($failedCount % 5 === 0 && $this->staffModel->getLockTimeRemaining($userRecord['id']) > 0) {
                    $formattedTime = $this->staffModel->getFormattedLockTimeRemaining($userRecord['id']);
                    $_SESSION['message'] = "Error: Invalid username or password. Account locked after {$failedCount} failed attempts. Try again in {$formattedTime}.";
                } else {
                    $_SESSION['message'] = "Error: Invalid username or password. ({$failedCount} failed attempt" . ($failedCount !== 1 ? "s" : "") . ")";
                }
            } else {
                $_SESSION['message'] = "Error: Invalid username or password.";
            }
            require __DIR__ . '/../Views/Login/Page.php';
        }
    }

    /**
     * Handle user logout
     *
     * Destroys the session and redirects to login page.
     */
    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    /**
     * Check if current user exists
     *
     * Verifies if the logged-in user still exists in the database.
     * Logs out if user doesn't exist.
     */
    public function checkUserExists() {
        $existence = $this->staffModel->getAccount($_SESSION['id']);
        if (!$existence) {
            $this->logout();
        }
    }

    /**
     * Refresh user's last active timestamp
     *
     * Updates the last active time for the current user.
     */
    public function refreshLastActiveAt() {
        $this->staffModel->updateLastActiveAt($_SESSION['id']);
    }

    // ==================== PERMISSIONS METHODS ====================

    /**
     * Load user permissions into session
     *
     * Retrieves and sets user permissions and role-based process tasks.
     */
    public function getPermissions() {
        $permissions = $this->staffModel->getUserPermissions($_SESSION['id']);
        $_SESSION['permissions'] = array_column($permissions, 'name');

        $userRoleProcessTasks = $this->staffModel->getUserRoleProcessTasks($_SESSION['id']);
        if (!empty($userRoleProcessTasks) && !in_array('canTakeTasks', $_SESSION['permissions'])) {
            $_SESSION['permissions'][] = 'canTakeTasks';
        }
    }

    // ==================== ACCOUNT MANAGEMENT METHODS ====================

    /**
     * Display account management page
     *
     * Renders the account page with user data, roles, stats, and logs.
     */
    public function showAccountManagementPage() {
        $page = "account";
        $accountImage = $this->staffModel->getAccountImage($_SESSION['id']);

        $roleList = $this->staffModel->getAllUserRolesByUserID($_SESSION['id']);
        $stats = $this->staffModel->getUserStatsByID($_SESSION['id']);
        $logsList = $this->staffModel->getUserActivityLogsByID($_SESSION['id']);

        $note = $this->staffModel->getAccount($_SESSION['id'])['note'] ?? '';
        require_once __DIR__ . '/../Views/Account/Page.php';
    }

    /**
     * Update user username
     *
     * Validates and updates the username for the current user.
     */
    public function setUsername() {
        $username = strtolower(trim($_POST['username'] ?? ''));

        if (empty($username)) {
            $_SESSION['message'] = "Error: Username cannot be empty.";
            header('Location: index.php?page=account');
            return;
        }

        if (strlen($username) < 3) {
            $_SESSION['message'] = "Error: Username must be at least 3 characters long.";
            header('Location: index.php?page=account');
            return;
        }

        $update = $this->staffModel->updateUsername($_SESSION['id'], $username);

        if ($update) {
            $_SESSION['username'] = $username;
            $_SESSION['message'] = "Success: Username updated to {$username}.";
            header('Location: index.php?page=account');
        } else {
            $_SESSION['message'] = "Error: Username already exists.";
            header('Location: index.php?page=account');
        }
    }

    /**
     * Update user contact information
     *
     * Validates and updates phone number and email for the current user.
     */
    public function setContacts() {
        $postPhone = $_POST['phoneNum'] ?? null;
        $postEmail = $_POST['emailAddress'] ?? null;

        $phoneNum = (!empty($postPhone)) ? $postPhone : $_SESSION['phoneNumber'];
        $emailAddress = (!empty($postEmail)) ? $postEmail : $_SESSION['email'];

        // Validate phone format
        if (!empty($postPhone) && !preg_match('/^09\d{9}$/', $postPhone)) {
            $_SESSION['message'] = "Error: Invalid phone number format. Must be 09XXXXXXXXX.";
            header('Location: index.php?page=account');
            return;
        }

        // Validate email format
        if (!empty($postEmail) && !filter_var($postEmail, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['message'] = "Error: Invalid email address format.";
            header('Location: index.php?page=account');
            return;
        }

        $this->staffModel->updateContacts($_SESSION['id'], $phoneNum, $emailAddress);

        $changes = [];
        if (!empty($postPhone)) $changes[] = "phone to {$postPhone}";
        if (!empty($postEmail)) $changes[] = "email to {$postEmail}";
        $changeLog = !empty($changes) ? implode(" and ", $changes) : "contact information";

        $_SESSION['phoneNumber'] = $phoneNum;
        $_SESSION['email'] = $emailAddress;
        $_SESSION['message'] = "Success: Contact information updated.";
        header('Location: index.php?page=account');
    }

    /**
     * Update user password
     *
     * Validates current password and updates to new password with security checks.
     */
    public function setPassword() {
        $passCurrent = $_POST['passwordCurrent'];
        $passNew = $_POST['passwordNew'];
        $passRetype = $_POST['passwordRetype'];

        $user = $this->staffModel->authenticate($_SESSION['username'], $passCurrent);

        if (!$user) {
            $this->staffModel->insertUserActivityLog($_SESSION['id'], "Security Alert", "Failed password change attempt - incorrect current password", "red");
            $_SESSION['message'] = "Error: Incorrect current password.";
            header('Location: index.php?page=account');
            return;
        }

        if (empty($passNew)) {
            $_SESSION['message'] = "Error: New password cannot be empty.";
            header('Location: index.php?page=account');
            return;
        }

        if ($passNew !== $passRetype) {
            $_SESSION['message'] = "Error: New and retyped password do not match.";
            header('Location: index.php?page=account');
            return;
        }

        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/', $passNew)) {
            $_SESSION['message'] = "Error: Password must have at least 8 characters, and must contain a number, alphabet, and symbol.";
            header('Location: index.php?page=account');
            return;
        }

        if ($passCurrent === $passNew) {
            $_SESSION['message'] = "Error: New password must be different from current password.";
            header('Location: index.php?page=account');
            return;
        }

        $this->staffModel->updatePassword($_SESSION['id'], $passNew);
        $_SESSION['message'] = "Success: Password changed successfully.";
        header('Location: index.php?page=account');
    }

    /**
     * Update user note
     *
     * Updates the personal note for the current user.
     */
    public function setUserNote() {
        $note = strtolower(trim($_POST['userNote'] ?? ''));

        $_SESSION['message'] = $this->staffModel->updateUsernote($_SESSION['id'], $note);

        header('Location: index.php?page=account');
    }

    /**
     * Upload account image
     *
     * Handles file upload for user profile image.
     */
    public function uploadAccountImage() {
        $image = $_FILES['image'];

        $result = $this->staffModel->insertAccountImage($_SESSION['id'], $image);
        $_SESSION['message'] = $result;

        header("Location: index.php?page=account");
    }

    // ==================== STAFF MANAGEMENT METHODS ====================

    /**
     * Display staff management page
     *
     * Renders the staff page with filtering options and staff data.
     */
    public function showStaff() {
        $page = "staff";

        // Extract filter parameters from GET
        $search = isset($_GET['search']) ? strtolower(trim($_GET['search'])) : '';
        $onlineStatus = isset($_GET['onlineStatus']) ? strtolower(trim($_GET['onlineStatus'])) : '';
        $activityStatus = isset($_GET['activityStatus']) ? strtolower(trim($_GET['activityStatus'])) : '';
        $roleId = isset($_GET['roleId']) ? (int)$_GET['roleId'] : '';

        $roleList = $this->staffModel->getAllRoles();
        $userRoles = $this->staffModel->getAllUserRoles();
        $roleGovernance = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));
        $userProcessTaskList = $this->staffModel->getAllUserProcessTasksDetailed();
        $accountImageMap = $this->staffModel->getAllAccountImagesMapped();
        $userStatsList = $this->staffModel->getAllUserStats();
        $userActivityLogsList = $this->staffModel->getAllUserActivityLogs();
        $userMiscTaskList = $this->staffModel->getAllMiscellaneousTasks();

        $userMiscTaskMap = [];
        foreach ($userMiscTaskList as $miscTask) {
            $userMiscTaskMap[$miscTask['userID']] = $miscTask;
        }

        if ($search !== '' || $onlineStatus !== '' || $activityStatus !== '' || $roleId !== '') {
            $staffList = $this->staffModel->getfilteredStaff($search, $onlineStatus, $activityStatus, $roleId);
        } else {
            $staffList = $this->staffModel->getStaffList();
        }

        $userTaskCountMap = [];

        foreach ($this->staffModel->getAllUsersTaskCount() as $item) {
            $userTaskCountMap[$item['userID']] = $item['taskCount'];
        }

        $currentUserId = $_SESSION['id'];
        $staffList = array_filter($staffList, function ($staff) use ($currentUserId) {
            return $staff['id'] !== $currentUserId;
        });

        require __DIR__ . '/../Views/Staff/Page.php';
    }

    /**
     * Display account creation page
     *
     * Shows the form for creating new user accounts if permission allows.
     */
    public function showAccountCreationPage() {
        if (in_array('canCreateUserAccounts', $_SESSION['permissions'])) {
            $page = "staff";
            $lastPage = 'staff';
            $backLink = 'index.php?page=staff';
            require_once __DIR__ . '/../Views/Staff/CreateAccount.php';
        } else {
            header("Location: index.php?page=staff");
        }
    }

    /**
     * Create new user account
     *
     * Processes form data to create a new user account.
     */
    public function createAccount() {
        if (in_array('canCreateUserAccounts', $_SESSION['permissions'])) {
            $username = strtolower(trim($_POST['username']));
            $firstName = ucwords(strtolower(trim($_POST['firstName'])));
            $middleName = ucwords(strtolower(trim($_POST['middleName'] ?? '')));
            $lastName = ucwords(strtolower(trim($_POST['lastName'])));
            $phoneNum = $_POST['phoneNum'];
            $emailAddress = $_POST['emailAddress'];

            $creation = $this->staffModel->insertAccount($username, $firstName, $middleName, $lastName, $phoneNum, $emailAddress);

            if ($creation) {
                $_SESSION['message'] = "Success: User Account for " . $firstName . " created.";
            } else {
                $_SESSION['message'] = "Error: User with that username already exists.";
                header("Location: index.php?page=staff&action=create");
                exit;
            }
        } else {
            $_SESSION['message'] = "Error: You dont have permission to create roles";
        }
        header("Location: index.php?page=staff");
    }

    /**
     * Delete user account
     *
     * Removes a user account if permission and governance rules allow.
     */
    public function deleteAccount() {
        $error = '';
        if (in_array('canDeleteUserAccounts', $_SESSION['permissions'])) {
            $id = $_POST['deletedID'];

            $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($id, $_SESSION['id']);

            if (!$governanceRules['canDelete']) {
                $_SESSION['message'] = "You dont have the authority to delete this user because of their role";
            } else {
                $_SESSION['message'] = $this->staffModel->removeAccount($id);
            }
        } else {
            $_SESSION['message'] = "You dont have permission to delete accounts";
        }
        header("Location: index.php?page=staff");
    }

    /**
     * Assign miscellaneous task to staff
     *
     * Assigns a task to a staff member if permissions allow.
     */
    public function assignMiscTask() {
        if (in_array('canAssignMiscTasksToStaff', $_SESSION['permissions'])) {
            $assigneeID = $_POST['selectedID'];
            $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($assigneeID, $_SESSION['id']);

            if (!$governanceRules['canGrant']) {
                $_SESSION['message'] = "Error: You dont have the authority to assign something to this user because of their role";
            } else {
                $description = $_POST['description'];
                $_SESSION['message'] = $this->staffModel->insertMiscellaneousTask($assigneeID, $description);
            }
        } else {
            $_SESSION['message'] = "Error: You dont have permission to assign miscellaneous tasks to anyone.";
        }
        header("Location: index.php?page=staff");
    }

    /**
     * Update miscellaneous task status
     *
     * Completes or unassigns a miscellaneous task for a staff member.
     */
    public function setMiscTask() {
        if (in_array('canFinalizeMiscTasksToStaff', $_SESSION['permissions'])) {
            $assigneeID = $_POST['selectedID'];
            $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($assigneeID, $_SESSION['id']);

            if (!$governanceRules['canAlter']) {
                $_SESSION['message'] = "Error: You dont have the authority to alter this user's miscellaneous task because of their role";
            } else {
                $action = $_POST['miscTaskAction'] ?? '';

                if ($action === 'complete') {
                    $_SESSION['message'] = $this->staffModel->completeMiscellaneousTask($assigneeID);
                } else if ($action === 'unassign') {
                    $_SESSION['message'] = $this->staffModel->unassignMiscellaneousTask($assigneeID);
                } else {
                    $_SESSION['message'] = 'Error: Invalid action.';
                }
            }
        } else {
            $_SESSION['message'] = "Error: You dont have permission to finalize miscellaneous tasks to anyone.";
        }
        header("Location: index.php?page=staff");
    }

    // ==================== ROLE MANAGEMENT METHODS ====================

    /**
     * Display role management page
     *
     * Shows the role management interface with permissions and governance data.
     */
    public function showRoleManagementPage() {
        if (in_array('canAlterRoles', $_SESSION['permissions'])) {
            $page = "staff";
            $lastPage = 'staff';
            $backLink = 'index.php?page=staff';
            $userPermissionsList = $this->staffModel->getUserPermissions($_SESSION['id']);
            $roleTally = $this->staffModel->getAllRolesTally($_SESSION['id']);
            $roleList = $this->staffModel->getAllRoles();
            $rolePermissionsList = $this->staffModel->getAllRolePermissions();
            $roleGovernanceList = $this->staffModel->getAllRoleManagementGovernance();
            $processTaskList = $this->staffModel->getAllRoleProcessTasks();
            $processList = $this->servicesModel->getAllProcesses();

            require_once __DIR__ . '/../Views/Staff/RoleManagement.php';
        } else {
            header("Location: index.php?page=staff");
        }
    }

    /**
     * Update user roles
     *
     * Assigns or revokes roles for a user based on governance rules.
     */
    public function setUserRoles() {
        $error = '';
        if (in_array('canAlterAccountRoles', $_SESSION['permissions'])) {
            $userID = $_POST['selectedID'];

            $userNewRoles = $_POST['roleHiddenInput'] ?? [];
            $userPastRoles = $this->staffModel->getUserRoles($userID);

            $userNewRoles = array_map('intval', $userNewRoles);
            $userPastRoles = array_map('intval', $userPastRoles);

            $removedRoles = array_diff($userPastRoles, $userNewRoles);
            $addedRoles = array_diff($userNewRoles, $userPastRoles);

            $isRevoking = !empty($removedRoles);
            $isGranting = !empty($addedRoles);

            $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($userID, $_SESSION['id']);

            if ($isRevoking && !$governanceRules['canRevoke']) {
                $_SESSION['message'] = "You dont have the authority to revoke roles from this user because of their role";
            } else if ($isGranting && !$governanceRules['canGrant']) {
                $_SESSION['message'] = "You dont have the authority to grant roles for this user because of their role";
            } else {
                $_SESSION['message'] = $this->staffModel->updateUserRoles($userID, $userNewRoles);
            }
        } else {
            $_SESSION['message'] = "You dont have permission to alter user roles";
        }
        header("Location: index.php?page=staff");
    }

    /**
     * Update role permissions
     *
     * Modifies the permissions assigned to a specific role.
     */
    public function setRolePermissions() {
        $error = '';
        if (in_array('canAlterRoles', $_SESSION['permissions'])) {
            $roleID = $_POST['selectedID'];
            $governanceRules = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));

            $canAlter = true;

            foreach ($governanceRules as $rule) {
                if ($rule['roleSubjectID'] == $roleID) {
                    $canAlter = $rule['canAlter'];
                    break;
                }
            }

            $newRolePermissions = $_POST['newPermissions'] ?? [];
            $oldRolePermissions =  array_column($this->staffModel->getRolePermissions($roleID), 'id');

            $newRolePermissions = array_map('intval', $newRolePermissions);
            $oldRolePermissions = array_map('intval', $oldRolePermissions);

            $revokedPermissions = array_diff($oldRolePermissions, $newRolePermissions);
            $grantedPermissions = array_diff($newRolePermissions, $oldRolePermissions);

            $userPermissions = array_column($this->staffModel->getUserPermissions($_SESSION['id']), 'id');
            $userPermissions = array_map('intval', $userPermissions);

            $hasUnauthorizedRevocation = !empty(array_diff($revokedPermissions, $userPermissions));
            $hasUnauthorizedGrant = !empty(array_diff($grantedPermissions, $userPermissions));

            if ($canAlter && !$hasUnauthorizedRevocation && !$hasUnauthorizedGrant) {
                $_SESSION['message'] = $this->staffModel->updateRolePermissions($roleID, $newRolePermissions);
            } else if (!$canAlter) {
                $_SESSION['message'] = "You dont have the authority to alter this role";
            } else if ($hasUnauthorizedRevocation) {
                $_SESSION['message'] = "You dont have the authority to revoke permissions you have tried to revoke";
            } else if ($hasUnauthorizedGrant) {
                $_SESSION['message'] = "You dont have the authority to grant permissions you have tried to grant";
            }
        } else {
            $_SESSION['message'] = "You dont have permission to alter roles";
        }
        header("Location: index.php?page=staff&action=manageRoles");
    }

    /**
     * Update role management governance
     *
     * Sets governance rules for role management operations.
     */
    public function setRoleManagementGovernance() {
        if (in_array('canAlterRoles', $_SESSION['permissions'])) {
            $roleID = intval($_POST['selectedID']);
            $roleSubjects = $_POST['roleSubjects'];
            $canGrants = $_POST['canGrants'];
            $canRevokes = $_POST['canRevokes'];
            $canAlters = $_POST['canAlters'];
            $canDeletes = $_POST['canDeletes'];

            $newGovernanceRows = [];
            foreach ($roleSubjects as $index => $subjectId) {
                $newGovernanceRows[] = [
                    'roleSubjectID' => $subjectId,
                    'canGrant' => $canGrants[$index] ?? 0,
                    'canRevoke' => $canRevokes[$index] ?? 0,
                    'canAlter' => $canAlters[$index] ?? 0,
                    'canDelete' => $canDeletes[$index] ?? 0,
                ];
            }

            $oldIndex = [];
            foreach ($this->staffModel->getRoleManagementGovernance([$roleID]) as $row) {
                $oldIndex[(int)$row['roleSubjectID']] = [
                    'canGrant' => (int)$row['canGrant'],
                    'canRevoke' => (int)$row['canRevoke'],
                    'canAlter' => (int)$row['canAlter'],
                    'canDelete' => (int)$row['canDelete'],
                ];
            }

            $createdIDs  = [];
            $modifiedIDs = [];
            $deletedIDs  = [];

            foreach ($newGovernanceRows as $row) {
                $subjectId = (int)$row['roleSubjectID'];

                if (!isset($oldIndex[$subjectId])) {
                    $createdIDs[] = $subjectId;
                    continue;
                }

                $oldFlags = $oldIndex[$subjectId];
                if (
                    (int)$row['canGrant'] !== $oldFlags['canGrant'] ||
                    (int)$row['canRevoke'] !== $oldFlags['canRevoke'] ||
                    (int)$row['canAlter'] !== $oldFlags['canAlter'] ||
                    (int)$row['canDelete'] !== $oldFlags['canDelete']
                ) {
                    $modifiedIDs[] = $subjectId;
                }

                unset($oldIndex[$subjectId]);
            }

            $deletedIDs = array_keys($oldIndex);

            $alterableRoles = array_column($this->staffModel->getAllRolesTally($_SESSION['id']), 'id');

            $hasUnauthorizedCreation = !empty(array_diff($createdIDs, $alterableRoles));
            $hasUnauthorizedModification = !empty(array_diff($modifiedIDs, $alterableRoles));
            $hasUnauthorizedDeletion = !empty(array_diff($deletedIDs, $alterableRoles));

            $governanceRules = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));

            $canAlter = true;

            foreach ($governanceRules as $rule) {
                if ($rule['roleSubjectID'] == $roleID) {
                    $canAlter = $rule['canAlter'];
                    break;
                }
            }

            if ($canAlter && !$hasUnauthorizedCreation && !$hasUnauthorizedModification && !$hasUnauthorizedDeletion) {
                $_SESSION['message'] = $this->staffModel->updateRoleManagementGovernance($roleID, $newGovernanceRows);
            } else if (!$canAlter) {
                $_SESSION['message'] = "You dont have the authority to alter this role";
            } else if ($hasUnauthorizedCreation) {
                $_SESSION['message'] = "You dont have the authority to create specific management rules on one or many rules you have tried to create";
            } else if ($hasUnauthorizedModification) {
                $_SESSION['message'] = "You dont have the authority to modify specific management rules on one or many rules you have tried to modify";
            } else if ($hasUnauthorizedDeletion) {
                $_SESSION['message'] = "You dont have the authority to delete specific management rules on one or many rules you have tried to delete";
            }
        } else {
            $_SESSION['message'] = "You dont have permission to alter roles";
        }
        header("Location: index.php?page=staff&action=manageRoles");
    }

    /**
     * Update role process tasks
     *
     * Assigns process tasks to a specific role.
     */
    public function setRoleProcessTasks() {
        if (in_array('canAlterRoles', $_SESSION['permissions'])) {
            $roleID = $_POST['selectedID'];
            $processes = $_POST['processTasks'];
            $governanceRules = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));

            $canAlter = true;

            foreach ($governanceRules as $rule) {
                if ($rule['roleSubjectID'] == $roleID) {
                    $canAlter = $rule['canAlter'];
                    break;
                }
            }

            if ($canAlter) {
                $_SESSION['message'] = $this->staffModel->updateRoleProcessTasks($roleID, $processes);
            } else {
                $_SESSION['message'] = "You dont have the authority to alter this role";
            }
        } else {
            $_SESSION['message'] = "You dont have permission to alter roles";
        }
        header("Location: index.php?page=staff&action=manageRoles");
    }

    /**
     * Create new role
     *
     * Creates a new role with the given name.
     */
    public function createRole() {
        if (in_array('canCreateRoles', $_SESSION['permissions'])) {
            $name = strtolower(trim($_POST['name']));
            $creation = $this->staffModel->insertRole($name);

            if ($creation) {
                $_SESSION['message'] = "Success: Role named {$name} created.";
            } else {
                $_SESSION['message'] = "Error: Role name already exists.";
            }
        } else {
            $_SESSION['message'] = "Error: You dont have permission to create roles.";
        }
        header("Location: index.php?page=staff&action=manageRoles");
    }

    /**
     * Delete role
     *
     * Removes a role if governance rules allow.
     */
    public function deleteRole() {
        if (in_array('canDeleteRoles', $_SESSION['permissions'])) {
            $roleID = $_POST['selectedID'];
            $governanceRules = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));

            $canDelete = true;

            foreach ($governanceRules as $rule) {
                if ($rule['roleSubjectID'] == $roleID) {
                    $canDelete = $rule['canDelete'];
                    break;
                }
            }

            if ($canDelete) {
                $_SESSION['message'] = $this->staffModel->removeRole($roleID);
            } else {
                $_SESSION['message'] = "You dont have the authority to delete this role";
            }
        } else {
            $_SESSION['message'] = "You dont have permission to delete roles";
        }
        header("Location: index.php?page=staff&action=manageRoles");
    }
}
