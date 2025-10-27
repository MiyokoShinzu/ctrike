<?php
header('Content-Type: application/json');
session_start();
include_once "../src/connection.php";

// Assuming you store the user UUID in session like: $_SESSION['uuid']
$uuid = $_SESSION['uuid'] ?? 1; // fallback for testing

$sql = "SELECT 
            rear_tire_pressure,
            side_tire_pressure,
            front_tire_pressure,
            voltage,
            temperature,
            distance,
            vibration,
            datetime_received
        FROM telemetry
        WHERE uuid = ?
        ORDER BY datetime_received ASC
        LIMIT 7";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uuid);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$speed = [];
$battery = [];
$temperature = [];
$time = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date("M d H:i", strtotime($row['datetime_received']));
    $battery[] = (float)$row['voltage'];
    $temperature[] = (float)$row['temperature'];
    $speed[] = (float)$row['distance'] / 10; 
    $time[] = 1;
}

echo json_encode([
    "labels" => $labels,
    "speed" => $speed,
    "battery" => $battery,
    "temperature" => $temperature,
    "time" => $time
]);
