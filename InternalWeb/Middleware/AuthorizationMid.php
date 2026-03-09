<?php
class AuthorizationMid {
    public static function check($page) {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($page === 'staff' && !in_array('canManageStaff', $_SESSION['permissions'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }
}
