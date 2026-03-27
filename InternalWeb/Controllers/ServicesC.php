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

    public function showProcessesManagementPage() {
        $page = "services";
        $lastPage = 'services';
        $backLink = 'index.php?page=services';
        $processList = $this->servicesModel->getAllProcesses();
        require __DIR__ . '/../Views/Services/ProcessManagement.php';
    }

    public function toggleServiceStatus() {
        $selectedID = $_POST['selectedID'];
        $this->servicesModel->updateServiceStatus($selectedID);

        header("Location: index.php?page=services");
        exit();
    }

    public function createService() {
        $name = ucwords(strtolower(trim($_POST['name'])));

        $creation = $this->servicesModel->insertService($name);
        $error = null;

        if ($creation) {
            header('Location: index.php?page=services');
        } else {
            $page = 'services';
            $error = "Service name already exists.";
            require __DIR__ . '/../Views/Services/Page.php';
        }
    }

    public function deleteService() {
        $serviceID = $_POST['selectedID'];
        $this->servicesModel->removeService($serviceID);

        header('Location: index.php?page=services');
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
        $service = $this->servicesModel->getServiceByID($serviceID);
        $processList = $this->servicesModel->getServiceProcess($serviceID);
        $subservicesList = $this->servicesModel->getSubservices($serviceID);
        $processes = $this->servicesModel->getAllProcesses();
        require __DIR__ . '/../Views/Services/ServicePage.php';
    }

    public function toggleServiceBooleans($serviceID, $type) {
        if (in_array('canCreateServices', $_SESSION['permissions'])) {
            if ($type === 0) {
                $this->servicesModel->toggleServiceHasDesign($serviceID);
            } else {
                $this->servicesModel->toggleServiceHasVariableList($serviceID);
            }
        } else {
            $_SESSION['error'] = "You dont have permission to toggle this.";
        }
        header("Location: index.php?page=services&service=" . $serviceID);
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
            $service = $this->servicesModel->getServiceByID($serviceID);
            $processList = $this->servicesModel->getServiceProcess($serviceID);
            $subservicesList = $this->servicesModel->getSubservices($serviceID);
            $processes = $this->servicesModel->getAllProcesses();
            require __DIR__ . '/../Views/Services/ServicePage.php';
        }
    }

    public function deleteSubservice($serviceID) {
        $subserviceID = $_POST['selectedID'];
        $this->servicesModel->removeSubservice($subserviceID);

        header("Location: index.php?page=services&service=" . $serviceID);
        exit();
    }

    public function setServiceProcess($serviceID) {
        $processes = $_POST['processList'];
        $this->servicesModel->updateServiceProcess($serviceID, $processes);

        header("Location: index.php?page=services&service=" . $serviceID);
        exit();
    }

    public function createProcess() {
        if (in_array('canCreateServiceProcesses', $_SESSION['permissions'])) {
            $processName = $_POST['name'];
            $creation = $this->servicesModel->insertProcess($processName);

            if (!$creation) {
                $_SESSION['error'] = "Process name already exists";
            }
        } else {
            $_SESSION['error'] = "You dont have permission to create processes";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }

    public function deleteProcess() {
        if (in_array('canCreateServiceProcesses', $_SESSION['permissions'])) {
            $selectedID = (int) $_POST['selectedID'];
            $serviceProcesses = $this->servicesModel->getAllServiceProcesses();

            $canDelete = true;

            foreach ($serviceProcesses as $serviceProcess) {
                if ((int)$serviceProcess['id'] === $selectedID) {
                    $canDelete = false;
                    break;
                }
            }

            if ($canDelete) {
                $this->servicesModel->removeProcess($selectedID);
            } else {
                $_SESSION['error'] = "Cannot delete this process because it is in use in one or more services";
            }
        } else {
            $_SESSION['error'] = "You dont have permission to delete processes";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }

    public function setProcess() {
        if (in_array('canCreateServiceProcesses', $_SESSION['permissions'])) {
            $selectedID = (int) $_POST['id'];
            $minAssign = (int) $_POST['minAssign'];
            $maxAssign = (int) $_POST['maxAssign'];
            $hasGCAccess = $_POST['hasGCAccess'];
            $designAccess = $_POST['designAccess'];
            $variableListAccess = $_POST['variableListAccess'];

            $this->servicesModel->updateProcess($selectedID, $minAssign, $maxAssign, $hasGCAccess, $designAccess, $variableListAccess);
        } else {
            $_SESSION['error'] = "You dont have permission to modify processes";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }
}
