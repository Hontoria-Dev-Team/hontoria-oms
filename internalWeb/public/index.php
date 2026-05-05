<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/AuthorizationC.php';
require_once __DIR__ . '/../Controllers/ServicesC.php';
require_once __DIR__ . '/../Controllers/OrdersC.php';
require_once __DIR__ . '/../Controllers/InventoryC.php';
require_once __DIR__ . '/../Controllers/SalesC.php';
require_once __DIR__ . '/../Middleware/AuthorizationMid.php';

session_start();

// ─────────────────── security headers ───────────────────
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; frame-ancestors 'self'; base-uri 'self'; form-action 'self';");
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

if (!isset($_SESSION['permissions']) || !is_array($_SESSION['permissions'])) {
    $_SESSION['permissions'] = [];
}

// ─────────────────── helpers ───────────────────

//
// Sanitise a GET value to only contain safe characters.
// Returns a default if the value is missing or invalid.
//
function SanitiseRouteValue(string $key, string $default = 'show'): string {
    $value = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);
    if ($value === null) {
        return $default;
    }
    $value = trim($value);
    // Allow only letters, digits, underscore, hyphen
    $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
    if ($value === '' || preg_match('/^[^a-zA-Z0-9]/', $value)) {
        return $default;
    }
    return $value;
}

//
// Send a redirect header and stop execution immediately.
//
function Redirect(string $uri): void {
    header('Location: ' . $uri);
    exit;
}

// ─────────────────── routing ───────────────────

$page   = SanitiseRouteValue('page', 'login');
$action = SanitiseRouteValue('action', 'show');

$allowedPages = [
    'login',
    'logout',
    'dashboard',
    'staff',
    'account',
    'services',
    'orders',
    'tasks',
    'inventory',
    'sales'
];
if (!in_array($page, $allowedPages, true)) {
    $page   = 'login';
    $action = 'show';
}

$authorization = new AuthorizationC($pdo);
$services      = new ServicesC($pdo);
$orders        = new OrdersC($pdo);
$inventory     = new InventoryC($pdo);
$sales         = new SalesC($pdo);

$protectedPages = ['dashboard', 'account', 'staff', 'services', 'orders', 'tasks', 'inventory', 'sales'];
if (in_array($page, $protectedPages, true)) {
    AuthorizationMid::check($page);
}

