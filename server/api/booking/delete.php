<?php
// FILE: api/booking/delete.php

<?php
require_once '../auth/cors.php'; 
require_once '../../config/database.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "ID Kosong"]);
    exit;
}

$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$stmt = $conn->prepare("DELETE FROM booking WHERE booking_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo json_encode(["status" => "success"]);
?>