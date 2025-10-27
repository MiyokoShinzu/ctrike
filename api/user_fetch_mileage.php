<?php
include "../src/connection.php";
header('Content-Type: application/json');

$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$start || !$end) {
	echo json_encode(["error" => "Missing start or end date"]);
	exit;
}

$query = $mysqli->prepare("
    SELECT 
        HOUR(datetime_received) AS hour_label,
        SUM(distance) AS total_distance
    FROM telemetry_data
    WHERE DATE(datetime_received) BETWEEN ? AND ?
    GROUP BY HOUR(datetime_received)
    ORDER BY hour_label
");

$query->bind_param("ss", $start, $end);
$query->execute();
$result = $query->get_result();

$labels = [];
$mileage = [];

while ($row = $result->fetch_assoc()) {
	$labels[] = sprintf("%02d:00", $row['hour_label']);
	$mileage[] = floatval($row['total_distance']);
}

if (empty($labels)) {
	for ($h = 0; $h < 24; $h++) {
		$labels[] = sprintf("%02d:00", $h);
		$mileage[] = 0;
	}
}

echo json_encode([
	"labels" => $labels,
	"mileage" => $mileage
]);
