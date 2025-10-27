<?php
header('Content-Type: application/json');
include '../src/connection.php';

if (!isset($_GET['date'])) {
	echo json_encode(['error' => 'Missing date']);
	exit;
}

$date = $_GET['date'];

// Query: fetch average vibration every hour of the selected day
$query = "
    SELECT 
        DATE_FORMAT(datetime_received, '%H:00') AS hour_label,
        ROUND(AVG(vibration), 2) AS avg_vibration
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
$vibration = [];

while ($row = $result->fetch_assoc()) {
	$labels[] = $row['hour_label'];
	$vibration[] = (float)$row['avg_vibration'];
}

echo json_encode([
	'labels' => $labels,
	'vibration' => $vibration
]);

$stmt->close();
$mysqli->close();
