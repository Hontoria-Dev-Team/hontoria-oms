<?php
class InventoryM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllInventory() {
        $query = "SELECT * FROM inventory";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventoryByID($inventoryID) {
        $query = "SELECT * FROM inventory WHERE id = :id";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':id', $inventoryID);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInventoryByName($name) {
        $query = "SELECT * FROM inventory WHERE name = :name";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':name', $name);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllInventoryCurrentQuantityMap() {
        $query = "
            SELECT
                inventoryRecords.inventoryID,
                inventoryRecords.quantity
            FROM inventoryRecords
            INNER JOIN (
                SELECT inventoryID, MAX(date) AS maxDate
                FROM inventoryRecords
                GROUP BY inventoryID
            ) latestDates ON inventoryRecords.inventoryID = latestDates.inventoryID
                AND inventoryRecords.date = latestDates.maxDate
            ORDER BY inventoryRecords.inventoryID ASC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $map = [];

        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $map[$item['inventoryID']] = $item['quantity'];
        }

        return $map;
    }

    public function getAllInventoryLastRestockMap() {
        $query = "
            SELECT inventoryRecords.inventoryID,
                inventoryRecords.date,
                inventoryRecords.added
            FROM inventoryRecords
            INNER JOIN (
                SELECT inventoryID, MAX(date) AS maxDate
                FROM inventoryRecords
                WHERE added > 0
                GROUP BY inventoryID
            ) latestDates ON inventoryRecords.inventoryID = latestDates.inventoryID
                        AND inventoryRecords.date = latestDates.maxDate
            ORDER BY inventoryRecords.inventoryID ASC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        $map = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $map[$row['inventoryID']] = [
                'date'     => $row['date'],
                'quantity' => $row['added']
            ];
        }
        return $map;
    }

    public function getAllInventoryRecords() {
        $query = "SELECT * FROM inventoryRecords ORDER BY inventoryID ASC, date DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventoryRecordsByIDAndDateRange($inventoryID, $monthRange = 12) {
        date_default_timezone_set('Asia/Manila');

        // Validate inputs
        $inventoryID = (int)$inventoryID;
        $monthRange = max(1, (int)$monthRange);

        // If inventory ID is invalid, return empty array
        if ($inventoryID <= 0) {
            return [];
        }

        // Calculate start date in PHP: (today - N months - 7 days)
        $startDate = date('Y-m-d', strtotime("-$monthRange months -7 days"));
        $endDate = date('Y-m-d');

        $query = "
            SELECT * FROM inventoryRecords
            WHERE inventoryID = :inventoryID
            AND date >= :startDate
            AND date <= :endDate
            ORDER BY date DESC
        ";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':inventoryID', $inventoryID, PDO::PARAM_INT);
        $stmt->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':endDate', $endDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInventoryRecord($inventoryID, $date) {
        $query = "SELECT * FROM inventoryRecords WHERE inventoryID = :inventoryID AND date = :date";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':inventoryID', $inventoryID);
        $stmt->bindParam(':date', $date);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastInventoryRecordBefore($inventoryID, $date) {
        $query = "
            SELECT * FROM inventoryRecords
            WHERE inventoryID = :inventoryID AND date < :date
            ORDER BY date DESC
            LIMIT 1
        ";
        $stmt = $this->pdo->prepare($query);

        $stmt->bindParam(':inventoryID', $inventoryID, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateInventoryRecord($inventoryID, $change) {
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');
        $change = (int)$change;

        if ($change === 0) {
            return 'Error: Cannot update record with zero change.';
        }

        $isRestock = $change > 0;
        $absChange = abs($change);

        try {
            $this->pdo->beginTransaction();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'inventory update',
                'Updated the quantity of ' . ucfirst($this->getInventoryByID($inventoryID)['name']) . ' by ' . $change . '.',
                'yellow'
            );

            $existing = $this->getInventoryRecord($inventoryID, $today);

            if ($existing) {
                $currentQty   = (int)$existing['quantity'];
                $currentCons  = (int)$existing['consumption'];
                $currentAdded = (int)$existing['added'];

                if ($isRestock) {
                    $newQty   = $currentQty + $absChange;
                    $newCons  = $currentCons;
                    $newAdded = $currentAdded + $absChange;
                } else {
                    if ($currentQty < $absChange) {
                        $this->pdo->rollBack();
                        return 'Error: Cannot consume more than current quantity (' . $currentQty . ').';
                    }

                    $newQty   = $currentQty - $absChange;
                    $newCons  = $currentCons + $absChange;
                    $newAdded = $currentAdded;
                }

                $stmt = $this->pdo->prepare("
                    UPDATE inventoryRecords
                    SET quantity = :qty, consumption = :cons, added = :add
                    WHERE inventoryID = :id AND date = :d
                ");

                $stmt->bindParam(':qty', $newQty);
                $stmt->bindParam(':cons', $newCons);
                $stmt->bindParam(':add', $newAdded);
                $stmt->bindParam(':id', $inventoryID);
                $stmt->bindParam(':d', $today);
                $stmt->execute();
            } else {
                $lastRecord = $this->getLastInventoryRecordBefore($inventoryID, $today);
                $lastQty = $lastRecord ? (int)$lastRecord['quantity'] : 0;

                if ($isRestock) {
                    $newQty   = $lastQty + $absChange;
                    $newCons  = 0;
                    $newAdded = $absChange;
                } else {
                    if ($lastQty < $absChange) {
                        $this->pdo->rollBack();
                        return 'Error: Cannot consume more than current quantity (' . $lastQty . ').';
                    }

                    $newQty   = $lastQty - $absChange;
                    $newCons  = $absChange;
                    $newAdded = 0;
                }

                $stmt = $this->pdo->prepare("
                    INSERT INTO inventoryRecords (date, inventoryID, quantity, consumption, added)
                    VALUES (:d, :id, :qty, :cons, :add)
                ");

                $stmt->bindParam(':d', $today);
                $stmt->bindParam(':id', $inventoryID);
                $stmt->bindParam(':qty', $newQty);
                $stmt->bindParam(':cons', $newCons);
                $stmt->bindParam(':add', $newAdded);
                $stmt->execute();
            }

            $this->pdo->commit();
            return "Success: Inventory record updated successfully.";
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return "Error: Failed. " . ($e->getMessage());
        }
    }

    public function deleteInventoryRecord($inventoryID) {
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');

        try {
            $existing = $this->getInventoryRecord($inventoryID, $today);
            if (!$existing) {
                return "Error: No record found for today to reset.";
            }

            $countStmt = $this->pdo->prepare("SELECT COUNT(*) FROM inventoryRecords WHERE inventoryID = :id");
            $countStmt->bindParam(':id', $inventoryID);
            $countStmt->execute();
            $totalRecords = (int)$countStmt->fetchColumn();

            if ($totalRecords <= 1) {
                return "Error: Cannot delete last remaining inventory record.";
            }

            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("
                DELETE FROM inventoryRecords
                WHERE inventoryID = :id AND date = :d
            ");

            $stmt->bindParam(':id', $inventoryID);
            $stmt->bindParam(':d', $today);
            $stmt->execute();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'inventory reset',
                'Reset the inventory record of ' . ucfirst($this->getInventoryByID($inventoryID)['name']) . ' for the day.',
                'red'
            );

            $this->pdo->commit();
            return "Success: Today's inventory record is reset.";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Error: Failed. " . $e->getMessage();
        }
    }

    public function insertInventoryItem($name, $quantity) {
        $name = strtolower(trim($name));

        if (empty($name)) {
            return "Error: Item name cannot be empty.";
        }

        if ($quantity < 1) {
            return "Error: Initial quantity must be at least 1.";
        }

        $item = $this->getInventoryByName($name);

        if ($item) {
            return "Error: Item name already exists. Please choose a different name.";
        }

        $this->insertUserActivityLog(
            $_SESSION['id'],
            'inventory creation',
            'Created a new inventory item called ' . ucfirst($name) . ' with initial quantity ' . $quantity . '.',
            'green'
        );

        $query = "INSERT INTO inventory (name) VALUES (:name);";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->execute();

        $this->updateInventoryRecord($this->pdo->lastInsertId(), $quantity);

        return "Success: Inventory item created successfully.";
    }

    public function deleteInventoryItem($inventoryID) {
        try {
            $this->pdo->beginTransaction();

            $item = $this->getInventoryByID($inventoryID);
            if (!$item) {
                $this->pdo->rollBack();
                return "Error: Inventory item not found.";
            }

            $stmt1 = $this->pdo->prepare("DELETE FROM inventoryRecords WHERE inventoryID = :id");
            $stmt1->bindParam(':id', $inventoryID);
            $stmt1->execute();

            $stmt2 = $this->pdo->prepare("DELETE FROM inventory WHERE id = :id");
            $stmt2->bindParam(':id', $inventoryID);
            $stmt2->execute();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'inventory deletion',
                'Deleted inventory item ' . ucfirst($item['name']) . '.',
                'red'
            );

            $this->pdo->commit();
            return "Success: Inventory item deleted.";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Error: Failed. " . $e->getMessage();
        }
    }

    public function updateInventoryItemMinQuantity($inventoryID, $minQuantity) {
        if ($minQuantity < 0) {
            return "Error: Minimum quantity cannot be negative.";
        }

        $item = $this->getInventoryByID($inventoryID);
        if (!$item) {
            return "Error: Inventory item not found.";
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE inventory SET minQuantity = :minQty WHERE id = :id");
            $stmt->bindParam(':minQty', $minQuantity);
            $stmt->bindParam(':id', $inventoryID);
            $stmt->execute();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'inventory update',
                'Updated the minimum quantity of ' . ucfirst($item['name']) . ' to ' . $minQuantity . '.',
                'yellow'
            );

            $this->pdo->commit();
            return "Success: Minimum quantity updated.";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Error: Failed. " . $e->getMessage();
        }
    }

    public function updateInventoryItemMaxAvgConsumption($inventoryID, $maxAvgConsumption) {
        if ($maxAvgConsumption < 0) {
            return "Error: Max average consumption cannot be negative.";
        }

        $item = $this->getInventoryByID($inventoryID);
        if (!$item) {
            return "Error: Inventory item not found.";
        }

        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("UPDATE inventory SET maxAvgConsumption = :maxAvg WHERE id = :id");
            $stmt->bindParam(':maxAvg', $maxAvgConsumption);
            $stmt->bindParam(':id', $inventoryID);
            $stmt->execute();

            $this->insertUserActivityLog(
                $_SESSION['id'],
                'inventory update',
                'Updated the maximum average consumption of ' . ucfirst($item['name']) . ' to ' . $maxAvgConsumption . '.',
                'yellow'
            );

            $this->pdo->commit();
            return "Success: Maximum average consumption updated.";
        } catch (Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return "Error: Failed. " . $e->getMessage();
        }
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
