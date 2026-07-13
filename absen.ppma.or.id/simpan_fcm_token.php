<?php
// simpan_fcm_token.php
session_start();
require_once __DIR__ . '/includes/config.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

// Ambil data JSON dari request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['token']) && !empty($data['token'])) {
    $token = $conn->real_escape_string($data['token']);
    $user_id = $_SESSION['user_id'];
    
    // Update token ke database
    $sql = "UPDATE users SET fcm_token = '$token' WHERE id = '$user_id'";
    
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success', 'message' => 'Token FCM berhasil disimpan']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan token: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Token tidak valid']);
}
?>
