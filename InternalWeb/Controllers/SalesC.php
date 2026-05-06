<?php
class SalesC {
    private $salesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/SalesM.php';
        $this->salesModel = new SalesM($pdo);
    }

    public function showPage() {
        $page = "sales";

        $granularity = $_GET['granularity'] ?? 'daily';
        $range = (int)($_GET['range'] ?? 30);
        $selectedDateRaw = $_GET['selectedDate'] ?? date('Y-m-d');
        $selectedTimestamp = strtotime($selectedDateRaw);
        if ($selectedTimestamp === false) {
            $selectedDate = date('Y-m-d');
        } else {
            $selectedDate = date('Y-m-d', $selectedTimestamp);
        }
        $endDate = $selectedDate;

        if ($granularity === 'daily') {
            $startDate = date('Y-m-d', strtotime("$selectedDate - $range days - 7 days"));
        } elseif ($granularity === 'weekly') {
            $days = $range * 7;
            $startDate = date('Y-m-d', strtotime("$selectedDate - $days days - 7 days"));
        } elseif ($granularity === 'monthly') {
            $startDate = date('Y-m-d', strtotime("$selectedDate - $range months - 7 days"));
        } else {
            $startDate = date('Y-m-d', strtotime("$selectedDate - 30 days - 7 days"));
        }

        $salesRecords = $this->salesModel->getFilteredSalesRecords($startDate, $endDate);
        $salesOrders = $this->salesModel->getAllSalesOrders();
        $canManageSalesRecords = in_array('canManageSalesRecords', $_SESSION['permissions'] ?? []);

        require __DIR__ . '/../Views/Sales/Page.php';
    }

    public function createInflowRecord() {
        if (!in_array('canManageSalesRecords', $_SESSION['permissions'] ?? [])) {
            $_SESSION['message'] = "Error: No proper permission to do action.";
            header("Location: index.php?page=sales");
            exit;
        }

        $value = $_POST['value'] ?? '';
        if ($value === '' || !is_numeric($value) || $value <= 0) {
            $_SESSION['message'] = "Error: Value must be a positive number.";
            header("Location: index.php?page=sales");
            exit;
        }

        if (isset($_POST['isOrderInflow'])) {
            $orderID = $_POST['orderID'] ?? '';
            if ($orderID === '') {
                $_SESSION['message'] = "Error: Order ID is required.";
                header("Location: index.php?page=sales");
                exit;
            }

            $result = $this->salesModel->updateSalesOrder($orderID, $value);

            if (strpos($result, 'Error:') === 0) {
                $_SESSION['message'] = $result;
                header("Location: index.php?page=sales");
                exit;
            }

            $type        = $result;
            $description = "Order #" . $orderID . " Payment";
        } else {
            $type        = $_POST['type'] ?? '';
            $description = $_POST['description'] ?? '';

            if ($type === '' || $description === '') {
                $_SESSION['message'] = "Error: Type and description are required.";
                header("Location: index.php?page=sales");
                exit;
            }
        }

        $this->salesModel->insertInflowRecord($type, $description, $value);
        $_SESSION['message'] = "Success: Inflow record added.";
        header("Location: index.php?page=sales");
        exit;
    }

    public function createOutflowRecord() {
        if (!in_array('canManageSalesRecords', $_SESSION['permissions'] ?? [])) {
            $_SESSION['message'] = "Error: No proper permission to do action.";
            header("Location: index.php?page=sales");
            exit;
        }

        $type        = $_POST['type'] ?? '';
        $description = $_POST['description'] ?? '';
        $value       = $_POST['value'] ?? '';

        if ($type === '' || $description === '' || $value === '') {
            $_SESSION['message'] = "Error: All fields are required.";
            header("Location: index.php?page=sales");
            exit;
        }

        if (!is_numeric($value) || $value <= 0) {
            $_SESSION['message'] = "Error: Value must be a positive number.";
            header("Location: index.php?page=sales");
            exit;
        }

        $this->salesModel->insertOutflowRecord($type, $description, $value);
        $_SESSION['message'] = "Success: Outflow record added.";
        header("Location: index.php?page=sales");
    }

    public function removeRecord() {
        if (!in_array('canManageSalesRecords', $_SESSION['permissions'] ?? [])) {
            $_SESSION['message'] = "Error: No proper permission to do action.";
            header("Location: index.php?page=sales");
            exit;
        }

        $recordID = $_POST['recordID'] ?? null;

        if ($recordID === null) {
            $_SESSION['message'] = "Error: No record specified.";
            header("Location: index.php?page=sales");
            exit;
        }

        $_SESSION['message'] = $this->salesModel->deleteRecord($recordID);
        header("Location: index.php?page=sales");
    }
}
