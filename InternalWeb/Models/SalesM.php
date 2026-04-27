<?php
class SalesM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAllSalesRecords() {
        $query = "SELECT * FROM salesRecords";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSalesOrders() {
        $query = "SELECT * FROM salesOrder";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateSalesOrder($orderID, $payment) {
        // Reject negative payments
        if ($payment <= 0) {
            return "Error: Payment must be greater than zero.";
        }

        // Check remaining balance
        $query = "SELECT priceTotal, pricePaid FROM salesOrder WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            return "Error: Sales order not found.";
        }

        $remaining = $order['priceTotal'] - $order['pricePaid'];
        if ($payment > $remaining) {
            return "Error: Payment exceeds remaining balance of ₱" . number_format($remaining, 2) . ".";
        }

        // Update pricePaid
        $query = "UPDATE salesOrder SET pricePaid = pricePaid + :payment WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->bindParam(':payment', $payment);
        $stmt->execute();

        // If fully paid, delete the salesOrder record (optional – remove FK constraint)
        $query = "SELECT pricePaid, priceTotal FROM salesOrder WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && $row['pricePaid'] >= $row['priceTotal']) {
            $query = "DELETE FROM salesOrder WHERE orderID = :orderID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID);
            $stmt->execute();
        }

        // Return the service name for logging / display
        $query = "
            SELECT CONCAT(services.name, ' ', subservices.name) AS serviceName
            FROM orders
            JOIN subservices ON orders.subserviceID = subservices.id
            JOIN services ON subservices.serviceID = services.id
            WHERE orders.id = :orderID
        ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    public function insertInflowRecord($type, $description, $value) {
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');
        $type = ucwords(strtolower($type));

        // If this is an order payment, check for an existing record today with the same description
        if (strpos($description, 'Order #') === 0) {
            $query = "SELECT id, value FROM salesRecords
                  WHERE date = :date AND description = :desc AND isInflow = 1 LIMIT 1";
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([':date' => $today, ':desc' => $description]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                // Update the existing record – add the new payment
                $newValue = $existing['value'] + $value;
                $query = "UPDATE salesRecords SET value = :val WHERE id = :id";
                $stmt = $this->pdo->prepare($query);
                $stmt->execute([':val' => $newValue, ':id' => $existing['id']]);
                return "Success: Existing order payment record updated.";
            }
        }

        // Otherwise, insert a new record
        $query = "INSERT INTO salesRecords (date, isInflow, type, description, value)
              VALUES (:date, 1, :type, :description, :value)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':date'        => $today,
            ':type'        => $type,
            ':description' => $description,
            ':value'       => $value
        ]);
        return "Success: Inflow record added.";
    }

    public function insertOutflowRecord($type, $description, $value) {
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');
        $type = ucwords(strtolower($type));

        $query = "INSERT INTO salesRecords (date, isInflow, type, description, value) VALUES (:date, 0, :type, :description, :value)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            ':date' => $today,
            ':type' => $type,
            ':description' => $description,
            ':value' => $value
        ]);
    }

    public function deleteRecord($recordID) {
        date_default_timezone_set('Asia/Manila');
        $today = date('Y-m-d');

        // Fetch the record
        $query = "SELECT * FROM salesRecords WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $recordID, PDO::PARAM_INT);
        $stmt->execute();
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            return "Error: Record not found.";
        }

        // Only allow deletion of today's records
        if ($record['date'] !== $today) {
            return "Error: You can only delete records from today.";
        }

        // Check if it's an order payment inflow
        if ($record['isInflow'] == 1 && strpos($record['description'], 'Order #') === 0) {
            // Extract orderID from description e.g. "Order #3 Payment"
            preg_match('/Order #(\d+)/', $record['description'], $matches);
            if (empty($matches[1])) {
                return "Error: Invalid order payment record.";
            }
            $orderID = (int)$matches[1];
            $paymentAmount = (float)$record['value'];

            // Check if salesOrder row still exists
            $query = "SELECT * FROM salesOrder WHERE orderID = :orderID";
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
            $stmt->execute();
            $salesOrder = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($salesOrder) {
                // Deduct from pricePaid (ensuring non-negative)
                $newPaid = max($salesOrder['pricePaid'] - $paymentAmount, 0);
                $query = "UPDATE salesOrder SET pricePaid = :paid WHERE orderID = :orderID";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':paid', $newPaid);
                $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                // Recreate the salesOrder row with priceTotal = paymentAmount, pricePaid = 0
                // (This effectively reverts the payment to unpaid state)
                $query = "INSERT INTO salesOrder (orderID, priceTotal, pricePaid)
                      VALUES (:orderID, :total, 0)";
                $stmt = $this->pdo->prepare($query);
                $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
                $stmt->bindParam(':total', $paymentAmount);
                $stmt->execute();
            }
        }

        // Delete the sales record
        $query = "DELETE FROM salesRecords WHERE id = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':id', $recordID, PDO::PARAM_INT);
        $stmt->execute();

        return "Success: Record deleted.";
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
