<?php
class ServicesC {
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->servicesModel = new ServicesM($pdo);
    }

    // Display the services management page with all services, subservices, processes, and related data
    public function showServices($serviceID, $subserviceID) {
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

    // Create a new service with the given name, checking user permissions
    public function createService() {
        $name = ucwords(strtolower(trim($_POST['name'])));

        if (!in_array('canCreateServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->insertService($name);
        }

        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Delete a service, checking user permissions
    public function removeService() {
        $serviceID = $_POST['selectedServiceID'];

        if (!in_array('canDeleteServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->deleteService($serviceID);
        }

        header('Location: index.php?page=services');
    }

    // Toggle the active/inactive status of a service
    public function toggleServiceStatus() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterServiceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the service's status.";
        } else {
            $this->servicesModel->updateServiceStatus($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Toggle whether a service supports design functionality
    public function toggleHasDesign() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $this->servicesModel->toggleServiceHasDesign($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Toggle whether a service supports variable lists
    public function toggleHasVariableList() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $this->servicesModel->toggleServiceHasVariableList($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit();
    }

    // Update the processes associated with a service
    public function setServiceProcess() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $processes = $_POST['processList'];
            $_SESSION['message'] = $this->servicesModel->updateServiceProcess($selectedServiceID, $processes);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Display the processes management page
    public function showProcessesManagementPage() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services");
            exit();
        }

        $page = "services";
        $lastPage = 'services';
        $backLink = 'index.php?page=services';
        $processList = $this->servicesModel->getAllProcesses();

        require __DIR__ . '/../Views/Services/ProcessManagement.php';
    }

    // Create a new process with the given name
    public function createProcess() {
        if (in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $processName = $_POST['name'];
            $creation = $this->servicesModel->insertProcess($processName);

            $_SESSION['message'] = $creation ? "Success: Created process" : "Error: Process name already exists.";
        } else {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }

    // Delete a process, checking if it's in use
    public function removeProcess() {
        if (in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $selectedID = (int) $_POST['selectedID'];

            $_SESSION['message'] = $this->servicesModel->deleteProcess($selectedID);
        } else {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }

    // Update a process's settings (assignments, access levels)
    public function setProcess() {
        if (in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $selectedID = (int) $_POST['id'];
            $minAssign = (int) $_POST['minAssign'];
            $maxAssign = (int) $_POST['maxAssign'];
            $hasGCAccess = $_POST['hasGCAccess'];
            $designAccess = $_POST['designAccess'];
            $variableListAccess = $_POST['variableListAccess'];

            $this->servicesModel->updateProcess($selectedID, $minAssign, $maxAssign, $hasGCAccess, $designAccess, $variableListAccess);

            $_SESSION['message'] = "Success: Updated process.";
        } else {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        }
        header("Location: index.php?page=services&action=manageProcesses");
    }

    // Create a new subservice under a service
    public function createSubservice() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];
        $name = ucwords(strtolower(trim($_POST['name'])));

        if (!in_array('canCreateSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create subservices.";
        } else {
            $_SESSION['message'] = $this->servicesModel->insertSubservice($name, $selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Delete a subservice
    public function removeSubservice() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canDeleteSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete subservices.";
        } else {
            $_SESSION['message'] = $this->servicesModel->deleteSubservice($selectedSubserviceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID);
    }

    // Update subservice information (price per unit, description)
    public function setSubserviceInfo() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $description = $_POST['description'];
            $pricePerUnit = $_POST['pricePerUnit'];
            $_SESSION['message'] = $this->servicesModel->updateSubserviceInfo($selectedSubserviceID, $pricePerUnit, $description);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Toggle the active/inactive status of a subservice
    public function toggleSubserviceStatus() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubserviceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the subservice's status.";
        } else {
            $_SESSION['message'] = $this->servicesModel->updateSubserviceStatus($selectedSubserviceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Upload images for a subservice
    public function uploadSubserviceImages() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $images = $_FILES['images'];
            $_SESSION['message'] = $this->servicesModel->insertSubserviceImages($selectedSubserviceID, $images);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }

    // Delete an image from a subservice
    public function removeSubserviceImage() {
        $selectedServiceID = $_POST['selectedServiceID'];
        $selectedSubserviceID = $_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $selectedID = $_POST['selectedID'];
            $_SESSION['message'] = $this->servicesModel->deleteSubserviceImage($selectedID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
    }
}
