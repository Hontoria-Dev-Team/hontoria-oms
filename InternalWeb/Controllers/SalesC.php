<?php
class SalesC {
    private $salesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/SalesM.php';
        $this->salesModel = new SalesM($pdo);
    }

    public function showPage() {
        $page = "sales";

        $salesRecords = $this->salesModel->getAllSalesRecords();

        require __DIR__ . '/../Views/Sales/Page.php';
    }

    public function setInventoryRecord() {
        $selectedID = $_POST['id'];
        $change = (int)$_POST['change'];

        $_SESSION['message'] = $this->salesModel->updateInventoryRecord($selectedID, $change);

        header("Location: index.php?page=inventory");
    }

    public function createInventoryItem() {
        $name = $_POST['name'];
        $quantity = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->salesModel->insertInventoryItem($name, $quantity);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryItem() {
        $selectedID = $_POST['id'];

        $_SESSION['message'] = $this->salesModel->deleteInventoryItem($selectedID);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMinQuantity() {
        $selectedID = $_POST['id'];
        $minQuantity = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->salesModel->updateInventoryItemMinQuantity($selectedID, $minQuantity);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMaxAvgConsumption() {
        $selectedID = $_POST['id'];
        $maxAvgConsumption = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->salesModel->updateInventoryItemMaxAvgConsumption($selectedID, $maxAvgConsumption);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryRecord() {
        $selectedID = $_POST['id'];

        $_SESSION['message'] = $this->salesModel->deleteInventoryRecord($selectedID);

        header("Location: index.php?page=inventory");
    }
}
