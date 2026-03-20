<?php
class OrdersC {
    private $ordersModel;
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/OrdersM.php';
        $this->ordersModel = new OrdersM($pdo);
        $this->servicesModel = new ServicesM($pdo);
    }

    public function showOrders($search = '', $status = '') {
        $page = "orders";

        $orderList = $this->ordersModel->getAllOrders();
        $orderProcessList = $this->ordersModel->getAllOrderProcesses();

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

    public function showOrderCreation() {
        $page = "orders";
        $lastPage = 'orders';
        $backLink = 'index.php?page=orders';

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

    public function createOrder() {
        $subserviceID = $_POST['subserviceType'];
        $customerName = $_POST['customerName'];
        $messengerGCLink = $_POST['messengerGCLink'];
        $deadlineAt = $_POST['deadlineAt'];
        $priceTotal = $_POST['priceTotal'];
        $groupDescriptions = $_POST['groupDescriptions'];
        $groupQuantities = $_POST['groupQuantities'];
        $orderProcess = $_POST['orderProcess'];

        $this->ordersModel->insertOrder($subserviceID, $customerName, $messengerGCLink, $deadlineAt, $priceTotal, $groupDescriptions, $groupQuantities, $orderProcess);
        header('Location: index.php?page=orders');
    }

    public function deleteOrder() {
        $orderID = $_POST['selectedID'];

        $this->ordersModel->removeOrder($orderID);
        header('Location: index.php?page=orders');
    }

    public function setDeadline() {
        $orderID = $_POST['selectedID'];
        $newDeadline = $_POST['newDeadline'];

        $this->ordersModel->updateDeadline($orderID, $newDeadline);
        header('Location: index.php?page=orders');
    }
}
