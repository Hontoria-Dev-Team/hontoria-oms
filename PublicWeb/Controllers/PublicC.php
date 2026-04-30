<?php
class PublicC {
    private $publicModel;

    public function __construct($pdo) {
        // Require the model and pass the $pdo connection to it
        require_once __DIR__ . '/../Models/PublicM.php';
        $this->publicModel = new \PublicM($pdo);
    }

    public function showHomePage() {
        $page = "home"; // Can be used inside the view if needed
        require __DIR__ . '/../Views/Home/HomePage.php';
    }

    public function showServicesPage() {
        $page = "services";
        $servicesCatalog = $this->publicModel->getServicesCatalog();
        require __DIR__ . '/../Views/Services/ServicesPage.php';
    }

    public function showAboutUsPage() {
        $page = "about";
        require __DIR__ . '/../Views/AboutUs/AboutusPage.php';
    }

    public function showOrderPage($orderID) {
        $page = "order";
        $orderData = $this->publicModel->getOrderByID($orderID);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            $redirect = "index.php?page=order&orderID=" . urlencode($orderID);

            if ($action === 'approveDesign') {
                $this->publicModel->approveDesign($orderID);
                header('Location: ' . $redirect . '&status=designApproved');
                exit;
            }

            if ($action === 'approveVariableList') {
                $this->publicModel->approveVariableList($orderID);
                header('Location: ' . $redirect . '&status=variableListApproved');
                exit;
            }
        }

        if (!$orderData) {
            $error = "Order not found.";
            require __DIR__ . '/../Views/Order/Page.php';
            return;
        }

        $orderProcesses = $this->publicModel->getOrderProcessDetails($orderID, $orderData['isArchived'] ?? false);
        $variableList = $this->publicModel->getVariableListByOrderID($orderID);

        $message = null;
        if (isset($_GET['status'])) {
            if ($_GET['status'] === 'designApproved') {
                $message = 'Design approved successfully.';
            } elseif ($_GET['status'] === 'variableListApproved') {
                $message = 'Variable list approved successfully.';
            }
        }

        require __DIR__ . '/../Views/Order/Page.php';
    }
}
