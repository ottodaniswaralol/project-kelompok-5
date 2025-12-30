<?php
// Ambil data variabel dari Railway
$conn = mysqli_connect(
    getenv('MYSQLHOST'), 
    getenv('MYSQLUSER'), 
    getenv('MYSQLPASSWORD'), 
    getenv('MYSQLDATABASE'), 
    getenv('MYSQLPORT')
);

// Jika host kosong (berarti lu lagi jalanin di laptop/XAMPP)
if (!$host) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "room_booking";
    $port = "3306";
}

// Koneksi ke database (Hanya panggil satu kali di sini)
$conn = mysqli_connect($host, $user, $pass, $db, $port);

// Cek koneksi
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>