<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/PublicC.php';
require_once __DIR__ . '/../Middleware/CsrfM.php';

session_start();

// ─────────────────── CSRF Protection ───────────────────
CsrfM::initializeToken();

// ─────────────────── Security Headers ───────────────────
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: blob:; font-src 'self' data: https://fonts.gstatic.com https://cdnjs.cloudflare.com; frame-src 'self' https://www.google.com; frame-ancestors 'self'; base-uri 'self'; form-action 'self';");
if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
}

// ─────────────────── Helpers ───────────────────

/**
 * Sanitise a GET value to only contain safe characters.
 * Returns a default if the value is missing or invalid.
 */
function SanitiseRouteValue(string $key, string $default = 'home'): string {
    $value = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);
    if ($value === null) {
        return $default;
    }
    $value = trim($value);
    // Allow only letters, digits, underscore, hyphen
    $value = preg_replace('/[^a-zA-Z0-9_-]/', '', $value);
    if ($value === '' || preg_match('/^[^a-zA-Z0-9]/', $value)) {
        return $default;
    }
    return $value;
}

/**
 * Sanitise the order code parameter (alphanumeric only, case-insensitive)
 */
function SanitiseOrderCode(string $key): string {
    $value = filter_input(INPUT_GET, $key, FILTER_UNSAFE_RAW);
    if ($value === null) {
        return '-1';
    }
    $value = trim($value);
    // Allow only letters and digits
    $value = preg_replace('/[^a-zA-Z0-9]/', '', $value);
    return $value ?: '-1';
}

/**
 * Send a redirect header and stop execution immediately.
 */
function Redirect(string $uri): void {
    header('Location: ' . $uri);
    exit;
}

// ─────────────────── Routing ───────────────────

$page = SanitiseRouteValue('page', 'home');

$allowedPages = [
    'home',
    'services',
    'about',
    'order'
];
if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}

date_default_timezone_set('Asia/Manila');

// Instantiate the controller, passing the $pdo connection
$controller = new \PublicC($pdo);

// Route to the correct method
switch ($page) {
    case 'home':
        $controller->showHomePage();
        break;

    case 'services':
        $controller->showServicesPage();
        break;

    case 'about':
        $controller->showAboutUsPage();
        break;

    case 'order':
        $code = SanitiseOrderCode('code');
        $controller->showOrderPage($code);
        break;

    default:
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}
