<?php
// includes/config.php
$host = 'localhost';
$username = 'ppmawaro_admin'; // GANTI dengan username database Anda di cPanel
$password = '.A7991h80d70.'; // GANTI dengan password database Anda di cPanel
$database = 'ppmawaro_absensi_ppma'; // Sesuai dengan nama database Anda

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Database connection failed: " . $conn->connect_error);
    // Jangan tampilkan error detail ke user
    die(json_encode(['success' => false, 'error' => 'Database connection failed']));
}

$conn->set_charset("utf8mb4");
?>