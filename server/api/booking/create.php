<?php
// === HEADER CORS (Sudah Benar) ===
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === DEBUGGING (Agar Error terlihat di Logs Railway) ===
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json; charset=UTF-8");

// === CEK FILE DATABASE SEBELUM REQUIRE ===
$db_file = '../../config/database.php';

if (!file_exists($db_file)) {
    http_response_code(500);
    // Ini akan memberitahu kita jika path-nya salah
    echo json_encode(["status" => "error", "message" => "File database tidak ditemukan di: " . realpath('../../config/')]);
    exit();
}

require_once $db_file;

// === CEK KONEKSI ===
if (!isset($conn) || !$conn) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Koneksi database gagal (variabel \$conn tidak ada)"]);
    exit();
}

try {
    // 2. Baca data format JSON dari React
    $data = json_decode(file_get_contents("php://input"), true);
    
    $user_id = $data['user_id'] ?? 1;
    $event_name = $data['event_name'] ?? '';
    $org = $data['organization'] ?? '';
    $pic = $data['pic'] ?? ''; // Tambahkan ini
    $phone = $data['phone'] ?? ''; // Tambahkan ini
    $desc = $data['event_description'] ?? ''; // Tambahkan ini
    $start_datetime = $data['start_datetime'] ?? '';
    $end_datetime = $data['end_datetime'] ?? '';
    
    // Pastikan rooms di-encode jadi string JSON untuk masuk ke satu kolom
    $rooms_json = json_encode($data['rooms'] ?? []); 

    $conn->begin_transaction();

    // 3. Query Insert Lengkap (Sesuaikan dengan jumlah kolom di tabel booking lu)
    $sql1 = "INSERT INTO booking (user_id, event_name, organization, pic, phone, event_description, start_datetime, end_datetime, rooms) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    // Bind 9 parameter (i = integer, s = string)
    $stmt1->bind_param("issssssss", $user_id, $event_name, $org, $pic, $phone, $desc, $start_datetime, $end_datetime, $rooms_json);
    
    if (!$stmt1->execute()) throw new Exception("Gagal insert booking: " . $stmt1->error);
    
    $new_id = $conn->insert_id;

    // 4. Input status awal ke tabel approval
    $sql2 = "INSERT INTO booking_approval (booking_id, step, status) VALUES (?, 'baa', 'pending')";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("i", $new_id);
    
    if (!$stmt2->execute()) throw new Exception("Gagal insert approval");

    $conn->commit();
    echo json_encode(["status" => "success", "message" => "Booking Berhasil!", "id" => $new_id]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollback();
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>