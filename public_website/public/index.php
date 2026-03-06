<?php
/**
 * Hontoria Printing Services - Main Router
 * Routes requests to appropriate controllers
 */

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('VIEWS_PATH', BASE_PATH . '/views');

// Autoloader - loads classes from multiple locations
spl_autoload_register(function ($class) {
    $paths = [
        // Shared components (used by all pages)
        APP_PATH . '/components/home_components/' . $class . '.php',
        // Services components
        APP_PATH . '/components/services_components/' . $class . '.php',
        // About Us components
        APP_PATH . '/components/aboutus_components/' . $class . '.php',
        // Controllers
        APP_PATH . '/controllers/' . $class . '.php',
        // Models
        APP_PATH . '/models/' . $class . '.php',
    ];

    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Simple routing based on 'page' parameter
$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        if (class_exists('HomeController')) {
            (new \HomeController())->index();
        } else {
            die('ERROR: HomeController not found.');
        }
        break;

    case 'services':
        if (class_exists('ServicesController')) {
            (new \ServicesController())->index();
        } else {
            die('ERROR: ServicesController not found.');
        }
        break;

    case 'about':
        if (class_exists('AboutUsController')) {
            (new \AboutUsController())->index();
        } else {
            die('ERROR: AboutUsController not found.');
        }
        break;

    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><a href="?page=home">Back to Home</a>';
        break;
}
?>