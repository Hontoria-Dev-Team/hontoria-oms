<?php
class OrdersM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllOrders() {
        $query = "
            SELECT
                orders.id,
                services.name AS serviceName,
                subservices.name AS subserviceName,
                orders.priceTotal,
                orders.customerName,
                orders.createdAt,
                orders.deadlineAt,
                orders.messengerGCLink,
                CASE
                    WHEN EXISTS (
                        SELECT 1
                        FROM orderProcess op
                        JOIN userProcessTasks upt ON upt.orderProcessID = op.id
                        WHERE op.orderID = orders.id
                    ) THEN 'Active'
                    ELSE 'Idle'
                END AS status
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            ORDER BY orders.id ASC
        ";

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

        $query = "INSERT INTO orderProcess (orderID, phase, minAssign, maxAssign, status) VALUES (:orderID, :phase, :minAssign, :maxAssign, :status)";
        $stmt = $this->pdo->prepare($query);

        for ($i = 0; $i < count($orderProcess); $i++) {
            $stmt->execute([
                ':orderID' => $orderID,
                ':phase' => $i + 1,
                ':minAssign' =>  $orderProcess[$i]['minAssign'],
                ':maxAssign' => $orderProcess[$i]['maxAssign'],
                ':status' => $orderProcess[$i]['status'],
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
                      orderProcess.minAssign,
                      orderProcess.maxAssign,
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

    public function getAvailableOrderTasks($userID, $processTasks) {
        $processIDs = array_column($processTasks, 'processID');

        if (empty($processIDs)) {
            return [];
        }

        $query = "
            SELECT
                orderProcess.id,
                orderProcess.orderID,
                processes.id AS processID,
                services.name AS serviceName,
                subservices.name AS subserviceName,
                orders.customerName,
                orderProcess.minAssign,
                orderProcess.maxAssign,
                orders.deadlineAt,
                orders.messengerGCLink,
                processes.name AS processName,
                userProcessTasks.status AS taskStatus,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum,
                CASE WHEN userCheck.userID IS NOT NULL THEN TRUE ELSE FALSE END AS isAssigned,
                CASE WHEN COUNT(userProcessTasks.orderProcessID) >= orderProcess.maxAssign THEN TRUE ELSE FALSE END AS isFull
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            LEFT JOIN userProcessTasks ON orderProcess.id = userProcessTasks.orderProcessID
            LEFT JOIN userProcessTasks userCheck ON orderProcess.id = userCheck.orderProcessID
                AND userCheck.userID = :userID
            WHERE processes.id IN (:processIDs)
            AND orderProcess.status IN ('active', 'partially complete')
            GROUP BY orderProcess.id, orderProcess.orderID, processes.id
            ORDER BY orderProcess.orderID, orderProcess.phase
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':userID' => $userID,
            ':processIDs' => implode(',', $processIDs)
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertUserProcessTask($userID, $orderProcessID) {
        $query = "INSERT INTO userProcessTasks (userID, orderProcessID) VALUES (:userID, :orderProcessID)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        return $stmt->execute();
    }

    public function getAllTaskAssigneeList() {
        $query = "
            SELECT
                userProcessTasks.userID,
                userProcessTasks.orderProcessID,
                users.firstName,
                users.middleName,
                users.lastName
            FROM userProcessTasks
            JOIN users ON userProcessTasks.userID = users.id
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateUserProcessTaskStatus($userID, $orderProcessID, $status) {
        $query = "UPDATE userProcessTasks SET status = :status WHERE userID = :userID AND orderProcessID = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        return $stmt->execute();
    }
}
