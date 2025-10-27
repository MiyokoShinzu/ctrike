<?php
header('Content-Type: application/json');
session_start();
include_once "../src/connection.php"; // contains $mysqli

// Optional: get UUID (defaults to 1 for testing)
$uuid = $_SESSION['uuid'] ?? 1;

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

// Build query dynamically (with or without date filters)
if ($start && $end) {
    $stmt = $mysqli->prepare("
        SELECT rear_tire_pressure, side_tire_pressure, front_tire_pressure,
               voltage, temperature, distance, vibration, datetime_received
        FROM telemetry_data
        WHERE uuid = ?
          AND DATE(datetime_received) BETWEEN ? AND ?
        ORDER BY datetime_received ASC
    ");
    $stmt->bind_param("sss", $uuid, $start, $end);
} else {
    $stmt = $mysqli->prepare("
        SELECT rear_tire_pressure, side_tire_pressure, front_tire_pressure,
               voltage, temperature, distance, vibration, datetime_received
        FROM telemetry_data
        WHERE uuid = ?
        ORDER BY datetime_received ASC
        LIMIT 7
    ");
    $stmt->bind_param("s", $uuid);
}

$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$battery = [];
$vibration = [];
$speed = [];
$temperature = [];
$tire = [];
$time = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = date("M d H:i", strtotime($row['datetime_received']));
    $battery[] = (float)$row['voltage'];
    $vibration[] = (float)$row['vibration'];
    $speed[] = (float)$row['distance'] / 10; // pseudo-speed
    $temperature[] = (float)$row['temperature'];
    $tire[] = ($row['rear_tire_pressure'] + $row['side_tire_pressure'] + $row['front_tire_pressure']) / 3;
    $time[] = rand(1, 10) / 2; // dummy hours for now
}

echo json_encode([
    "labels" => $labels,
    "battery" => $battery,
    "vibration" => $vibration,
    "speed" => $speed,
    "temperature" => $temperature,
    "tire" => $tire,
    "time" => $time
]);
?>
