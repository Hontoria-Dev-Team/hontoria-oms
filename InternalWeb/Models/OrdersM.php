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

    public function getAllOrderGroups() {
        $query = "SELECT orderID, description, quantity FROM orderGroups";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

        $placeholders = implode(',', array_fill(0, count($processIDs), '?'));

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
                processes.designAccess,
                userCheck.status AS taskStatus,
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
                AND userCheck.userID = ?
            WHERE processes.id IN ($placeholders)
            AND orderProcess.status IN ('active', 'partially complete')
            GROUP BY orderProcess.id, orderProcess.orderID, processes.id
            ORDER BY orderProcess.orderID, orderProcess.phase
        ";

        $params = array_merge([$userID], $processIDs);

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);

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
                users.lastName,
                userProcessTasks.status
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
        $stmt->execute();

        return $this->updateOrderProcess($orderProcessID);
    }

    public function findSingleOrderDesignByID($orderID) {
        $query = "SELECT imageName FROM orderDesigns WHERE orderID = :orderID LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function insertOrderDesign($orderID, $imageFile) {
        $storageDir = __DIR__ . '/../../Storage/Designs/';

        $existingDesign = $this->findSingleOrderDesignByID($orderID);

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExtension, $allowed)) {
            return false;
        }

        $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '.' . $fileExtension;
        $targetPath = $storageDir . $newFileName;

        if (move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
            try {
                if ($existingDesign) {
                    $query = "UPDATE orderDesigns SET imageName = :imageName WHERE orderID = :orderID";
                } else {
                    $query = "INSERT INTO orderDesigns (orderID, imageName) VALUES (:orderID, :imageName)";
                }

                $stmt = $this->pdo->prepare($query);
                $success = $stmt->execute([
                    ':orderID'   => $orderID,
                    ':imageName' => $newFileName
                ]);

                if ($success && $existingDesign) {
                    $oldFilePath = $storageDir . $existingDesign['imageName'];
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                return $success;
            } catch (PDOException $e) {
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                error_log("Database error: " . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    public function getAllOrderDesigns() {
        $storageDir = __DIR__ . '/../../Storage/Designs/';

        $query = "SELECT orderID, imageName, approved FROM orderDesigns";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $designs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $results = [];

        foreach ($designs as $design) {
            $filePath = $storageDir . $design['imageName'];

            if (file_exists($filePath)) {
                $fileData = file_get_contents($filePath);

                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->file($filePath);

                $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($fileData);

                $results[] = [
                    'orderID' => $design['orderID'],
                    'image' => $base64Image,
                    'approved' => $design['approved']
                ];
            }
        }

        return $results;
    }

    public function getOrderProcessTaskStatus($orderProcessID) {
        $query = "
            SELECT
                CASE
                    WHEN COUNT(*) = SUM(status = 'complete') THEN 'complete'
                    WHEN COUNT(*) = SUM(status = 'pending') THEN 'pending'
                    ELSE 'partially complete'
                END AS finalStatus
            FROM userProcessTasks
            WHERE orderProcessID = :orderProcessID
            GROUP BY orderProcessID
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function updateOrderProcess($orderProcessID) {
        $status = $this->getOrderProcessTaskStatus($orderProcessID);

        if ($status === 'pending') return;

        $query = "UPDATE orderProcess SET status = :status WHERE id = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();

        $query = "SELECT phase, orderID FROM orderProcess WHERE id = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();

        $orderProcess = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextProcessPhase = $orderProcess['phase'] + 1;
        $orderID = $orderProcess['orderID'];

        $query = "SELECT id FROM orderProcess WHERE phase = :phase AND orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':phase', $nextProcessPhase);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();

        $result = $stmt->fetchColumn();

        if ($result === false) return;

        $query = "UPDATE orderProcess SET status = 'active' WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $result);
        $stmt->execute();

        if ($status !== 'complete') return;

        $query = "DELETE FROM userProcessTasks WHERE orderProcessID = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        return $stmt->execute();
    }
}
