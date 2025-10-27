<?php
header('Content-Type: application/json');
include '../src/connection.php';

if (!isset($_GET['date'])) {
	echo json_encode(['error' => 'Missing date']);
	exit;
}

$date = $_GET['date'];

// Fetch hourly average tire pressures for that date
$query = "
    SELECT 
        DATE_FORMAT(datetime_received, '%H:00') AS hour_label,
        ROUND(AVG(rear_tire_pressure), 2) AS avg_rear,
        ROUND(AVG(side_tire_pressure), 2) AS avg_side,
        ROUND(AVG(front_tire_pressure), 2) AS avg_front
    FROM telemetry_data
    WHERE DATE(datetime_received) = ?
    GROUP BY HOUR(datetime_received)
    ORDER BY HOUR(datetime_received)
";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
	echo json_encode(['error' => 'SQL prepare failed: ' . $mysqli->error]);
	exit;
}

$stmt->bind_param("s", $date);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$rear = [];
$side = [];
$front = [];

while ($row = $result->fetch_assoc()) {
	$labels[] = $row['hour_label'];
	$rear[] = (float)$row['avg_rear'];
	$side[] = (float)$row['avg_side'];
	$front[] = (float)$row['avg_front'];
}

echo json_encode([
	'labels' => $labels,
	'rear' => $rear,
	'side' => $side,
	'front' => $front
]);

$stmt->close();
$mysqli->close();
