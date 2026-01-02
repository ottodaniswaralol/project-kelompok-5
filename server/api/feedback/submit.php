<?php
// 1. Panggil CORS paling atas (Keluar 2 tingkat: feedback/ -> api/ -> auth/)
require_once '../auth/cors.php'; 

// 2. Koneksi Database (Keluar 2 tingkat ke config)
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // 3. Baca data JSON dari React
    $data = json_decode(file_get_contents("php://input"), true);

    $booking_id = $data['booking_id'] ?? null;
    $user_id = $data['user_id'] ?? null;
    $rating = $data['rating'] ?? 0;
    $message = $data['message'] ?? '';

    if (!$booking_id || !$user_id) {
        throw new Exception("Data booking atau user tidak lengkap");
    }

    // 4. Gunakan Prepared Statement agar aman dari SQL Injection & Error Karakter
    $stmt = $conn->prepare("INSERT INTO booking_feedback (booking_id, user_id, rating, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $booking_id, $user_id, $rating, $message);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Feedback berhasil dikirim"]);
    } else {
        throw new Exception($stmt->error);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => $e->getMessage()
    ]);
}
?>
