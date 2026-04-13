<?php
// db.php — Railway.app Public Network Connection
error_reporting(0);

// Hardcoded credentials directly from Railway Public Network tab
$host    = 'metro.proxy.rlwy.net';
$port    = '19531';
$db      = 'railway';
$user    = 'root';
$pass    = 'nQHMGkffOHiSPWcwjCVaOKsxWevsXusK';
$charset = 'utf8mb4';

// The DSN string now explicitly includes the custom port
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
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}
// DO NOT ADD A CLOSING PHP TAG HERE!
