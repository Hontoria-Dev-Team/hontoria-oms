<?php
class OrdersC {
    private $staffModel;
    private $ordersModel;
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/StaffM.php';
        require_once __DIR__ . '/../Models/OrdersM.php';
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->staffModel = new StaffM($pdo);
        $this->ordersModel = new OrdersM($pdo);
        $this->servicesModel = new ServicesM($pdo);
    }

    public function showOrders() {
        $page = "orders";
        $search = trim($_GET['search'] ?? '');
        $status = trim(strtolower($_GET['status'] ?? ''));
        $serviceID = filter_input(INPUT_GET, 'serviceID', FILTER_VALIDATE_INT, ['options' => ['default' => -1]]);

        $serviceList = $this->servicesModel->getServices();
        $orderList = $this->ordersModel->getFilteredOrders($search, $status, $serviceID);
        $orderProcessList = $this->ordersModel->getAllOrderProcesses();
        $taskAssigneeList = $this->ordersModel->getAllTaskAssigneeList();
        $userProcessList = $this->staffModel->getAllUserAssignableProcessTasks();
        $userTaskCountTally = $this->staffModel->getAllUsersTaskCount();
        $userProcessTasksList = $this->staffModel->getAllUserProcessTasks();
        $designList = $this->ordersModel->getAllOrderDesigns();
        $variableListMap = $this->ordersModel->getAllOrderVariableListMapped();

        $orderAssigneeCountMap = [];

        foreach ($this->ordersModel->getAllOrdersAssigneeCount() as $item) {
            $orderAssigneeCountMap[$item['orderID']] = $item['assigneeCount'];
        }

        // if ($search !== '' || $status !== '') {
        //     $staffList = $this->staffModel->getfilteredStaff($search, $status);
        // } else {
        //     $staffList = $this->staffModel->getStaffList();
        // }

        // $currentUserId = $_SESSION['id'];
        // $staffList = array_filter($staffList, function ($staff) use ($currentUserId) {
        //     return $staff['id'] !== $currentUserId;
        // });

        // foreach ($staffList as &$staff) {
        //     $perms = $this->staffModel->getUserPermissions($staff['id']);
        //     $staff['canManageStaff'] = in_array('canManageStaff', $perms) ? 1 : 0;
        // }
        // unset($staff);

        $error = null;
        require __DIR__ . '/../Views/Orders/Page.php';
    }

    public function showArchive($search = '') {
        $page = "orders";
        $lastPage = 'orders';
        $backLink = 'index.php?page=orders';

        $orderList = $this->ordersModel->getAllArchivedOrders();
        $orderDesignList = $this->ordersModel->getAllArchivedOrderDesigns();
        $orderGroupList = $this->ordersModel->getAllArchivedOrderGroups();
        $orderAssignmentList = $this->ordersModel->getAllArchivedOrderAssignments();

        require __DIR__ . '/../Views/Orders/Archive.php';
    }

    public function showOrderCreation() {
        $page = "orders";
        $lastPage = 'orders';
        $backLink = 'index.php?page=orders';
        $processList = $this->servicesModel->getAllProcesses();

        $serviceList = array_filter(
            $this->servicesModel->getServices(),
            function ($service) {
                if (empty($this->servicesModel->getServiceProcess($service['id']))) {
                    return false;
                }
                return $service['isActive'] != '0';
            }
        );

        $subserviceList = [];
        $serviceProcessList = [];

        foreach ($serviceList as $service) {
            $subservices = $this->servicesModel->getSubservices($service['id']);
            $serviceProcesses = $this->servicesModel->getServiceProcess($service['id']);

            $subservices = array_filter($subservices, function ($subservice) {
                return $subservice['isActive'] !== '0';
            });

            foreach ($subservices as &$subservice) {
                $subservice['serviceID'] = $service['id'];
            }
            unset($subservice);

            foreach ($serviceProcesses as &$serviceProcess) {
                $serviceProcess['serviceID'] = $service['id'];
            }
            unset($serviceProcess);

            $subserviceList = array_merge($subserviceList, $subservices);
            $serviceProcessList = array_merge($serviceProcessList, $serviceProcesses);
        }

        $error = null;
        require __DIR__ . '/../Views/Orders/CreateOrder.php';
    }

    public function showTasks() {
        $page = "tasks";
        $miscTaskAssigned = $this->staffModel->getMiscellaneousTaskByUserID($_SESSION['id']);
        $roleProcessTasks = $this->staffModel->getUserRoleProcessTasks($_SESSION['id']);
        $availableTasks =  $this->ordersModel->getAvailableOrderTasks($_SESSION['id'], $roleProcessTasks);
        $assigneeList =  $this->ordersModel->getAllTaskAssigneeList();
        $designList = $this->ordersModel->getAllOrderDesigns();
        $variableListMap = $this->ordersModel->getAllOrderVariableListMapped();
        $orderGroupList = $this->ordersModel->getAllOrderGroups();
        require __DIR__ . '/../Views/Tasks/Page.php';
    }

    public function createOrder() {
        $subserviceID = $_POST['subserviceType'];
        $customerName = $_POST['customerName'];
        $messengerGCLink = $_POST['messengerGCLink'];
        $deadlineAt = $_POST['deadlineAt'];
        $priceTotal = $_POST['priceTotal'];
        $groupDescriptions = $_POST['groupDescriptions'];
        $groupQuantities = $_POST['groupQuantities'];
        $processStatuses = $_POST['orderProcessStatus'];
        $minAssigns = $_POST['minAssigns'];
        $maxAssigns = $_POST['maxAssigns'];

        $orderProcess = [];

        foreach (array_keys($processStatuses) as $index) {
            $orderProcess[] = [
                'status' => $processStatuses[$index],
                'minAssign' => $minAssigns[$index],
                'maxAssign' => $maxAssigns[$index],
            ];
        }

        $this->ordersModel->insertOrder($subserviceID, $customerName, $messengerGCLink, $deadlineAt, $priceTotal, $groupDescriptions, $groupQuantities, $orderProcess);
        header('Location: index.php?page=orders');
    }

    public function deleteOrder() {
        $orderID = $_POST['selectedID'];

        $_SESSION['message'] = $this->ordersModel->archiveOrder($orderID, false);

        header('Location: index.php?page=orders');
    }

    public function setDeadline() {
        $orderID = $_POST['selectedID'];
        $newDeadline = $_POST['newDeadline'];

        $this->ordersModel->updateDeadline($orderID, $newDeadline);
        header('Location: index.php?page=orders');
    }

    public function assignToTask() {
        $orderProcessID = $_POST['orderProcessID'];

        if (!in_array('canSelfAssignToTasks', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to assign yourself to tasks.";
        } else {
            $this->ordersModel->insertUserProcessTask($_SESSION['id'], $orderProcessID);
        }

        header('Location: index.php?page=tasks');
    }

    public function assignEmployeeToTask() {
        $userID = $_POST['userID'];
        $orderProcessID = $_POST['orderProcessID'];

        if (!in_array('canAssignStaffToOrders', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to assign staff to tasks.";
        } else {
            $this->ordersModel->insertUserProcessTask($userID, $orderProcessID);
        }

        header('Location: index.php?page=orders');
    }

    public function unassignEmployeeToTask() {
        $userID = $_POST['userID'];
        $orderProcessID = $_POST['orderProcessID'];

        if ($userID == $_SESSION['id']) {
            if (!in_array('canSelfUnassignToTasks', $_SESSION['permissions'])) {
                $_SESSION['message'] = "Error: You do not have permission to unassign yourself from tasks.";
            } else {
                $this->ordersModel->removeUserProcessTask($userID, $orderProcessID);
            }
        } else {
            if (!in_array('canUnassignStaffToOrders', $_SESSION['permissions'])) {
                $_SESSION['message'] = "Error: You do not have permission to unassign staff from tasks.";
            } else {
                $this->ordersModel->removeUserProcessTask($userID, $orderProcessID);
            }
        }

        header('Location: index.php?page=orders');
    }

    public function changeUserProcessTaskStatus() {
        $orderProcessID = $_POST['selectedID'];
        $taskStatus = $_POST['taskStatus'];

        $result = $this->ordersModel->updateUserProcessTaskStatus($_SESSION['id'], $orderProcessID, $taskStatus);
        if (is_string($result)) {
            $_SESSION['message'] = $result;
        }

        header('Location: index.php?page=tasks');
    }

    public function uploadOrderDesign() {
        $orderID = $_POST['selectedID'];
        $designImage = $_FILES['designImage'];

        $this->ordersModel->insertOrderDesign($orderID, $designImage);

        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
        header('Location: index.php?page=' . $redirectPage);
    }

    public function updateVariableList() {
        $orderID = $_POST['selectedID'];
        $json = $_POST['variableListData'] ?? '';

        if (!$orderID || !$json) {
            $_SESSION['message'] = "Error: Missing data.";
            $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
            header("Location: index.php?page=" . $redirectPage);
            exit;
        }

        $data = json_decode($json, true);
        if (!$data) {
            $_SESSION['message'] = "Error: Invalid data.";
            $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
            header("Location: index.php?page=" . $redirectPage);
            exit;
        }

        $_SESSION['message'] = $this->ordersModel->updateVariableList($orderID, $data);
        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
        header("Location: index.php?page=" . $redirectPage);
    }

    public function verifyCompleteOrder() {
        $orderID = $_POST['selectedID'];

        $_SESSION['message'] = $this->ordersModel->archiveOrder($orderID, true);

        header('Location: index.php?page=orders');
    }
}
