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

    public function showOrderPage($code) {
        $page = "order";
        $orderPageData = $this->publicModel->getPublicOrderPageByCode($code);
        $requiresPassword = false;
        $passwordVerified = false;
        $message = null;
        $error = null;

        if (!$orderPageData) {
            $error = "Order not found.";
            require __DIR__ . '/../Views/Order/Page.php';
            return;
        }

        $orderID = $orderPageData['orderID'];
        $requiresPassword = !empty($orderPageData['passwordHash']);
        if (!empty($_SESSION['order_access_' . $code])) {
            $passwordVerified = true;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];

            if ($action === 'verifyPassword') {
                $password = $_POST['password'] ?? '';
                if ($this->publicModel->verifyPublicOrderPassword($code, $password)) {
                    $passwordVerified = true;
                    $_SESSION['order_access_' . $code] = true;
                } else {
                    $error = "Incorrect password.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }
            } elseif ($action === 'setPassword') {
                if ($requiresPassword && !$passwordVerified) {
                    $error = "Enter the current password before updating this order password.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                $password = $_POST['password'] ?? '';
                $passwordConfirm = $_POST['passwordConfirm'] ?? '';

                if ($password !== $passwordConfirm) {
                    $error = "Passwords do not match.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                if (strlen(trim($password)) < 10) {
                    $error = "Password must be at least 10 characters long.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                if (!preg_match('/\d/', $password)) {
                    $error = "Password must contain at least one number.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                if ($this->publicModel->setPublicOrderPassword($code, $password)) {
                    $message = "Password saved successfully.";
                    $passwordVerified = true;
                    $_SESSION['order_access_' . $code] = true;
                } else {
                    $error = "Unable to save password. Please try again.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }
            } elseif ($action === 'approveDesign') {
                if ($requiresPassword && !$passwordVerified) {
                    $error = "Password verification is required to approve the design.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                $this->publicModel->approveDesign($orderID);
                header('Location: index.php?page=order&orderCode=' . urlencode($code) . '&status=designApproved');
                exit;
            } elseif ($action === 'approveVariableList') {
                if ($requiresPassword && !$passwordVerified) {
                    $error = "Password verification is required to approve the variable list.";
                    require __DIR__ . '/../Views/Order/Page.php';
                    return;
                }

                $this->publicModel->approveVariableList($orderID);
                header('Location: index.php?page=order&orderCode=' . urlencode($code) . '&status=variableListApproved');
                exit;
            }
        }

        if ($requiresPassword && !$passwordVerified) {
            require __DIR__ . '/../Views/Order/Page.php';
            return;
        }

        $orderData = $this->publicModel->getPublicOrderByID($orderID);
        if (!$orderData) {
            $error = "Order not found.";
            require __DIR__ . '/../Views/Order/Page.php';
            return;
        }

        $orderProcesses = $this->publicModel->getOrderProcessDetails($orderID, $orderData['isArchived'] ?? false);
        $variableList = $this->publicModel->getVariableListByOrderID($orderID);

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
