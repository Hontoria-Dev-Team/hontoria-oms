<?php
class StaffM {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
}
