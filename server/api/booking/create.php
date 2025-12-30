<?php
// MATIKAN ERROR HTML BIAR JSON BERSIH
ini_set('display_errors', 0);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

include_once '../../config/database.php';

try {
    // 1. Ambil Data Frontend
    $user_id = $_POST['user_id'] ?? 1;
    $event_name = $_POST['event_name'] ?? '';
    $org = $_POST['organization'] ?? '';
    $pic = $_POST['pic'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $desc = $_POST['event_description'] ?? '';
    $start_datetime = $_POST['start_datetime'] ?? '';
    $end_datetime = $_POST['end_datetime'] ?? '';
    $rooms_json = $_POST['rooms'] ?? '[]';

    // 2. Mulai Transaksi (Wajib karena isi 2 tabel)
    $conn->begin_transaction();

    // A. INSERT KE TABEL BOOKING (Data Kegiatan)
    // Perhatikan: Kita TIDAK insert status disini, karena status ada di tabel sebelah
    $sql1 = "INSERT INTO booking (user_id, event_name, organization, pic, phone, event_description, start_datetime, end_datetime, rooms) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("issssssss", $user_id, $event_name, $org, $pic, $phone, $desc, $start_datetime, $end_datetime, $rooms_json);
    
    if (!$stmt1->execute()) throw new Exception("Gagal insert booking: " . $stmt1->error);
    
    $new_booking_id = $conn->insert_id; // Ambil ID baru

    // B. INSERT KE TABEL BOOKING_APPROVAL (Status Awal)
    // Kita set step='baa' dan status='pending' (Menunggu)
    $sql2 = "INSERT INTO booking_approval (booking_id, step, status) VALUES (?, 'baa', 'pending')";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $new_booking_id);
    
    if (!$stmt2->execute()) throw new Exception("Gagal insert approval: " . $stmt2->error);

    // Sukses -> Commit
    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Berhasil Disimpan!", "id" => $new_booking_id]);

} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>