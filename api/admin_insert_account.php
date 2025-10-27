<?php
header('Content-Type: application/json');
include "../src/connection.php";

$response = ['success' => false, 'message' => ''];

// Read and decode JSON body
$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username   = trim($mysqli->real_escape_string($data['username'] ?? ''));
    $password   = trim($data['password'] ?? '');
    $vehicle_id = trim($mysqli->real_escape_string($data['vehicle_id'] ?? ''));
    $contact    = trim($mysqli->real_escape_string($data['contact'] ?? ''));
    $fullname   = trim($mysqli->real_escape_string($data['fullname'] ?? ''));
    $address    = trim($mysqli->real_escape_string($data['address'] ?? ''));
    $access     = 1; // default access (optional)

    if (empty($username) || empty($password)) {
        $response['message'] = "Username and password are required.";
    } else {
        // ✅ Check if username already exists
        $check_username_sql = "SELECT id FROM users WHERE username='$username'";
        $check_username_result = $mysqli->query($check_username_sql);

        // ✅ Check if vehicle ID already exists
        $check_vehicle_sql = "SELECT id FROM users WHERE vehicle_id='$vehicle_id'";
        $check_vehicle_result = $mysqli->query($check_vehicle_sql);

        if (
            $check_username_result && $check_username_result->num_rows > 0 &&
            $check_vehicle_result && $check_vehicle_result->num_rows > 0
        ) {
            $response['message'] = "Both username and vehicle ID already exist.";
        } elseif ($check_username_result && $check_username_result->num_rows > 0) {
            $response['message'] = "Username already exists.";
        } elseif ($check_vehicle_result && $check_vehicle_result->num_rows > 0) {
            $response['message'] = "Vehicle ID already exists.";
        } else {
            // ✅ Proceed with insert
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, password, vehicle_id, contact, fullname, address, access)
                           VALUES ('$username', '$hashed_password', '$vehicle_id', '$contact', '$fullname', '$address', '$access')";

            if ($mysqli->query($insert_sql)) {
                $response['success'] = true;
                $response['message'] = "Account successfully created.";
            } else {
                $response['message'] = "Database error: " . $mysqli->error;
            }
        }
    }
} else {
    $response['message'] = "Invalid request method. Use POST.";
}

$mysqli->close();
echo json_encode($response);
