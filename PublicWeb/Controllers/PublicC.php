<?php
class PublicC {
    private $publicModel;

    public function __construct($pdo) {
        // Require the model and pass the $pdo connection to it
        require_once __DIR__ . '/../Models/PublicM.php';
        $this->publicModel = new \PublicM($pdo);
    }

    /**
     * SECURITY: Verify hCaptcha token from user submission.
     * Returns true if valid, false otherwise.
     */
    private function verifyCaptcha(): bool {
        $token = $_POST['h-captcha-response'] ?? '';
        if (empty($token)) return false;

        $response = file_get_contents(
            'https://api.hcaptcha.com/siteverify',
            false,
            stream_context_create([
                'http' => [
                    'method'  => 'POST',
                    'header'  => 'Content-Type: application/x-www-form-urlencoded',
                    'content' => http_build_query([
                        'secret'   => HCAPTCHA_SECRET_KEY,
                        'response' => $token,
                        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                        'sitekey'  => HCAPTCHA_SITE_KEY,
                    ])
                ]
            ])
        );

        if ($response === false) {
            error_log("hCaptcha API request failed");
            return false;
        }

        $result = json_decode($response, true);
        return ($result['success'] ?? false) === true;
    }

    /**
     * STRUCTURE: Private helper to render the order page with consistent variable scope.
     * Eliminates 8+ repeated require calls throughout the method.
     */
    private function renderOrderView(array $vars): void {
        extract($vars, EXTR_SKIP);
        require __DIR__ . '/../Views/Order/Page.php';
    }

    /**
     * STRUCTURE: Handle password verification action.
     * Returns array with 'error' key on failure, or passwordVerified flag on success.
     */
    private function handleVerifyPassword(string $code): array {
        $password = $_POST['password'] ?? '';

        // SECURITY: Check if order is currently locked due to brute force attempts
        $lockStatus = $this->publicModel->getOrderLockStatus($code);
        if ($lockStatus !== null) {
            $now = new \DateTime();
            $secondsRemaining = $lockStatus->getTimestamp() - $now->getTimestamp();
            return ['error' => "Too many failed attempts. Please try again in " . ceil($secondsRemaining) . " seconds."];
        }

        if ($this->publicModel->verifyPublicOrderPassword($code, $password)) {
            $_SESSION['order_access_' . $code] = true;
            return ['passwordVerified' => true];
        }
        return ['error' => 'Incorrect password.'];
    }

    /**
     * STRUCTURE: Handle password set/update action.
     * Returns array with error on validation failure, or message + verified flag on success.
     */
    private function handleSetPassword(string $code, bool $requiresPassword, bool $passwordVerified): array {
        if ($requiresPassword && !$passwordVerified) {
            return ['error' => 'Enter the current password before updating this order password.'];
        }

        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['passwordConfirm'] ?? '';

        if ($password !== $passwordConfirm) {
            return ['error' => 'Passwords do not match.'];
        }

        // CODE QUALITY: Validation delegated to model, but kept here for immediate UX feedback
        if (strlen(trim($password)) < 10) {
            return ['error' => 'Password must be at least 10 characters long.'];
        }

        if (!preg_match('/\d/', $password)) {
            return ['error' => 'Password must contain at least one number.'];
        }

        if ($this->publicModel->setPublicOrderPassword($code, $password)) {
            $_SESSION['order_access_' . $code] = true;
            return ['message' => 'Password saved successfully.', 'passwordVerified' => true];
        }

        return ['error' => 'Unable to save password. Please try again.'];
    }

    /**
     * STRUCTURE: Handle design approval action.
     * Redirects on success (exits), returns error array on permission check failure.
     */
    private function handleApproveDesign(int $orderID, string $code, bool $requiresPassword, bool $passwordVerified): array {
        if ($requiresPassword && !$passwordVerified) {
            return ['error' => 'Password verification is required to approve the design.'];
        }

        $this->publicModel->approveDesign($orderID);
        header('Location: index.php?page=order&code=' . urlencode($code) . '&status=designApproved');
        exit;
    }

