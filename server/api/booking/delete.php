<?php
// FILE: api/booking/delete.php

// Matikan error HTML biar bersih
ini_set('display_errors', 0);

// --- HEADER CORS LENGKAP (WAJIB ADA) ---
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
// BARIS INI YANG MEMPERBAIKI ERROR KAMU:
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle Request Preflight (Browser tanya: "Boleh kirim JSON gak?")
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

include_once '../../config/database.php';

// Ambil data JSON
$data = json_decode(file_get_contents("php://input"));
$id = $data->id ?? null;

if (!$id) {
    echo json_encode(["status" => "error", "message" => "ID Kosong"]);
    exit;
}

try {
    // FORCE DELETE (Matikan Foreign Key Check)
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Hapus berdasarkan booking_id
    $stmt = $conn->prepare("DELETE FROM booking WHERE booking_id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Hapus juga data di approval biar bersih (Opsional)
        $conn->query("DELETE FROM booking_approval WHERE booking_id = $id");
        
        echo json_encode(["status" => "success", "message" => "Terhapus"]);
    } else {
        throw new Exception($stmt->error);
    }

    // Nyalakan lagi Foreign Key Check
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

} catch (Exception $e) {
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>