<?php
class ServicesM {
    private $pdo;
    private $storageDirectory; // Directory path for storing subservice images

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->storageDirectory = __DIR__ . '/../../Storage/SubserviceImages/';
    }

    // -----------------------------------------------------
    // Service Getters
    // -----------------------------------------------------

    //
    // Retrieve all services ordered by active status and name.
    // @return array
    //
    public function GetServices() {
        $query = "SELECT * FROM services ORDER BY isActive DESC, name ASC";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Get service details by its identifier.
    // @param int $serviceIdentifier
    // @return array|false
    //
    public function GetServiceByID($serviceIdentifier) {
        $query = "SELECT name, hasDesign, hasVariableList, isActive FROM services WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    //
    // Get service by exact name (used for duplicate checks).
    // @param string $serviceName
    // @return array|false
    //
    public function GetServiceByName($serviceName) {
        $query = "SELECT id, description FROM services WHERE name = :name";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $serviceName);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    //
    // Get the number of orders linked to a service (via subservices).
    // @param int $serviceIdentifier
    // @return int
    //
    public function GetServiceOrderCount($serviceIdentifier) {
        $query = "
            SELECT COUNT(orders.id) AS orderCount
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            WHERE services.id = :id
        ";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['orderCount'] : 0;
    }

    // -----------------------------------------------------
    // Service Operations
    // -----------------------------------------------------

    //
    // Insert a new service after verifying the name is unique and non-empty.
    // Returns a success/error message string.
    //
    public function InsertService($serviceName) {
        $serviceName = trim($serviceName);
        if ($serviceName === '') {
            return "Error: Empty service name.";
        }

        $existingService = $this->GetServiceByName($serviceName);
        if ($existingService) {
            return "Error: Service name already exists.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'service creation',
            'Created a new service called ' . ucfirst($serviceName) . '.',
            'yellow'
        );

        $query = "INSERT INTO services (name) VALUES (:name)";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $serviceName);
        $statement->execute();

        return "Success: Created service.";
    }

    //
    // Delete a service and all its related data (subservices, images, process links).
    // Uses a transaction to ensure atomicity. Fails if the service has active orders.
    //
    public function DeleteService($serviceIdentifier) {
        if ($this->GetServiceOrderCount($serviceIdentifier) > 0) {
            return "Error: The service cannot be deleted since it has active orders.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Service not found.";
        }

        try {
            $this->pdo->beginTransaction();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'service deletion',
                'Deleted the ' . ucfirst($service['name']) . ' service.',
                'red'
            );

            $subservices = $this->GetSubservices($serviceIdentifier);
            if (!empty($subservices)) {
                $subserviceIdentifiers = array_column($subservices, 'id');
                $placeholders = implode(',', array_fill(0, count($subserviceIdentifiers), '?'));

                // Delete image files on disk first
                $query = "SELECT imageName FROM subserviceImages WHERE subserviceID IN ($placeholders)";
                $statement = $this->pdo->prepare($query);
                $statement->execute($subserviceIdentifiers);
                $images = $statement->fetchAll(PDO::FETCH_ASSOC);

                foreach ($images as $image) {
                    $filePath = $this->storageDirectory . $image['imageName'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // Delete image records
                $query = "DELETE FROM subserviceImages WHERE subserviceID IN ($placeholders)";
                $statement = $this->pdo->prepare($query);
                $statement->execute($subserviceIdentifiers);

                // Delete subservices
                $query = "DELETE FROM subservices WHERE serviceID = ?";
                $statement = $this->pdo->prepare($query);
                $statement->execute([$serviceIdentifier]);
            }

            // Delete service-process links
            $query = "DELETE FROM serviceProcess WHERE serviceID = ?";
            $statement = $this->pdo->prepare($query);
            $statement->execute([$serviceIdentifier]);

            // Delete the service itself
            $query = "DELETE FROM services WHERE id = ?";
            $statement = $this->pdo->prepare($query);
            $statement->execute([$serviceIdentifier]);

            $this->pdo->commit();
            return "Success: Deleted service.";
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            return "Error: Failed. " . $exception->getMessage();
        }
    }

    //
    // Toggle the active/inactive status of a service.
    // Activation requires at least one active subservice and a non-empty process.
    //
    public function UpdateServiceStatus($serviceIdentifier) {
        $process = $this->GetServiceProcess($serviceIdentifier);
        $subservices = $this->GetSubservices($serviceIdentifier);

        if (empty($process)) {
            return "Error: The service has an empty process to be activated.";
        }
        if (empty($subservices) || !$subservices[0]['isActive']) {
            return "Error: The service has no active subservices to be activated.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Service not found.";
        }

        // Log status change before executing
        if ($service['isActive']) {
            $this->insertUserActivityLog(
                $_SESSION['id'],
                'service deactivation',
                'Deactivated the ' . ucfirst($service['name']) . ' service.',
                'red'
            );
        } else {
            $this->insertUserActivityLog(
                $_SESSION['id'],
                'service activation',
                'Activated the ' . ucfirst($service['name']) . ' service.',
                'yellow'
            );
        }

        $query = "UPDATE services SET isActive = !isActive WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Updated service status.";
    }

    //
    // Toggle whether the service requires a design.
    // Only allowed when the service has no orders and is inactive.
    //
    public function ToggleServiceHasDesign($serviceIdentifier) {
        if ($this->GetServiceOrderCount($serviceIdentifier) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Service not found.";
        }
        if ($service['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'service objectives',
            $service['hasDesign']
                ? 'Made the ' . ucfirst($service['name']) . ' service not to require a design.'
                : 'Made the ' . ucfirst($service['name']) . ' service to require a design.',
            $service['hasDesign'] ? 'red' : 'yellow'
        );

        $query = "UPDATE services SET hasDesign = !hasDesign WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Updated service objectives.";
    }

    //
    // Toggle whether the service requires a variable list.
    // Only allowed when the service has no orders and is inactive.
    //
    public function ToggleServiceHasVariableList($serviceIdentifier) {
        if ($this->GetServiceOrderCount($serviceIdentifier) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Service not found.";
        }
        if ($service['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'service objectives',
            $service['hasVariableList']
                ? 'Made the ' . ucfirst($service['name']) . ' service not to require a variable list.'
                : 'Made the ' . ucfirst($service['name']) . ' service to require a variable list.',
            $service['hasVariableList'] ? 'red' : 'yellow'
        );

        $query = "UPDATE services SET hasVariableList = !hasVariableList WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Updated service objectives.";
    }

    // -----------------------------------------------------
    // Process Getters
    // -----------------------------------------------------

    //
    // Retrieve the ordered list of processes assigned to a specific service.
    // Returns array of [id, name, phase].
    //
    public function GetServiceProcess($serviceIdentifier) {
        $query = "SELECT processes.id, processes.name, serviceProcess.phase
                  FROM serviceProcess
                  JOIN processes ON serviceProcess.processesID = processes.id
                  WHERE serviceProcess.serviceID = :id
                  ORDER BY serviceProcess.phase ASC";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Remove all process associations for a service (used before re-insertion).
    //
    public function ClearServiceProcess($serviceIdentifier) {
        $query = "DELETE FROM serviceProcess WHERE serviceID = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        return $statement->execute();
    }

    //
    // Replace the entire process sequence for a service.
    // Only allowed when the service has no orders and is inactive.
    //
    public function UpdateServiceProcess($serviceIdentifier, $processIdentifiers) {
        if ($this->GetServiceOrderCount($serviceIdentifier) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Service not found.";
        }
        if ($service['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        // Validate that all provided process identifiers exist
        $validIdentifiers = array_map('intval', array_column($this->GetAllProcesses(), 'id'));
        foreach ($processIdentifiers as $processIdentifier) {
            if (!in_array((int)$processIdentifier, $validIdentifiers, true)) {
                return "Error: One or more selected processes do not exist.";
            }
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'service process',
            'Modified the service process of the ' . ucfirst($service['name']) . ' service.',
            'yellow'
        );

        $this->ClearServiceProcess($serviceIdentifier);

        if (!empty($processIdentifiers)) {
            $query = "INSERT INTO serviceProcess (serviceID, processesID, phase) VALUES (:serviceID, :processID, :phase)";
            $statement = $this->pdo->prepare($query);

            foreach ($processIdentifiers as $index => $processIdentifier) {
                $statement->execute([
                    ':serviceID' => $serviceIdentifier,
                    ':processID' => (int)$processIdentifier,
                    ':phase'     => $index + 1
                ]);
            }
        }

        return "Success: Updated service process.";
    }

    //
    // Return true if the process is assigned to at least one service that has active orders.
    //
    public function IsProcessLockedByOrders($processIdentifier) {
        $query = "
        SELECT COUNT(orders.id) AS orderCount
        FROM orders
        JOIN subservices ON orders.subserviceID = subservices.id
        JOIN services ON subservices.serviceID = services.id
        JOIN serviceProcess ON serviceProcess.serviceID = services.id
        WHERE serviceProcess.processesID = :processID
    ";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':processID', $processIdentifier, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return ($result && (int)$result['orderCount'] > 0);
    }

    //
    // Get all service-process relationships across all services.
    //
    public function GetAllServiceProcesses() {
        $query = "SELECT serviceProcess.serviceID, serviceProcess.phase, processes.name, processes.id
                  FROM serviceProcess
                  JOIN processes ON serviceProcess.processesID = processes.id
                  ORDER BY serviceProcess.serviceID ASC, serviceProcess.phase ASC";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Retrieve all processes ordered alphabetically by name.
    //
    public function GetAllProcesses() {
        $query = "SELECT * FROM processes ORDER BY name";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Check whether a process with a given name exists; returns its id or false.
    //
    public function GetSingleProcessByName($processName) {
        $query = "SELECT id FROM processes WHERE name = :name";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $processName);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_COLUMN);
    }

    //
    // Fetch a process name by its identifier; returns the name or false.
    //
    public function GetSingleProcessByID($processIdentifier) {
        $query = "SELECT name FROM processes WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $processIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_COLUMN);
    }

    // -----------------------------------------------------
    // Process Operations
    // -----------------------------------------------------

    //
    // Create a new process after confirming the name is unique and non-empty.
    //
    public function InsertProcess($processName) {
        $processName = trim($processName);
        if ($processName === '') {
            return false;
        }

        $existing = $this->GetSingleProcessByName($processName);
        if ($existing) {
            return false;
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'process creation',
            'Created a new process called ' . ucfirst($processName) . '.',
            'yellow'
        );

        $query = "INSERT INTO processes (name) VALUES (:name)";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $processName);
        return $statement->execute();
    }

    //
    // Delete a process if it is not currently in use by any service.
    //
    public function DeleteProcess($processIdentifier) {
        $allServiceProcesses = $this->GetAllServiceProcesses();
        $inUse = false;

        foreach ($allServiceProcesses as $serviceProcess) {
            if ((int)$serviceProcess['id'] === (int)$processIdentifier) {
                $inUse = true;
                break;
            }
        }

        if ($this->IsProcessLockedByOrders($processIdentifier)) {
            return "Error: Cannot delete this process because it is used by a service with active orders.";
        }

        if ($inUse) {
            return "Error: Cannot delete this process because it is in use in one or more services";
        }

        $processName = $this->GetSingleProcessByID($processIdentifier);
        if (!$processName) {
            return "Error: Process not found.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'process deletion',
            'Deleted the ' . ucfirst($processName) . ' process.',
            'red'
        );

        // Remove role task associations for this process
        $query = "DELETE FROM roleProcessTasks WHERE processID = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $processIdentifier, PDO::PARAM_INT);
        $statement->execute();

        // Delete the process itself
        $query = "DELETE FROM processes WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $processIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Deleted process.";
    }

    //
    // Update the configuration of a process (access levels, assignment limits).
    //
    public function UpdateProcess($processIdentifier, $minAssignDefault, $maxAssignDefault, $hasGCAccess, $designAccess, $variableListAccess) {
        $processName = $this->GetSingleProcessByID($processIdentifier);
        if (!$processName) {
            return false;
        }

        if ($this->IsProcessLockedByOrders($processIdentifier)) {
            return "Error: Cannot update this process because it is used by a service with active orders.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'process update',
            'Updated the ' . ucfirst($processName) . ' process.',
            'yellow'
        );

        $query = "UPDATE processes
                  SET minAssignDefault = :minAssignDefault,
                      maxAssignDefault = :maxAssignDefault,
                      hasGCAccess = :hasGCAccess,
                      designAccess = :designAccess,
                      variableListAccess = :variableListAccess
                  WHERE id = :id";
        $statement = $this->pdo->prepare($query);

        $statement->bindParam(':id', $processIdentifier, PDO::PARAM_INT);
        $statement->bindParam(':minAssignDefault', $minAssignDefault, PDO::PARAM_INT);
        $statement->bindParam(':maxAssignDefault', $maxAssignDefault, PDO::PARAM_INT);
        $statement->bindParam(':hasGCAccess', $hasGCAccess, PDO::PARAM_INT);
        $statement->bindParam(':designAccess', $designAccess);
        $statement->bindParam(':variableListAccess', $variableListAccess);

        return $statement->execute();
    }

    // -----------------------------------------------------
    // Subservice Getters
    // -----------------------------------------------------

    //
    // Get all subservices belonging to a service, ordered by active status and name.
    //
    public function GetSubservices($serviceIdentifier) {
        $query = "SELECT id, name, isActive, description, pricePerUnit
                  FROM subservices
                  WHERE serviceID = :id
                  ORDER BY isActive DESC, name ASC";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Get a single subservice by its identifier.
    //
    public function GetSubservice($subserviceIdentifier) {
        $query = "SELECT serviceID, name, isActive, description, pricePerUnit
                  FROM subservices
                  WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $subserviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    //
    // Get all subservices across every service.
    //
    public function GetAllSubservices() {
        $query = "SELECT * FROM subservices ORDER BY isActive DESC, name ASC";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Check if a subservice name exists under a given service.
    //
    public function GetSingleSubserviceByName($subserviceName, $serviceIdentifier) {
        $query = "SELECT isActive, description, pricePerUnit
                  FROM subservices
                  WHERE serviceID = :serviceID AND name = :name
                  LIMIT 1";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $subserviceName);
        $statement->bindParam(':serviceID', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    //
    // Get the number of orders linked to a specific subservice.
    //
    public function GetSubserviceOrderCount($subserviceIdentifier) {
        $query = "SELECT COUNT(orders.id) AS orderCount
                  FROM orders
                  WHERE subserviceID = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $subserviceIdentifier, PDO::PARAM_INT);
        $statement->execute();
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['orderCount'] : 0;
    }

    // -----------------------------------------------------
    // Subservice Operations
    // -----------------------------------------------------

    //
    // Create a new subservice under a service after verifying uniqueness.
    //
    public function InsertSubservice($subserviceName, $serviceIdentifier) {
        $subserviceName = trim($subserviceName);
        if ($subserviceName === '') {
            return "Error: Empty subservice name.";
        }

        $existing = $this->GetSingleSubserviceByName($subserviceName, $serviceIdentifier);
        if ($existing) {
            return "Error: Subservice name already exists under this service.";
        }

        $service = $this->GetServiceByID($serviceIdentifier);
        if (!$service) {
            return "Error: Parent service not found.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'subservice creation',
            'Created a new subservice called ' . ucfirst($subserviceName) . ' under the ' . ucfirst($service['name']) . ' service.',
            'yellow'
        );

        $query = "INSERT INTO subservices (name, serviceID, pricePerUnit) VALUES (:name, :serviceID, 1)";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':name', $subserviceName);
        $statement->bindParam(':serviceID', $serviceIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Created subservice.";
    }

    //
    // Delete a subservice and its associated images inside a transaction.
    // Refuses if the subservice is active or has existing orders.
    //
    public function DeleteSubservice($subserviceIdentifier) {
        $subservice = $this->GetSubservice($subserviceIdentifier);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        if ($this->GetSubserviceOrderCount($subserviceIdentifier) > 0) {
            return "Error: The subservice cannot be deleted since it has active orders.";
        }
        if ($subservice['isActive'] == 1) {
            return "Error: The subservice cannot be deleted since it is still active.";
        }

        try {
            $this->pdo->beginTransaction();

            $service = $this->GetServiceByID($subservice['serviceID']);
            $this->insertUserActivityLog(
                $_SESSION['id'],
                'subservice deletion',
                'Deleted the ' . ucfirst($subservice['name']) . ' subservice under the ' . ucfirst($service['name']) . ' service.',
                'red'
            );

            // Delete image files from storage
            $query = "SELECT imageName FROM subserviceImages WHERE subserviceID = :id";
            $statement = $this->pdo->prepare($query);
            $statement->execute([':id' => $subserviceIdentifier]);
            $images = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($images as $image) {
                $filePath = $this->storageDirectory . $image['imageName'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Remove image records
            $query = "DELETE FROM subserviceImages WHERE subserviceID = :id";
            $statement = $this->pdo->prepare($query);
            $statement->execute([':id' => $subserviceIdentifier]);

            // Delete the subservice
            $query = "DELETE FROM subservices WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            $statement->execute([':id' => $subserviceIdentifier]);

            $this->pdo->commit();
            return "Success: Deleted subservice.";
        } catch (PDOException $exception) {
            $this->pdo->rollBack();
            return "Error: Failed. " . $exception->getMessage();
        }
    }

    //
    // Activate or deactivate a subservice.
    // Prevents disabling the last active subservice under a service.
    //
    public function UpdateSubserviceStatus($subserviceIdentifier) {
        $subservice = $this->GetSubservice($subserviceIdentifier);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        $siblingSubservices = $this->GetSubservices($subservice['serviceID']);

        // Check if this is the only active subservice and user wants to deactivate
        if ($subservice['isActive'] == 1) {
            $activeSiblings = array_filter($siblingSubservices, function ($sibling) {
                return $sibling['isActive'] == 1;
            });
            if (count($activeSiblings) === 1) {
                return "Error: The subservice cannot be disabled since it is the last active subservice.";
            }
        }

        $service = $this->GetServiceByID($subservice['serviceID']);
        $this->insertUserActivityLog(
            $_SESSION['id'],
            $subservice['isActive'] ? 'subservice deactivation' : 'subservice activation',
            ($subservice['isActive'] ? 'Deactivated' : 'Activated') . ' the ' . ucfirst($subservice['name']) . ' subservice under the ' . ucfirst($service['name']) . ' service.',
            $subservice['isActive'] ? 'red' : 'yellow'
        );

        $query = "UPDATE subservices SET isActive = !isActive WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $subserviceIdentifier, PDO::PARAM_INT);
        $statement->execute();

        return "Success: Updated subservice status.";
    }

    //
    // Update a subservice's price per unit and description.
    //
    public function UpdateSubserviceInfo($subserviceIdentifier, $pricePerUnit, $description) {
        $subservice = $this->GetSubservice($subserviceIdentifier);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        $service = $this->GetServiceByID($subservice['serviceID']);
        $this->insertUserActivityLog(
            $_SESSION['id'],
            'subservice update',
            'Updated the ' . ucfirst($subservice['name']) . ' subservice under the ' . ucfirst($service['name']) . ' service.',
            'yellow'
        );

        $query = "UPDATE subservices SET pricePerUnit = :pricePerUnit, description = :description WHERE id = :id";
        $statement = $this->pdo->prepare($query);
        $statement->bindParam(':id', $subserviceIdentifier, PDO::PARAM_INT);
        $statement->bindParam(':pricePerUnit', $pricePerUnit);
        $statement->bindParam(':description', $description);
        $statement->execute();

        return "Success: Updated subservice.";
    }

    // -----------------------------------------------------
    // Utility Functions
    // -----------------------------------------------------

    //
    // Get the order count grouped by service (used for stats and maps).
    //
    public function GetAllServicesOrderCount() {
        $query = "
            SELECT services.id AS serviceID, COUNT(orders.id) AS orderCount
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            GROUP BY services.id
        ";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Return an associative array mapping serviceID -> order count.
    //
    public function GetAllServicesOrderCountMapped() {
        $orderCounts = $this->GetAllServicesOrderCount();
        $map = [];
        foreach ($orderCounts as $item) {
            $map[$item['serviceID']] = $item['orderCount'];
        }
        return $map;
    }

    //
    // Get order count grouped by subservice.
    //
    public function GetAllSubservicesOrderCount() {
        $query = "
            SELECT subserviceID, COUNT(orders.id) AS orderCount
            FROM orders
            GROUP BY subserviceID
        ";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Fetch all subservice image records.
    //
    public function GetAllSubserviceImages() {
        $query = "SELECT * FROM subserviceImages";
        $statement = $this->pdo->prepare($query);
        $statement->execute();
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    //
    // Upload multiple images for a subservice, validating formats and generating unique filenames.
    //
    public function InsertSubserviceImages($subserviceIdentifier, $filesArray) {
        if (!is_dir($this->storageDirectory)) {
            mkdir($this->storageDirectory, 0755, true);
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate all files before any upload
        for ($i = 0; $i < count($filesArray['name']); $i++) {
            $extension = strtolower(pathinfo($filesArray['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) {
                return "Error: Invalid file format.";
            }
        }

        $uploadedFileNames = [];

        // Upload files one by one
        for ($i = 0; $i < count($filesArray['name']); $i++) {
            $extension = strtolower(pathinfo($filesArray['name'][$i], PATHINFO_EXTENSION));
            $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '_' . $i . '.' . $extension;
            $targetPath = $this->storageDirectory . $newFileName;
            $tempPath = $filesArray['tmp_name'][$i];

            if (move_uploaded_file($tempPath, $targetPath)) {
                $uploadedFileNames[] = $newFileName;
            } else {
                // Cleanup previously uploaded files on failure
                foreach ($uploadedFileNames as $uploadedFile) {
                    $filePath = $this->storageDirectory . $uploadedFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                return "Error: Upload failed.";
            }
        }

        try {
            $query = "INSERT INTO subserviceImages (subserviceID, imageName) VALUES (:subserviceID, :imageName)";
            $statement = $this->pdo->prepare($query);

            foreach ($uploadedFileNames as $imageName) {
                $statement->execute([
                    ':subserviceID' => $subserviceIdentifier,
                    ':imageName' => $imageName
                ]);
            }

            $subservice = $this->GetSubservice($subserviceIdentifier);
            $service = $this->GetServiceByID($subservice['serviceID']);

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'subservice update',
                'Uploaded ' . count($filesArray['name']) . ' image/s to the ' . ucfirst($subservice['name']) . ' subservice under the ' . ucfirst($service['name']) . ' service.',
                'yellow'
            );

            return "Success: Upload successful.";
        } catch (PDOException $exception) {
            // Rollback file storage on database failure
            foreach ($uploadedFileNames as $uploadedFile) {
                $filePath = $this->storageDirectory . $uploadedFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return "Error: Failed. " . $exception->getMessage();
        }
    }

    //
    // Delete a single subservice image (both file and database record).
    //
    public function DeleteSubserviceImage($imageIdentifier) {
        try {
            $query = "SELECT subserviceID, imageName FROM subserviceImages WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            $statement->bindParam(':id', $imageIdentifier, PDO::PARAM_INT);
            $statement->execute();
            $imageRecord = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$imageRecord) {
                return "Error: Image record not found.";
            }

            // Delete the physical file
            $filePath = $this->storageDirectory . $imageRecord['imageName'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    return "Error: Failed to delete image file.";
                }
            }

            // Delete database record
            $query = "DELETE FROM subserviceImages WHERE id = :id";
            $statement = $this->pdo->prepare($query);
            $statement->bindParam(':id', $imageIdentifier, PDO::PARAM_INT);
            $result = $statement->execute();

            if (!$result) {
                return "Error: Failed to delete database record.";
            }

            $subservice = $this->GetSubservice($imageRecord['subserviceID']);
            $service = $this->GetServiceByID($subservice['serviceID']);

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'subservice update',
                'Deleted an image from the ' . ucfirst($subservice['name']) . ' subservice under the ' . ucfirst($service['name']) . ' service.',
                'red'
            );

            return "Success: Deletion successful.";
        } catch (PDOException $exception) {
            return "Error: Failed. " . $exception->getMessage();
        }
    }

    // -----------------------------------------------------
    // Activity Logging
    // -----------------------------------------------------

    //
    // Insert a record into the user activity log.
    //
    public function InsertUserActivityLog($userIdentifier, $logHeading, $logMessage, $logColor) {
        $query = "INSERT INTO userActivityLog (userID, head, log, color) VALUES (:userID, :head, :log, :color)";
        $statement = $this->pdo->prepare($query);
        $statement->execute([
            ':userID' => $userIdentifier,
            ':head'   => strtolower($logHeading),
            ':log'    => $logMessage,
            ':color'  => strtolower($logColor)
        ]);
    }
}
