<?php
class PublicM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * SECURITY: Check if an order page is currently locked due to brute force attempts.
     * Returns true if locked and still within lockout window, false otherwise.
     */
    private function isOrderLocked($code): bool {
        try {
            $stmt = $this->pdo->prepare("SELECT lockedUntil FROM publicOrderPages WHERE orderCode = :code LIMIT 1");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || empty($row['lockedUntil'])) {
                return false;
            }

            // SECURITY: Check if lock has expired
            $lockedUntil = new \DateTime($row['lockedUntil']);
            $now = new \DateTime();
            return $now < $lockedUntil;
        } catch (\PDOException $e) {
            error_log("Database error in isOrderLocked: " . $e->getMessage());
            return false;
        }
    }

    /**
     * SECURITY: Increment failed password attempts and apply exponential lockout.
     * Locks for 30s base, doubling every 5 attempts: 30s (5-9), 60s (10-14), 120s (15-19), etc.
     */
    private function incrementFailedAttempts($code): void {
        try {
            // Increment failed attempts
            $stmt = $this->pdo->prepare(
                "UPDATE publicOrderPages SET failedAttempts = failedAttempts + 1 WHERE orderCode = :code"
            );
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();

            // Fetch updated count and calculate lockout time
            $stmt = $this->pdo->prepare("SELECT failedAttempts FROM publicOrderPages WHERE orderCode = :code LIMIT 1");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            // SECURITY: Only apply/update lockout at 5-failure increments (5, 10, 15, 20, 25, 30, etc.)
            if ($row && $row['failedAttempts'] % 5 == 0) {
                // Calculate exponential lockout: (failedAttempts / 5) = how many 5-increment cycles
                // Each cycle = 30s * 2^(cycle - 1)
                // At 5 failures: 30 * 2^0 = 30s
                // At 10 failures: 30 * 2^1 = 60s
                // At 15 failures: 30 * 2^2 = 120s
                // At 20 failures: 30 * 2^3 = 240s
                $cycles = $row['failedAttempts'] / 5;
                $lockoutSeconds = intval(30 * pow(2, $cycles - 1));

                // FIX: Use modify() instead of add() to avoid "passed by reference" error
                $now = new \DateTime();
                $lockedUntil = $now->modify('+' . $lockoutSeconds . ' seconds');
                $lockedUntilStr = $lockedUntil->format('Y-m-d H:i:s');

                $stmt = $this->pdo->prepare(
                    "UPDATE publicOrderPages SET lockedUntil = :lockedUntil WHERE orderCode = :code"
                );
                $stmt->bindParam(':lockedUntil', $lockedUntilStr, PDO::PARAM_STR);
                $stmt->bindParam(':code', $code, PDO::PARAM_STR);
                $stmt->execute();
            }
        } catch (\PDOException $e) {
            error_log("Database error in incrementFailedAttempts: " . $e->getMessage());
        }
    }

    /**
     * SECURITY: Reset failed attempts and clear lock on successful password verification.
     */
    private function resetFailedAttempts($code): void {
        try {
            $stmt = $this->pdo->prepare(
                "UPDATE publicOrderPages SET failedAttempts = 0, lockedUntil = NULL WHERE orderCode = :code"
            );
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
        } catch (\PDOException $e) {
            error_log("Database error in resetFailedAttempts: " . $e->getMessage());
        }
    }

    /**
     * SECURITY: Private helper to fetch only the password hash for a given order code.
     * Prevents exposing the hash through public methods.
     */
    private function fetchPasswordHash($code) {
        try {
            $stmt = $this->pdo->prepare("SELECT passwordHash FROM publicOrderPages WHERE orderCode = :code LIMIT 1");
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Database error in fetchPasswordHash: " . $e->getMessage());
            return false;
        }
    }

    public function getServicesCatalog() {
        try {
            // Get all services
            $query = "SELECT id, name, isActive FROM services ORDER BY isActive DESC, name ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get all subservices
            $query = "SELECT id, serviceID, name, description, pricePerUnit, isActive FROM subservices ORDER BY isActive DESC, name ASC";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $subservices = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Get all subservice images
            $query = "SELECT subserviceID, imageName FROM subserviceImages";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute();
            $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Build image map: subserviceID => [images]
            $imageMap = [];
            foreach ($images as $image) {
                if (!isset($imageMap[$image['subserviceID']])) {
                    $imageMap[$image['subserviceID']] = [];
                }
                $imageMap[$image['subserviceID']][] = [
                    'imageName' => $image['imageName']
                ];
            }

            // Build subservice map: serviceID => [subservices]
            $subserviceMap = [];
            foreach ($subservices as $subservice) {
                $serviceID = $subservice['serviceID'];
                if (!isset($subserviceMap[$serviceID])) {
                    $subserviceMap[$serviceID] = [];
                }

                $subserviceMap[$serviceID][] = [
                    'name' => $subservice['name'],
                    'description' => $subservice['description'],
                    'pricePerUnit' => $subservice['pricePerUnit'],
                    'isActive' => $subservice['isActive'],
                    'images' => $imageMap[$subservice['id']] ?? []
                ];
            }

            // Build final catalog: services with their subservices
            $catalog = [];
            foreach ($services as $service) {
                $catalog[] = [
                    'name' => $service['name'],
                    'isActive' => $service['isActive'],
                    'subservices' => $subserviceMap[$service['id']] ?? []
                ];
            }

            return $catalog;
        } catch (\PDOException $e) {
            error_log("Database error in getServicesCatalog: " . $e->getMessage());
            return [];
        }
    }

    public function getPublicOrderPageByCode($code) {
        try {
            $query = "SELECT orderCode, orderID, passwordHash FROM publicOrderPages WHERE orderCode = :code LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $page = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($page) {
                // SECURITY: Add hasPassword flag before stripping the hash to prevent accidental exposure
                $page['hasPassword'] = !empty($page['passwordHash']);
                unset($page['passwordHash']);
            }

            return $page ?: null;
        } catch (\PDOException $e) {
            error_log("Database error in getPublicOrderPageByCode: " . $e->getMessage());
            return null;
        }
    }

    public function setPublicOrderPassword($code, $password) {
        $password = trim($password);
        if (strlen($password) < 10 || !preg_match('/\d/', $password)) {
            return false;
        }

        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            $query = "UPDATE publicOrderPages SET passwordHash = :passwordHash WHERE orderCode = :code";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':passwordHash', $passwordHash, PDO::PARAM_STR);
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();

            // CODE QUALITY: Check rowCount instead of pre-fetching page existence
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in setPublicOrderPassword: " . $e->getMessage());
            return false;
        }
    }

    public function verifyPublicOrderPassword($code, $password) {
        try {
            // SECURITY: Check if order page is currently locked due to brute force attempts
            if ($this->isOrderLocked($code)) {
                return false;
            }

            // CODE QUALITY: Use private helper to fetch only the hash (eliminates duplicate call)
            $passwordHash = $this->fetchPasswordHash($code);
            if ($passwordHash === false || empty($passwordHash)) {
                return false;
            }

            // SECURITY: Verify password and track attempts
            if (password_verify($password, $passwordHash)) {
                // Reset failed attempts on successful verification
                $this->resetFailedAttempts($code);
                return true;
            }

            // SECURITY: Increment failed attempts on wrong password
            $this->incrementFailedAttempts($code);
            return false;
        } catch (\PDOException $e) {
            error_log("Database error in verifyPublicOrderPassword: " . $e->getMessage());
            return false;
        }
    }

    public function getPublicOrderByID($id) {
        try {
            $query = "
            SELECT
                orders.id,
                services.name AS serviceName,
                subservices.name AS subserviceName,
                orders.priceTotal,
                COALESCE(salesOrder.pricePaid, 0) AS pricePaid,
                orders.customerName,
                orders.createdAt,
                orders.deadlineAt,
                orders.messengerGCLink,
                services.hasVariableList AS serviceHasVariableList,
                COALESCE(orderDesigns.id, 0) AS designExists,
                orderDesigns.imageName AS designImage,
                COALESCE(orderDesigns.approved, 0) AS designApproved,
                COALESCE(variableLists.orderID, 0) AS variableListExists,
                COALESCE(variableLists.approved, 0) AS variableListApproved,
                CASE
                    WHEN NOT EXISTS (
                        SELECT 1 FROM orderProcess
                        WHERE orderProcess.orderID = orders.id
                        AND orderProcess.status != 'complete'
                    ) THEN
                        CASE
                            WHEN NOT EXISTS (
                                SELECT 1 FROM salesOrder
                                WHERE salesOrder.orderID = orders.id
                            ) THEN 'For Verification'
                            ELSE 'Unpaid'
                        END
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
            LEFT JOIN salesOrder ON salesOrder.orderID = orders.id
            LEFT JOIN orderDesigns ON orderDesigns.orderID = orders.id
            LEFT JOIN variableLists ON variableLists.orderID = orders.id
            WHERE orders.id = :id
            LIMIT 1
        ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($order) {
                $order['isArchived'] = false;
                $order['pricePaid'] = (float)$order['pricePaid'];
                return $order;
            }

            $query = "SELECT * FROM orderArchive WHERE id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order) {
                return null;
            }

            return [
                'id' => $order['id'],
                'serviceName' => $order['serviceName'],
                'subserviceName' => $order['subserviceName'],
                'priceTotal' => (float)$order['priceTotal'],
                'pricePaid' => (float)$order['priceTotal'],
                'customerName' => $order['customerName'],
                'createdAt' => $order['createdAt'],
                'deadlineAt' => $order['deadlineAt'],
                'messengerGCLink' => $order['messengerGCLink'],
                'status' => 'Complete',
                'serviceHasVariableList' => false,
                'designExists' => 0,
                'designImage' => '',
                'designApproved' => 1,
                'variableListExists' => 0,
                'variableListApproved' => 1,
                'isArchived' => true,
            ];
        } catch (\PDOException $e) {
            error_log("Database error in getPublicOrderByID: " . $e->getMessage());
            return null;
        }
    }

    public function getOrderProcessDetails($orderID, $isArchived = false) {
        try {
            if ($isArchived) {
                $query = "
                SELECT
                    CAST(processPhase AS UNSIGNED) AS phase,
                    processName,
                    COUNT(*) AS assignedNum,
                    'complete' AS status
                FROM orderTasksAssignmentArchive
                WHERE orderArchiveID = :orderID
                GROUP BY processPhase, processName
                ORDER BY phase
            ";

                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            $query = "
            SELECT
                orderProcess.phase,
                processes.name AS processName,
                orderProcess.status,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            LEFT JOIN userProcessTasks ON orderProcess.id = userProcessTasks.orderProcessID
            WHERE orderProcess.orderID = :orderID
            GROUP BY orderProcess.id
            ORDER BY orderProcess.phase
        ";

            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Database error in getOrderProcessDetails: " . $e->getMessage());
            return [];
        }
    }

    public function getVariableListByOrderID($orderID) {
        try {
            $query = "SELECT * FROM variableLists WHERE orderID = :orderID LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            $list = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$list) {
                return null;
            }

            $query = "SELECT * FROM variableListColumns WHERE orderID = :orderID ORDER BY displayOrder";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $query = "SELECT * FROM variableListValues WHERE orderID = :orderID ORDER BY rowNumber, columnID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $rows = [];
            foreach ($values as $value) {
                $rowNumber = $value['rowNumber'];
                if (!isset($rows[$rowNumber])) {
                    $rows[$rowNumber] = [];
                }
                $rows[$rowNumber][$value['columnID']] = $value['valueText'];
            }

            return [
                'approved' => (int)$list['approved'],
                'columns' => $columns,
                'rows' => $rows,
            ];
        } catch (\PDOException $e) {
            error_log("Database error in getVariableListByOrderID: " . $e->getMessage());
            return null;
        }
    }

    public function approveDesign($orderID) {
        try {
            $query = "UPDATE orderDesigns SET approved = 1 WHERE orderID = :orderID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in approveDesign: " . $e->getMessage());
            return false;
        }
    }

    public function approveVariableList($orderID) {
        try {
            $query = "UPDATE variableLists SET approved = 1 WHERE orderID = :orderID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            error_log("Database error in approveVariableList: " . $e->getMessage());
            return false;
        }
    }

    /**
     * SECURITY: Public method to check lock status and return lockout time remaining.
     * Returns null if not locked, or DateTime object if locked.
     */
    public function getOrderLockStatus($code) {
        try {
            $stmt = $this->pdo->prepare(
                "SELECT lockedUntil FROM publicOrderPages WHERE orderCode = :code LIMIT 1"
            );
            $stmt->bindParam(':code', $code, PDO::PARAM_STR);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || empty($row['lockedUntil'])) {
                return null;
            }

            $lockedUntil = new \DateTime($row['lockedUntil']);
            $now = new \DateTime();

            // Return lock status only if still locked
            if ($now < $lockedUntil) {
                return $lockedUntil;
            }

            return null;
        } catch (\PDOException $e) {
            error_log("Database error in getOrderLockStatus: " . $e->getMessage());
            return null;
        }
    }
}
