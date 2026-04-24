<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// includes/auth.php
require_once 'config.php';

// Fungsi untuk memastikan session sudah dimulai
function ensure_session_started() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
}

// Fungsi login
function login($username, $password, $remember = false) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // PERBAIKAN: Terima password plain text atau hash
        $stored_password = $user['password'];
        
        // Cek apakah password di database adalah hash
        $is_hashed = password_verify($password, $stored_password);
        
        // Jika tidak di-hash, bandingkan langsung
        $valid_password = $is_hashed || ($password === $stored_password);
        
        // Set session dengan pengaturan yang benar untuk subdomain
        if (session_status() == PHP_SESSION_NONE) {
            $domain = $_SERVER['HTTP_HOST'];
            $domain_parts = explode('.', $domain);
            $domain_count = count($domain_parts);
            
            if ($domain_count >= 3) {
                $main_domain = '.' . $domain_parts[$domain_count-2] . '.' . $domain_parts[$domain_count-1];
                session_set_cookie_params([
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => $main_domain,
                    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
            
            session_start();
        }
        
        if ($valid_password) {
            // Pastikan session sudah dimulai
            ensure_session_started();
            
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['foto_profil'] = $user['foto_profil'] ?? null;
            $_SESSION['dark_mode'] = $user['dark_mode'] ?? 0;
            $_SESSION['kelas_id'] = $user['kelas_id'] ?? null;
            $_SESSION['murid_id'] = $user['murid_id'] ?? null;
            
            setcookie('dark_mode_pref', $_SESSION['dark_mode'], time() + (86400 * 30), "/");
            
            if ($remember) {
                setcookie('remember_user', $user['id'], time() + (86400 * 30), "/");
            }
            
            return true;
        }
    }
    return false;
}

// Fungsi untuk mendapatkan murid yang terkait dengan guru
function get_murid_terkait_guru($guru_id) {
    global $conn;
    
    $sql = "SELECT DISTINCT m.* 
            FROM murid m
            LEFT JOIN kelas_madin km ON m.kelas_madin_id = km.kelas_id
            LEFT JOIN kelas_quran kq ON m.kelas_quran_id = kq.id  
            LEFT JOIN kamar k ON m.kamar_id = k.kamar_id
            WHERE km.guru_id = ? OR kq.guru_id = ? OR k.guru_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $guru_id, $guru_id, $guru_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $murid_list = [];
    while ($row = $result->fetch_assoc()) {
        $murid_list[] = $row;
    }
    
    return $murid_list;
}

// Fungsi logout - DIPERBAIKI
function logout() {
    // Pastikan session sudah dimulai sebelum menghancurkannya
    ensure_session_started();
    
    // Hapus semua variabel session
    $_SESSION = array();
    
    // Hapus cookie session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Hancurkan session
    session_destroy();
    
    // Hapus cookie remember me
    setcookie('remember_user', '', time() - 3600, "/");
}

// Fungsi untuk memeriksa apakah user sudah login - DIPERBAIKI
function check_auth() {
    // TAMBAHKAN KODE INI DI SINI:
    if (strpos($_SERVER['REQUEST_URI'], '.well-known') !== false) {
        return true; 
    }
    
    // Pastikan session sudah started
    ensure_session_started();
    
    // Periksa apakah user_id ada di session
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }
    
    // Jika tidak, cek remember me cookie
    if (isset($_COOKIE['remember_user'])) {
        $user_id = $_COOKIE['remember_user'];
        // Validasi user_id dari cookie (pastikan ada di database)
        global $conn;
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['foto_profil'] = $user['foto_profil'] ?? null;
            $_SESSION['dark_mode'] = $user['dark_mode'] ?? 0;
            $_SESSION['kelas_id'] = $user['kelas_id'] ?? null;
            $_SESSION['murid_id'] = $user['murid_id'] ?? null;
            return true;
        }
    }
    
    return false;
}

// Fungsi untuk mendapatkan greeting berdasarkan waktu
function get_greeting() {
    $hour = date('H');
    if ($hour < 10) {
        return 'Selamat Pagi';
    } elseif ($hour < 15) {
        return 'Selamat Siang';
    } elseif ($hour < 19) {
        return 'Selamat Sore';
    } else {
        return 'Selamat Malam';
    }
}

?>