<?php
class ServicesC {
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->servicesModel = new ServicesM($pdo);
    }

    public function showServices($serviceID, $subserviceID) {
        if (!in_array('canViewServicesPage', $_SESSION['permissions'])) {
            require __DIR__ . '/../Views/.Misc/ErrorPage.php';
            exit();
        }

        $page = "services";
        $servicesList = $this->servicesModel->getServices();
        $serviceProcessList = $this->servicesModel->getAllServiceProcesses();
        $subserviceList = $this->servicesModel->getAllSubservices();
        $subserviceOrderCountTally = $this->servicesModel->getAllSubservicesOrderCount();
        $processesList = $this->servicesModel->getAllProcesses();
        $subserviceImageList = $this->servicesModel->getAllSubserviceImages();
        $serviceOrderCountMap = $this->servicesModel->getAllServicesOrderCountMapped();

        require __DIR__ . '/../Views/Services/Page.php';
    }

    public function showProcessesManagementPage() {
        if (!in_array('canViewServicesPage', $_SESSION['permissions'])) {
            require __DIR__ . '/../Views/.Misc/ErrorPage.php';
            exit();
        }

        $page = "services";
        $lastPage = 'services';
        $backLink = 'index.php?page=services';
        $processList = $this->servicesModel->getAllProcesses();
        require __DIR__ . '/../Views/Services/ProcessManagement.php';
    }

    public function toggleServiceStatus() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $this->servicesModel->updateServiceStatus($selectedServiceID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    public function toggleHasDesign() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $this->servicesModel->toggleServiceHasDesign($selectedServiceID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    public function toggleHasVariableList() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $this->servicesModel->toggleServiceHasVariableList($selectedServiceID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    public function createService() {
        $name = ucwords(strtolower(trim($_POST['name'])));

        if (empty($name)) {
            $_SESSION['error'] = "Empty service name.";
        } else {
            $creation = $this->servicesModel->insertService($name);

            if (!$creation) {
                $_SESSION['error'] = "Service name already exists.";
            }
        }

        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    public function deleteService() {
        $serviceID = $_POST['selectedServiceID'];
        $this->servicesModel->removeService($serviceID);

        header('Location: index.php?page=services');
    }

    public function setSubserviceInfo() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $description = $_POST['description'];
        $pricePerUnit = $_POST['pricePerUnit'];
        $this->servicesModel->updateSubserviceInfo($selectedSubserviceID, $pricePerUnit, $description);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    public function toggleSubserviceStatus() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $this->servicesModel->updateSubserviceStatus($selectedSubserviceID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    public function createSubservice() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $name = ucwords(strtolower(trim($_POST['name'])));

        if (empty($name)) {
            $_SESSION['error'] = "Empty subservice name.";
        } else {
            $creation = $this->servicesModel->insertSubservice($name, $selectedServiceID);

            if (!$creation) {
                $_SESSION['error'] = "Subservice name already exists.";
            }
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    public function deleteSubservice() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $this->servicesModel->removeSubservice($selectedSubserviceID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID);
        exit();
    }

    public function setServiceProcess() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $processes = $_POST['processList'];
        $this->servicesModel->updateServiceProcess($selectedServiceID, $processes);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    public function createProcess() {
        if (in_array('canAlterServices', $_SESSION['permissions'])) {
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
        if (in_array('canAlterServices', $_SESSION['permissions'])) {
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
        if (in_array('canAlterServices', $_SESSION['permissions'])) {
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

    public function uploadSubserviceImages() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $images = $_FILES['images'];

        $this->servicesModel->insertSubserviceImages($selectedSubserviceID, $images);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    public function removeSubserviceImage() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $selectedID = $_POST['selectedID'];

        $this->servicesModel->deleteSubserviceImage($selectedID);

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }
}
