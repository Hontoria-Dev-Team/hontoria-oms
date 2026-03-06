<?php
class AuthorizationC {
    private $staffModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        $this->staffModel = new StaffM($pdo);
    }

    public function showLogin() {
        $page = "login";
        $pageTitle = "Internal Login";
        $error = null;
        require __DIR__ . '/../Views/Login/Page.php';
    }

    public function showStaff() {
        $page = "staff";
        $search = "";
        $status = "";
        $pageTitle = "Staff Panel - Hontoria OMS";
        $staffList = $this->staffModel->getAllStaff();
        $error = null;
        require __DIR__ . '/../Views/Staff/Page.php';
    }

    public function filterStaff($search, $status) {
        $page = "staff";
        $pageTitle = "Staff Panel - Hontoria OMS";
        $staffList = $this->staffModel->getfilteredStaff($search, $status);
        $error = null;
        require __DIR__ . '/../Views/Staff/Page.php';
    }

    public function login() {
        $username = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';

        $user = $this->staffModel->authenticate($username, $password);
        $error = null;

        if ($user) {
            $this->staffModel->updateOnlineStatus($user['id']);

            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['firstName'] . ' ' . $user['lastName'];
            $_SESSION['logged_in'] = true;

            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $error = "Invalid username or password.";
        }

        $pageTitle = "Internal Login";
        require __DIR__ . '/../Views/Login/Page.php';
    }

    public function logout() {
        $this->staffModel->updateOnlineStatus($_SESSION['user_id']);
        session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }

    // public function
}
