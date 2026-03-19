<?php
class OrdersM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getOrderList() {
        $query = "SELECT
                      orders.id,
                      services.NAME AS serviceName,
                      subservices.NAME AS subserviceName,
                      orders.customerName,
                      orders.createdAt,
                      orders.deadlineAt,
                      orders.messengerGCLink
                  FROM orders
                  JOIN subservices ON orders.subserviceID = subservices.id
                  JOIN services ON subservices.serviceID = services.id
                  ORDER BY orders.id ASC;";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertOrder() {
    }
}
