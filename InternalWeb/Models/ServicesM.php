<?php
class ServicesM {
    private $pdo;
    private $storageDir;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->storageDir = __DIR__ . '/../../Storage/SubserviceImages/';
    }

    // ========== SERVICE GETTERS ==========

    public function GetAllServices() {
        $query = "SELECT * FROM services ORDER BY isActive DESC, name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetServiceById($serviceID) {
        $query = "SELECT name, hasDesign, hasVariableList, isActive FROM services WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetServiceByName($name) {
        $query = "SELECT id FROM services WHERE name = :name";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function GetServiceOrderCount($serviceID) {
        $query = "
            SELECT COUNT(orders.id) AS orderCount
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            WHERE subservices.serviceID = :id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['orderCount'] ?? 0);
    }

    public function GetAllServicesOrderCountMapped() {
        $query = "
            SELECT services.id AS serviceID, COUNT(orders.id) AS orderCount
            FROM services
            LEFT JOIN subservices ON services.id = subservices.serviceID
            LEFT JOIN orders ON subservices.id = orders.subserviceID
            GROUP BY services.id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($result as $row) {
            $map[$row['serviceID']] = (int)$row['orderCount'];
        }
        return $map;
    }

    // ========== SERVICE OPERATIONS ==========

    public function CreateService($name) {
        if (empty(trim($name))) {
            return "Error: Service name cannot be empty.";
        }

        $existingService = $this->GetServiceByName($name);
        if ($existingService) {
            return "Error: Service name already exists.";
        }

        $query = "INSERT INTO services (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $this->LogUserActivity($_SESSION['id'], 'service creation', "Created service: $name", 'yellow');
            return "Success: Service created.";
        }
        return "Error: Failed to create service.";
    }

    public function DeleteService($serviceID) {
        if ($this->GetServiceOrderCount($serviceID) > 0) {
            return "Error: Cannot delete service with active orders.";
        }

        try {
            $this->pdo->beginTransaction();

            $service = $this->GetServiceById($serviceID);
            if (!$service) {
                return "Error: Service not found.";
            }

            // Delete all subservice images and files
            $subservices = $this->GetSubservicesByServiceId($serviceID);
            foreach ($subservices as $subservice) {
                $this->DeleteSubserviceImages($subservice['id']);
            }

            // Delete subservices
            $query = "DELETE FROM subservices WHERE serviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
            $stmt->execute();

            // Delete service-process associations
            $query = "DELETE FROM serviceProcess WHERE serviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
            $stmt->execute();

            // Delete service
            $query = "DELETE FROM services WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            $this->LogUserActivity($_SESSION['id'], 'service deletion', "Deleted service: {$service['name']}", 'red');
            return "Success: Service deleted.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed to delete service.";
        }
    }

    public function ToggleServiceStatus($serviceID) {
        $service = $this->GetServiceById($serviceID);
        if (!$service) {
            return "Error: Service not found.";
        }

        $processes = $this->GetServiceProcesses($serviceID);
        $subservices = $this->GetSubservicesByServiceId($serviceID);

        if ($service['isActive'] === 0) {
            if (empty($processes)) {
                return "Error: Cannot activate service without processes.";
            }
            if (empty($subservices) || !$subservices[0]['isActive']) {
                return "Error: Cannot activate service without active subservices.";
            }
        }

        $query = "UPDATE services SET isActive = NOT isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $action = $service['isActive'] ? 'deactivation' : 'activation';
            $color = $service['isActive'] ? 'red' : 'yellow';
            $this->LogUserActivity($_SESSION['id'], "service $action", "Updated service status: {$service['name']}", $color);
            return "Success: Service status updated.";
        }
        return "Error: Failed to update service status.";
    }

    public function ToggleServiceDesignRequirement($serviceID) {
        $service = $this->GetServiceById($serviceID);
        if (!$service) {
            return "Error: Service not found.";
        }

        if ($this->GetServiceOrderCount($serviceID) > 0) {
            return "Error: Cannot modify service with active orders.";
        }
        if ($service['isActive']) {
            return "Error: Cannot modify active service.";
        }

        $query = "UPDATE services SET hasDesign = NOT hasDesign WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $status = $service['hasDesign'] ? 'no longer requires' : 'now requires';
            $this->LogUserActivity($_SESSION['id'], 'service modification', "Service {$service['name']} $status design", 'yellow');
            return "Success: Service design requirement updated.";
        }
        return "Error: Failed to update service design requirement.";
    }

    public function ToggleServiceVariableListRequirement($serviceID) {
        $service = $this->GetServiceById($serviceID);
        if (!$service) {
            return "Error: Service not found.";
        }

        if ($this->GetServiceOrderCount($serviceID) > 0) {
            return "Error: Cannot modify service with active orders.";
        }
        if ($service['isActive']) {
            return "Error: Cannot modify active service.";
        }

        $query = "UPDATE services SET hasVariableList = NOT hasVariableList WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $status = $service['hasVariableList'] ? 'no longer requires' : 'now requires';
            $this->LogUserActivity($_SESSION['id'], 'service modification', "Service {$service['name']} $status variable list", 'yellow');
            return "Success: Service variable list requirement updated.";
        }
        return "Error: Failed to update service variable list requirement.";
    }

    // ========== PROCESS GETTERS ==========

    public function GetAllProcesses() {
        $query = "SELECT * FROM processes ORDER BY name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetAllServiceProcesses() {
        $query = "
            SELECT serviceProcess.serviceID, serviceProcess.phase, processes.id, processes.name
            FROM serviceProcess
            JOIN processes ON serviceProcess.processesID = processes.id
            ORDER BY serviceProcess.serviceID ASC, serviceProcess.phase ASC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetServiceProcesses($serviceID) {
        $query = "
            SELECT processes.id, processes.name, serviceProcess.phase
            FROM serviceProcess
            JOIN processes ON serviceProcess.processesID = processes.id
            WHERE serviceProcess.serviceID = :id
            ORDER BY serviceProcess.phase ASC
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetProcessByName($name) {
        $query = "SELECT id FROM processes WHERE name = :name";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function GetProcessById($processID) {
        $query = "SELECT name FROM processes WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $processID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    // ============= PROCESS MISC =============

    private function IsProcessLockedByOrders($processID) {
        $query = "
            SELECT COUNT(orders.id)
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            WHERE subservices.serviceID IN (
                SELECT serviceID FROM serviceProcess WHERE processesID = :id
            )
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $processID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // ========== PROCESS OPERATIONS ==========

    public function CreateProcess($name) {
        if (empty(trim($name))) {
            return "Error: Process name cannot be empty.";
        }

        $existingProcess = $this->GetProcessByName($name);
        if ($existingProcess) {
            return "Error: Process name already exists.";
        }

        $query = "INSERT INTO processes (name) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $this->LogUserActivity($_SESSION['id'], 'process creation', "Created process: $name", 'yellow');
            return "Success: Process created.";
        }
        return "Error: Failed to create process.";
    }

    public function DeleteProcess($processID) {
        if ($this->IsProcessLockedByOrders($processID)) {
            return "Error: Cannot delete process because it is used in a service with active orders.";
        }

        try {
            $this->pdo->beginTransaction();

            $query = "DELETE FROM roleProcessTasks WHERE processID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $processID, PDO::PARAM_INT);
            $stmt->execute();

            $query = "DELETE FROM processes WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $processID, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();

            $processName = $this->GetProcessById($processID) ?? "Unknown";
            $this->LogUserActivity($_SESSION['id'], 'process deletion', "Deleted process: $processName", 'red');
            return "Success: Process deleted.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed to delete process.";
        }
    }

    public function UpdateProcess($processID, $minAssign, $maxAssign, $hasGroupChatAccess, $designAccess, $variableListAccess) {
        if ($minAssign < 1 || $maxAssign < $minAssign) {
            return "Error: Invalid assignment values.";
        }

        // If the process belongs to a service with orders, block changes to design/variable‑list access
        if ($this->IsProcessLockedByOrders($processID)) {
            $current = $this->pdo->prepare("SELECT designAccess, variableListAccess FROM processes WHERE id = :id");
            $current->bindParam(':id', $processID, PDO::PARAM_INT);
            $current->execute();
            $currentRow = $current->fetch(PDO::FETCH_ASSOC);
            if ($currentRow) {
                if (
                    $currentRow['designAccess'] !== $designAccess ||
                    $currentRow['variableListAccess'] !== $variableListAccess
                ) {
                    return "Error: Cannot modify design or variable‑list access while the process is used in a service with active orders.";
                }
            }
        }
        $query = "
            UPDATE processes
            SET minAssignDefault = :minAssign,
                maxAssignDefault = :maxAssign,
                hasGCAccess = :hasGCAccess,
                designAccess = :designAccess,
                variableListAccess = :variableListAccess
            WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $processID, PDO::PARAM_INT);
        $stmt->bindParam(':minAssign', $minAssign, PDO::PARAM_INT);
        $stmt->bindParam(':maxAssign', $maxAssign, PDO::PARAM_INT);
        $stmt->bindParam(':hasGCAccess', $hasGroupChatAccess, PDO::PARAM_INT);
        $stmt->bindParam(':designAccess', $designAccess);
        $stmt->bindParam(':variableListAccess', $variableListAccess);

        if ($stmt->execute()) {
            $processName = $this->GetProcessById($processID) ?? "Unknown";
            $this->LogUserActivity($_SESSION['id'], 'process update', "Updated process: $processName", 'yellow');
            return "Success: Process updated.";
        }
        return "Error: Failed to update process.";
    }

    public function UpdateServiceProcesses($serviceID, $processIDs) {
        if ($this->GetServiceOrderCount($serviceID) > 0) {
            return "Error: Cannot modify service with active orders.";
        }

        $service = $this->GetServiceById($serviceID);
        if (!$service) {
            return "Error: Service not found.";
        }
        if ($service['isActive']) {
            return "Error: Cannot modify active service.";
        }

        try {
            $this->pdo->beginTransaction();

            // Delete existing associations
            $query = "DELETE FROM serviceProcess WHERE serviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
            $stmt->execute();

            // Insert new associations
            if (!empty($processIDs)) {
                $query = "INSERT INTO serviceProcess (serviceID, processesID, phase) VALUES (:serviceID, :processID, :phase)";
                $stmt = $this->pdo->prepare($query);

                foreach ($processIDs as $index => $processID) {
                    $phase = $index + 1;
                    $stmt->bindParam(':serviceID', $serviceID, PDO::PARAM_INT);
                    $stmt->bindParam(':processID', $processID, PDO::PARAM_INT);
                    $stmt->bindParam(':phase', $phase, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }

            $this->pdo->commit();
            $this->LogUserActivity($_SESSION['id'], 'service modification', "Updated processes for service: {$service['name']}", 'yellow');
            return "Success: Service processes updated.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed to update service processes.";
        }
    }

    // ========== SUBSERVICE GETTERS ==========

    public function GetAllSubservices() {
        $query = "SELECT * FROM subservices ORDER BY isActive DESC, name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetSubservicesByServiceId($serviceID) {
        $query = "SELECT id, name, isActive, description, pricePerUnit FROM subservices WHERE serviceID = :id ORDER BY isActive DESC, name ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $serviceID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function GetSubserviceById($subserviceID) {
        $query = "SELECT serviceID, name, isActive, description, pricePerUnit FROM subservices WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function GetSubserviceByName($name, $serviceID) {
        $query = "SELECT id FROM subservices WHERE name = :name AND serviceID = :serviceID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':serviceID', $serviceID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function GetSubserviceOrderCount($subserviceID) {
        $query = "SELECT COUNT(id) AS orderCount FROM orders WHERE subserviceID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)($result['orderCount'] ?? 0);
    }

    public function GetAllSubservicesOrderCountMapped() {
        $query = "SELECT subserviceID, COUNT(id) AS orderCount FROM orders GROUP BY subserviceID";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($result as $row) {
            $map[$row['subserviceID']] = (int)$row['orderCount'];
        }
        return $map;
    }

    // ========== SUBSERVICE OPERATIONS ==========

    public function CreateSubservice($name, $serviceID) {
        if (empty(trim($name))) {
            return "Error: Subservice name cannot be empty.";
        }

        $service = $this->GetServiceById($serviceID);
        if (!$service) {
            return "Error: Service not found.";
        }

        $existingSubservice = $this->GetSubserviceByName($name, $serviceID);
        if ($existingSubservice) {
            return "Error: Subservice name already exists.";
        }

        $query = "INSERT INTO subservices (serviceID, name, pricePerUnit) VALUES (:serviceID, :name, 1)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':serviceID', $serviceID, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);

        if ($stmt->execute()) {
            $this->LogUserActivity($_SESSION['id'], 'subservice creation', "Created subservice: $name under {$service['name']}", 'yellow');
            return "Success: Subservice created.";
        }
        return "Error: Failed to create subservice.";
    }

    public function DeleteSubservice($subserviceID) {
        $subservice = $this->GetSubserviceById($subserviceID);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        if ($this->GetSubserviceOrderCount($subserviceID) > 0) {
            return "Error: Cannot delete subservice with active orders.";
        }
        if ($subservice['isActive']) {
            return "Error: Cannot delete active subservice.";
        }

        try {
            $this->pdo->beginTransaction();

            $this->DeleteSubserviceImages($subserviceID);

            $query = "DELETE FROM subservices WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
            $stmt->execute();

            $this->pdo->commit();
            $service = $this->GetServiceById($subservice['serviceID']);
            $this->LogUserActivity($_SESSION['id'], 'subservice deletion', "Deleted subservice: {$subservice['name']} from {$service['name']}", 'red');
            return "Success: Subservice deleted.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed to delete subservice.";
        }
    }

    public function ToggleSubserviceStatus($subserviceID) {
        $subservice = $this->GetSubserviceById($subserviceID);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        $serviceSubservices = $this->GetSubservicesByServiceId($subservice['serviceID']);
        if ($subservice['isActive'] && count($serviceSubservices) === 1) {
            return "Error: Cannot disable last active subservice.";
        }

        $query = "UPDATE subservices SET isActive = NOT isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $service = $this->GetServiceById($subservice['serviceID']);
            $action = $subservice['isActive'] ? 'deactivation' : 'activation';
            $color = $subservice['isActive'] ? 'red' : 'yellow';
            $this->LogUserActivity($_SESSION['id'], "subservice $action", "Updated {$subservice['name']} status under {$service['name']}", $color);
            return "Success: Subservice status updated.";
        }
        return "Error: Failed to update subservice status.";
    }

    public function UpdateSubserviceInfo($subserviceID, $pricePerUnit, $description) {
        $subservice = $this->GetSubserviceById($subserviceID);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        $query = "UPDATE subservices SET pricePerUnit = :price, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
        $stmt->bindParam(':price', $pricePerUnit, PDO::PARAM_INT);
        $stmt->bindParam(':description', $description);

        if ($stmt->execute()) {
            $service = $this->GetServiceById($subservice['serviceID']);
            $this->LogUserActivity($_SESSION['id'], 'subservice update', "Updated {$subservice['name']} info under {$service['name']}", 'yellow');
            return "Success: Subservice updated.";
        }
        return "Error: Failed to update subservice.";
    }

    // ========== SUBSERVICE IMAGES ==========

    public function GetAllSubserviceImages() {
        $query = "SELECT * FROM subserviceImages ORDER BY subserviceID ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function UploadSubserviceImages($subserviceID, $files) {
        if (!is_array($files) || !isset($files['name'], $files['tmp_name'])) {
            return "Error: Invalid file input.";
        }

        $subservice = $this->GetSubserviceById($subserviceID);
        if (!$subservice) {
            return "Error: Subservice not found.";
        }

        if (!is_dir($this->storageDir)) {
            mkdir($this->storageDir, 0755, true);
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $uploadedCount = 0;
        $failedFiles = [];

        for ($i = 0; $i < count($files['name']); $i++) {
            $fileName = $files['name'][$i];
            $tmpPath = $files['tmp_name'][$i];

            if (empty($fileName) || !is_uploaded_file($tmpPath)) {
                $failedFiles[] = $fileName;
                continue;
            }

            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($extension, $allowedExtensions)) {
                $failedFiles[] = $fileName;
                continue;
            }

            $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '_' . $i . '.' . $extension;
            $targetPath = $this->storageDir . $newFileName;

            if (move_uploaded_file($tmpPath, $targetPath)) {
                try {
                    $query = "INSERT INTO subserviceImages (subserviceID, imageName) VALUES (:subserviceID, :imageName)";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->bindParam(':subserviceID', $subserviceID, PDO::PARAM_INT);
                    $stmt->bindParam(':imageName', $newFileName);
                    $stmt->execute();
                    $uploadedCount++;
                } catch (PDOException $e) {
                    unlink($targetPath);
                    $failedFiles[] = $fileName;
                }
            } else {
                $failedFiles[] = $fileName;
            }
        }

        $service = $this->GetServiceById($subservice['serviceID']);
        $message = "Uploaded $uploadedCount image(s) to {$subservice['name']}";
        if (!empty($failedFiles)) {
            $message .= " (" . count($failedFiles) . " failed)";
        }

        if ($uploadedCount > 0) {
            $this->LogUserActivity($_SESSION['id'], 'subservice update', "$message under {$service['name']}", 'yellow');
            return $uploadedCount === count($files['name'])
                ? "Success: All images uploaded."
                : "Warning: Some images failed to upload.";
        }

        return "Error: No images uploaded.";
    }

    public function DeleteSubserviceImage($imageID) {
        $query = "SELECT subserviceID, imageName FROM subserviceImages WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $imageID, PDO::PARAM_INT);
        $stmt->execute();
        $image = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$image) {
            return "Error: Image not found.";
        }

        try {
            $filePath = $this->storageDir . $image['imageName'];
            if (file_exists($filePath) && !unlink($filePath)) {
                return "Error: Failed to delete image file.";
            }

            $query = "DELETE FROM subserviceImages WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $imageID, PDO::PARAM_INT);
            $stmt->execute();

            $subservice = $this->GetSubserviceById($image['subserviceID']);
            $service = $this->GetServiceById($subservice['serviceID']);
            $this->LogUserActivity($_SESSION['id'], 'subservice update', "Deleted image from {$subservice['name']} under {$service['name']}", 'red');
            return "Success: Image deleted.";
        } catch (PDOException $e) {
            return "Error: Failed to delete image.";
        }
    }

    private function DeleteSubserviceImages($subserviceID) {
        $query = "SELECT imageName FROM subserviceImages WHERE subserviceID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($images as $image) {
            $filePath = $this->storageDir . $image['imageName'];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $query = "DELETE FROM subserviceImages WHERE subserviceID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $subserviceID, PDO::PARAM_INT);
        $stmt->execute();
    }

    // ========== UTILITY ==========

    private function LogUserActivity($userID, $head, $log, $color) {
        $query = "INSERT INTO userActivityLog (userID, head, log, color) VALUES (:userID, :head, :log, :color)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':userID' => $userID,
            ':head'   => strtolower($head),
            ':log'    => $log,
            ':color'  => strtolower($color)
        ]);
    }
}
