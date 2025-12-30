<?php
// 1. Ambil data dari Railway
$host = getenv('MYSQLHOST'); 
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db   = getenv('MYSQLDATABASE');
$port = getenv('MYSQLPORT');

// 2. Kalau di lokal (XAMPP), variabel di atas pasti kosong, maka pake ini:
if (!$host) {
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "room_booking";
    $port = "3306";
}

// 3. CUKUP SATU KALI SAJA MANGGIL mysqli_connect
$conn = mysqli_connect($host, $user, $pass, $db, $port);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>
