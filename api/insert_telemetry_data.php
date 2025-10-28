<?php
include '../src/connection.php';
date_default_timezone_set('Asia/Manila');
$d = date('Y-m-d H:i:s');
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $rear_tire_pressure = $_GET['rear_tire_pressure'];
    $side_tire_pressure = $_GET['side_tire_pressure'];
    $front_tire_pressure = $_GET['front_tire_pressure'];
    $vibration = $_GET['vibration'];
    $distance = $_GET['distance'];
    $voltage = $_GET['voltage'];
    $temperature = $_GET['temperature'];
    $vehicle_id = $_GET['vehicle_id'];
    $sql = "Insert into telemetry_data(rear_tire_pressure, side_tire_pressure, front_tire_pressure, vibration, distance, voltage, temperature, datetime_received, vehicle_id) 
    values('$rear_tire_pressure', '$side_tire_pressure', '$front_tire_pressure', '$vibration', '$distance', '$voltage', '$temperature', '$d', '$vehicle_id')";
    $result = $mysqli->query($sql);
    if ($result) {
        echo json_encode(['success' => '1']);
    } else {
        echo json_encode(['success' => '0']);
    }
}
?>