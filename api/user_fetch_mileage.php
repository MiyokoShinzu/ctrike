<?php
include "../src/connection.php";
header('Content-Type: application/json');

// Get start and end date from AJAX
$start = $_GET['start'] ?? '';
$end = $_GET['end'] ?? '';

if (!$start || !$end) {
	echo json_encode(["error" => "Missing start or end date"]);
	exit;
}

// Query: sum of distance grouped per day
$query = $mysqli->prepare("
    SELECT 
        DATE(datetime_received) AS date,
        SUM(distance) AS total_distance
    FROM telemetry_data
    WHERE DATE(datetime_received) BETWEEN ? AND ?
    GROUP BY DATE(datetime_received)
    ORDER BY DATE(datetime_received)
");

$query->bind_param("ss", $start, $end);
$query->execute();
$result = $query->get_result();

$labels = [];
$mileage = [];

// Collect results
while ($row = $result->fetch_assoc()) {
	$labels[] = $row['date'];
	$mileage[] = floatval($row['total_distance']);
}

// Handle empty results (so chart doesn’t break)
if (empty($labels)) {
	$period = new DatePeriod(
		new DateTime($start),
		new DateInterval('P1D'),
		(new DateTime($end))->modify('+1 day')
	);

	foreach ($period as $date) {
		$labels[] = $date->format('Y-m-d');
		$mileage[] = 0;
	}
}

// Send JSON
echo json_encode([
	"labels" => $labels,
	"mileage" => $mileage
]);
