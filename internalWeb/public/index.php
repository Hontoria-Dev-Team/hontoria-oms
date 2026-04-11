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

$protectedPages = ['dashboard', 'account', 'staff', 'services', 'orders'];

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
        } else if ($action === 'setRoles') {
            $authorization->setUserRoles();
        } else if ($action === 'create') {
            $authorization->showAccountCreationPage();
        } else if ($action === 'createFinal') {
            $authorization->createAccount();
        } else if ($action === 'manageRoles') {
            $authorization->showRoleManagementPage();
        } else if ($action === 'changeRolePermissions') {
            $authorization->setRolePermissions();
        } else if ($action === 'changeManagementRules') {
            $authorization->setRoleManagementGovernance();
        } else if ($action === 'changeProcessTasks') {
            $authorization->setRoleProcessTasks();
        } else if ($action === 'createRole') {
            $authorization->createRole();
        } else if ($action === 'deleteRole') {
            $authorization->deleteRole();
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
        if ($action === 'toggleServiceStatus') {
            $services->toggleServiceStatus();
        } else if ($action === 'toggleSubserviceStatus') {
            $services->toggleSubserviceStatus();
        } else if ($action === 'toggleHasDesign') {
            $services->toggleHasDesign();
        } else if ($action === 'toggleHasVariableList') {
            $services->toggleHasVariableList();
        } else if ($action === 'createService') {
            $services->createService();
        } else if ($action === 'deleteService') {
            $services->deleteService();
        } else if ($action === 'createSubservice') {
            $services->createSubservice();
        } else if ($action === 'deleteSubservice') {
            $services->deleteSubservice();
        } else if ($action === 'updateServiceProcess') {
            $services->setServiceProcess();
        } else if ($action === 'manageProcesses') {
            $services->showProcessesManagementPage();
        } else if ($action === 'createProcess') {
            $services->createProcess();
        } else if ($action === 'updateProcess') {
            $services->setProcess();
        } else if ($action === 'deleteProcess') {
            $services->deleteProcess();
        } else if ($action === 'updateSubserviceInfo') {
            $services->setSubserviceInfo();
        } else if ($action === 'uploadSubserviceImages') {
            $services->uploadSubserviceImages();
        } else if ($action === 'removeSubserviceImage') {
            $services->removeSubserviceImage();
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
        } else if ($action === 'assignEmployeeToTask') {
            $orders->assignEmployeeToTask();
        } else if ($action === 'removeAssignment') {
            $orders->unassignEmployeeToTask();
        } else {
            $orders->showOrders();
        }
        break;

    case 'tasks':
        if ($action === 'assignToTask') {
            $orders->assignToTask();
        } else if ($action === 'uploadDesign') {
            $orders->uploadOrderDesign();
        } else if ($action === 'updateTaskStatus') {
            $orders->changeUserProcessTaskStatus();
        } else {
            $orders->showTasks();
        }
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

if ($page !== 'login') {
    $authorization->checkUserExists();
    $authorization->keepOnline();
    $authorization->getPermissions();
}
