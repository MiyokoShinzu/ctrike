<?php
header('Content-Type: application/json');
session_start();
include_once "../src/connection.php";

$uuid = $_SESSION['vehicle_id'] ?? 1;
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$start || !$end) {
    echo json_encode(["error" => "Missing start or end date"]);
    exit;
}

$stmt = $mysqli->prepare("
    SELECT rear_tire_pressure, side_tire_pressure, front_tire_pressure,
           voltage, temperature, distance, vibration, datetime_received
    FROM telemetry_data
    WHERE vehicle_id = ?
      AND DATE(datetime_received) = ?
    ORDER BY datetime_received DESC LIMIT 12
");
$stmt->bind_param("ss", $uuid, $start);
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
    $labels[] = date("H:i", strtotime($row['datetime_received']));
    $battery[] = (float)$row['voltage'];
    $vibration[] = (float)$row['vibration'];
    $speed[] = (float)$row['distance'] / 10;
    $temperature[] = (float)$row['temperature'];
    $tire[] = ($row['rear_tire_pressure'] + $row['side_tire_pressure'] + $row['front_tire_pressure']) / 3;
    $time[] = rand(1, 10) / 2;
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
