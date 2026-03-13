<?php
/**
 * Hontoria Printing Services - Main Router
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// ── Autoloader ────────────────────────────────────────────────────────────────
spl_autoload_register(function ($class) {
    $paths = [
        // ★ Reusable components (Header, Footer) — checked first
        APP_PATH . '/components/reusable_components/' . $class . '.php',
        // Page-specific components
        APP_PATH . '/components/home_components/'     . $class . '.php',
        APP_PATH . '/components/services_components/' . $class . '.php',
        APP_PATH . '/components/aboutus_components/'  . $class . '.php',
        // Controllers & Models
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/'      . $class . '.php',
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// ── Router ────────────────────────────────────────────────────────────────────
$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        (new \HomeController())->index();
        break;

    case 'services':
        (new \ServicesController())->index();
        break;

    case 'about':
        (new \AboutUsController())->index();
        break;

    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><a href="?page=home">Back to Home</a>';
        break;
}
?>