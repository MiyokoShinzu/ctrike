<?php
header('Content-Type: application/json');
session_start();
include_once "../src/connection.php"; // uses $mysqli

// Optional: handle session-based UUID (adjust as needed)
$uuid = $_SESSION['uuid'] ?? 1; // fallback for testing

// Prepare SQL query safely
$stmt = $mysqli->prepare("
    SELECT 
        rear_tire_pressure,
        side_tire_pressure,
        front_tire_pressure,
        voltage,
        temperature,
        distance,
        vibration,
        datetime_received
    FROM telemetry_data
    WHERE uuid = ?
    ORDER BY datetime_received ASC
    LIMIT 7
");

if (!$stmt) {
    echo json_encode(["error" => "Prepare failed: " . $mysqli->error]);
    exit;
}

$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo json_encode(["error" => "Query failed: " . $mysqli->error]);
    exit;
}

$labels = [];
$speed = [];
$battery = [];
$temperature = [];
$time = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date("M d H:i", strtotime($row['datetime_received']));
    $battery[] = (float)$row['voltage'];
    $temperature[] = (float)$row['temperature'];
    $speed[] = (float)$row['distance'] / 10; // pseudo-speed
    $time[] = 1;
}

echo json_encode([
    "labels" => $labels,
    "speed" => $speed,
    "battery" => $battery,
    "temperature" => $temperature,
    "time" => $time
]);
