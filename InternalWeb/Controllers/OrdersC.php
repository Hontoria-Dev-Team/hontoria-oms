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

    // ================================================================
    //  PAGE DISPLAY METHODS
    // ================================================================

    // Show the main orders dashboard with filtering options.
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

        require __DIR__ . '/../Views/Orders/Page.php';
    }

    // Show the archived orders page.
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

    // Show the order creation form.
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

        require __DIR__ . '/../Views/Orders/CreateOrder.php';
    }

    // Show the tasks page for the currently logged‑in user.
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

    // ================================================================
    //  ORDER ACTIONS
    // ================================================================

    // Create a new order with its process chain and groups.
    public function createOrder() {
        if (in_array('canCreateOrders', $_SESSION['permissions'])) {
            $subserviceID = $_POST['subserviceType'];
            $customerName = $_POST['customerName'];
            $messengerGCLink = $_POST['messengerGCLink'];
            $deadlineAt = $_POST['deadlineAt'];
            $groupDescriptions = $_POST['groupDescriptions'];
            $groupQuantities = $_POST['groupQuantities'];
            $processStatuses = $_POST['orderProcessStatus'];
            $minAssigns = $_POST['minAssigns'];
            $maxAssigns = $_POST['maxAssigns'];

            // Calculate total quantity
            $totalQuantity = array_sum($groupQuantities);

            // Get the selected subservice
            $subservice = $this->servicesModel->getSubservice($subserviceID);

            if (!$subservice) {
                $_SESSION['message'] = "Error: Invalid subservice selected.";
                header('Location: index.php?page=orders');
                return;
            }

            $pricePerUnit = $subservice['pricePerUnit'];
            $priceTotal = $totalQuantity * $pricePerUnit;

            // Apply discount if permission and provided
            if (in_array('canApplyDiscountToOrders', $_SESSION['permissions'])) {
                $discount = (float)($_POST['priceDiscount'] ?? 0);
                $priceTotal -= $discount;
                if ($priceTotal < 0) $priceTotal = 0;
            }

            $orderProcess = [];

            foreach (array_keys($processStatuses) as $index) {
                $orderProcess[] = [
                    'status' => $processStatuses[$index],
                    'minAssign' => $minAssigns[$index],
                    'maxAssign' => $maxAssigns[$index],
                ];
            }

            $_SESSION['message'] = $this->ordersModel->insertOrder($subserviceID, $customerName, $messengerGCLink, $deadlineAt, $priceTotal, $groupDescriptions, $groupQuantities, $orderProcess);
        } else {
            $_SESSION['message'] = "Error: You do not have permission to create orders.";
        }
        header('Location: index.php?page=orders');
    }

    // Archive an incomplete order (soft delete).
    public function deleteOrder() {
        if (in_array('canDeleteOrders', $_SESSION['permissions'])) {
            $orderID = $_POST['selectedID'];
            $_SESSION['message'] = $this->ordersModel->archiveOrder($orderID, false);
        } else {
            $_SESSION['message'] = "Error: You do not have permission to delete orders.";
        }
        header('Location: index.php?page=orders');
    }

    // Update the deadline of an existing order.
    public function setDeadline() {
        if (in_array('canAlterOrders', $_SESSION['permissions'])) {
            $orderID = $_POST['selectedID'];
            $newDeadline = $_POST['newDeadline'];

            $_SESSION['message'] = $this->ordersModel->updateDeadline($orderID, $newDeadline);
        } else {
            $_SESSION['message'] = "Error: You do not have permission to alter orders.";
        }
        header('Location: index.php?page=orders');
    }

    // Complete an order (archive with status “completed”).
    public function verifyCompleteOrder() {
        if (in_array('canVerifyOrderCompletion', $_SESSION['permissions'])) {
            $orderID = $_POST['selectedID'];

            $_SESSION['message'] = $this->ordersModel->archiveOrder($orderID, true);
        } else {
            $_SESSION['message'] = "Error: You do not have permission to verify orders.";
        }
        header('Location: index.php?page=orders');
    }

    // ================================================================
    //  TASK ASSIGNMENT & MANAGEMENT
    // ================================================================

    // Self‑assign the current user to a task.
    public function assignToTask() {
        $orderProcessID = $_POST['orderProcessID'];

        if (!in_array('canSelfAssignToTasks', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to assign yourself to tasks.";
        } else if ($this->ordersModel->hasMiscTask($_SESSION['id'])) {
            $_SESSION['message'] = "Error: You are currently assigned to a miscellaneous task and cannot assign yourself to process tasks yet.";
        } else {
            $_SESSION['message'] = $this->ordersModel->insertUserProcessTask($_SESSION['id'], $orderProcessID);
        }

        header('Location: index.php?page=tasks');
    }

    // Assign a specific user (staff member) to a task.
    public function assignEmployeeToTask() {
        $userID = $_POST['userID'];
        $orderProcessID = $_POST['orderProcessID'];

        if (!in_array('canAssignStaffToOrders', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to assign staff to tasks.";
        } else {
            $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($userID, $_SESSION['id']);

            if (!$governanceRules['canGrant']) {
                $_SESSION['message'] = "Error: You do not have the authority to assign tasks to this user because of their role.";
            } else {
                $_SESSION['message'] = $this->ordersModel->insertUserProcessTask($userID, $orderProcessID);
            }
        }

        header('Location: index.php?page=orders');
    }

    // Unassign a user from a task (self or other, depending on permission).
    public function unassignEmployeeToTask() {
        $userID = $_POST['userID'];
        $orderProcessID = $_POST['orderProcessID'];

        if ($userID == $_SESSION['id']) {
            if (!in_array('canSelfUnassignToTasks', $_SESSION['permissions'])) {
                $_SESSION['message'] = "Error: You do not have permission to unassign yourself from tasks.";
            } else {
                $_SESSION['message'] = $this->ordersModel->removeUserProcessTask($userID, $orderProcessID);
            }
        } else {
            if (!in_array('canUnassignStaffToOrders', $_SESSION['permissions'])) {
                $_SESSION['message'] = "Error: You do not have permission to unassign staff from tasks.";
            } else {
                $governanceRules = $this->staffModel->getGovernanceRulesBetweenUsers($userID, $_SESSION['id']);

                if (!$governanceRules['canRevoke']) {
                    $_SESSION['message'] = "Error: You do not have the authority to unassign tasks to this user because of their role.";
                } else {
                    $_SESSION['message'] = $this->ordersModel->removeUserProcessTask($userID, $orderProcessID);
                }
            }
        }

        header('Location: index.php?page=orders');
    }

    // Update the status of a user process task (pending, partially complete, complete).
    public function changeUserProcessTaskStatus() {
        $orderProcessID = $_POST['selectedID'];
        $taskStatus = $_POST['taskStatus'];

        if ($this->ordersModel->hasMiscTask($_SESSION['id'])) {
            $_SESSION['message'] = "Error: You are currently assigned to a miscellaneous task and cannot update or do other tasks yet.";
        } else if (!$this->ordersModel->hasEnoughAssigned($orderProcessID)) {
            $_SESSION['message'] = "Error: This task does not have enough staff assigned to begin work yet.";
        } else {
            $result = $this->ordersModel->updateUserProcessTaskStatus($_SESSION['id'], $orderProcessID, $taskStatus);
            if (is_string($result)) {
                $_SESSION['message'] = $result;
            }
        }

        header('Location: index.php?page=tasks');
    }

    // ================================================================
    //  ORDER DESIGN & VARIABLE LIST ACTIONS
    // ================================================================

    // Upload a design image for an order.
    public function uploadOrderDesign() {
        $orderID = $_POST['selectedID'];
        $designImage = $_FILES['designImage'];

        if (!empty($_POST['orderPageUpdate'])) {
            if (in_array('canAlterOrders', $_SESSION['permissions'])) {
                $_SESSION['message'] = $this->ordersModel->insertOrderDesign($orderID, $designImage, true);
            } else {
                $_SESSION['message'] = "Error: You do not have permission to alter orders.";
            }
        } else {
            if ($this->ordersModel->hasMiscTask($_SESSION['id'])) {
                $_SESSION['message'] = "Error: You are currently assigned to a miscellaneous task and cannot update or do other tasks yet.";
            } else if (!$this->ordersModel->hasEnoughAssigned(0, $_SESSION['id'], $orderID)) {
                $_SESSION['message'] = "Error: This task does not have enough staff assigned to begin work yet.";
            } else {
                $_SESSION['message'] = $this->ordersModel->insertOrderDesign($orderID, $designImage, false);
            }
        }

        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
        header('Location: index.php?page=' . $redirectPage);
    }

    // Update the variable list (JSON data) for an order.
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

        if (!empty($_POST['orderPageUpdate'])) {
            if (in_array('canAlterOrders', $_SESSION['permissions'])) {
                $_SESSION['message'] = $this->ordersModel->updateVariableList($orderID, $data, true);
            } else {
                $_SESSION['message'] = "Error: You do not have permission to alter orders.";
            }
        } else {
            if ($this->ordersModel->hasMiscTask($_SESSION['id'])) {
                $_SESSION['message'] = "Error: You are currently assigned to a miscellaneous task and cannot update or do other tasks yet.";
            } else if (!$this->ordersModel->hasEnoughAssigned(0, $_SESSION['id'], $orderID)) {
                $_SESSION['message'] = "Error: This task does not have enough staff assigned to begin work yet.";
            } else {
                $_SESSION['message'] = $this->ordersModel->updateVariableList($orderID, $data, false);
            }
        }

        $redirectPage = isset($_GET['page']) ? $_GET['page'] : 'tasks';
        header("Location: index.php?page=" . $redirectPage);
    }
}
