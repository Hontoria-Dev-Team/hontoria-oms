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

// Debug: Show paths
echo "<!-- DEBUG:\n";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "APP_PATH: " . APP_PATH . "\n";
echo "-->\n";

// Autoloader - loads classes from multiple locations
spl_autoload_register(function ($class) {
    $paths = [
        // Shared components (used by all pages)
        APP_PATH . '/components/home_components/' . $class . '.php',
        // Services components
        APP_PATH . '/components/services_components/' . $class . '.php',
        // Profile components
        APP_PATH . '/components/profile_components/' . $class . '.php',
        // About components
        APP_PATH . '/components/aboutus_components/' . $class . '.php',
        // Controllers
        APP_PATH . '/controllers/' . $class . '.php',
        // Models
        APP_PATH . '/models/' . $class . '.php',
    ];
    
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            echo "<!-- Loaded: $class from $file -->\n";
            return;
        }
    }
    
    // Class not found - show error
    echo "<!-- ERROR: Could not find class '$class' in any of these paths:\n";
    foreach ($paths as $p) {
        echo "  - $p (exists: " . (file_exists($p) ? 'YES' : 'NO') . ")\n";
    }
    echo "-->\n";
});

// Simple routing based on 'page' parameter
$page = $_GET['page'] ?? 'home';

// Route to appropriate controller
switch ($page) {
    case 'home':
        if (class_exists('HomeController')) {
            $controller = new \HomeController();
            $controller->index();
        } else {
            die('ERROR: HomeController class not found!');
        }
        break;
        
    case 'services':
        if (class_exists('ServicesController')) {
            $controller = new \ServicesController();
            $controller->index();
        } else {
            die('ERROR: ServicesController class not found! Check if file exists at: ' . APP_PATH . '/controllers/ServicesController.php');
        }
        break;
        
    case 'profile':
        echo '<h1>Profile page coming soon!</h1><a href="?page=home">Back to Home</a>';
        break;
        
    case 'about':
        echo '<h1>About page coming soon!</h1><a href="?page=home">Back to Home</a>';
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 - Page Not Found</h1><a href="?page=home">Back to Home</a>';
        break;
}
?>