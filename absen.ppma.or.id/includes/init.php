<?php
// includes/init.php

// Aktifkan output buffering di awal
if (!ob_get_level()) {
    ob_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set zona waktu ke Waktu Indonesia Barat
date_default_timezone_set('Asia/Jakarta');

// Session configuration - HARUS di atas semua output
if (session_status() == PHP_SESSION_NONE) {
    // Atur session cookie parameters untuk subdomain
    $domain = $_SERVER['HTTP_HOST'];
    $domain_parts = explode('.', $domain);
    $domain_count = count($domain_parts);
    
    if ($domain_count >= 3) {
        // Untuk subdomain seperti absen.quizb.my.id
        $main_domain = '.' . $domain_parts[$domain_count-2] . '.' . $domain_parts[$domain_count-1];
        
        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => $main_domain,
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    // Start session
    session_start();
    
    // Debug logging
    if (!isset($_SESSION['session_debug'])) {
        $_SESSION['session_debug'] = [
            'started' => date('Y-m-d H:i:s'),
            'domain' => $domain,
            'session_id' => session_id()
        ];
    }
}

// PERBAIKAN: Jangan deklarasikan fungsi check_auth() di sini
// Fungsi check_auth() sudah ada di auth.php yang akan di-include nanti

// Pastikan variabel session yang diperlukan diinisialisasi
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = '';
}

// Include file config.php terlebih dahulu untuk mendefinisikan $conn
require_once __DIR__ . '/config.php';

// Dapatkan guru_id jika user adalah guru
$guru_id = null;
if (isset($_SESSION['role']) && $_SESSION['role'] === 'guru' && isset($_SESSION['user_id'])) {
    // Pastikan koneksi database tersedia
    if ($conn) {
        $sql_guru = "SELECT guru_id FROM guru WHERE user_id = ?";
        $stmt_guru = $conn->prepare($sql_guru);
        if ($stmt_guru) {
            $stmt_guru->bind_param("i", $_SESSION['user_id']);
            $stmt_guru->execute();
            $result_guru = $stmt_guru->get_result();
            
            if ($result_guru->num_rows > 0) {
                $guru_data = $result_guru->fetch_assoc();
                $guru_id = $guru_data['guru_id'];
                $_SESSION['guru_id'] = $guru_id; // Simpan di session untuk akses mudah
            } else {
                // FALLBACK: Coba cari berdasarkan nip atau nama yang sama dengan username
                if (isset($_SESSION['username'])) {
                    $username = $_SESSION['username'];
                    $sql_fallback = "SELECT guru_id FROM guru WHERE nip = ? OR nama = ? LIMIT 1";
                    $stmt_fallback = $conn->prepare($sql_fallback);
                    if ($stmt_fallback) {
                        $stmt_fallback->bind_param("ss", $username, $username);
                        $stmt_fallback->execute();
                        $res_fallback = $stmt_fallback->get_result();
                        if ($res_fallback->num_rows > 0) {
                            $guru_data = $res_fallback->fetch_assoc();
                            $guru_id = $guru_data['guru_id'];
                            $_SESSION['guru_id'] = $guru_id;
                            
                            // Perbaiki relasi secara otomatis untuk ke depannya
                            $sql_update = "UPDATE guru SET user_id = ? WHERE guru_id = ?";
                            $stmt_update = $conn->prepare($sql_update);
                            if ($stmt_update) {
                                $stmt_update->bind_param("ii", $_SESSION['user_id'], $guru_id);
                                $stmt_update->execute();
                                $stmt_update->close();
                            }
                        }
                        $stmt_fallback->close();
                    }
                }
            }
            $stmt_guru->close();
        } else {
            // Log error atau handle error prepare statement
            error_log("Error preparing statement for guru_id: " . $conn->error);
        }
    } else {
        // Handle error koneksi database
        error_log("Database connection is not available in init.php");
    }
}

// PERBAIKAN: Clear cache hijriyah jika diperlukan
if (isset($_GET['clear_hijri_cache'])) {
    unset($_SESSION['hijri_date_cache']);
    unset($_SESSION['hijri_date_cache_nav']);
    // PERBAIKAN: Gunakan JavaScript redirect untuk menghindari header issues
    echo '<script>window.location.href = "' . str_replace('?clear_hijri_cache=1', '', $_SERVER['REQUEST_URI']) . '";</script>';
    exit;
}

// Inisialisasi cache hijriyah
if (!isset($_SESSION['hijri_date_cache'])) {
    $_SESSION['hijri_date_cache'] = [
        'date' => '',
        'hijri_date' => ''
    ];
}

if (!isset($_SESSION['hijri_date_cache_nav'])) {
    $_SESSION['hijri_date_cache_nav'] = [
        'date' => '',
        'hijri_date' => ''
    ];
}

// Autoloader untuk classes
spl_autoload_register(function($class) {
    $classFile = __DIR__ . '/../pages/' . $class . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// Include file-file essential yang bukan class
// PERBAIKAN: Pindahkan require_once auth.php ke atas agar fungsi check_auth() tersedia
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// Navigation.php akan di-include secara manual di setiap halaman 
// karena memerlukan variabel yang mungkin di-set di halaman tertentu
?>