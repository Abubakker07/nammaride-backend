<?php
// db.php — Railway.app compatible
// Railway auto-injects these exact env variable names when you add a MySQL plugin
error_reporting(0);

$host    = getenv('MYSQLHOST')     ?: 'localhost';
$port    = getenv('MYSQLPORT')     ?: '3306';
$db      = getenv('MYSQLDATABASE') ?: 'nammaride_db';
$user    = getenv('MYSQLUSER')     ?: 'root';
$pass    = getenv('MYSQLPASSWORD') ?: '';
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
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}
// DO NOT ADD A CLOSING PHP TAG HERE!