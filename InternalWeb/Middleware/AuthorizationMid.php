<?php
class AuthorizationMid {
    private static $pagePermissions = [
        'services' => 'canViewServiceList',
        'orders' => 'canViewOrders',
        'staff' => 'canViewStaffList',
    ];

    public static function check($page) {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: index.php?page=login');
            exit;
        }

        if (isset(self::$pagePermissions[$page]) && !in_array(self::$pagePermissions[$page], $_SESSION['permissions'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }
}
