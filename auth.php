<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");

// Turn off HTML error reporting
error_reporting(0);

require 'db.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->firebase_uid) || !isset($data->phone_number)) {
    http_response_code(400);
    echo json_encode(["error" => "Missing Firebase UID or Phone Number"]);
    exit;
}

$uid = $data->firebase_uid;
$phone = $data->phone_number;

try {
    $stmt = $pdo->prepare("SELECT id, phone_number FROM users WHERE firebase_uid = ?");
    $stmt->execute([$uid]);
    $user = $stmt->fetch();

    if ($user) {
        echo json_encode([
            "status" => "success",
            "message" => "Login successful",
            "is_new_user" => false,
            "user" => [
                "local_id" => $user['id'],
                "phone" => $user['phone_number']
            ]
        ]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (firebase_uid, phone_number) VALUES (?, ?)");
        $stmt->execute([$uid, $phone]);
        
        $new_local_id = $pdo->lastInsertId();
        
        echo json_encode([
            "status" => "success",
            "message" => "Registration successful",
            "is_new_user" => true,
            "user" => [
                "local_id" => $new_local_id,
                "phone" => $phone
            ]
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error"]);
}
// DO NOT ADD A CLOSING PHP TAG HERE!