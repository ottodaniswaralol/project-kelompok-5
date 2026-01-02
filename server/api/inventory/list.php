<?php
// 1. WAJIB: Panggil CORS paling atas agar izin akses terkirim ke browser
require_once '../auth/cors.php'; 

// 2. Koneksi Database (Naik 2 tingkat ke folder config)
require_once '../../config/database.php';

header('Content-Type: application/json');

try {
    // 3. Query ambil data inventory
    $q = $conn->query("SELECT * FROM inventory");

    if (!$q) {
        throw new Exception($conn->error);
    }

    $data = [];
    while ($row = $q->fetch_assoc()) {
        $data[] = $row;
    }

    // 4. Kirim hasil dalam format JSON
    echo json_encode($data);

} catch (Exception $e) {
    // Jika ada error database, tetap kirim format JSON agar frontend tidak bingung
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Gagal mengambil data inventaris: " . $e->getMessage()
    ]);
}
?>