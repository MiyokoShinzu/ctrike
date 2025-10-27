<?php
include "../src/connection.php";
header('Content-Type: application/json');

// Get week range
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$start || !$end) {
    echo json_encode(["error" => "Missing start or end date"]);
    exit;
}

// Query daily average voltage
$query = $mysqli->prepare("
    SELECT 
        DATE(datetime_received) AS date,
        AVG(voltage) AS avg_voltage
    FROM telemetry_data
    WHERE DATE(datetime_received) BETWEEN ? AND ?
    GROUP BY DATE(datetime_received)
    ORDER BY DATE(datetime_received)
");
$query->bind_param("ss", $start, $end);
$query->execute();
$result = $query->get_result();

$labels = [];
$battery = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['date'];
    $battery[] = round(floatval($row['avg_voltage']), 2);
}

// Fill empty days (so the chart still displays a week)
if (empty($labels)) {
    $period = new DatePeriod(
        new DateTime($start),
        new DateInterval('P1D'),
        (new DateTime($end))->modify('+1 day')
    );
    foreach ($period as $date) {
        $labels[] = $date->format('Y-m-d');
        $battery[] = 0;
    }
}

echo json_encode([
    "labels" => $labels,
    "battery" => $battery
]);
?>
