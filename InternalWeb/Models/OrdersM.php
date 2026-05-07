<?php
class OrdersM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // ================================================================
    //  ORDER LOOKUP & FILTERING
    // ================================================================

    // Get all orders (unfiltered).
    public function getAllOrders() {
        return $this->getFilteredOrders('', '', -1);
    }

    // Get orders filtered by search term, status, and/or service ID.
    public function getFilteredOrders($search = '', $status = '', $serviceID = -1) {
        $search = trim($search);
        $normalizedStatus = strtolower(trim($status));
        $serviceID = intval($serviceID);
        $searchId = ctype_digit($search) ? intval($search) : 0;
        $searchName = '%' . $search . '%';

        $query = "
            SELECT *
            FROM (
                SELECT
                    orders.id,
                    services.id AS serviceID,
                    services.name AS serviceName,
                    subservices.name AS subserviceName,
                    services.hasDesign AS hasDesign,
                    services.hasVariableList AS hasVariableList,
                    orders.priceTotal,
                    orders.customerName,
                    orders.createdAt,
                    orders.deadlineAt,
                    orders.messengerGCLink,
                    publicOrderPages.orderCode,
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
                LEFT JOIN publicOrderPages ON publicOrderPages.orderID = orders.id
            ) AS ordersView
            WHERE 1=1
        ";

        if ($search !== '') {
            $query .= " AND (ordersView.id = :searchId OR ordersView.customerName LIKE :searchName)";
        }

        $statusMap = [
            'idle' => 'Idle',
            'active' => 'Active',
            'unpaid' => 'Unpaid',
            'for verification' => 'For Verification',
        ];

        if ($normalizedStatus !== '' && isset($statusMap[$normalizedStatus])) {
            $query .= " AND ordersView.status = :status";
        }

        if ($serviceID > 0) {
            $query .= " AND ordersView.serviceID = :serviceID";
        }

        $query .= " ORDER BY ordersView.id ASC";

        $stmt = $this->pdo->prepare($query);

        if ($search !== '') {
            $stmt->bindValue(':searchId', $searchId, PDO::PARAM_INT);
            $stmt->bindValue(':searchName', $searchName, PDO::PARAM_STR);
        }

        if ($normalizedStatus !== '' && isset($statusMap[$normalizedStatus])) {
            $stmt->bindValue(':status', $statusMap[$normalizedStatus], PDO::PARAM_STR);
        }

        if ($serviceID > 0) {
            $stmt->bindValue(':serviceID', $serviceID, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get a single order by its ID.
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

    // ================================================================
    //  ORDER GROUPS
    // ================================================================

    // Get all groups for a specific order.
    public function getOrderGroupsByID($id) {
        $query = "SELECT * FROM orderGroups WHERE orderID = :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all order groups across all orders.
    public function getAllOrderGroups() {
        $query = "SELECT orderID, description, quantity FROM orderGroups";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    //  ORDER DESIGN
    // ================================================================

    // Get the design image record for a specific order.
    public function getOrderDesignByID($id) {
        $query = "SELECT * FROM orderDesigns WHERE orderID =  :id";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Check if a design record already exists for an order.
    public function findSingleOrderDesignByID($orderID) {
        $query = "SELECT imageName FROM orderDesigns WHERE orderID = :orderID LIMIT 1";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Upload or replace a design image for an order.
    public function insertOrderDesign($orderID, $imageFile, $bypassCheck) {
        $storageDir = __DIR__ . '/../../Storage/Designs/';

        // Verify current user has assignment to this order with design access
        if (!$bypassCheck) {
            $query = "
                SELECT COUNT(*) as count
                FROM userProcessTasks upt
                JOIN orderProcess op ON upt.orderProcessID = op.id
                JOIN orders o ON op.orderID = o.id
                JOIN subservices ss ON o.subserviceID = ss.id
                JOIN serviceProcess sp ON ss.serviceID = sp.serviceID AND op.phase = sp.phase
                JOIN processes p ON sp.processesID = p.id
                WHERE o.id = :orderID
                AND upt.userID = :userID
                AND p.designAccess = 'view & update'
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':orderID' => $orderID,
                ':userID' => $_SESSION['id']
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || (int)$result['count'] === 0) {
                return "Error: You do not have permission to upload designs for this order.";
            }
        }

        $existingDesign = $this->findSingleOrderDesignByID($orderID);

        // Check if existing design is approved - prevent changes to approved designs
        if ($existingDesign) {
            $query = "SELECT approved FROM orderDesigns WHERE orderID = :orderID LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':orderID' => $orderID]);
            $designData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($designData && (int)$designData['approved'] === 1) {
                return "Error: Cannot modify an approved design.";
            }
        }

        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $fileExtension = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($fileExtension, $allowed)) {
            return "Error: Invalid file format.";
        }

        $newFileName = bin2hex(random_bytes(10)) . '_' . time() . '.' . $fileExtension;
        $targetPath = $storageDir . $newFileName;

        if (move_uploaded_file($imageFile['tmp_name'], $targetPath)) {
            try {
                if ($existingDesign) {
                    $query = "UPDATE orderDesigns SET imageName = :imageName, approved = FALSE WHERE orderID = :orderID";
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

                if ($success) {
                    $this->insertUserActivityLog($_SESSION['id'], 'Order Update', "Uploaded design for order ID {$orderID}", 'yellow');
                    return "Success: Design uploaded successfully.";
                }

                return "Error: Database operation failed.";
            } catch (PDOException $e) {
                if (file_exists($targetPath)) {
                    unlink($targetPath);
                }
                return "Error: " . $e->getMessage();
            }
        }

        return "Error: File upload failed.";
    }

    // Get all order designs as base64-encoded images.
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

    // ================================================================
    //  VARIABLE LISTS
    // ================================================================

    // Get the full variable list data (columns, values, row checks) for an order.
    public function getOrderVariableListByID($id) {
        // 1. List metadata
        $query = "SELECT * FROM variableLists WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $list = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$list) {
            return null;   // no variable list for this order
        }

        // 2. Columns (ordered by displayOrder)
        $query = "SELECT * FROM variableListColumns WHERE orderID = :id ORDER BY displayOrder";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Values (all cells)
        $query = "SELECT * FROM variableListValues WHERE orderID = :id ORDER BY rowNumber, columnID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 4. Row checks
        $query = "SELECT * FROM variableListRowChecks WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $rowChecksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Build a map: rowNumber => isChecked (boolean)
        $rowChecks = [];
        foreach ($rowChecksData as $rc) {
            $rowChecks[(int)$rc['rowNumber']] = (bool)$rc['isChecked'];
        }

        return [
            'list'      => $list,
            'columns'   => $columns,
            'values'    => $values,
            'rowChecks' => $rowChecks,
        ];
    }

    // Get all variable lists keyed by order ID.
    public function getAllOrderVariableListMapped() {
        // Fetch all orders that have a variable list
        $query = "SELECT orderID FROM variableLists";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $orderIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $map = [];
        foreach ($orderIDs as $oid) {
            $map[$oid] = $this->getOrderVariableListByID($oid);
        }

        return $map;
    }

    // Update the variable list for an order (columns, values, row checks).
    public function updateVariableList($orderID, $data, $bypassCheck) {
        // Verify current user has assignment to this order with variable list access
        if (!$bypassCheck) {
            $query = "
                SELECT COUNT(*) as count
                FROM userProcessTasks upt
                JOIN orderProcess op ON upt.orderProcessID = op.id
                JOIN orders o ON op.orderID = o.id
                JOIN subservices ss ON o.subserviceID = ss.id
                JOIN serviceProcess sp ON ss.serviceID = sp.serviceID AND op.phase = sp.phase
                JOIN processes p ON sp.processesID = p.id
                WHERE o.id = :orderID
                AND upt.userID = :userID
                AND p.variableListAccess = 'view & update'
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':orderID' => $orderID,
                ':userID' => $_SESSION['id']
            ]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result || (int)$result['count'] === 0) {
                return "Error: You do not have permission to update variable lists for this order.";
            }
        }

        // Normalize everything to lowercase
        foreach ($data['columns'] as &$col) {
            $col['columnName'] = strtolower($col['columnName']);
        }
        unset($col);
        foreach ($data['values'] as &$val) {
            $val['valueText'] = strtolower($val['valueText']);
        }
        unset($val);

        $this->pdo->beginTransaction();
        try {
            // Original data
            $origCols = $this->pdo->prepare("SELECT * FROM variableListColumns WHERE orderID = ? ORDER BY displayOrder");
            $origCols->execute([$orderID]);
            $origCols = $origCols->fetchAll(PDO::FETCH_ASSOC);

            $origVals = $this->pdo->prepare("SELECT * FROM variableListValues WHERE orderID = ?");
            $origVals->execute([$orderID]);
            $origVals = $origVals->fetchAll(PDO::FETCH_ASSOC);

            // Find the immutable "group" column
            $groupCol = null;
            foreach ($origCols as $c) {
                if (strtolower($c['columnName']) === 'group') {
                    $groupCol = $c;
                    break;
                }
            }
            if (!$groupCol) throw new Exception("Group column not found.");

            // Ensure group column is present, first, and named correctly
            $inGroupId = null;
            foreach ($data['columns'] as $idx => $col) {
                if (!empty($col['id']) && $col['id'] == $groupCol['id']) {
                    if ($idx !== 0) throw new Exception("Group column must be first.");
                    if ($col['columnName'] !== 'group') throw new Exception("Group column cannot be renamed.");
                    $inGroupId = $col['id'];
                }
            }
            if (!$inGroupId) throw new Exception("Group column cannot be deleted.");

            // Normalize displayOrder so the group column stays first and all other columns are sequential.
            $normalizedCols = [];
            $nextOrder = 1;
            foreach ($data['columns'] as $col) {
                $col['displayOrder'] = $nextOrder++;
                $normalizedCols[] = $col;
            }
            $data['columns'] = $normalizedCols;

            // Row count must not change
            $origRows = array_unique(array_column($origVals, 'rowNumber'));
            $newRows  = array_unique(array_column($data['values'], 'rowNumber'));
            sort($origRows);
            sort($newRows);
            if ($origRows != $newRows) throw new Exception("Rows cannot be added or removed.");

            // Group column values must match the originals (lowercased)
            $origGroupVals = [];
            foreach ($origVals as $v) {
                if ($v['columnID'] == $groupCol['id']) {
                    $origGroupVals[$v['rowNumber']] = strtolower($v['valueText']);
                }
            }
            foreach ($data['values'] as $v) {
                $colId   = $v['columnID'] ?? null;
                $tempKey = $v['tempKey'] ?? null;
                $isGroup = $colId == $groupCol['id'] || ($tempKey && $tempKey === ($data['columns'][0]['tempKey'] ?? null));
                if ($isGroup) {
                    $row  = $v['rowNumber'];
                    $expected = $origGroupVals[$row] ?? null;
                    if ($expected !== null && $v['valueText'] !== $expected) {
                        throw new Exception("Values in the group column are immutable.");
                    }
                }
            }

            // ----- Update / insert columns -----
            $colMap = [];
            $stmtUpd = $this->pdo->prepare("UPDATE variableListColumns SET columnName = ?, displayOrder = ? WHERE id = ?");
            $stmtIns = $this->pdo->prepare("INSERT INTO variableListColumns (orderID, columnName, displayOrder) VALUES (?, ?, ?)");
            foreach ($data['columns'] as $col) {
                if (!empty($col['id'])) {
                    $stmtUpd->execute([$col['columnName'], $col['displayOrder'], $col['id']]);
                    $colMap[$col['id']] = $col['id'];
                } else {
                    $stmtIns->execute([$orderID, $col['columnName'], $col['displayOrder']]);
                    $newId = $this->pdo->lastInsertId();
                    if (!empty($col['tempKey'])) $colMap[$col['tempKey']] = $newId;
                }
            }

            // Delete removed columns (except group)
            $existingIds = array_column($origCols, 'id');
            $incomingIds = array_filter(array_column($data['columns'], 'id'));
            $toDelete = array_diff($existingIds, $incomingIds);
            $stmtDel = $this->pdo->prepare("DELETE FROM variableListColumns WHERE id = ?");
            foreach ($toDelete as $delId) {
                if ($delId == $groupCol['id']) continue;
                $stmtDel->execute([$delId]);
            }

            // ----- Update / insert values -----
            $stmtUpdVal = $this->pdo->prepare("UPDATE variableListValues SET valueText = ?, rowNumber = ?, columnID = ? WHERE id = ?");
            $stmtInsVal = $this->pdo->prepare("INSERT INTO variableListValues (orderID, rowNumber, columnID, valueText) VALUES (?, ?, ?, ?)");
            $handled = [];
            $existingValIds = array_column($origVals, 'id');

            foreach ($data['values'] as $v) {
                $realColId = null;
                if (!empty($v['columnID']) && isset($colMap[$v['columnID']])) $realColId = $v['columnID'];
                elseif (!empty($v['tempKey']) && isset($colMap[$v['tempKey']])) $realColId = $colMap[$v['tempKey']];
                if (!$realColId) continue;

                if (!empty($v['id']) && in_array($v['id'], $existingValIds)) {
                    $stmtUpdVal->execute([$v['valueText'], $v['rowNumber'], $realColId, $v['id']]);
                    $handled[] = $v['id'];
                } else {
                    $stmtInsVal->execute([$orderID, $v['rowNumber'], $realColId, $v['valueText']]);
                }
            }

            // Delete orphaned values
            $valToDelete = array_diff($existingValIds, $handled);
            $stmtDelVal = $this->pdo->prepare("DELETE FROM variableListValues WHERE id = ?");
            foreach ($valToDelete as $delId) $stmtDelVal->execute([$delId]);

            // ----- Update row checks -----
            $stmtDelChk = $this->pdo->prepare("DELETE FROM variableListRowChecks WHERE orderID = ?");
            $stmtDelChk->execute([$orderID]);

            if (!empty($data['rowChecks'])) {
                $stmtInsChk = $this->pdo->prepare(
                    "INSERT INTO variableListRowChecks (orderID, rowNumber, isChecked) VALUES (?, ?, ?)"
                );
                foreach ($data['rowChecks'] as $rc) {
                    $stmtInsChk->execute([
                        $orderID,
                        $rc['rowNumber'],
                        isset($rc['isChecked']) && $rc['isChecked'] ? 1 : 0
                    ]);
                }
            }

            $this->pdo->commit();
            $this->insertUserActivityLog($_SESSION['id'], 'Order Update', "Updated variable list for order ID {$orderID}", 'yellow');
            return "Success: Variable list updated.";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return "Error: " . $e->getMessage();
        }
    }

    // ================================================================
    //  ORDER PROCESSES
    // ================================================================

    // Get all processes for a specific order (with assignment counts).
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

    // Get a single order process record by its ID.
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

    // Get all order processes across all orders.
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
                services.hasDesign AS hasDesign,
                services.hasVariableList AS hasVariableList,
                processes.designAccess,
                processes.variableListAccess,
                COUNT(userProcessTasks.orderProcessID) AS assignedNum
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            JOIN services ON subservices.serviceID = services.id
            LEFT JOIN userProcessTasks
                ON orderProcess.id = userProcessTasks.orderProcessID
            GROUP BY
                orderProcess.id,
                orderProcess.orderID,
                services.hasDesign,
                services.hasVariableList,
                processes.designAccess,
                processes.variableListAccess
            ORDER BY orderProcess.orderID, orderProcess.phase
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get the aggregate status of a process based on its assigned user tasks.
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

    // Update an order process status and cascade to the next phase if needed.
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

        if ($status === 'complete' || $status === 'partially complete') {
            if ($result) {
                // Check if next is 'pending'
                $query = "SELECT status FROM orderProcess WHERE id = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':id', $result);
                $stmt->execute();
                $nextStatus = $stmt->fetchColumn();

                if ($nextStatus === 'pending') {
                    // Set this one to 'active'
                    $query = "UPDATE orderProcess SET status = 'active' WHERE id = :id";
                    $stmt = $this->pdo->prepare($query);
                    $stmt->bindParam(':id', $result);
                    $stmt->execute();
                }
            }
        }
    }

    // ================================================================
    //  TASK ASSIGNMENT & MANAGEMENT
    // ================================================================

    // Get all users assigned to processes of a given order.
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

    // Get available tasks that a user can self-assign to.
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
                services.hasDesign,
                services.hasVariableList,
                subservices.name AS subserviceName,
                orders.customerName,
                orderProcess.minAssign,
                orderProcess.maxAssign,
                orders.deadlineAt,
                orders.messengerGCLink,
                processes.name AS processName,
                processes.hasGCAccess,
                processes.designAccess,
                processes.variableListAccess,
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

    // Assign a user to an order process task.
    public function insertUserProcessTask($userID, $orderProcessID) {
        $process = $this->getOrderProcesseeByID($orderProcessID);
        $message = '';

        if ($_SESSION['id'] == $userID) {
            $this->insertUserActivityLog(
                $userID,
                'task assignment',
                'Self-Assigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'yellow'
            );

            $message = 'Success: Self-Assigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.';
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
                'Assigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'yellow'
            );

            $message = 'Success: Assigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.';
        }

        $query = "INSERT INTO userProcessTasks (userID, orderProcessID) VALUES (:userID, :orderProcessID)";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();

        return $message;
    }

    // Remove a user from an order process task.
    public function removeUserProcessTask($userID, $orderProcessID) {
        $process = $this->getOrderProcesseeByID($orderProcessID);
        $message = '';

        if ($_SESSION['id'] == $userID) {
            $this->insertUserActivityLog(
                $userID,
                'task unassignment',
                'Self-Unassigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'red'
            );

            $message = 'Success: Self-Unassigned to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.';
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
                'Unassigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.',
                'red'
            );

            $message = 'Success: Unassigned ' . $userFullName . ' to ' . $process['processName'] . ' Order #' . $process['orderID'] . '.';
        }

        $query = "DELETE FROM userProcessTasks WHERE userID = :userID AND orderProcessID = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();

        return $message;
    }

    // Get all task assignments across all orders.
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

    // Update the status of a user's process task (pending → partially complete → complete).
    public function updateUserProcessTaskStatus($userID, $orderProcessID, $status) {
        $status = strtolower(trim($status));
        $validStatuses = ['pending', 'partially complete', 'complete'];

        if (!in_array($status, $validStatuses, true)) {
            return "Error: Invalid task status.";
        }

        $stmt = $this->pdo->prepare(
            "SELECT * FROM userProcessTasks WHERE userID = :userID AND orderProcessID = :orderProcessID LIMIT 1"
        );
        $stmt->execute([
            ':userID' => $userID,
            ':orderProcessID' => $orderProcessID
        ]);
        $task = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$task) {
            return "Error: You are not assigned to this task.";
        }

        $currentStatus = strtolower($task['status']);
        if ($currentStatus === 'complete') {
            return "Error: Completed tasks cannot be updated.";
        }

        $currentIndex = array_search($currentStatus, $validStatuses, true);
        $requestedIndex = array_search($status, $validStatuses, true);
        if ($requestedIndex === false || $requestedIndex <= $currentIndex) {
            return "Error: Invalid task transition.";
        }

        $query = "
            SELECT
                orderProcess.orderID,
                orderProcess.phase,
                processes.designAccess,
                processes.variableListAccess,
                services.hasDesign,
                services.hasVariableList
            FROM orderProcess
            JOIN orders ON orderProcess.orderID = orders.id
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            JOIN serviceProcess
                ON subservices.serviceID = serviceProcess.serviceID
                AND orderProcess.phase = serviceProcess.phase
            JOIN processes ON serviceProcess.processesID = processes.id
            WHERE orderProcess.id = :orderProcessID
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':orderProcessID' => $orderProcessID]);
        $process = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$process) {
            return "Error: Task process data not found.";
        }

        $designRequired = $process['hasDesign'] == 1 && $process['designAccess'] === 'view & update';
        if ($designRequired) {
            $query = "SELECT approved FROM orderDesigns WHERE orderID = :orderID LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':orderID' => $process['orderID']]);
            $design = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$design || (int)$design['approved'] !== 1) {
                return "Error: Design must be approved before updating task status.";
            }
        }

        $variableListRequired = $process['hasVariableList'] == 1 && $process['variableListAccess'] === 'view & update';
        if ($variableListRequired) {
            $listData = $this->getOrderVariableListByID($process['orderID']);
            if (!$listData || empty($listData['list']) || (int)$listData['list']['approved'] !== 1) {
                return "Error: Variable list approval required before updating task status.";
            }
        }

        if ($status === 'complete') {
            $query = "
                SELECT COUNT(*) FROM userProcessTasks upt
                JOIN orderProcess op ON upt.orderProcessID = op.id
                WHERE op.orderID = (SELECT orderID FROM orderProcess WHERE id = :orderProcessID)
                AND op.phase < (SELECT phase FROM orderProcess WHERE id = :orderProcessID)
                AND upt.status != 'complete'
                LIMIT 1
            ";

            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':orderProcessID' => $orderProcessID]);
            if ($stmt->fetchColumn() > 0) {
                return "Error: Cannot complete task. All preceding tasks in the order must be complete.";
            }
        }

        if ($status === 'complete') {
            $query = "UPDATE userProcessTasks SET status = :status, completedAt = NOW() WHERE userID = :userID AND orderProcessID = :orderProcessID";
        } else {
            $query = "UPDATE userProcessTasks SET status = :status WHERE userID = :userID AND orderProcessID = :orderProcessID";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':status' => $status,
            ':userID' => $userID,
            ':orderProcessID' => $orderProcessID
        ]);

        $this->updateOrderProcess($orderProcessID);
        return "Success: Task status updated.";
    }

    // Get the count of distinct assignees per order.
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

    // Delete all task assignments for an order.
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

    // ================================================================
    //  ORDER CREATION & DELETION
    // ================================================================

    // Create a new order with its process chain, groups, variable list, sales record, and public page.
    public function insertOrder($subserviceID, $customerName, $messengerGCLink, $deadlineAt, $priceTotal, $groupDescriptions, $groupQuantities, $orderProcess) {
        try {
            $this->pdo->beginTransaction();

            // Insert base order record
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

            // Insert order groups
            $query = "INSERT INTO orderGroups (orderID, description, quantity) VALUES (:orderID, :description, :quantity)";
            $stmt = $this->pdo->prepare($query);

            for ($i = 0; $i < count($groupDescriptions); $i++) {
                $stmt->execute([
                    ':orderID' => $orderID,
                    ':description' => strtolower($groupDescriptions[$i]),
                    ':quantity' => $groupQuantities[$i],
                ]);
            }

            // Check if the service requires a variable list
            $query = "SELECT services.hasVariableList FROM subservices JOIN services ON subservices.serviceID = services.id WHERE subservices.id = :subserviceID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':subserviceID', $subserviceID);
            $stmt->execute();
            $service = $stmt->fetch(PDO::FETCH_ASSOC);

            // Only create variable list if service requires it
            if ($service && $service['hasVariableList']) {
                $query = "INSERT INTO variableLists (orderID) VALUES (:orderID)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':orderID', $orderID);
                $stmt->execute();
                $variableListID = $this->pdo->lastInsertId();

                $query = "INSERT INTO variableListColumns (orderID, displayOrder, columnName) VALUES (:orderID, 1, 'group')";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':orderID', $orderID);
                $stmt->execute();
                $groupColumnId = $this->pdo->lastInsertId();

                $rowNumber = 1;
                foreach ($groupDescriptions as $index => $description) {
                    $quantity = $groupQuantities[$index];
                    for ($j = 0; $j < $quantity; $j++) {
                        // Insert the cell value
                        $query = "INSERT INTO variableListValues (orderID, rowNumber, columnID, valueText) VALUES (:orderID, :rowNumber, :columnID, :value)";
                        $stmt = $this->pdo->prepare($query);
                        $stmt->execute([
                            ':orderID'   => $orderID,
                            ':rowNumber' => $rowNumber,
                            ':columnID'  => $groupColumnId,
                            ':value'     => strtolower($description)
                        ]);

                        // Insert the corresponding row check (defaults to unchecked)
                        $query = "INSERT INTO variableListRowChecks (orderID, rowNumber) VALUES (:orderID, :rowNumber)";
                        $stmt = $this->pdo->prepare($query);
                        $stmt->execute([
                            ':orderID'   => $orderID,
                            ':rowNumber' => $rowNumber
                        ]);

                        $rowNumber++;
                    }
                }
            }

            // Insert order process phases
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

            // Insert sales order record
            $query = "INSERT INTO salesOrder (orderID, priceTotal) VALUES (:orderID, :priceTotal)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID);
            $stmt->bindParam(':priceTotal', $priceTotal);
            $stmt->execute();

            // Insert public order page
            $orderCode = bin2hex(random_bytes(10)) . time();
            $query = "INSERT INTO publicOrderPages (orderCode, orderID) VALUES (:orderCode, :orderID)";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderCode', $orderCode);
            $stmt->bindParam(':orderID', $orderID);
            $stmt->execute();

            // Commit transaction
            $this->pdo->commit();

            // Log order creation activity
            $this->insertUserActivityLog($_SESSION['id'], 'order creation', 'Created Order #' . $orderID . ' for ' . $customerName . '.', 'yellow');

            return "Success: Order #" . $orderID . " created successfully for " . $customerName . ".";
        } catch (PDOException $e) {
            // Rollback on any database error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return "Error: Failed to create order. " . $e->getMessage();
        } catch (Exception $e) {
            // Rollback on any other error
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return "Error: An unexpected error occurred while creating the order.";
        }
    }

    // Permanently delete an order and all its related data.
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

        $query = "DELETE FROM variableLists WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM salesOrder WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM publicOrderPages WHERE orderID = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $query = "DELETE FROM orders WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Update the deadline of an order.
    public function updateDeadline($id, $deadlineAt) {
        $this->insertUserActivityLog($_SESSION['id'], 'order update', 'Updated the deadline for Order #' . $id . '.', 'yellow');

        $query = "UPDATE orders SET deadlineAt = :deadlineAt WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':deadlineAt', $deadlineAt);
        $stmt->execute();

        return "Success: Updated Order #" . $id . " deadline.";
    }

    // Check if an order's sales record is fully paid.
    public function isOrderFullyPaid($orderID) {
        $query = "SELECT COUNT(*) FROM salesOrder WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();
        return $stmt->fetchColumn() == 0;
    }

    // ================================================================
    //  ORDER ARCHIVING
    // ================================================================

    // Archive an order (move to archive tables, then delete original).
    public function archiveOrder($id, $isCompleted) {
        try {
            $this->pdo->beginTransaction();

            $order = $this->getOrderByID($id);

            if ($isCompleted) {
                if ($order['status'] !== 'For Verification') {
                    $this->pdo->rollBack();
                    return "Error: This order is not yet ready to be verified. Current status: " . $order['status'];
                }
                if (!$this->isOrderFullyPaid($id)) {
                    $this->pdo->rollBack();
                    return "Error: This order is not yet ready to be verified. Current status: " . $order['status'];
                }
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

    // ================================================================
    //  ARCHIVE LOOKUP
    // ================================================================

    // Get all archived orders.
    public function getAllArchivedOrders() {
        $query = "SELECT * FROM orderArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all archived order designs.
    public function getAllArchivedOrderDesigns() {
        $query = "SELECT * FROM orderDesignArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all archived order groups.
    public function getAllArchivedOrderGroups() {
        $query = "SELECT * FROM orderGroupArchive";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all archived task assignments.
    public function getAllArchivedOrderAssignments() {
        $query = "SELECT * FROM orderTasksAssignmentArchive ORDER BY processPhase ASC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ================================================================
    //  MISCELLANEOUS TASK CHECKS
    // ================================================================

    // Check if a user currently has a miscellaneous task assigned.
    public function hasMiscTask($userID) {
        $query = "SELECT userID FROM miscellaneousTasks WHERE userID = :userID LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    // ================================================================
    //  TASK ASSIGNMENT VALIDATION
    // ================================================================

    // Check if enough users are assigned to a task to start work on it.
    public function hasEnoughAssigned($orderProcessID = 0, $userID = 0, $orderID = 0) {
        // If orderProcessID is provided, use it directly
        if ($orderProcessID > 0) {
            $query = "SELECT minAssign FROM orderProcess WHERE id = :orderProcessID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderProcessID', $orderProcessID);
            $stmt->execute();
            $orderProcess = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orderProcess) {
                return false;
            }
        } else {
            // Otherwise, get the orderProcessID from userID and orderID
            if ($userID <= 0 || $orderID <= 0) {
                return false;
            }

            $query = "
                SELECT upt.orderProcessID
                FROM userProcessTasks upt
                JOIN orderProcess op ON upt.orderProcessID = op.id
                WHERE upt.userID = :userID AND op.orderID = :orderID
                LIMIT 1
            ";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':userID', $userID);
            $stmt->bindParam(':orderID', $orderID);
            $stmt->execute();
            $userTask = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userTask) {
                return false;
            }

            $orderProcessID = $userTask['orderProcessID'];

            // Get the minimum required assignments for this process
            $query = "SELECT minAssign FROM orderProcess WHERE id = :orderProcessID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderProcessID', $orderProcessID);
            $stmt->execute();
            $orderProcess = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$orderProcess) {
                return false;
            }
        }

        // Count how many users are currently assigned to this process
        $query = "SELECT COUNT(*) as assignedCount FROM userProcessTasks WHERE orderProcessID = :orderProcessID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderProcessID', $orderProcessID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return true if assigned count meets or exceeds minimum
        return $result['assignedCount'] >= $orderProcess['minAssign'];
    }

    // ================================================================
    //  ACTIVITY LOGGING
    // ================================================================

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
