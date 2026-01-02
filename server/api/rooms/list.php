<?php
require_once '../auth/cors.php'; // Keluar ke api/, masuk ke auth/
require_once '../../config/database.php'; // Keluar ke api/, keluar ke server/, masuk ke config/

header('Content-Type: application/json');

$q = $conn->query("SELECT * FROM rooms");
$data = [];
while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>