<?php
include "../src/connection.php";
header('Content-Type: application/json');

// Get selected date
$date = $_GET['date'] ?? '';

if (!$date) {
    echo json_encode(["error" => "Missing date parameter"]);
    exit;
}

// Query: average voltage grouped by hour for the selected day
$query = $mysqli->prepare("
    SELECT 
        DATE_FORMAT(datetime_received, '%H:00') AS hour,
        AVG(voltage) AS avg_voltage
    FROM telemetry_data
    WHERE DATE(datetime_received) = ?
    GROUP BY HOUR(datetime_received)
    ORDER BY HOUR(datetime_received)
");
$query->bind_param("s", $date);
$query->execute();
$result = $query->get_result();

$labels = [];
$battery = [];

// Collect hourly averages
while ($row = $result->fetch_assoc()) {
    $labels[] = $row['hour'];
    $battery[] = round(floatval($row['avg_voltage']), 2);
}

// Fill empty hours (0–23)
if (empty($labels)) {
    for ($h = 0; $h < 24; $h++) {
        $labels[] = sprintf("%02d:00", $h);
        $battery[] = 0;
    }
}

echo json_encode([
    "labels" => $labels,
    "battery" => $battery
]);
?>
