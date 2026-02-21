<?php
class AuthorizationC {
    private $userModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        $this->userModel = new StaffM($pdo);
    }

    public function showPage() {
        $pageTitle = "Internal Login";
        $error = null;
        require __DIR__ . '/../Views/Login/Page.php';
    }

    public function login() {
        $username = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? '';
        $error = null;

        if (empty($username) || empty($password)) {
            $error = 'Username and password are required.';
        } else {
            $user = $this->userModel->findByUsername($username);

            if ($user) {
                if (password_verify($password, $user['passwordHash'])) {

                    if ($user['isActive'] == 1) {

                        session_start();
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['full_name'] = trim($user['firstName'] . ' ' . $user['lastName']);
                        $_SESSION['logged_in'] = true;

                        $this->userModel->updateLastLogin($user['id']);

                        header('Location: index.php?page=dashboard');
                        exit;
                    } else {
                        $error = 'Account is deactivated. Contact administrator.';
                    }
                } else {
                    $error = 'Invalid username or password.';
                }
            } else {
                $error = 'Invalid username or password.';
            }
        }

        $pageTitle = "Internal Login";
        require __DIR__ . '/../Views/Login/Page.php';
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
