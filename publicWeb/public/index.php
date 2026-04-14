<?php
// Require configuration (which gives us the $pdo variable)


// Require the public controller
require_once __DIR__ . '/../Controllers/PublicC.php';

session_start();

// Determine which page to load (default to home)
$page = $_GET['page'] ?? 'home';

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

    default:
        // If the page doesn't exist, show the error page
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}
?>