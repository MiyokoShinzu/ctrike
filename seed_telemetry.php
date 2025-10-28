<?php
include "./src/connection.php";

$vehicle_id = 'v-1000';

// Define date range (October 20–28)
$start = new DateTime('2025-10-20');
$end = new DateTime('2025-10-28');

$query = $mysqli->prepare("
    INSERT INTO telemetry_data 
    (vehicle_id, rear_tire_pressure, side_tire_pressure, front_tire_pressure, voltage, temperature, distance, vibration, datetime_received)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

if (!$query) {
	die("SQL prepare failed: " . $mysqli->error);
}

$currentDistance = 0.0;

for ($date = clone $start; $date <= $end; $date->modify('+1 day')) {
	// Generate 8–10 readings per day
	$numReadings = rand(8, 10);

	for ($i = 0; $i < $numReadings; $i++) {
		$rear = rand(300, 350) / 10;  // 30.0–35.0
		$side = $rear - rand(1, 5) / 10; // slightly lower
		$front = $rear - rand(1, 5) / 10;
		$voltage = rand(700, 725) / 10; // 70.0–72.5
		$temperature = rand(340, 420) / 10; // 34.0–42.0 °C
		$currentDistance += rand(40, 100); // distance accumulates daily
		$vibration = rand(2, 9) / 100; // 0.02–0.09
		$time = sprintf("%02d:%02d:%02d", rand(6, 18), rand(0, 59), rand(0, 59));
		$datetime_received = $date->format('Y-m-d') . " " . $time;

		$query->bind_param(
			"sddddddds",
			$vehicle_id,
			$rear,
			$side,
			$front,
			$voltage,
			$temperature,
			$currentDistance,
			$vibration,
			$datetime_received
		);

		if (!$query->execute()) {
			echo "❌ Error inserting for {$datetime_received}: " . $query->error . "<br>";
		} else {
			echo "✅ Inserted reading for {$datetime_received}<br>";
		}
	}
}

$query->close();
$mysqli->close();

echo "<hr><b>✅ All telemetry data generated successfully for vehicle v-1000 (Oct 20–28, 2025).</b>";
