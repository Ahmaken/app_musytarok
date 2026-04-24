<?php
// logout.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Mulai output buffering SEBELUM session_start()
ob_start();

// Mulai session dengan pengaturan domain yang benar
if (session_status() == PHP_SESSION_NONE) {
    $domain = $_SERVER['HTTP_HOST'];
    $domain_parts = explode('.', $domain);
    $domain_count = count($domain_parts);
    
    // Atur cookie domain untuk subdomain
    if ($domain_count >= 3) {
        // Untuk subdomain (misal: absen.quizb.my.id)
        $main_domain = '.' . $domain_parts[$domain_count-2] . '.' . $domain_parts[$domain_count-1];
        
        // Pastikan session cookie dikirim dengan domain yang benar
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

// Log informasi sebelum logout
error_log("=== LOGOUT PROCESS ===");
error_log("Domain: " . $_SERVER['HTTP_HOST']);
error_log("Session ID: " . session_id());
error_log("User: " . ($_SESSION['username'] ?? 'Unknown'));

// Hapus semua data session
$_SESSION = [];

// Hapus cookie session dengan domain yang sama
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    
    // Hapus cookie session dengan domain yang benar
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],  // Gunakan domain yang sama
        $params["secure"], 
        $params["httponly"]
    );
    
    // Juga hapus cookie dengan domain yang lebih spesifik (tanpa titik diawal)
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $_SERVER['HTTP_HOST'],  // Domain spesifik
        $params["secure"], 
        $params["httponly"]
    );
}

// Hapus cookie remember_user jika ada
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 42000, '/', $_SERVER['HTTP_HOST']);
    setcookie('remember_user', '', time() - 42000, '/', '.' . $_SERVER['HTTP_HOST']);
}

// Hancurkan session
session_destroy();

// Clear semua output buffer
while (ob_get_level()) {
    ob_end_clean();
}

// Redirect ke halaman login
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
$login_url = $protocol . $_SERVER['HTTP_HOST'] . '/index.php';

header("Location: " . $login_url);
exit();
?>