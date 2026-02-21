<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';

session_start();

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'show';

$controller = new AuthorizationC($pdo);

switch ($page) {
    case 'login':
        if ($action === 'authenticate') {
            $controller->login();
        } else {
            $controller->showPage();
        }
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'dashboard':
        // Check if logged in
        if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
            header('Location: index.php?page=login');
            exit;
        }
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    default:
        $controller->showPage();
        break;
}
