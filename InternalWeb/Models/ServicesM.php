<?php
class ServicesM {
    private $pdo;
    private $storageDir; // Directory path for storing subservice images

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->storageDir = __DIR__ . '/../../Storage/SubserviceImages/'; // Initialize storage directory path
    }

    // Service Getters

    // Retrieve all services ordered by active status and name
    public function getServices() {
        $query = "SELECT * FROM services ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get service details by ID
    public function getServiceByID($id) {
        $query = "SELECT name, hasDesign, hasVariableList, isActive FROM services WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get service by name
    public function getServiceByName($name) {
        $query = "SELECT id, description FROM services WHERE name = :name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Get order count for a service
    public function getServiceOrderCount($id) {
        $query = "
            SELECT
                COUNT(orders.id) AS orderCount
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            WHERE services.id = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['orderCount'] : 0;
    }

    // Service Operations

    // Insert a new service, checking for duplicates
    public function insertService($name) {
        if (empty($name)) {
            return "Error: Empty service name.";
        }

        $service = $this->getServiceByName($name);

        if ($service) {
            return "Error: Service name already exists.";
        }

        $query = "INSERT INTO services (name) VALUES (:name);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return "Success: Created service.";
    }

    // Delete a service and all its related data (subservices, images, processes)
    // Uses transaction to ensure data integrity
    public function deleteService($id) {
        if ($this->getServiceOrderCount($id) > 0) {
            return "Error: The service cannot be deleted since it has active orders.";
        }

        try {
            $this->pdo->beginTransaction();

            // Get all subservices for this service
            $subservices = $this->getSubservices($id);

            if (!empty($subservices)) {
                $subserviceIds = array_column($subservices, 'id');
                $placeholders = implode(',', array_fill(0, count($subserviceIds), '?'));

                // Get all images to delete files first
                $query = "SELECT imageName FROM subserviceImages WHERE subserviceID IN ($placeholders)";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($subserviceIds);
                $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Delete image files from storage
                foreach ($images as $image) {
                    $filePath = $this->storageDir . $image['imageName'];
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }

                // Delete subserviceImages records
                $query = "DELETE FROM subserviceImages WHERE subserviceID IN ($placeholders)";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute($subserviceIds);

                // Delete subservices
                $query = "DELETE FROM subservices WHERE serviceID = ?";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([$id]);
            }

            // Delete serviceProcess associations
            $query = "DELETE FROM serviceProcess WHERE serviceID = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);

            // Delete the service
            $query = "DELETE FROM services WHERE id = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);

            $this->pdo->commit();
            return "Success: Deleted service.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed. " . ($e->getMessage());
        }
    }

    // Toggle service active status
    public function updateServiceStatus($id) {
        $process = $this->getServiceProcess($id);
        $subservices = $this->getSubservices($id);

        if (empty($process)) {
            return "Error: The service has an empty process to be activated.";
        } else if (empty($subservices) || !$subservices[0]['isActive']) {
            return "Error: The service has no active subservices to be activated.";
        }

        $query = "UPDATE services SET isActive = !isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Updated service status.";
    }

    // Toggle design capability for service
    public function toggleServiceHasDesign($id) {
        if ($this->getServiceOrderCount($id) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        } else if ($this->getServiceByID($id)['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        $query = "UPDATE services SET hasDesign = !hasDesign WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Updated service objectives.";
    }

    // Toggle variable list capability for service
    public function toggleServiceHasVariableList($id) {
        if ($this->getServiceOrderCount($id) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        } else if ($this->getServiceByID($id)['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        $query = "UPDATE services SET hasVariableList = !hasVariableList WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Updated service objectives.";
    }

    // Get processes associated with a service, ordered by phase
    public function getServiceProcess($serviceID) {
        $query = "SELECT processes.id, processes.name, serviceProcess.phase
                  FROM serviceProcess
                  JOIN processes ON serviceProcess.processesID = processes.id
                  WHERE serviceProcess.serviceID = :id
                  ORDER BY serviceProcess.phase ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Clear all process associations for a service
    public function clearServiceProcess($id) {
        $query = "DELETE FROM serviceProcess WHERE serviceID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Update process associations for a service
    public function updateServiceProcess($id, $processes) {
        if ($this->getServiceOrderCount($id) > 0) {
            return "Error: The service cannot be edited since it has active orders.";
        } else if ($this->getServiceByID($id)['isActive'] == 1) {
            return "Error: The service cannot be edited since it is active.";
        }

        $this->clearServiceProcess($id);

        if (!empty($processes)) {
            $query = "INSERT INTO serviceProcess (serviceID, processesID, phase) VALUES (:serviceID, :processID, :phase)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($processes); $i++) {
                $stmt->execute([
                    ':serviceID' => $id,
                    ':processID' => $processes[$i],
                    ':phase' => $i + 1
                ]);
            }
        }

        return "Success: Updated service process.";
    }

    // Process Getters

    // Get all service-process associations with process details
    public function getAllServiceProcesses() {
        $query = "SELECT serviceProcess.serviceID, serviceProcess.phase, processes.name, processes.id
                  FROM serviceProcess
                  JOIN processes ON serviceProcess.processesID = processes.id
                  ORDER BY serviceProcess.serviceID ASC, serviceProcess.phase ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all processes ordered by name
    public function getAllProcesses() {
        $query = "SELECT * FROM processes ORDER BY name";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if a process exists by name
    public function getSingleProcessByName($name) {
        $query = "SELECT id FROM processes WHERE name = :name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    // Process Operations

    // Insert a new process, checking for duplicates
    public function insertProcess($name) {
        $process = $this->getSingleProcessByName($name);

        if ($process) {
            return false;
        }

        $query = "INSERT INTO processes (name) VALUES (:name);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    // Delete a process if not in use by any service
    public function deleteProcess($id) {
        $serviceProcesses = $this->getAllServiceProcesses();
        $canDelete = true;

        // Check if process is used in any service
        foreach ($serviceProcesses as $serviceProcess) {
            if ((int)$serviceProcess['id'] === $id) {
                $canDelete = false;
                break;
            }
        }

        if (!$canDelete) {
            return "Error: Cannot delete this process because it is in use in one or more services";
        }

        // Remove role associations
        $query = "DELETE FROM roleProcessTasks WHERE processID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // Delete the process
        $query = "DELETE FROM processes WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Deleted process.";
    }

    // Update process settings
    public function updateProcess($id, $minAssignDefault, $maxAssignDefault, $hasGCAccess, $designAccess, $variableListAccess) {
        $query = "UPDATE processes
                  SET minAssignDefault = :minAssignDefault,
                      maxAssignDefault = :maxAssignDefault,
                      hasGCAccess = :hasGCAccess,
                      designAccess = :designAccess,
                      variableListAccess = :variableListAccess
                  WHERE id = :id";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':minAssignDefault', $minAssignDefault);
        $stmt->bindParam(':maxAssignDefault', $maxAssignDefault);
        $stmt->bindParam(':hasGCAccess', $hasGCAccess);
        $stmt->bindParam(':designAccess', $designAccess);
        $stmt->bindParam(':variableListAccess', $variableListAccess);

        return $stmt->execute();
    }

    // Subservice Getters

    // Get all subservices for a specific service
    public function getSubservices($serviceID) {
        $query = "SELECT id, name, isActive, description, pricePerUnit
                  FROM subservices
                  WHERE serviceID = :id
                  ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all subservices across all services
    public function getAllSubservices() {
        $query = "SELECT * FROM subservices ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Check if a subservice exists by name and service
    public function getSingleSubserviceByName($name, $serviceID) {
        $query = "SELECT isActive, description, pricePerUnit
                  FROM subservices
                  WHERE serviceID = :serviceID AND name = :name
                  LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':serviceID', $serviceID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Subservice Operations

    // Insert a new subservice under a service
    public function insertSubservice($name, $serviceID) {
        if (empty($name)) {
            return "Error: Empty subservice name.";
        }

        $user = $this->getSingleSubserviceByName($name, $serviceID);

        if ($user) {
            return "Error: Service name already exists.";
        }

        $query = "INSERT INTO subservices (name, serviceID, pricePerUnit) VALUES
            (:name, :serviceID, 1);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':serviceID', $serviceID);
        $stmt->execute();

        return "Success: Created subservice.";
    }

    // Delete a subservice and its associated images
    // Uses transaction for data integrity
    public function deleteSubservice($id) {
        try {
            $this->pdo->beginTransaction();

            // Get all images for this subservice
            $query = "SELECT imageName FROM subserviceImages WHERE subserviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Delete image files from storage
            foreach ($images as $image) {
                $filePath = $this->storageDir . $image['imageName'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete subserviceImages records
            $query = "DELETE FROM subserviceImages WHERE subserviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);

            // Delete the subservice
            $query = "DELETE FROM subservices WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);

            $this->pdo->commit();
            return "Success: Deleted subservice.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed. " . $e->getMessage();
        }
    }

    // Toggle subservice active status
    public function updateSubserviceStatus($id) {
        $query = "UPDATE subservices SET isActive = !isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Updated subservice status.";
    }

    // Update subservice price and description
    public function updateSubserviceInfo($id, $pricePerUnit, $description) {
        $query = "UPDATE subservices SET pricePerUnit = :pricePerUnit, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':pricePerUnit', $pricePerUnit);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return "Success: Updated subservice.";
    }

    // Utility Functions

    // Get order count for each service (used for statistics)
    public function getAllServicesOrderCount() {
        $query = "
            SELECT
                services.id AS serviceID,
                COUNT(orders.id) AS orderCount
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            GROUP BY services.id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get order count mapped by service ID
    public function getAllServicesOrderCountMapped() {
        $map = [];

        foreach ($this->getAllServicesOrderCount() as $item) {
            $map[$item['serviceID']] = $item['orderCount'];
        }

        return $map;
    }

    // Get order count for each subservice
    public function getAllSubservicesOrderCount() {
        $query = "
            SELECT
                subserviceID,
                COUNT(orders.id) AS orderCount
            FROM orders
            GROUP BY subserviceID
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all subservice images
    public function getAllSubserviceImages() {
        $query = "SELECT * FROM subserviceImages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Upload multiple images for a subservice
    // Validates file types, generates unique names, handles errors
    public function insertSubserviceImages($subserviceID, $images) {
        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        // Validate all files first
        for ($i = 0; $i < count($images['name']); $i++) {
            $fileExtension = strtolower(pathinfo($images['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowed)) {
                return "Error: Invalid file format.";
            }
        }

        $uploadedFiles = [];

        // Upload files
        for ($i = 0; $i < count($images['name']); $i++) {
            $fileExtension = strtolower(pathinfo($images['name'][$i], PATHINFO_EXTENSION));
            $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '_' . $i . '.' . $fileExtension;
            $targetPath = $this->storageDir . $newFileName;
            $tmpName = $images['tmp_name'][$i];

            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedFiles[] = $newFileName;
            } else {
                // Clean up already uploaded files on failure
                foreach ($uploadedFiles as $uploadedFile) {
                    $filePath = $this->storageDir . $uploadedFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                return "Error: Upload failed.";
            }
        }

        try {
            // Insert records into database
            $query = "INSERT INTO subserviceImages (subserviceID, imageName) VALUES (:subserviceID, :imageName)";
            $stmt = $this->pdo->prepare($query);

            foreach ($uploadedFiles as $imageName) {
                $stmt->execute([
                    ':subserviceID' => $subserviceID,
                    ':imageName' => $imageName
                ]);
            }

            return "Success: Upload successful.";
        } catch (PDOException $e) {
            // Clean up files if database insert fails
            foreach ($uploadedFiles as $uploadedFile) {
                $filePath = $this->storageDir . $uploadedFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return "Error: Failed. " . $e->getMessage();
        }
    }

    // Delete a specific subservice image
    // Removes both file and database record
    public function deleteSubserviceImage($id) {
        try {
            // Get the image filename
            $query = "SELECT imageName FROM subserviceImages WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $image = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$image) {
                return "Error: Image record not found.";
            }

            // Delete the physical file
            $filePath = $this->storageDir . $image['imageName'];
            if (file_exists($filePath)) {
                if (!unlink($filePath)) {
                    return "Error: Failed to delete image file.";
                }
            }

            // Delete the database record
            $query = "DELETE FROM subserviceImages WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $result = $stmt->execute();

            if (!$result) {
                return "Error: Failed to delete database record.";
            }

            return "Success: Deletion successful.";
        } catch (PDOException $e) {
            return "Error: Failed. " . $e->getMessage();
        }
    }
}
