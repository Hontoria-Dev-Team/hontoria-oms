<?php
class AuthorizationMid {
    public static function check() {
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: index.php?page=login');
            exit;
        }
    }
}
