<?php
class ServicesC {
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->servicesModel = new ServicesM($pdo);
    }

    /** Display the services management page */
    public function ShowServices($serviceID, $subserviceID) {
        $page = "services";
        $servicesList            = $this->servicesModel->GetAllServices();
        $serviceProcessList      = $this->servicesModel->GetAllServiceProcesses();
        $subserviceList          = $this->servicesModel->GetAllSubservices();
        $processesList           = $this->servicesModel->GetAllProcesses();
        $subserviceImageList     = $this->servicesModel->GetAllSubserviceImages();
        $serviceOrderCountMap    = $this->servicesModel->GetAllServicesOrderCountMapped();
        $subserviceOrderCountMap = $this->servicesModel->GetAllSubservicesOrderCountMapped();

        // Convert map to array format expected by the front‑end
        $subserviceOrderCountTally = [];
        foreach ($subserviceOrderCountMap as $subID => $count) {
            $subserviceOrderCountTally[] = [
                'subserviceID' => $subID,
                'orderCount'   => $count
            ];
        }

        require __DIR__ . '/../Views/Services/Page.php';
    }

    /** Create a new service */
    public function CreateService() {
        $name = ucwords(strtolower(trim($_POST['name'])));
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canCreateServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->CreateService($name);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Delete a service */
    public function DeleteService() {
        $serviceID = (int)$_POST['selectedServiceID'];

        if (!in_array('canDeleteServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->DeleteService($serviceID);
        }

        header('Location: index.php?page=services');
        exit;
    }

    /** Toggle service active/inactive */
    public function ToggleServiceStatus() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterServiceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the service's status.";
        } else {
            $_SESSION['message'] = $this->servicesModel->ToggleServiceStatus($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Toggle design requirement for a service */
    public function ToggleHasDesign() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->ToggleServiceDesignRequirement($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Toggle variable‑list requirement for a service */
    public function ToggleHasVariableList() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->ToggleServiceVariableListRequirement($selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Update the processes assigned to a service */
    public function UpdateServiceProcess() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];
        $processIDs           = $_POST['processList'] ?? [];

        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
        } else {
            $_SESSION['message'] = $this->servicesModel->UpdateServiceProcesses($selectedServiceID, $processIDs);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Show the process management page */
    public function ShowProcessesManagementPage() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services");
            exit;
        }

        $page        = "services";
        $lastPage    = 'services';
        $backLink    = 'index.php?page=services';
        $processList = $this->servicesModel->GetAllProcesses();

        require __DIR__ . '/../Views/Services/ProcessManagement.php';
    }

    /** Create a new process */
    public function CreateProcess() {
        $processName = ucwords(strtolower(trim($_POST['name'])));

        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        } else {
            $_SESSION['message'] = $this->servicesModel->CreateProcess($processName);
        }

        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    /** Delete a process */
    public function DeleteProcess() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        } else {
            $selectedID = (int)$_POST['selectedID'];
            $_SESSION['message'] = $this->servicesModel->DeleteProcess($selectedID);
        }

        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    /** Update a process's settings */
    public function UpdateProcess() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
        } else {
            $selectedID         = (int)$_POST['id'];
            $minAssign          = (int)$_POST['minAssign'];
            $maxAssign          = (int)$_POST['maxAssign'];
            $hasGCAccess        = $_POST['hasGCAccess'] ?? '0';
            $designAccess       = $_POST['designAccess'];
            $variableListAccess = $_POST['variableListAccess'];

            $_SESSION['message'] = $this->servicesModel->UpdateProcess(
                $selectedID,
                $minAssign,
                $maxAssign,
                $hasGCAccess,
                $designAccess,
                $variableListAccess
            );
        }

        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    /** Create a new subservice under a service */
    public function CreateSubservice() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];
        $name                 = ucwords(strtolower(trim($_POST['name'])));

        if (!in_array('canCreateSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create subservices.";
        } else {
            $_SESSION['message'] = $this->servicesModel->CreateSubservice($name, $selectedServiceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Delete a subservice */
    public function DeleteSubservice() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canDeleteSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete subservices.";
        } else {
            $_SESSION['message'] = $this->servicesModel->DeleteSubservice($selectedSubserviceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID);
        exit;
    }

    /** Update subservice description and price */
    public function UpdateSubserviceInfo() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $description  = $_POST['description'];
            $pricePerUnit = $_POST['pricePerUnit'];
            $_SESSION['message'] = $this->servicesModel->UpdateSubserviceInfo(
                $selectedSubserviceID,
                $pricePerUnit,
                $description
            );
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Toggle subservice active/inactive */
    public function ToggleSubserviceStatus() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubserviceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the subservice's status.";
        } else {
            $_SESSION['message'] = $this->servicesModel->ToggleSubserviceStatus($selectedSubserviceID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Upload images for a subservice */
    public function UploadSubserviceImages() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $images = $_FILES['images'];
            $_SESSION['message'] = $this->servicesModel->UploadSubserviceImages($selectedSubserviceID, $images);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }

    /** Delete a single subservice image */
    public function DeleteSubserviceImage() {
        $selectedServiceID    = (int)$_POST['selectedServiceID'];
        $selectedSubserviceID = (int)$_POST['selectedSubserviceID'];

        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
        } else {
            $selectedID = (int)$_POST['selectedID'];
            $_SESSION['message'] = $this->servicesModel->DeleteSubserviceImage($selectedID);
        }

        header("Location: index.php?page=services&serviceID=" . $selectedServiceID . "&subserviceID=" . $selectedSubserviceID);
        exit;
    }
}
