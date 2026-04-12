<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
require 'db.php';

// Fetch the 10 routes with the exact column names from your image
$stmt = $pdo->query("SELECT route_id, destination_name, dest_lat, dest_lng FROM routes");
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
?>