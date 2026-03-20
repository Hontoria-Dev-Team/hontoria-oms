<?php
class ServicesM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getServices() {
        $query = "SELECT id, name, isActive, description FROM services ORDER BY isActive DESC, name ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceByID($id) {
        $query = "SELECT name, description FROM services WHERE id = :id";

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
        $service = $this->getServiceByName($name);

        if ($service) {
            return false;
        }

        $query = "INSERT INTO services (name) VALUES (:name);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        return $stmt->execute();
    }

    public function removeService($id) {
        $query = "DELETE FROM services WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
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
        $query = "SELECT * FROM subservices";

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
        return $stmt->execute();
    }

    public function updateSubserviceInfo($id, $pricePerUnit, $description) {
        $query = "UPDATE subservices SET pricePerUnit = :pricePerUnit, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':pricePerUnit', $pricePerUnit);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    public function removeSubservice($id) {
        $query = "DELETE FROM subservices WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function insertSubservice($name, $serviceID) {
        $user = $this->getSingleSubserviceByName($name, $serviceID);

        if ($user) {
            return false;
        }

        $query = "INSERT INTO subservices (name, serviceID, pricePerUnit) VALUES
            (:name, :serviceID, 0);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':serviceID', $serviceID);
        return $stmt->execute();
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
        $query = "SELECT id, name FROM processes ORDER BY name";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
