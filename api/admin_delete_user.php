<?php
include '../src/connection.php';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $id = $_GET['id'];
    $sql = "Delete from users where id = '$id'";
    $result = $mysqli->query($sql);
    if ($result) {
        echo json_encode(['success' => '1']);
    } else {
        echo json_encode(['success' => '0']);
    }
}
$mysqli->close();
?>