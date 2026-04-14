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
        require __DIR__ . '/../Views/Services/ServicesPage.php';
    }

    public function showAboutUsPage() {
        $page = "about";
        require __DIR__ . '/../Views/AboutUs/AboutusPage.php';
    }
}
?>