switch ($page) {
    // ────────────────── login ──────────────────
    case 'login':
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            Redirect('index.php?page=dashboard');
        }
        if ($action === 'authenticate') {
            $authorization->login();
        } else {
            $authorization->showLogin();
        }
        break;

    // ────────────────── logout ──────────────────
    case 'logout':
        $authorization->logout();
        break;

    // ────────────────── dashboard ──────────────────
    case 'dashboard':
        require_once __DIR__ . '/../Views/Dashboard/Page.php';
        break;

    // ────────────────── staff ──────────────────
    case 'staff':
        $staffActions = [
            'filter'              => fn() => $authorization->showStaff(
                filter_input(INPUT_GET, 'search', FILTER_UNSAFE_RAW) ?? '',
                filter_input(INPUT_GET, 'status', FILTER_UNSAFE_RAW) ?? ''
            ),
            'setRoles'              => fn() => $authorization->setUserRoles(),
            'create'                => fn() => $authorization->showAccountCreationPage(),
            'createFinal'           => fn() => $authorization->createAccount(),
            'manageRoles'           => fn() => $authorization->showRoleManagementPage(),
            'changeRolePermissions' => fn() => $authorization->setRolePermissions(),
            'changeManagementRules' => fn() => $authorization->setRoleManagementGovernance(),
            'changeProcessTasks'    => fn() => $authorization->setRoleProcessTasks(),
            'createRole'            => fn() => $authorization->createRole(),
            'deleteRole'            => fn() => $authorization->deleteRole(),
            'delete'                => fn() => $authorization->deleteAccount(),
            'assignMiscTask'        => fn() => $authorization->assignMiscTask(),
            'updateMiscTask'        => fn() => $authorization->setMiscTask(),
        ];
        ($staffActions[$action] ?? fn() => $authorization->showStaff())();
        break;

    // ────────────────── account ──────────────────
    case 'account':
        $accountActions = [
            'rename'         => fn() => $authorization->setUsername(),
            'updateContacts' => fn() => $authorization->setContacts(),
            'changePassword' => fn() => $authorization->setPassword(),
            'uploadImage'    => fn() => $authorization->uploadAccountImage(),
            'setUserNote'    => fn() => $authorization->setUserNote(),
        ];
        ($accountActions[$action] ?? fn() => $authorization->showAccountManagementPage())();
        break;

    // ────────────────── services ──────────────────
    case 'services':
        $serviceID    = filter_input(INPUT_GET, 'serviceID', FILTER_VALIDATE_INT, ['options' => ['default' => -1]]);
        $subserviceID = filter_input(INPUT_GET, 'subserviceID', FILTER_VALIDATE_INT, ['options' => ['default' => -1]]);

        $serviceActions = [
            'toggleServiceStatus'    => fn() => $services->toggleServiceStatus(),
            'toggleSubserviceStatus' => fn() => $services->toggleSubserviceStatus(),
            'toggleHasDesign'        => fn() => $services->toggleHasDesign(),
            'toggleHasVariableList'  => fn() => $services->toggleHasVariableList(),
            'createService'          => fn() => $services->createService(),
            'deleteService'          => fn() => $services->removeService(),
            'createSubservice'       => fn() => $services->createSubservice(),
            'deleteSubservice'       => fn() => $services->removeSubservice(),
            'updateServiceProcess'   => fn() => $services->setServiceProcess(),
            'manageProcesses'        => fn() => $services->showProcessesManagementPage(),
            'createProcess'          => fn() => $services->createProcess(),
            'updateProcess'          => fn() => $services->setProcess(),
            'deleteProcess'          => fn() => $services->removeProcess(),
            'updateSubserviceInfo'   => fn() => $services->setSubserviceInfo(),
            'uploadSubserviceImages' => fn() => $services->uploadSubserviceImages(),
            'removeSubserviceImage'  => fn() => $services->removeSubserviceImage(),
        ];
        ($serviceActions[$action] ?? fn() => $services->showServices($serviceID, $subserviceID))();
        break;

    // ────────────────── orders ──────────────────
    case 'orders':
        $orderActions = [
            'create'               => fn() => $orders->showOrderCreation(),
            'createFinal'          => fn() => $orders->createOrder(),
            'changeDeadline'       => fn() => $orders->setDeadline(),
            'delete'               => fn() => $orders->deleteOrder(),
            'assignEmployeeToTask' => fn() => $orders->assignEmployeeToTask(),
            'removeAssignment'     => fn() => $orders->unassignEmployeeToTask(),
            'verifyComplete'       => fn() => $orders->verifyCompleteOrder(),
            'uploadDesign'         => fn() => $orders->uploadOrderDesign(),
            'updateVariableList'   => fn() => $orders->updateVariableList(),
            'viewArchive'          => fn() => $orders->showArchive(),
        ];
        ($orderActions[$action] ?? fn() => $orders->showOrders())();
        break;

    // ────────────────── tasks ──────────────────
    case 'tasks':
        $taskActions = [
            'assignToTask'       => fn() => $orders->assignToTask(),
            'uploadDesign'       => fn() => $orders->uploadOrderDesign(),
            'updateVariableList' => fn() => $orders->updateVariableList(),
            'updateTaskStatus'   => fn() => $orders->changeUserProcessTaskStatus(),
        ];
        ($taskActions[$action] ?? fn() => $orders->showTasks())();
        break;

    // ────────────────── inventory ──────────────────
    case 'inventory':
        $inventoryActions = [
            'updateRecord'            => fn() => $inventory->setInventoryRecord(),
            'resetRecord'             => fn() => $inventory->removeInventoryRecord(),
            'createItem'              => fn() => $inventory->createInventoryItem(),
            'deleteItem'              => fn() => $inventory->removeInventoryItem(),
            'changeMinQuantity'       => fn() => $inventory->changeInventoryItemMinQuantity(),
            'changeMaxAvgConsumption' => fn() => $inventory->changeInventoryItemMaxAvgConsumption(),
        ];
        ($inventoryActions[$action] ?? fn() => $inventory->showPage())();
        break;

    // ────────────────── sales ──────────────────
    case 'sales':
        $salesActions = [
            'createInflowRecord'  => fn() => $sales->createInflowRecord(),
            'createOutflowRecord' => fn() => $sales->createOutflowRecord(),
            'deleteRecord'        => fn() => $sales->removeRecord(),
        ];
        ($salesActions[$action] ?? fn() => $sales->showPage())();
        break;

    // ────────────────── fallback ──────────────────
    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}

// Session maintenance (only for logged‑in sessions)
if ($page !== 'login') {
    $authorization->checkUserExists();
    $authorization->refreshLastActiveAt();
    $authorization->getPermissions();
}
