<?php
// 1. CORS HARDCODE (Wajib buat metode POST dari React/Frontend)
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
    // 3. Baca data JSON dari Frontend
    $data = json_decode(file_get_contents("php://input"), true);

    // 4. Validasi & Sanitasi Input
    $booking_id = isset($data['booking_id']) ? (int)$data['booking_id'] : 0;
    $user_id    = isset($data['user_id']) ? (int)$data['user_id'] : 0;
    $rating     = isset($data['rating']) ? (int)$data['rating'] : 0;
    $message    = isset($data['message']) ? trim($data['message']) : '';

    // Pastikan ID valid
    if ($booking_id == 0 || $user_id == 0) {
        http_response_code(400); // Bad Request
        echo json_encode(["status" => "error", "message" => "Data booking atau user tidak valid"]);
        exit;
    }

    // 5. Query Insert (Tetap pakai Prepared Statement - Good Job!)
    $stmt = $conn->prepare("INSERT INTO booking_feedback (booking_id, user_id, rating, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    // Note: Saya tambah 'NOW()' di query untuk created_at kalau kolomnya ada.
    // Kalau kolom created_at di db lo settingannya 'CURRENT_TIMESTAMP' otomatis, hapus ', created_at' dan ', NOW()'
    
    // Bind param: integer, integer, integer, string
    $stmt->bind_param("iiis", $booking_id, $user_id, $rating, $message);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Terima kasih! Feedback berhasil dikirim."]);
    } else {
        throw new Exception("Gagal menyimpan feedback: " . $stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Server Error: " . $e->getMessage()
    ]);
}
?>