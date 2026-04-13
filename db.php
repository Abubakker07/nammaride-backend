<?php
// db.php — The Final Boss Edition
error_reporting(0);

// 1. Prioritize Railway's Private Network Environment Variables (Fastest & Most Secure)
$host    = getenv('MYSQLHOST');
$port    = getenv('MYSQLPORT');
$db      = getenv('MYSQLDATABASE');
$user    = getenv('MYSQLUSER');
$pass    = getenv('MYSQLPASSWORD');

// 2. Fallback to Public Credentials if environment variables aren't linked properly
if (!$host) {
    $host = 'metro.proxy.rlwy.net';
    $port = '19531';
    $db   = 'railway';
    $user = 'root';
    $pass = 'nQHMGkffOHiSPWcwjCVaOKsxWevsXusK';
}

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
    // CRITICAL FIX: We are now exposing exactly WHY Railway is blocking the connection
    echo json_encode([
        "error" => "Database connection failed",
        "railway_error_details" => $e->getMessage(),
        "host_attempted" => $host,
        "port_attempted" => $port
    ]);
    exit;
}
// DO NOT ADD A CLOSING PHP TAG HERE!
