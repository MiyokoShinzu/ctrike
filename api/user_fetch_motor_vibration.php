<?php
header('Content-Type: application/json');
include '../src/connection.php'; // adjust path if needed

if (!isset($_GET['start']) || !isset($_GET['end'])) {
	echo json_encode(['error' => 'Missing start or end date']);
	exit;
}

$start = $_GET['start'];
$end = $_GET['end'];

// Prepare query: fetch average vibration per day
$query = "
    SELECT 
        DATE(datetime_received) AS date_label,
        ROUND(AVG(vibration), 2) AS avg_vibration
    FROM telemetry_data
    WHERE DATE(datetime_received) BETWEEN ? AND ?
    GROUP BY DATE(datetime_received)
    ORDER BY DATE(datetime_received) ASC
";

$stmt = $mysqli->prepare($query);
if (!$stmt) {
	echo json_encode(['error' => 'SQL prepare failed: ' . $mysqli->error]);
	exit;
}

$stmt->bind_param("ss", $start, $end);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$vibration = [];

while ($row = $result->fetch_assoc()) {
	$labels[] = $row['date_label'];
	$vibration[] = (float)$row['avg_vibration'];
}

echo json_encode([
	'labels' => $labels,
	'vibration' => $vibration
]);

$stmt->close();
$mysqli->close();
