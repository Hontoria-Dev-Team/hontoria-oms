<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Controllers/ServicesC.php';
require_once __DIR__ . '/../Controllers/OrdersC.php';
require_once __DIR__ . '/../Controllers/InventoryC.php';
require_once __DIR__ . '/../Controllers/SalesC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

require_once __DIR__ . '/../Middleware/SecurityHeadersMid.php';

session_start();

// Security headers
SecurityHeadersMid::setHeaders();

if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    $_SESSION['permissions'] = [];
}

function sanitizeRouteValue(string $key, string $default = 'show'): string {
    $value = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);

    if ($value === null) {
        return $default;
    }

    $value = trim($value);
    $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);

    if ($value === '' || preg_match('/^[^a-zA-Z0-9]/', $value)) {
        return $default;
    }

    return $value;
}

function redirect(string $uri): void {
    header('Location: ' . $uri);
    exit;
}

$page = sanitizeRouteValue('page', 'login');
$action = sanitizeRouteValue('action', 'show');

$allowedPages = ['login', 'logout', 'dashboard', 'staff', 'account', 'services', 'orders', 'tasks', 'inventory', 'sales'];
if (!in_array($page, $allowedPages, true)) {
    $page = 'login';
    $action = 'show';
}

$authorization = new AuthorizationC($pdo);
$services = new ServicesC($pdo);
$orders = new OrdersC($pdo);
$inventory = new InventoryC($pdo);
$sales = new SalesC($pdo);

$protectedPages = ['dashboard', 'account', 'staff', 'services', 'orders', 'tasks', 'inventory', 'sales'];
if (in_array($page, $protectedPages, true)) {
    AuthorizationMid::check($page);
}

switch ($page) {
    case 'login':
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            redirect('index.php?page=dashboard');
        }

        if ($action === 'authenticate') {
            $authorization->login();
            break;
        }

        $authorization->showLogin();
        break;

    case 'logout':
        $authorization->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    case 'staff':
        $staffActions = [
            'filter' => function () use ($authorization) {
                $search = filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW) ?? '';
                $status = filter_input(INPUT_GET, 'status', FILTER_UNSAFE_RAW) ?? '';
                $authorization->showStaff(trim($search), trim($status));
            },
            'setRoles' => fn() => $authorization->setUserRoles(),
            'create' => fn() => $authorization->showAccountCreationPage(),
            'createFinal' => fn() => $authorization->createAccount(),
            'manageRoles' => fn() => $authorization->showRoleManagementPage(),
            'changeRolePermissions' => fn() => $authorization->setRolePermissions(),
            'changeManagementRules' => fn() => $authorization->setRoleManagementGovernance(),
            'changeProcessTasks' => fn() => $authorization->setRoleProcessTasks(),
            'createRole' => fn() => $authorization->createRole(),
            'deleteRole' => fn() => $authorization->deleteRole(),
            'delete' => fn() => $authorization->deleteAccount(),
            'assignMiscTask' => fn() => $authorization->assignMiscTask(),
            'updateMiscTask' => fn() => $authorization->setMiscTask(),
        ];

        $staffActions[$action] ?? $authorization->showStaff();
        break;

    case 'account':
        $accountActions = [
            'rename' => fn() => $authorization->setUsername(),
            'updateContacts' => fn() => $authorization->setContacts(),
            'changePassword' => fn() => $authorization->setPassword(),
            'uploadImage' => fn() => $authorization->uploadAccountImage(),
            'setUserNote' => fn() => $authorization->setUserNote(),
        ];

        $accountActions[$action] ?? $authorization->showAccountManagementPage();
        break;

    case 'services':
        $serviceID = filter_input(INPUT_GET, 'serviceID', FILTER_VALIDATE_INT, ['options' => ['default' => -1]]);
        $subserviceID = filter_input(INPUT_GET, 'subserviceID', FILTER_VALIDATE_INT, ['options' => ['default' => -1]]);

        $serviceActions = [
            'toggleServiceStatus' => fn() => $services->toggleServiceStatus(),
            'toggleSubserviceStatus' => fn() => $services->toggleSubserviceStatus(),
            'toggleHasDesign' => fn() => $services->toggleHasDesign(),
            'toggleHasVariableList' => fn() => $services->toggleHasVariableList(),
            'createService' => fn() => $services->createService(),
            'deleteService' => fn() => $services->removeService(),
            'createSubservice' => fn() => $services->createSubservice(),
            'deleteSubservice' => fn() => $services->removeSubservice(),
            'updateServiceProcess' => fn() => $services->setServiceProcess(),
            'manageProcesses' => fn() => $services->showProcessesManagementPage(),
            'createProcess' => fn() => $services->createProcess(),
            'updateProcess' => fn() => $services->setProcess(),
            'deleteProcess' => fn() => $services->removeProcess(),
            'updateSubserviceInfo' => fn() => $services->setSubserviceInfo(),
            'uploadSubserviceImages' => fn() => $services->uploadSubserviceImages(),
            'removeSubserviceImage' => fn() => $services->removeSubserviceImage(),
        ];

        ($serviceActions[$action] ?? fn() => $services->showServices($serviceID, $subserviceID))();
        break;

    case 'orders':
        $orderActions = [
            'create' => fn() => $orders->showOrderCreation(),
            'createFinal' => fn() => $orders->createOrder(),
            'changeDeadline' => fn() => $orders->setDeadline(),
            'delete' => fn() => $orders->deleteOrder(),
            'assignEmployeeToTask' => fn() => $orders->assignEmployeeToTask(),
            'removeAssignment' => fn() => $orders->unassignEmployeeToTask(),
            'verifyComplete' => fn() => $orders->verifyCompleteOrder(),
            'uploadDesign' => fn() => $orders->uploadOrderDesign(),
            'updateVariableList' => fn() => $orders->updateVariableList(),
            'viewArchive' => fn() => $orders->showArchive(),
        ];

        ($orderActions[$action] ?? fn() => $orders->showOrders())();
        break;

    case 'tasks':
        $taskActions = [
            'assignToTask' => fn() => $orders->assignToTask(),
            'uploadDesign' => fn() => $orders->uploadOrderDesign(),
            'updateVariableList' => fn() => $orders->updateVariableList(),
            'updateTaskStatus' => fn() => $orders->changeUserProcessTaskStatus(),
        ];

        ($taskActions[$action] ?? fn() => $orders->showTasks())();
        break;

    case 'inventory':
        $inventoryActions = [
            'updateRecord' => fn() => $inventory->setInventoryRecord(),
            'resetRecord' => fn() => $inventory->removeInventoryRecord(),
            'createItem' => fn() => $inventory->createInventoryItem(),
            'deleteItem' => fn() => $inventory->removeInventoryItem(),
            'changeMinQuantity' => fn() => $inventory->changeInventoryItemMinQuantity(),
            'changeMaxAvgConsumption' => fn() => $inventory->changeInventoryItemMaxAvgConsumption(),
        ];

        ($inventoryActions[$action] ?? fn() => $inventory->showPage())();
        break;

    case 'sales':
        $salesActions = [
            'createInflowRecord' => fn() => $sales->createInflowRecord(),
            'createOutflowRecord' => fn() => $sales->createOutflowRecord(),
            'deleteRecord' => fn() => $sales->removeRecord(),
        ];

        ($salesActions[$action] ?? fn() => $sales->showPage())();
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

if ($page !== 'login') {
    $authorization->checkUserExists();
    $authorization->refreshLastActiveAt();
    $authorization->getPermissions();
}
