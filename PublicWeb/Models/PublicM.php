<?php
class PublicM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getServicesCatalog() {
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
    }

    public function getOrderByID($id) {
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
    }

    public function getOrderProcessDetails($orderID, $isArchived = false) {
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
    }

    public function getVariableListByOrderID($orderID) {
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
    }

    public function approveDesign($orderID) {
        $query = "UPDATE orderDesigns SET approved = 1 WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function approveVariableList($orderID) {
        $query = "UPDATE variableLists SET approved = 1 WHERE orderID = :orderID";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
