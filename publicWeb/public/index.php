<?php
require_once __DIR__ . '/../../Config/config.php';
require_once __DIR__ . '/../Controllers/PublicC.php';

session_start();

$page = $_GET['page'] ?? 'home';

$controller = new PublicC($pdo);

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
        require_once __DIR__ . '/../Views/.Misc/ErrorPage.php';
        break;
}
