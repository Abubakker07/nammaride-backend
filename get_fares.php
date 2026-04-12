<?php
// get_fares.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");

require 'db.php';

if (!isset($_GET['route_id']) || !is_numeric($_GET['route_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Valid route_id is required."]);
    exit;
}

$route_id = (int)$_GET['route_id'];

// 1. Fetch Route Data
$stmt = $pdo->prepare("SELECT distance_km, est_time_min, destination_name FROM routes WHERE route_id = ?");
$stmt->execute([$route_id]);
$route = $stmt->fetch();

if (!$route) {
    http_response_code(404);
    echo json_encode(["error" => "Route not found."]);
    exit;
}

$D = (float)$route['distance_km'];
$T = (int)$route['est_time_min'];

// 2. Fetch Base Pricing Constraints
$stmt = $pdo->query("SELECT * FROM vehicle_pricing");
$pricing_data = $stmt->fetchAll();

$fare_results = [];

// 3. AI Smart Surge Logic (Time-Aware)
date_default_timezone_set('Asia/Kolkata');
$current_hour = (int)date('H');

$is_peak_hour = false;
$surge_multiplier = 1.0;
$surge_reason = "Normal fares in your area";

if (($current_hour >= 8 && $current_hour <= 11) || ($current_hour >= 17 && $current_hour <= 21)) {
    $is_peak_hour = true;
    $surge_multiplier = 1.25; 
    $surge_reason = "Fares are slightly higher due to peak traffic";
}

// --- NEW: Prepare Driver Query outside the loop for high performance ---
// This safely picks 1 random driver assigned to this specific route and vehicle type
$driverStmt = $pdo->prepare("SELECT driver_name, vehicle_model, license_plate, rating, otp FROM drivers WHERE route_id = ? AND vehicle_type = ? ORDER BY RAND() LIMIT 1");

// 4. Execute NammaRide Pricing Algorithm
foreach ($pricing_data as $vehicle) {
    $v_type = $vehicle['vehicle_type'];
    
    // Airport Geofence Restriction
    if (stripos($route['destination_name'], 'Airport') !== false) {
        if ($v_type === 'Bike' || $v_type === 'Auto') {
            continue; // Skip this loop iteration entirely
        }
    }
    
    // --- NEW: Fetch Dynamic Local Driver ---
    $driverStmt->execute([$route_id, $v_type]);
    $assigned_driver = $driverStmt->fetch(PDO::FETCH_ASSOC);
    
    // Fallback safety net just in case the database is missing a driver for this route
    if (!$assigned_driver) {
        $assigned_driver = [
            "driver_name" => "Srinivas Gowda",
            "vehicle_model" => "Toyota Etios",
            "license_plate" => "KA 01 AB 8921",
            "rating" => "4.9",
            "otp" => "4192"
        ];
    }
    
    // Determine free distance and time included in the base fare
    $base_km_included = 0;
    $free_mins_included = 0;
    
    if ($v_type === 'Auto') {
        $base_km_included = 2;
        $free_mins_included = 5;
    } elseif ($v_type === 'Mini Cab' || $v_type === 'SUV') {
        $base_km_included = 4;
        $free_mins_included = 5;
    } elseif ($v_type === 'Bike') {
        $base_km_included = 0; 
        $free_mins_included = 0;
    }

    // Calculate ONLY the chargeable distance and time
    $chargeable_distance = max(0, $D - $base_km_included);
    $chargeable_time = max(0, $T - $free_mins_included);

    // Aggregator Distance Tapering Logic (Long Distance Discount)
    $effective_rate_per_km = (float)$vehicle['rate_per_km'];
    if ($D > 10.0) {
        $effective_rate_per_km = $effective_rate_per_km * 0.85; 
    }

    $base_distance_charge = $chargeable_distance * $effective_rate_per_km;
    $base_time_charge = $chargeable_time * (float)$vehicle['rate_per_min'];
    $B = (float)$vehicle['base_fare'];
    $F_min = (float)$vehicle['min_fare'];
    
    // NammaRide Fixed Booking Fee
    $booking_fee = (float)$vehicle['platform_fee']; 

    // Apply Smart Surge
    $surged_variable_cost = ($base_distance_charge + $base_time_charge) * $surge_multiplier;
    
    // Dynamic 2026 Karnataka GST Calculation
    $subtotal = $B + $surged_variable_cost + $booking_fee;
    
    // Legally mandated 5% GST on aggregator rides
    $gst_tax = $subtotal * 0.05; 
    
    $raw_total = $subtotal + $gst_tax;
    $final_fare = max($raw_total, $F_min);

    // Build the Transparent UI Object
    $fare_results[] = [
        "vehicle_type" => $v_type,
        "display_name" => "Namma " . $v_type, 
        "destination" => $route['destination_name'],
        "total_fare" => round($final_fare, 2),
        "surge_active" => $is_peak_hour,
        "surge_message" => $surge_reason,
        "driver_details" => $assigned_driver, // <-- ATTACHED THE DATABASE DRIVER HERE!
        "breakdown_ui" => [
            "base_fare" => round($B, 2),
            "distance_charge" => round($base_distance_charge * $surge_multiplier, 2),
            "time_charge" => round($base_time_charge * $surge_multiplier, 2),
            "booking_fee" => round($booking_fee, 2),
            "government_tax_5_percent" => round($gst_tax, 2),
            "surge_applied" => round($surged_variable_cost - ($base_distance_charge + $base_time_charge), 2)
        ]
    ];
}

// 5. The O(n log n) Quick Sort Implementation
function quickSortFares($array) {
    $length = count($array);
    if ($length <= 1) {
        return $array;
    }
    $pivot = $array[0];
    $left = $right = array();
    for ($i = 1; $i < $length; $i++) {
        if ($array[$i]['total_fare'] < $pivot['total_fare']) {
            $left[] = $array[$i];
        } else {
            $right[] = $array[$i];
        }
    }
    return array_merge(quickSortFares($left), array($pivot), quickSortFares($right));
}

// 6. Sort and Output JSON
$sorted_fares = quickSortFares($fare_results);

echo json_encode([
    "status" => "success",
    "route_id" => $route_id,
    "options" => $sorted_fares
]);
?>