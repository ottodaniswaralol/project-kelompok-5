<?php
header('Content-Type: application/json');
include '../../config/database.php';

$data = [];
$q = $conn->query("SELECT * FROM rooms");

while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>
