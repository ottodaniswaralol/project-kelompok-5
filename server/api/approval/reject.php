<?php
// 1. CORS HARDCODE (Wajib buat metode POST)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Handle Preflight Request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Koneksi Database
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // 3. Ambil Input & Validasi (Biar gak "Undefined Index")
    $approval_id = isset($_POST['approval_id']) ? (int)$_POST['approval_id'] : 0;
    $approver_id = isset($_POST['approver_id']) ? (int)$_POST['approver_id'] : 0;
    $notes       = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    // Cek ID Valid
    if ($approval_id == 0 || $approver_id == 0) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap (ID Kosong)"]);
        exit;
    }

    // 4. Query Aman (Prepared Statement)
    // Update status jadi 'rejected', catat siapa yang nolak, dan kapan
    $sql = "UPDATE booking_approval 
            SET status = 'rejected', 
                approver_id = ?, 
                notes = ?, 
                approved_at = NOW()
            WHERE approval_id = ?";

    $stmt = $conn->prepare($sql);
    
    // "isi" artinya: integer, string, integer
    $stmt->bind_param("isi", $approver_id, $notes, $approval_id);

    if ($stmt->execute()) {
        // Cek apakah ada baris yang terupdate
        if ($stmt->affected_rows > 0) {
            echo json_encode(["status" => "success", "message" => "Pengajuan berhasil ditolak"]);
        } else {
            // Kalau 0, berarti approval_id tidak ditemukan di database
            echo json_encode(["status" => "error", "message" => "ID Approval tidak ditemukan"]);
        }
    } else {
        throw new Exception("Gagal eksekusi query");
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Server Error: " . $e->getMessage()]);
}
?>