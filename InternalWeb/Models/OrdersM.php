<?php
class OrdersM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrders() {
        $query = "SELECT
                      orders.id,
                      services.NAME AS serviceName,
                      subservices.NAME AS subserviceName,
                      orders.priceTotal,
                      orders.customerName,
                      orders.createdAt,
                      orders.deadlineAt,
                      orders.messengerGCLink
                  FROM orders
                  JOIN subservices ON orders.subserviceID = subservices.id
                  JOIN services ON subservices.serviceID = services.id
                  ORDER BY orders.id ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertOrder($subserviceID, $customerName, $messengerGCLink, $deadlineAt, $priceTotal, $groupDescriptions, $groupQuantities, $orderProcess) {
        $query = "INSERT INTO orders (subserviceID, customerName, messengerGCLink, priceTotal, deadlineAt) VALUES
            (:subserviceID, :customerName, :messengerGCLink, :priceTotal, :deadlineAt)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':subserviceID', $subserviceID);
        $stmt->bindParam(':customerName', $customerName);
        $stmt->bindParam(':messengerGCLink', $messengerGCLink);
        $stmt->bindParam(':priceTotal', $priceTotal);
        $stmt->bindParam(':deadlineAt', $deadlineAt);
        $stmt->execute();

        $orderID = $this->pdo->lastInsertId();

        $query = "INSERT INTO orderGroups (orderID, description, quantity) VALUES (:orderID, :description, :quantity)";
        $stmt = $this->pdo->prepare($query);

        for ($i = 0; $i < count($groupDescriptions); $i++) {
            $stmt->execute([
                ':orderID' => $orderID,
                ':description' => $groupDescriptions[$i],
                ':quantity' => $groupQuantities[$i],
            ]);
        }

        $query = "INSERT INTO orderProcess (orderID, phase, status) VALUES (:orderID, :phase, :status)";
        $stmt = $this->pdo->prepare($query);

        for ($i = 0; $i < count($orderProcess); $i++) {
            $stmt->execute([
                ':orderID' => $orderID,
                ':phase' => $i + 1,
                ':status' => $orderProcess[$i],
            ]);
        }
    }

    public function removeOrder($id) {
        $query = "DELETE FROM orderGroups WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM orderProcess WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllOrderProcesses() {
        $query = "SELECT
                      orderProcess.orderID,
                      processes.name AS processName,
                      orderProcess.phase,
                      orderProcess.status
                  FROM orderProcess
                  JOIN orders ON orderProcess.orderID = orders.id
                  JOIN subservices ON orders.subserviceID = subservices.id
                  JOIN serviceProcess
                      ON subservices.serviceID = serviceProcess.serviceID
                      AND orderProcess.phase = serviceProcess.phase
                  JOIN processes ON serviceProcess.processesID = processes.id
                  ORDER BY orderProcess.orderID, orderProcess.phase";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDeadline($id, $deadlineAt) {
        $query = "UPDATE orders SET deadlineAt = :deadlineAt WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':deadlineAt', $deadlineAt);
        return $stmt->execute();
    }
}