    /**
     * STRUCTURE: Handle variable list approval action.
     * Redirects on success (exits), returns error array on permission check failure.
     */
    private function handleApproveVariableList(int $orderID, string $code, bool $requiresPassword, bool $passwordVerified): array {
        if ($requiresPassword && !$passwordVerified) {
            return ['error' => 'Password verification is required to approve the variable list.'];
        }

        $this->publicModel->approveVariableList($orderID);
        header('Location: index.php?page=order&code=' . urlencode($code) . '&status=variableListApproved');
        exit;
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
        $message = null;
        $error = null;
        $orderPageData = null;
        $orderData = null;
        $orderProcesses = [];
        $variableList = null;
        $requiresPassword = false;
        $passwordVerified = false;
        $orderID = null;

        // SECURITY: Validate order code format early to prevent injection in session keys and redirects
        if (!preg_match('/^[a-zA-Z0-9_-]{1,64}$/', $code)) {
            $error = "Invalid order code.";
            $this->renderOrderView(compact(
                'page',
                'error',
                'message',
                'orderPageData',
                'orderData',
                'orderProcesses',
                'variableList',
                'requiresPassword',
                'passwordVerified'
            ));
            return;
        }

        $orderPageData = $this->publicModel->getPublicOrderPageByCode($code);
        if (!$orderPageData) {
            $error = "Order not found.";
            $this->renderOrderView(compact(
                'page',
                'error',
                'message',
                'orderPageData',
                'orderData',
                'orderProcesses',
                'variableList',
                'requiresPassword',
                'passwordVerified'
            ));
            return;
        }

        $orderID = $orderPageData['orderID'];
        // SECURITY: Use hasPassword flag from model instead of checking raw passwordHash
        $requiresPassword = (bool)($orderPageData['hasPassword'] ?? false);

        if (!empty($_SESSION['order_access_' . $code])) {
            $passwordVerified = true;
        }

        // ──────────────── Handle POST Actions ──────────────
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            // Validate CSRF token for all POST actions
            try {
                CsrfM::validateToken();
            } catch (Exception $e) {
                $error = "Security validation failed. Please try again.";
                $this->renderOrderView(compact(
                    'page',
                    'error',
                    'message',
                    'orderPageData',
                    'orderData',
                    'orderProcesses',
                    'variableList',
                    'requiresPassword',
                    'passwordVerified'
                ));
                return;
            }

            $action = $_POST['action'];

            // STRUCTURE: Route to private action handlers
            if ($action === 'verifyPassword') {
                // SECURITY: Verify hCaptcha before password verification
                if (!$this->verifyCaptcha()) {
                    $error = "CAPTCHA verification failed. Please try again.";
                    $this->renderOrderView(compact(
                        'page',
                        'error',
                        'message',
                        'orderPageData',
                        'orderData',
                        'orderProcesses',
                        'variableList',
                        'requiresPassword',
                        'passwordVerified'
                    ));
                    return;
                }

                $result = $this->handleVerifyPassword($code);
                if (isset($result['error'])) {
                    $error = $result['error'];
                    $this->renderOrderView(compact(
                        'page',
                        'error',
                        'message',
                        'orderPageData',
                        'orderData',
                        'orderProcesses',
                        'variableList',
                        'requiresPassword',
                        'passwordVerified'
                    ));
                    return;
                }
                $passwordVerified = $result['passwordVerified'] ?? false;
            } elseif ($action === 'setPassword') {
                $result = $this->handleSetPassword($code, $requiresPassword, $passwordVerified);
                if (isset($result['error'])) {
                    $error = $result['error'];
                    $this->renderOrderView(compact(
                        'page',
                        'error',
                        'message',
                        'orderPageData',
                        'orderData',
                        'orderProcesses',
                        'variableList',
                        'requiresPassword',
                        'passwordVerified'
                    ));
                    return;
                }
                $message = $result['message'] ?? null;
                $passwordVerified = $result['passwordVerified'] ?? false;
            } elseif ($action === 'approveDesign') {
                $result = $this->handleApproveDesign($orderID, $code, $requiresPassword, $passwordVerified);
                if (isset($result['error'])) {
                    $error = $result['error'];
                    $this->renderOrderView(compact(
                        'page',
                        'error',
                        'message',
                        'orderPageData',
                        'orderData',
                        'orderProcesses',
                        'variableList',
                        'requiresPassword',
                        'passwordVerified'
                    ));
                    return;
                }
            } elseif ($action === 'approveVariableList') {
                $result = $this->handleApproveVariableList($orderID, $code, $requiresPassword, $passwordVerified);
                if (isset($result['error'])) {
                    $error = $result['error'];
                    $this->renderOrderView(compact(
                        'page',
                        'error',
                        'message',
                        'orderPageData',
                        'orderData',
                        'orderProcesses',
                        'variableList',
                        'requiresPassword',
                        'passwordVerified'
                    ));
                    return;
                }
            }
        }

        // ──────────────── Check Password Gate ──────────────
        if ($requiresPassword && !$passwordVerified) {
            $this->renderOrderView(compact(
                'page',
                'error',
                'message',
                'orderPageData',
                'orderData',
                'orderProcesses',
                'variableList',
                'requiresPassword',
                'passwordVerified'
            ));
            return;
        }

        // ──────────────── Load Order Data ──────────────
        $orderData = $this->publicModel->getPublicOrderByID($orderID);
        if (!$orderData) {
            // CODE QUALITY: Distinct error for data unavailability vs. not found
            $error = "Order data unavailable.";
            $this->renderOrderView(compact(
                'page',
                'error',
                'message',
                'orderPageData',
                'orderData',
                'orderProcesses',
                'variableList',
                'requiresPassword',
                'passwordVerified'
            ));
            return;
        }

        $orderProcesses = $this->publicModel->getOrderProcessDetails($orderID, $orderData['isArchived'] ?? false);
        $variableList = $this->publicModel->getVariableListByOrderID($orderID);

        // ──────────────── Handle Status Messages ──────────────
        // SECURITY: Allowlist status values before setting message
        $allowedStatuses = ['designApproved', 'variableListApproved'];
        $status = in_array($_GET['status'] ?? '', $allowedStatuses, true) ? $_GET['status'] : null;

        if ($status === 'designApproved') {
            $message = 'Design approved successfully.';
        } elseif ($status === 'variableListApproved') {
            $message = 'Variable list approved successfully.';
        }

        // STRUCTURE: Use renderOrderView helper for consistent rendering
        $this->renderOrderView(compact(
            'page',
            'error',
            'message',
            'orderPageData',
            'orderData',
            'orderProcesses',
            'variableList',
            'requiresPassword',
            'passwordVerified'
        ));
    }
}
