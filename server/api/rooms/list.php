<?php
// ==========================================
// 1. HEADER CORS "PAKSA" (Wajib di Paling Atas)
// ==========================================
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Matikan Preflight Request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==========================================
// 2. KONEKSI & LOGIC
// ==========================================

// Sesuaikan path ini jika perlu (keluar api/ -> keluar server/ -> masuk config/)
require_once '../../config/database.php';

header('Content-Type: application/json');

$q = $conn->query("SELECT * FROM rooms");
$data = [];
while ($row = $q->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>