<?php
// db.php — The "God Mode" Multi-Environment Edition
error_reporting(0);

// Detect the current environment based on the server's URL or variables
$current_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';

if (getenv('MYSQLHOST')) {
    // ---------------------------------------------------------
    // 1. RAILWAY ENVIRONMENT (Auto-detected via env variables)
    // ---------------------------------------------------------
    $host    = getenv('MYSQLHOST');
    $port    = getenv('MYSQLPORT');
    $db      = getenv('MYSQLDATABASE');
    $user    = getenv('MYSQLUSER');
    $pass    = getenv('MYSQLPASSWORD');

} elseif (strpos($current_host, 'alwaysdata.net') !== false) {
    // ---------------------------------------------------------
    // 2. ALWAYS DATA ENVIRONMENT (Auto-detected via URL)
    // ---------------------------------------------------------
    // REPLACE THESE WITH YOUR EXACT ALWAYS DATA CREDENTIALS!
    $host = 'mysql-nammaride-free.alwaysdata.net'; 
    $port = '3306';                          // AlwaysData uses the standard port
    $db   = 'nammaride-free_nammaride_db';
    $user = 'nammaride-free';
    $pass = 'Nammaride@alwaysdata';      

} else {
    // ---------------------------------------------------------
    // 3. LOCALHOST ENVIRONMENT (Fallback for XAMPP/Laptop)
    // ---------------------------------------------------------
    $host = '127.0.0.1';
    $port = '3306';
    $db   = 'nammaride_db';
    $user = 'root';
    $pass = ''; // Default XAMPP password is empty
}

// ---------------------------------------------------------
// EXECUTE CONNECTION
// ---------------------------------------------------------
$charset = 'utf8mb4';
$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode([
        "error" => "Database connection failed",
        "environment_detected" => $current_host,
        "details" => $e->getMessage()
    ]);
    exit;
}
// DO NOT ADD A CLOSING PHP TAG HERE!
