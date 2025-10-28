<?php
header('Content-Type: application/json');
include '../src/connection.php';

// Check if POST data exists
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Invalid request method']);
    exit;
}

// Extract form data safely
$fields = [
    'motor_date', 'motor_condition',
    'battery_date', 'battery_condition',
    'rear_tire_date', 'rear_tire_condition',
    'side_tire_date', 'side_tire_condition',
    'front_tire_date', 'front_tire_condition'
];

$data = [];
foreach ($fields as $field) {
    $data[$field] = isset($_POST[$field]) ? $_POST[$field] : null;
}

// Create the setup table if not exists
$createTable = "
    CREATE TABLE IF NOT EXISTS installation_setup (
        id INT AUTO_INCREMENT PRIMARY KEY,
        motor_date DATE,
        motor_condition VARCHAR(50),
        battery_date DATE,
        battery_condition VARCHAR(50),
        rear_tire_date DATE,
        rear_tire_condition VARCHAR(50),
        side_tire_date DATE,
        side_tire_condition VARCHAR(50),
        front_tire_date DATE,
        front_tire_condition VARCHAR(50),
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    );
";
$mysqli->query($createTable);

// Check if record exists (we’ll only have one setup row)
$result = $mysqli->query("SELECT id FROM installation_setup LIMIT 1");

if ($result && $result->num_rows > 0) {
    // Update existing setup
    $stmt = $mysqli->prepare("
        UPDATE installation_setup SET 
            motor_date=?, motor_condition=?, 
            battery_date=?, battery_condition=?, 
            rear_tire_date=?, rear_tire_condition=?, 
            side_tire_date=?, side_tire_condition=?, 
            front_tire_date=?, front_tire_condition=?,
            updated_at=NOW()
        WHERE id=1
    ");
    $stmt->bind_param(
        "ssssssssss",
        $data['motor_date'], $data['motor_condition'],
        $data['battery_date'], $data['battery_condition'],
        $data['rear_tire_date'], $data['rear_tire_condition'],
        $data['side_tire_date'], $data['side_tire_condition'],
        $data['front_tire_date'], $data['front_tire_condition']
    );
} else {
    // Insert new setup
    $stmt = $mysqli->prepare("
        INSERT INTO installation_setup (
            motor_date, motor_condition,
            battery_date, battery_condition,
            rear_tire_date, rear_tire_condition,
            side_tire_date, side_tire_condition,
            front_tire_date, front_tire_condition
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssssssssss",
        $data['motor_date'], $data['motor_condition'],
        $data['battery_date'], $data['battery_condition'],
        $data['rear_tire_date'], $data['rear_tire_condition'],
        $data['side_tire_date'], $data['side_tire_condition'],
        $data['front_tire_date'], $data['front_tire_condition']
    );
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$mysqli->close();
