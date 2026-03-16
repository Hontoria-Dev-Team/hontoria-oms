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
        $backLink = "index.php?page=services";
        $service = $this->servicesModel->getService($serviceID);
        $processList = $this->servicesModel->getProcess($serviceID);
        $subservicesList = $this->servicesModel->getSubservices($serviceID);
        require __DIR__ . '/../Views/Services/ServicePage.php';
    }

    public function createSubservice($serviceID) {
        $name = $_POST['name'];
        $creation = $this->servicesModel->insertSubservice($name, $serviceID);

        if ($creation) {
            header("Location: index.php?page=services&service=" . $serviceID);
            exit();
        } else {
            $page = "services";
            $lastPage = "services";
            $backLink = "index.php?page=services";
            $error = "Subservice name already exists.";
            $service = $this->servicesModel->getService($serviceID);
            $processList = $this->servicesModel->getProcess($serviceID);
            $subservicesList = $this->servicesModel->getSubservices($serviceID);
            require __DIR__ . '/../Views/Services/ServicePage.php';
        }
    }

    public function deleteSubservice($serviceID) {
        $subserviceID = $_POST['selectedID'];
        $this->servicesModel->removeSubservice($subserviceID);

        header("Location: index.php?page=services&service=" . $serviceID);
        exit();
    }
}
