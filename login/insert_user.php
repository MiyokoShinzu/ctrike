<?php
header('Content-Type: application/json');
include "../src/connection.php";

$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $username   = trim($mysqli->real_escape_string($_GET['username'] ?? ''));
    $password   = trim($_GET['password'] ?? '');
    $vehicle_id = trim($mysqli->real_escape_string($_GET['vehicle_id'] ?? ''));
    $access = trim($mysqli->real_escape_string($_GET['access'] ?? ''));
    $contact = trim($mysqli->real_escape_string($_GET['contact'] ?? ''));
    $fullname = trim($mysqli->real_escape_string($_GET['fullname'] ?? ''));
    $address = trim($mysqli->real_escape_string($_GET['address'] ?? ''));

    if (empty($username) || empty($password)) {
        $response['message'] = "Username and password are required.";
    } else {
        $check_sql = "SELECT * FROM users WHERE username='$username'";
        $check_result = $mysqli->query($check_sql);

        if ($check_result->num_rows > 0) {
            $response['message'] = "Username already exists.";
        } else {
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
    $response['message'] = "Invalid request method. Use GET.";
}

$mysqli->close();
echo json_encode($response);
