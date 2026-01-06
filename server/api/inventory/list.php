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

// CATATAN: Kode asli kamu query ke tabel 'rooms'. 
// Jika ini benar untuk INVENTORY, ganti 'rooms' menjadi 'inventory'.
$table_name = 'inventory'; // Ganti jadi 'rooms' kalau ini file room list

// Saya ubah sedikit biar dinamis, atau kamu bisa tulis manual query-nya:
// $q = $conn->query("SELECT * FROM inventory"); 

// Menggunakan query asli kamu (tapi pastikan tabelnya benar):
$q = $conn->query("SELECT * FROM rooms"); 

$data = [];
if ($q) {
    while ($row = $q->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>