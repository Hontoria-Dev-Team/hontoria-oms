<?php
class ServicesM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getServices() {
        $query = "SELECT id, name, isActive, description FROM services";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getService($id) {
        $query = "SELECT name, description FROM services WHERE id = :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
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

    public function getProcess($serviceID) {
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
}
