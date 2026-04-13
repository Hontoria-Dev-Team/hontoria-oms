<?php
class ServicesM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getServices() {
        $query = "SELECT * FROM services ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceByID($id) {
        $query = "SELECT name, hasDesign, hasVariableList FROM services WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getServiceByName($name) {
        $query = "SELECT id, description FROM services WHERE name = :name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

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

    public function toggleServiceHasDesign($id) {
        $query = "UPDATE services SET hasDesign = !hasDesign WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function toggleServiceHasVariableList($id) {
        $query = "UPDATE services SET hasVariableList = !hasVariableList WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteService($id) {
        try {
            $this->pdo->beginTransaction();

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
                $storageDir = __DIR__ . '/../../Storage/SubserviceImages/';
                foreach ($images as $image) {
                    $filePath = $storageDir . $image['imageName'];
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

            // Delete serviceProcess
            $query = "DELETE FROM serviceProcess WHERE serviceID = ?";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([$id]);

            // Delete service
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

    public function getAllSubservices() {
        $query = "SELECT * FROM subservices ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

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

    public function getAllServiceProcesses() {
        $query = "SELECT serviceProcess.serviceID, serviceProcess.phase, processes.name, processes.id
                  FROM serviceProcess
                  JOIN processes ON serviceProcess.processesID = processes.id
                  ORDER BY serviceProcess.serviceID ASC, serviceProcess.phase ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSingleProcessByName($name) {
        $query = "SELECT id FROM processes WHERE name = :name";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_COLUMN);
    }

    public function updateServiceStatus($id) {
        $query = "UPDATE services SET isActive = !isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateSubserviceStatus($id) {
        $query = "UPDATE subservices SET isActive = !isActive WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Updated subservice status.";
    }

    public function updateSubserviceInfo($id, $pricePerUnit, $description) {
        $query = "UPDATE subservices SET pricePerUnit = :pricePerUnit, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':pricePerUnit', $pricePerUnit);
        $stmt->bindParam(':description', $description);
        $stmt->execute();

        return "Success: Updated subservice.";
    }

    public function deleteSubservice($id) {
        try {
            $this->pdo->beginTransaction();

            // Get all images for this subservice
            $query = "SELECT imageName FROM subserviceImages WHERE subserviceID = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':id' => $id]);
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Delete image files from storage
            $storageDir = __DIR__ . '/../../Storage/SubserviceImages/';
            foreach ($images as $image) {
                $filePath = $storageDir . $image['imageName'];
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

    public function clearServiceProcess($id) {
        $query = "DELETE FROM serviceProcess WHERE serviceID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function updateServiceProcess($id, $processes) {
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

    public function getAllProcesses() {
        $query = "SELECT * FROM processes ORDER BY name";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProcess($id) {
        $serviceProcesses = $this->getAllServiceProcesses();
        $canDelete = true;

        foreach ($serviceProcesses as $serviceProcess) {
            if ((int)$serviceProcess['id'] === $id) {
                $canDelete = false;
                break;
            }
        }

        if (!$canDelete) {
            return "Error: Cannot delete this process because it is in use in one or more services";
        }

        $query = "DELETE FROM roleProcessTasks WHERE processID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM processes WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return "Success: Deleted process.";
    }

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

    public function getAllServicesOrderCountMapped() {
        $map = [];

        foreach ($this->getAllServicesOrderCount() as $item) {
            $map[$item['serviceID']] = $item['orderCount'];
        }

        return $map;
    }

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

    public function getAllSubserviceImages() {
        $query = "SELECT * FROM subserviceImages";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertSubserviceImages($subserviceID, $images) {
        $storageDir = __DIR__ . '/../../Storage/SubserviceImages/';

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        for ($i = 0; $i < count($images['name']); $i++) {
            $fileExtension = strtolower(pathinfo($images['name'][$i], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowed)) {
                return "Error: Invalid file format.";
            }
        }

        $uploadedFiles = [];

        for ($i = 0; $i < count($images['name']); $i++) {
            $fileExtension = strtolower(pathinfo($images['name'][$i], PATHINFO_EXTENSION));
            $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '_' . $i . '.' . $fileExtension;
            $targetPath = $storageDir . $newFileName;
            $tmpName = $images['tmp_name'][$i];

            if (move_uploaded_file($tmpName, $targetPath)) {
                $uploadedFiles[] = $newFileName;
            } else {
                foreach ($uploadedFiles as $uploadedFile) {
                    $filePath = $storageDir . $uploadedFile;
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }
                }
                return "Error: Upload failed.";
            }
        }

        try {
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
            foreach ($uploadedFiles as $uploadedFile) {
                $filePath = $storageDir . $uploadedFile;
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }
            return "Error: Failed. " . $e->getMessage();
        }
    }

    public function deleteSubserviceImage($id) {
        $storageDir = __DIR__ . '/../../Storage/SubserviceImages/';

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
            $filePath = $storageDir . $image['imageName'];
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
