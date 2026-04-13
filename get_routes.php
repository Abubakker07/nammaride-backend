<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
error_reporting(0);
require 'db.php';

try {
    $stmt = $pdo->query("SELECT route_id, destination_name, dest_lat, dest_lng FROM routes");
    $routes = $stmt->fetchAll();
    
    // The "?: []" ensures that if fetchAll returns false/null, it safely outputs an empty array.
    echo json_encode($routes ?: []); 
} catch (PDOException $e) {
    // Even on error, return an empty array to prevent Android from crashing
    echo json_encode([]);
}
