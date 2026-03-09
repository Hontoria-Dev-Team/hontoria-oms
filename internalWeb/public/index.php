<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

session_start();

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'show';

$controller = new AuthorizationC($pdo);

$protectedPages = ['dashboard', 'staff'];

if (in_array($page, $protectedPages)) {
    AuthorizationMid::check($page);
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
            $controller->showLogin();
        }
        break;

    case 'logout':
        $controller->logout();
        break;

    case 'dashboard':
        $pageTitle = 'Dashboard - Hontoria OMS';
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    case 'staff':
        $pageTitle = 'Staff Panel - Hontoria OMS';
        if ($action === 'filter') {
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $controller->showStaff($search, $status);
        } else if ($action === 'updatePermissions') {
            $controller->updatePermissions();
        } else if ($action === 'create') {
            $lastPage = 'staff';
            $pageTitle = 'Account Creation - Hontoria OMS';
            require_once __DIR__ . '/../Views/Staff/CreateAccount.php';
        } else if ($action === 'createFinal') {
            $controller->createAccount();
        } else if ($action === 'delete') {
            $controller->deleteAccount();
        } else {
            $controller->showStaff();
        }
        break;

    case 'account':
        $pageTitle = 'Account Panel - Hontoria OMS';
        if ($action === 'rename') {
            $controller->setUsername();
        } else if ($action === 'updateContacts') {
            $controller->setContacts();
        } else if ($action === 'changePassword') {
            $controller->setPassword();
        } else {
            require_once __DIR__ . '/../Views/Account/Page.php';
        }
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

if ($page !== 'login') {
    $controller->keepOnline();
}
