<?php
$host = '127.0.0.1'; // 127.0.0.1 is more reliable than 'localhost' on XAMPP
$db   = 'your_database_name'; // CHANGE THIS to your actual database name
$user = 'root';
$pass = ''; // Default XAMPP password is empty
$port = '3306'; // Default MySQL port

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    // Create the PDO connection
    $pdo = new PDO($dsn, $user, $pass);
    // Set error mode to exception so errors are easier to catch
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (\PDOException $e) {
    // If connection fails, stop the page and show the error nicely
    die("Database Connection failed: " . $e->getMessage() . " Please ensure MySQL is running in XAMPP.");
}
?>