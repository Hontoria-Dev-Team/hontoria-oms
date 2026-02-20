<?php
class AuthorizationC {
    private $userModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        $this->userModel = new StaffM($pdo);
    }

    public function showPage() {
        $pageTitle = "Internal Login";
        require __DIR__ . '/../Views/Login/Page.php';
    }
}
