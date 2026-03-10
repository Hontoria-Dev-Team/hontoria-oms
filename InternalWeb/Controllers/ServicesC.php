<?php
class ServicesC {
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->servicesModel = new ServicesM($pdo);
    }

    public function showServices() {
        $page = "services";
        $servicesList = $this->servicesModel->getServices();
        require __DIR__ . '/../Views/Services/Page.php';
    }

    public function toggleServiceStatus() {
        $selectedID = $_POST['selectedID'];
        $this->servicesModel->updateServiceStatus($selectedID);

        header("Location: index.php?page=services");
        exit();
    }
}
