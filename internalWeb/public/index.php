<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Controllers/ServicesC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

session_start();

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'show';

$authorization = new AuthorizationC($pdo);
$services = new ServicesC($pdo);

$protectedPages = ['dashboard', 'staff', 'services'];

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
            $authorization->login();
        } else {
            $authorization->showLogin();
        }
        break;

    case 'logout':
        $authorization->logout();
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
            $authorization->showStaff($search, $status);
        } else if ($action === 'updatePermissions') {
            $authorization->updatePermissions();
        } else if ($action === 'create') {
            $lastPage = 'staff';
            $backLink = 'index.php?page=staff';
            $pageTitle = 'Account Creation - Hontoria OMS';
            require_once __DIR__ . '/../Views/Staff/CreateAccount.php';
        } else if ($action === 'createFinal') {
            $authorization->createAccount();
        } else if ($action === 'delete') {
            $authorization->deleteAccount();
        } else {
            $authorization->showStaff();
        }
        break;

    case 'account':
        $pageTitle = 'Account Panel - Hontoria OMS';
        if ($action === 'rename') {
            $authorization->setUsername();
        } else if ($action === 'updateContacts') {
            $authorization->setContacts();
        } else if ($action === 'changePassword') {
            $authorization->setPassword();
        } else {
            require_once __DIR__ . '/../Views/Account/Page.php';
        }
        break;

    case 'services':
        $service = $_GET['service'] ?? null;
        if ($service !== null) {
            if ($action === 'updateStatus') {
                $services->toggleSubserviceStatus($service);
            } else if ($action === 'updateInfo') {
                $services->setSubserviceInfo($service);
            } else if ($action === 'create') {
                $services->createSubservice($service);
            } else if ($action === 'delete') {
                $services->deleteSubservice($service);
            } else {
                $services->showService($service);
            }
        } else if ($action === 'updateStatus') {
            $services->toggleServiceStatus();
        } else {
            $services->showServices();
        }
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

if ($page !== 'login') {
    $authorization->keepOnline();
}
