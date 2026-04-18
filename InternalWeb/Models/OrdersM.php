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
                    WHEN NOT EXISTS (
                        SELECT 1 FROM orderProcess
                        WHERE orderProcess.orderID = orders.id
                        AND orderProcess.status != 'complete'
                    ) THEN 'For Verification'
                    WHEN NOT EXISTS (
                        SELECT 1 FROM userProcessTasks
                        JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
                        WHERE orderProcess.orderID = orders.id
                    ) THEN 'Idle'
                    ELSE 'Active'
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

    public function getOrderByID($id) {
        $query = "
            SELECT
                services.name AS serviceName,
                subservices.name AS subserviceName,
                orders.priceTotal,
                orders.customerName,
                orders.createdAt,
                orders.deadlineAt,
                orders.messengerGCLink,
                CASE
                    WHEN NOT EXISTS (
                        SELECT 1 FROM orderProcess
                        WHERE orderProcess.orderID = orders.id
                        AND orderProcess.status != 'complete'
                    ) THEN 'For Verification'
                    WHEN NOT EXISTS (
                        SELECT 1 FROM userProcessTasks
                        JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
                        WHERE orderProcess.orderID = orders.id
                    ) THEN 'Idle'
                    ELSE 'Active'
                END AS status
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            WHERE orders.id = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderGroupsByID($id) {
        $query = "SELECT * FROM orderGroups WHERE orderID = :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderDesignByID($id) {
        $query = "SELECT * FROM orderDesigns WHERE orderID =  :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderProcessByID($id) {
        $query = "
            SELECT
                orderProcess.id,
                orderProcess.orderID,
                processes.id AS processID,
                processes.name AS processName,
                orderProcess.phase,
                orderProcess.minAssign,
                orderProcess.maxAssign,
                orderProcess.status,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            LEFT JOIN userProcessTasks
                ON orderProcess.id = userProcessTasks.orderProcessID
            WHERE orderProcess.orderID = :id
            GROUP BY
                orderProcess.id,
                orderProcess.orderID
            ORDER BY orderProcess.phase
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrderProcesseeByID($id) {
        $query = "
            SELECT
                orderProcess.orderID,
                processes.id AS processID,
                processes.name AS processName,
                orderProcess.phase,
                orderProcess.minAssign,
                orderProcess.maxAssign,
                orderProcess.status,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            LEFT JOIN userProcessTasks
                ON orderProcess.id = userProcessTasks.orderProcessID
            WHERE orderProcess.id = :id
            GROUP BY
                orderProcess.id,
                orderProcess.orderID
            ORDER BY orderProcess.phase
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAssignedUsersToOrderByID($id) {
        $query = "
            SELECT
                users.id,
                users.firstName,
                users.middleName,
                users.lastName,
                processes.name AS processName,
                orderProcess.phase,
                userProcessTasks.assignedAt,
                userProcessTasks.completedAt
            FROM userProcessTasks
            JOIN users
                ON userProcessTasks.userID = users.id
            JOIN orderProcess
                ON userProcessTasks.orderProcessID = orderProcess.id
            JOIN orders
                ON orderProcess.orderID = orders.id
            JOIN subservices
                ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes
                ON serviceProcess.processesID = processes.id
            WHERE orders.id = :id
            ORDER BY PHASE ASC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
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

        $query = "DELETE FROM orderDesigns WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deleteAllAssignmentsFromOrderByID($id) {
        $query = "
            DELETE userProcessTasks
            FROM userProcessTasks
            JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
            WHERE orderProcess.orderID = :id
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function getAllOrderProcesses() {
        $query = "
            SELECT
                orderProcess.id,
                orderProcess.orderID,
                processes.id AS processID,
                processes.name AS processName,
                orderProcess.phase,
                orderProcess.minAssign,
                orderProcess.maxAssign,
                orderProcess.status,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            LEFT JOIN userProcessTasks
                ON orderProcess.id = userProcessTasks.orderProcessID
            GROUP BY
                orderProcess.id,
                orderProcess.orderID
            ORDER BY orderProcess.orderID, orderProcess.phase
        ";

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
        $process = $this->getOrderProcesseeByID($orderProcessID);

        if ($_SESSION['id'] == $userID) {
            $this->insertUserActivityLog(
                $userID,
                'task assignment',
                'Self-Assigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'yellow'
            );
        } else {
            $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $userID);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $middleInitial = $user['middleName'] ? substr($user['middleName'], 0, 1) . '. ' : '';
            $userFullName = $user['firstName'] . ' ' . $middleInitial . $user['lastName'];

            $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();

            $_user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_middleInitial = $_user['middleName'] ? substr($_user['middleName'], 0, 1) . '. ' : '';
            $_userFullName = $_user['firstName'] . ' ' . $_middleInitial . $_user['lastName'];

            $this->insertUserActivityLog(
                $userID,
                'task assignment',
                'Assigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . ' by ' . $_userFullName . '.',
                'yellow'
            );

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'task assigning',
                'Assigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'],
                'yellow'
            );
        }

        $query = "INSERT INTO userProcessTasks (userID, orderProcessID) VALUES (:userID, :orderProcessID)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        return $stmt->execute();
    }

    public function removeUserProcessTask($userID, $orderProcessID) {
        $process = $this->getOrderProcesseeByID($orderProcessID);

        if ($_SESSION['id'] == $userID) {
            $this->insertUserActivityLog(
                $userID,
                'task unassignment',
                'Self-Unassigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'red'
            );
        } else {
            $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $userID);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $middleInitial = $user['middleName'] ? substr($user['middleName'], 0, 1) . '. ' : '';
            $userFullName = $user['firstName'] . ' ' . $middleInitial . $user['lastName'];

            $query = "SELECT firstName, middleName, lastName FROM users WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $_SESSION['id']);
            $stmt->execute();

            $_user = $stmt->fetch(PDO::FETCH_ASSOC);
            $_middleInitial = $_user['middleName'] ? substr($_user['middleName'], 0, 1) . '. ' : '';
            $_userFullName = $_user['firstName'] . ' ' . $_middleInitial . $_user['lastName'];

            $this->insertUserActivityLog(
                $userID,
                'task unassignment',
                'Unassigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . ' by ' . $_userFullName . '.',
                'red'
            );

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'task unassigning',
                'Unassigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'],
                'red'
            );
        }

        $query = "DELETE FROM userProcessTasks WHERE userID = :userID AND orderProcessID = :orderProcessID";
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
        $query = strtolower($status) == 'complete' ?
            "UPDATE userProcessTasks SET status = :status, completedAt = NOW() WHERE userID = :userID AND orderProcessID = :orderProcessID" :
            "UPDATE userProcessTasks SET status = :status WHERE userID = :userID AND orderProcessID = :orderProcessID";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':status' => $status,
            ':userID' => $userID,
            ':orderProcessID' => $orderProcessID
        ]);

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

        if ($result) {
            $query = "UPDATE orderProcess SET status = 'active' WHERE id = :id";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $result);
            $stmt->execute();
        }
    }

    public function getAllOrdersAssigneeCount() {
        $query = "
            SELECT
                orderProcess.orderID,
                COUNT(DISTINCT userProcessTasks.userID) AS assigneeCount
            FROM userProcessTasks
            JOIN orderProcess ON userProcessTasks.orderProcessID = orderProcess.id
            GROUP BY orderProcess.orderID
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function archiveOrder($id, $isCompleted) {
        try {
            $this->pdo->beginTransaction();

            $order = $this->getOrderByID($id);

            if ($order['status'] !== 'For Verification' && $isCompleted) {
                $this->pdo->rollBack();
                return "Error: This order is not yet ready to be archived. Current status: " . $order['status'];
            }

            // Log Verification
            if ($isCompleted) {
                $this->insertUserActivityLog($_SESSION['id'], 'order verification', 'Verified Order #' . $id . '.', 'green');
            } else {
                $this->insertUserActivityLog($_SESSION['id'], 'order deletion', 'Deleted Order #' . $id . '.', 'red');
            }

            // Insert into orderArchive
            $query = "
                INSERT INTO orderArchive
                    (id, serviceName, subserviceName, customerName, messengerGCLink, priceTotal, createdAt, deadlineAt, isCompleted)
                VALUES
                    (:id, :serviceName, :subserviceName, :customerName, :messengerGCLink, :priceTotal, :createdAt, :deadlineAt, :isCompleted)
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':id' => $id,
                ':serviceName' => $order['serviceName'],
                ':subserviceName' => $order['subserviceName'],
                ':customerName' => $order['customerName'],
                ':messengerGCLink' => $order['messengerGCLink'],
                ':priceTotal' => $order['priceTotal'],
                ':createdAt' => $order['createdAt'],
                ':deadlineAt' => $order['deadlineAt'],
                ':isCompleted' => $isCompleted ? 1 : 0
            ]);

            // Archive design if exists
            $design = $this->getOrderDesignByID($id);
            if ($design) {
                $query = "INSERT INTO orderDesignArchive (orderArchiveID, imageName) VALUES (:orderArchiveID, :imageName)";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([
                    ':orderArchiveID' => $id,
                    ':imageName' => $design['imageName']
                ]);
            }

            // Archive groups
            $groups = $this->getOrderGroupsByID($id);
            if (!empty($groups)) {
                $query = "INSERT INTO orderGroupArchive (orderArchiveID, description, units) VALUES (:orderArchiveID, :description, :units)";
                $stmt = $this->pdo->prepare($query);
                foreach ($groups as $group) {
                    $stmt->execute([
                        ':orderArchiveID' => $id,
                        ':description' => $group['description'],
                        ':units' => $group['quantity']
                    ]);
                }
            }

            // Archive task assignments
            $assignees = $this->getAssignedUsersToOrderByID($id);
            if (!empty($assignees)) {
                $query = "
                    INSERT INTO orderTasksAssignmentArchive
                        (orderArchiveID, userFirstName, userMiddleName, userLastName, processName, processPhase, assignedAt)
                    VALUES
                        (:orderArchiveID, :userFirstName, :userMiddleName, :userLastName, :processName, :processPhase, :assignedAt)
                ";
                $stmt = $this->pdo->prepare($query);
                foreach ($assignees as $assignee) {
                    $stmt->execute([
                        ':orderArchiveID' => $id,
                        ':userFirstName' => $assignee['firstName'],
                        ':userMiddleName' => $assignee['middleName'] ?? null,
                        ':userLastName' => $assignee['lastName'],
                        ':processName' => $assignee['processName'],
                        ':processPhase' => $assignee['phase'],
                        ':assignedAt' => $assignee['assignedAt']
                    ]);

                    $durationInMinutes = abs(strtotime($assignee['completedAt']) - strtotime($assignee['assignedAt'])) / 60;

                    // Log Assignees Completing tasks
                    $this->insertUserActivityLog(
                        $assignee['id'],
                        'task completion',
                        'Completed the ' . $assignee['processName'] . ' Order #' . $id . ' task in ' . number_format($durationInMinutes, 2) . ' minutes.',
                        'green'
                    );

                    // Update Assignees Stats
                    $query = "
                        UPDATE userStats
                        SET
                            tasksCompleted = tasksCompleted + 1,
                            tasksCompletedDuration = tasksCompletedDuration + :taskCompletedDuration
                        WHERE userID = :id
                    ";
                    $_stmt = $this->pdo->prepare($query);
                    $_stmt->execute([
                        ':id' => $assignee['id'],
                        ':taskCompletedDuration' => $durationInMinutes
                    ]);
                }
            }

            // Delete original data only after all archives succeeded
            $this->deleteAllAssignmentsFromOrderByID($id);
            $this->removeOrder($id);

            $this->pdo->commit();
            return $isCompleted ? "Success: Order has been verified and archived." : "Success: Order has been deleted and archived.";
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return "Error: Failed. " . $e->getMessage();
        }
    }

    public function getAllArchivedOrders() {
        $query = "SELECT * FROM orderArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllArchivedOrderDesigns() {
        $query = "SELECT * FROM orderDesignArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllArchivedOrderGroups() {
        $query = "SELECT * FROM orderGroupArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllArchivedOrderAssignments() {
        $query = "SELECT * FROM orderTasksAssignmentArchive ORDER BY processPhase ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertUserActivityLog($userID, $head, $log, $color) {
        $query = "INSERT INTO userActivityLog (userID, head, log, color) VALUES (:userID, :head, :log, :color)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':userID' => $userID,
            ':head' => strtolower($head),
            ':log' => $log,
            ':color' => strtolower($color)
        ]);
    }
}
