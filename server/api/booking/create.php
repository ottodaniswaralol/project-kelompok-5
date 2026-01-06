<?php
// === FORCE CORS SUPER KUAT (Taruh Paling Atas) ===
// Mencegah blokir browser dan menangani preflight request
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// Jika browser cuma "tanya" (OPTIONS), langsung jawab OK dan stop.
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// === NYALAKAN DEBUGGING (Agar Error 500 ketahuan penyebabnya) ===
// Hapus bagian ini nanti kalau sudah production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set tipe konten jadi JSON
header("Content-Type: application/json; charset=UTF-8");

try {
    // === VALIDASI PATH DATABASE (Penyebab Utama Error 500) ===
    // Sesuaikan path ini dengan struktur folder Railway kamu
    // Asumsi: create.php ada di /server/api/booking/
    // Database ada di /server/config/database.php
    $db_path = '../../config/database.php';

    if (!file_exists($db_path)) {
        throw new Exception("File database.php tidak ditemukan di jalur: " . realpath($db_path) . ". Cek struktur folder.");
    }

    // Panggil database
    require_once $db_path;

    // Cek apakah variabel $conn berhasil dibuat di database.php
    if (!isset($conn) || !$conn) {
        throw new Exception("File database.php terpanggil, tapi koneksi \$conn gagal/null.");
    }

    // === BACA INPUT DARI REACT ===
    $inputRaw = file_get_contents("php://input");
    $data = json_decode($inputRaw, true);

    // Cek jika JSON rusak/kosong
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Input tidak valid. Raw input: '" . $inputRaw . "'");
    }

    // === AMBIL DATA ===
    $user_id        = $data['user_id'] ?? 1;
    $event_name     = $data['event_name'] ?? '';
    $org            = $data['organization'] ?? '';
    $pic            = $data['pic'] ?? '';
    $phone          = $data['phone'] ?? '';
    $desc           = $data['event_description'] ?? '';
    $start_datetime = $data['start_datetime'] ?? '';
    $end_datetime   = $data['end_datetime'] ?? '';
    
    // Convert array rooms jadi JSON String untuk disimpan di database
    $rooms_json     = json_encode($data['rooms'] ?? []); 

    // === EKSEKUSI DATABASE ===
    $conn->begin_transaction();

    // Query Booking
    $sql1 = "INSERT INTO booking (user_id, event_name, organization, pic, phone, event_description, start_datetime, end_datetime, rooms) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) throw new Exception("SQL Error (Prepare 1): " . $conn->error);

    $stmt1->bind_param("issssssss", $user_id, $event_name, $org, $pic, $phone, $desc, $start_datetime, $end_datetime, $rooms_json);
    
    if (!$stmt1->execute()) throw new Exception("Gagal simpan booking: " . $stmt1->error);
    
    $new_id = $conn->insert_id;

    // Query Approval Status
    $sql2 = "INSERT INTO booking_approval (booking_id, step, status) VALUES (?, 'baa', 'pending')";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) throw new Exception("SQL Error (Prepare 2): " . $conn->error);

    $stmt2->bind_param("i", $new_id);
    
    if (!$stmt2->execute()) throw new Exception("Gagal simpan status approval.");

    $conn->commit();

    // === RESPONSE SUKSES ===
    echo json_encode(["status" => "success", "message" => "Booking Berhasil Disimpan!", "id" => $new_id]);

} catch (Exception $e) {
    // === TANGKAP SEMUA ERROR BIAR GAK JADI 500 POLOS ===
    if (isset($conn)) $conn->rollback();
    
    // Tetap kirim 200/500 tapi dengan BODY JSON berisi pesan error
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "Server Error: " . $e->getMessage()
    ]);
}
?>