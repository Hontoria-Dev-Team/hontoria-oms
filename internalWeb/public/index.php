<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Controllers/ServicesC.php';
require_once __DIR__ . '/../Controllers/OrdersC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

session_start();

$page = $_GET['page'] ?? 'login';
$action = $_GET['action'] ?? 'show';

$authorization = new AuthorizationC($pdo);
$services = new ServicesC($pdo);
$orders = new OrdersC($pdo);

$protectedPages = ['dashboard', 'account', 'staff', 'services'];

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
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    case 'staff':
        if ($action === 'filter') {
            $search = $_GET['search'] ?? '';
            $status = $_GET['status'] ?? '';
            $authorization->showStaff($search, $status);
        } else if ($action === 'updatePermissions') {
            $authorization->updatePermissions();
        } else if ($action === 'create') {
            $lastPage = 'staff';
            $backLink = 'index.php?page=staff';
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
            } else if ($action === 'updateProcess') {
                $services->setServiceProcess($service);
            } else if ($action === 'create') {
                $services->createSubservice($service);
            } else if ($action === 'createProcess') {
                $services->createProcess($service);
            } else if ($action === 'delete') {
                $services->deleteSubservice($service);
            } else {
                $services->showService($service);
            }
        } else if ($action === 'updateStatus') {
            $services->toggleServiceStatus();
        } else if ($action === 'create') {
            $services->createService();
        } else if ($action === 'delete') {
            $services->deleteService();
        } else {
            $services->showServices();
        }
        break;

    case 'orders':
        if ($action === 'create') {
            $orders->showOrderCreation();
        } else if ($action === 'createFinal') {
            $orders->createOrder();
        } else if ($action === 'changeDeadline') {
            $orders->setDeadline();
        } else if ($action === 'delete') {
            $orders->deleteOrder();
        } else {
            $orders->showOrders();
        }
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

if ($page !== 'login') {
    $authorization->keepOnline();
}
