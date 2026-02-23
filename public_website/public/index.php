<?php
/**
 * Hontoria Printing Services - Main Router
 * Routes requests to appropriate controllers
 */

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define base paths - we are in public/ folder
define('BASE_PATH', dirname(__DIR__));  // Go up one level to public_website
define('APP_PATH', BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);  // Current directory (public)
define('VIEWS_PATH', BASE_PATH . '/views');

// Debug: Show paths (remove this after fixing)
echo "<!-- DEBUG INFO:\n";
echo "BASE_PATH: " . BASE_PATH . "\n";
echo "PUBLIC_PATH: " . PUBLIC_PATH . "\n";
echo "APP_PATH: " . APP_PATH . "\n";
echo "CSS file exists: " . (file_exists(PUBLIC_PATH . '/css/home.css') ? 'YES' : 'NO') . "\n";
echo "JS file exists: " . (file_exists(PUBLIC_PATH . '/js/home.js') ? 'YES' : 'NO') . "\n";
echo "Logo exists: " . (file_exists(PUBLIC_PATH . '/img/logo.jpg') ? 'YES' : 'NO') . "\n";
echo "-->\n";

// Autoloader - loads classes from components and controllers
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/components/home_components/' . $class . '.php',
        APP_PATH . '/controllers/' . $class . '.php',
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

// Route to appropriate controller
switch ($page) {
    case 'home':
        $controller = new \HomeController();
        $controller->index();
        break;
        
    case 'services':
        echo '<h1>Services page coming soon!</h1><a href="?page=home">Back to Home</a>';
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