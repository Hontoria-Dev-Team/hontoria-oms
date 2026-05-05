<?php
class ServicesC {
    private $servicesModel;

    public function __construct($pdo) {
        require_once __DIR__ . '/../Models/ServicesM.php';
        $this->servicesModel = new ServicesM($pdo);
    }

    //
    // Display the services management page with all services, subservices,
    // processes, order counts and images.
    //
    public function ShowServices($serviceIdentifier, $subserviceIdentifier) {
        $page = "services";

        // Provide the variable names the view uses
        $serviceID    = $serviceIdentifier;
        $subserviceID = $subserviceIdentifier;

        $servicesList = $this->servicesModel->GetServices();
        $serviceProcessList = $this->servicesModel->GetAllServiceProcesses();
        $subserviceList = $this->servicesModel->GetAllSubservices();
        $subserviceOrderCountTally = $this->servicesModel->GetAllSubservicesOrderCount();
        $processesList = $this->servicesModel->GetAllProcesses();
        $subserviceImageList = $this->servicesModel->GetAllSubserviceImages();
        $serviceOrderCountMap = $this->servicesModel->GetAllServicesOrderCountMapped();

        require __DIR__ . '/../Views/Services/Page.php';
    }

    //
    // Create a new service after verifying permissions and name validity.
    //
    public function CreateService() {
        if (!in_array('canCreateServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create services.";
            header("Location: index.php?page=services");
            exit;
        }

        $serviceName = ucwords(strtolower(trim($_POST['name'])));
        $_SESSION['message'] = $this->servicesModel->InsertService($serviceName);

        $selectedServiceIdentifier = $_POST['selectedServiceID'] ?? -1;
        $selectedSubserviceIdentifier = $_POST['selectedSubserviceID'] ?? -1;
        header("Location: index.php?page=services&serviceID=" . $selectedServiceIdentifier . "&subserviceID=" . $selectedSubserviceIdentifier);
        exit;
    }

    //
    // Delete a service after verifying permissions and order count.
    //
    public function DeleteService() {
        if (!in_array('canDeleteServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete services.";
            header('Location: index.php?page=services');
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $_SESSION['message'] = $this->servicesModel->DeleteService($serviceIdentifier);

        header('Location: index.php?page=services');
        exit;
    }

    //
    // Toggle the active/inactive status of a service.
    //
    public function ToggleServiceStatus() {
        if (!in_array('canAlterServiceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the service's status.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $_SESSION['message'] = $this->servicesModel->UpdateServiceStatus($serviceIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Toggle whether the service requires a design.
    //
    public function ToggleHasDesign() {
        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $_SESSION['message'] = $this->servicesModel->ToggleServiceHasDesign($serviceIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Toggle whether the service requires a variable list.
    //
    public function ToggleHasVariableList() {
        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $_SESSION['message'] = $this->servicesModel->ToggleServiceHasVariableList($serviceIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Helper to build the query string for service/subservice persistence.
    //
    private function BuildServiceRedirectParameters() {
        $selectedServiceID = $_POST['selectedServiceID'] ?? '';
        $selectedSubserviceID = $_POST['selectedSubserviceID'] ?? '';
        return "&serviceID=" . urlencode($selectedServiceID) . "&subserviceID=" . urlencode($selectedSubserviceID);
    }

    //
    // Update the process sequence for a service.
    //
    public function SetServiceProcess() {
        if (!in_array('canAlterServices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter services.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $processIdentifiers = $_POST['processList'] ?? [];
        // Ensure all process identifiers are integers
        $processIdentifiers = array_map('intval', $processIdentifiers);
        $_SESSION['message'] = $this->servicesModel->UpdateServiceProcess($serviceIdentifier, $processIdentifiers);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Display the process management page after verifying access.
    //
    public function ShowProcessesManagementPage() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services");
            exit;
        }

        $page = "services";
        $lastPage = 'services';
        $backLink = 'index.php?page=services';
        $processList = $this->servicesModel->GetAllProcesses();
        $lockedProcessIdentifiers = [];
        foreach ($processList as $process) {
            if ($this->servicesModel->IsProcessLockedByOrders($process['id'])) {
                $lockedProcessIdentifiers[] = (int)$process['id'];
            }
        }

        require __DIR__ . '/../Views/Services/ProcessManagement.php';
        exit;
    }

    //
    // Create a new process after verifying permissions.
    //
    public function CreateProcess() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services&action=manageProcesses");
            exit;
        }

        $processName = ucfirst(strtolower(trim($_POST['name'])));
        $creationResult = $this->servicesModel->InsertProcess($processName);

        $_SESSION['message'] = $creationResult ? "Success: Created process." : "Error: Process name already exists or is invalid.";
        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    //
    // Delete a process after verifying permissions.
    //
    public function RemoveProcess() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services&action=manageProcesses");
            exit;
        }

        $processIdentifier = (int)$_POST['selectedID'];
        $_SESSION['message'] = $this->servicesModel->DeleteProcess($processIdentifier);

        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    //
    // Update a process's settings (assignments, access levels).
    //
    public function SetProcess() {
        if (!in_array('canManageServiceProcesses', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to manage service processes.";
            header("Location: index.php?page=services&action=manageProcesses");
            exit;
        }

        $processIdentifier = (int)$_POST['id'];
        $minAssign = (int)$_POST['minAssign'];
        $maxAssign = (int)$_POST['maxAssign'];
        $hasGCAccess = (int)$_POST['hasGCAccess'];
        $designAccess = $_POST['designAccess'];
        $variableListAccess = $_POST['variableListAccess'];

        $result = $this->servicesModel->UpdateProcess($processIdentifier, $minAssign, $maxAssign, $hasGCAccess, $designAccess, $variableListAccess);
        $_SESSION['message'] = $result ? "Success: Updated process." : "Error: Process could not be updated.";

        header("Location: index.php?page=services&action=manageProcesses");
        exit;
    }

    //
    // Create a new subservice under the selected service.
    //
    public function CreateSubservice() {
        if (!in_array('canCreateSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to create subservices.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        $subserviceName = ucwords(strtolower(trim($_POST['name'])));
        $_SESSION['message'] = $this->servicesModel->InsertSubservice($subserviceName, $serviceIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Delete a subservice after verifying permissions.
    //
    public function RemoveSubservice() {
        if (!in_array('canDeleteSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to delete subservices.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $subserviceIdentifier = (int)$_POST['selectedSubserviceID'];
        $_SESSION['message'] = $this->servicesModel->DeleteSubservice($subserviceIdentifier);

        $serviceIdentifier = (int)$_POST['selectedServiceID'];
        header("Location: index.php?page=services&serviceID=" . $serviceIdentifier);
        exit;
    }

    //
    // Update subservice description and price per unit.
    //
    public function SetSubserviceInfo() {
        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $subserviceIdentifier = (int)$_POST['selectedSubserviceID'];
        $description = $_POST['description'] ?? '';
        $pricePerUnit = (float)$_POST['pricePerUnit'];
        $_SESSION['message'] = $this->servicesModel->UpdateSubserviceInfo($subserviceIdentifier, $pricePerUnit, $description);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Toggle the active/inactive status of a subservice.
    //
    public function ToggleSubserviceStatus() {
        if (!in_array('canAlterSubserviceStatus', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter the subservice's status.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $subserviceIdentifier = (int)$_POST['selectedSubserviceID'];
        $_SESSION['message'] = $this->servicesModel->UpdateSubserviceStatus($subserviceIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Upload one or more images for a subservice.
    //
    public function UploadSubserviceImages() {
        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $subserviceIdentifier = (int)$_POST['selectedSubserviceID'];
        $filesArray = $_FILES['images'] ?? [];
        $_SESSION['message'] = $this->servicesModel->InsertSubserviceImages($subserviceIdentifier, $filesArray);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }

    //
    // Remove a single image from a subservice.
    //
    public function RemoveSubserviceImage() {
        if (!in_array('canAlterSubservices', $_SESSION['permissions'])) {
            $_SESSION['message'] = "Error: You do not have permission to alter subservices.";
            $redirectParameters = $this->BuildServiceRedirectParameters();
            header("Location: index.php?page=services" . $redirectParameters);
            exit;
        }

        $imageIdentifier = (int)$_POST['selectedID'];
        $_SESSION['message'] = $this->servicesModel->DeleteSubserviceImage($imageIdentifier);

        $redirectParameters = $this->BuildServiceRedirectParameters();
        header("Location: index.php?page=services" . $redirectParameters);
        exit;
    }
}
