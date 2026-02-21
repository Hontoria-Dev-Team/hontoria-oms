<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

session_start();

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'show';

$controller = new AuthorizationC($pdo);

$protectedPages = ['dashboard'];

if (in_array($page, $protectedPages)) {
    AuthorizationMid::check();
}

switch ($page) {
    case 'login':
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
            header('Location: index.php?page=dashboard');
            exit;
        }

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
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    default:
        $controller->showPage();
        break;
}
