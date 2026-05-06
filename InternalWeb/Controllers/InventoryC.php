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
        
        // Load records only if a specific inventory item is selected
        $inventoryRecordList = [];
        $selectedInventoryID = (int)($_GET['id'] ?? 0);
        $monthRange = (int)($_GET['months'] ?? 12);
        
        if ($selectedInventoryID > 0 && $monthRange >= 1) {
            $inventoryRecordList = $this->inventoryModel->getInventoryRecordsByIDAndDateRange($selectedInventoryID, $monthRange);
        }

        require __DIR__ . '/../Views/Inventory/Page.php';
    }

    public function setInventoryRecord() {
        if (!in_array('canUpdateItemQuantity', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to update item quantity.";
            header("Location: index.php?page=inventory");
            return;
        }

        $selectedID = (int)($_POST['id'] ?? 0);
        $change = (int)($_POST['change'] ?? 0);

        if ($selectedID <= 0) {
            $_SESSION['message'] = "Error: Invalid item selected.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->updateInventoryRecord($selectedID, $change);

        header("Location: index.php?page=inventory");
    }

    public function createInventoryItem() {
        if (!in_array('canCreateItems', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to create inventory items.";
            header("Location: index.php?page=inventory");
            return;
        }

        $name = trim($_POST['name'] ?? '');
        $quantity = (int)($_POST['quantity'] ?? 0);

        if (empty($name)) {
            $_SESSION['message'] = "Error: Item name cannot be empty.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->insertInventoryItem($name, $quantity);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryItem() {
        if (!in_array('canDeleteItems', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to delete inventory items.";
            header("Location: index.php?page=inventory");
            return;
        }

        $selectedID = (int)($_POST['id'] ?? 0);

        if ($selectedID <= 0) {
            $_SESSION['message'] = "Error: Invalid item selected.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->deleteInventoryItem($selectedID);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMinQuantity() {
        if (!in_array('canModifyItems', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to modify inventory items.";
            header("Location: index.php?page=inventory");
            return;
        }

        $selectedID = (int)($_POST['id'] ?? 0);
        $minQuantity = (int)($_POST['quantity'] ?? 0);

        if ($selectedID <= 0) {
            $_SESSION['message'] = "Error: Invalid item selected.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->updateInventoryItemMinQuantity($selectedID, $minQuantity);

        header("Location: index.php?page=inventory");
    }

    public function changeInventoryItemMaxAvgConsumption() {
        if (!in_array('canModifyItems', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to modify inventory items.";
            header("Location: index.php?page=inventory");
            return;
        }

        $selectedID = (int)($_POST['id'] ?? 0);
        $maxAvgConsumption = (int)($_POST['quantity'] ?? 0);

        if ($selectedID <= 0) {
            $_SESSION['message'] = "Error: Invalid item selected.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->updateInventoryItemMaxAvgConsumption($selectedID, $maxAvgConsumption);

        header("Location: index.php?page=inventory");
    }

    public function removeInventoryRecord() {
        if (!in_array('canUpdateItemQuantity', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You don't have permission to reset inventory records.";
            header("Location: index.php?page=inventory");
            return;
        }

        $selectedID = (int)($_POST['id'] ?? 0);

        if ($selectedID <= 0) {
            $_SESSION['message'] = "Error: Invalid item selected.";
            header("Location: index.php?page=inventory");
            return;
        }

        $_SESSION['message'] = $this->inventoryModel->deleteInventoryRecord($selectedID);

        header("Location: index.php?page=inventory");
    }
}
