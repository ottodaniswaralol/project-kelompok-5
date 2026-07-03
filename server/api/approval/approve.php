<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
header('Content-Type: application/json');

try {
    $data        = json_decode(file_get_contents("php://input"), true);
    $approval_id = isset($data['approval_id']) ? (int)$data['approval_id'] : 0;
    $approver_id = isset($data['approver_id']) ? (int)$data['approver_id'] : 0;
    $notes       = isset($data['notes']) ? trim($data['notes']) : '';

    if ($approval_id == 0 || $approver_id == 0) {
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
        exit;
    }

    // Update approval jadi approved
    $stmt = $conn->prepare("UPDATE booking_approval 
                            SET status = 'approved', approver_id = ?, notes = ?, approved_at = NOW()
                            WHERE approval_id = ?");
    $stmt->bind_param("isi", $approver_id, $notes, $approval_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Cek step sekarang
        $step_stmt = $conn->prepare("SELECT step, booking_id FROM booking_approval WHERE approval_id = ?");
        $step_stmt->bind_param("i", $approval_id);
        $step_stmt->execute();
        $step_result = $step_stmt->get_result()->fetch_assoc();
        $booking_id  = $step_result['booking_id'];
        $step        = $step_result['step'];

        if ($step === 'bima') {
            // Teruskan ke GA
            $next_stmt = $conn->prepare("INSERT INTO booking_approval (booking_id, step, status) VALUES (?, 'ga', 'pending')");
            $next_stmt->bind_param("i", $booking_id);
            $next_stmt->execute();
        } else {
            // GA atau Marketing approve → final
            $final_stmt = $conn->prepare("UPDATE booking SET status = 'approved' WHERE booking_id = ?");
            $final_stmt->bind_param("i", $booking_id);
            $final_stmt->execute();
        }

        echo json_encode(["status" => "success", "message" => "Pengajuan berhasil disetujui"]);
    } else {
        echo json_encode(["status" => "error", "message" => "ID Approval tidak ditemukan"]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>