<?php
class AuthorizationC {
    private $staffModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        $this->staffModel = new StaffM($pdo);
    }

    public function showLogin() {
        $page = "login";
        $error = null;
        require __DIR__ . '/../Views/Login/Page.php';
    }

    public function checkUserExists() {
        $existence = $this->staffModel->getAccount($_SESSION['id']);
        if (!$existence) {
            $this->logout();
        }
    }

    public function showStaff($search = '', $status = '', $error = '') {
        $page = "staff";

        $roleList = $this->staffModel->getAllRoles();
        $userRoles = $this->staffModel->getAllUserRoles();
        $roleGovernance = $this->staffModel->getRoleManagementGovernance($this->staffModel->getUserRoles($_SESSION['id']));

        if ($search !== '' || $status !== '') {
            $staffList = $this->staffModel->getfilteredStaff($search, $status);
        } else {
            $staffList = $this->staffModel->getStaffList();
        }

        $currentUserId = $_SESSION['id'];
        $staffList = array_filter($staffList, function ($staff) use ($currentUserId) {
            return $staff['id'] !== $currentUserId;
        });

        require __DIR__ . '/../Views/Staff/Page.php';
    }

    public function login() {
        $username = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->staffModel->authenticate($username, $password);
        $error = null;

        if ($user) {
            $this->staffModel->updateOnlineStatus($user['id'], true);

            session_regenerate_id(true);
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['phoneNumber'] = $user['phone'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['full_name'] = $user['firstName'] . ' ' . $user['lastName'];
            $_SESSION['logged_in'] = true;

            $this->getPermissions();
            $this->staffModel->updateLastLogin($user['id']);
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = "Invalid username or password.";
            require __DIR__ . '/../Views/Login/Page.php';
        }
    }

    public function getPermissions() {
        $permissions = $this->staffModel->getUserPermissions($_SESSION['id']);
        $_SESSION['permissions'] = $permissions;
    }

    public function setUserRoles() {
        $error = '';
        if (in_array('canAlterAccountRoles', $_SESSION['permissions'])) {
            $userID = $_POST['selectedID'];
            $userRoles = $_POST['roleHiddenInput'] ?? [];
            $this->staffModel->updateUserRoles($userID, $userRoles);
        } else {
            $error = "You dont have permission to alter user roles"; //doesnt work fix
        }
        header("Location: index.php?page=staff");
    }

    public function createAccount() {
        $username = strtolower(trim($_POST['username']));
        $firstName = ucwords(strtolower(trim($_POST['firstName'])));
        $middleName = ucwords(strtolower(trim($_POST['middleName'] ?? '')));
        $lastName = ucwords(strtolower(trim($_POST['lastName'])));
        $phoneNum = $_POST['phoneNum'];
        $emailAddress = $_POST['emailAddress'];

        $creation = $this->staffModel->insertAccount($username, $firstName, $middleName, $lastName, $phoneNum, $emailAddress);
        $error = null;

        if ($creation) {
            header('Location: index.php?page=staff');
        } else {
            $page = 'staff';
            $lastPage = 'staff';
            $error = "Username already exists.";
            require __DIR__ . '/../Views/Staff/CreateAccount.php';
        }
    }

    public function deleteAccount() {
        $error = '';
        if (in_array('canDeleteUserAccounts', $_SESSION['permissions'])) {
            $id = $_POST['deletedID'];
            $this->staffModel->removeAccount($id);
        } else {
            $error = "You dont have permission to delete accounts"; //doesnt work fix
        }
        header("Location: index.php?page=staff");
    }

    public function setUsername() {
        $username = strtolower(trim($_POST['username'] ?? ''));

        $update = $this->staffModel->updateUsername($_SESSION['id'], $username);
        $error = null;

        if ($update) {
            $_SESSION['username'] = $username;
            header('Location: index.php?page=account');
        } else {
            $page = 'account';
            $error = "Username already exists.";
            require __DIR__ . '/../Views/Account/Page.php';
        }
    }

    public function setContacts() {
        $postPhone = $_POST['phoneNum'] ?? null;
        $postEmail = $_POST['emailAddress'] ?? null;

        $phoneNum = (!empty($postPhone)) ? $postPhone : $_SESSION['phoneNumber'];
        $emailAddress = (!empty($postEmail)) ? $postEmail : $_SESSION['email'];

        $this->staffModel->updateContacts($_SESSION['id'], $phoneNum, $emailAddress);

        $_SESSION['phoneNumber'] = $phoneNum;
        $_SESSION['email'] = $emailAddress;
        header('Location: index.php?page=account');
    }

    public function setPassword() {
        $passCurrent = $_POST['passwordCurrent'];
        $passNew = $_POST['passwordNew'];
        $passRetype = $_POST['passwordRetype'];

        $user = $this->staffModel->authenticate($_SESSION['username'], $passCurrent);

        if (!$user) {
            $page = 'account';
            $error = "Incorrect Password.";
            require __DIR__ . '/../Views/Account/Page.php';
            return;
        }

        if ($passNew !== $passRetype) {
            $page = 'account';
            $error = "New And Retyped Password Mismatch.";
            require __DIR__ . '/../Views/Account/Page.php';
            return;
        }

        $this->staffModel->updatePassword($_SESSION['id'], $passNew);
        header('Location: index.php?page=account');
    }

    public function logout() {
        $this->staffModel->updateOnlineStatus($_SESSION['id'], false);
        session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    public function keepOnline() {
        $this->staffModel->updateOnlineStatus($_SESSION['id'], true);
    }
}
