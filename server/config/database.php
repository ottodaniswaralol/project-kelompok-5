<?php
// 1. Ambil data dari Railway environment
$host = getenv('MYSQLHOST') ?: "localhost";
$user = getenv('MYSQLUSER') ?: "root";
$pass = getenv('MYSQLPASSWORD') ?: "";
$db   = getenv('MYSQLDATABASE') ?: "room_booking";
$port = getenv('MYSQLPORT') ?: "3306";

// 2. Gunakan error reporting yang tidak mengeluarkan HTML agar JSON tetap bersih
mysqli_report(MYSQLI_REPORT_OFF); 

// 3. Eksekusi koneksi
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// 4. Cek koneksi tanpa mematikan script secara kasar (mencegah 502)
if (!$conn) {
    // Pastikan header JSON terkirim agar browser tidak mengira ini error CORS
    header("Content-Type: application/json");
    http_response_code(500);
    echo json_encode([
        "status" => false, 
        "message" => "Database Connection Error",
        "debug" => mysqli_connect_error() // Opsional: hapus jika sudah production
    ]);
    exit; // Gunakan exit, jangan die() agar lebih bersih
}

// Set charset agar karakter khusus tidak error
mysqli_set_charset($conn, "utf8mb4");
?>