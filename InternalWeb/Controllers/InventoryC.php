<?php
class InventoryC {
    private $inventoryModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/InventoryM.php';
        $this->inventoryModel = new InventoryM($pdo);
    }

    public function showPage() {
        $page = "inventory";

        $inventoryList = $this->inventoryModel->getAllInventory();
        $inventoryQuantityMap = $this->inventoryModel->getAllInventoryCurrentQuantityMap();
        $inventoryLastRestockMap = $this->inventoryModel->getAllInventoryLastRestockMap();
        $inventoryRecordList = $this->inventoryModel->getAllInventoryRecords();

        require __DIR__ . '/../Views/Inventory/Page.php';
    }

    public function setInventoryRecord() {
        $selectedID = $_POST['id'];
        $change = (int)$_POST['change'];

        $_SESSION['message'] = $this->inventoryModel->updateInventoryRecord($selectedID, $change);

        header("Location: index.php?page=inventory");
    }

    public function createInventoryItem() {
        $name = $_POST['name'];
        $quantity = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->inventoryModel->insertInventoryItem($name, $quantity);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryItem() {
        $selectedID = $_POST['id'];

        $_SESSION['message'] = $this->inventoryModel->deleteInventoryItem($selectedID);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMinQuantity() {
        $selectedID = $_POST['id'];
        $minQuantity = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->inventoryModel->updateInventoryItemMinQuantity($selectedID, $minQuantity);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMaxAvgConsumption() {
        $selectedID = $_POST['id'];
        $maxAvgConsumption = (int)$_POST['quantity'];

        $_SESSION['message'] = $this->inventoryModel->updateInventoryItemMaxAvgConsumption($selectedID, $maxAvgConsumption);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryRecord() {
        $selectedID = $_POST['id'];

        $_SESSION['message'] = $this->inventoryModel->deleteInventoryRecord($selectedID);

        header("Location: index.php?page=inventory");
    }
}
