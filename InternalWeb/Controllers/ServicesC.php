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

    public function toggleSubserviceStatus($serviceID) {
        $selectedID = $_POST['selectedID'];
        $this->servicesModel->updateSubserviceStatus($selectedID);

        header("Location: index.php?page=services&service=" . $serviceID);
        exit();
    }

    public function setSubserviceInfo($serviceID) {
        $subserviceID = $_POST['selectedID'];
        $pricePerUnit = !empty($_POST['pricePerUnit']) ? $_POST['pricePerUnit'] : $_POST['setPricePerUnit'];
        $description = !empty($_POST['description']) ? $_POST['description'] : $_POST['setDescription'];
        $this->servicesModel->updateSubserviceInfo($subserviceID, $pricePerUnit, $description);

        header("Location: index.php?page=services&service=" . $serviceID);
        exit();
    }

    public function showService($serviceID) {
        $page = "services";
        $lastPage = "services";
        $service = $this->servicesModel->getService($serviceID);
        $processList = $this->servicesModel->getProcess($serviceID);
        $subservicesList = $this->servicesModel->getSubservices($serviceID);
        require __DIR__ . '/../Views/Services/ServicePage.php';
    }
}
