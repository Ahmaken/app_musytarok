<?php
// logout.php
session_start();

error_log("LOGOUT: User " . ($_SESSION['username'] ?? 'unknown') . " attempting logout");

// Tambahkan header untuk mencegah caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Regenerate session ID untuk mencegah session fixation
session_regenerate_id(true);

// Hapus semua data session
$_SESSION = array();

// Hapus cookie session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Clear semua cookies yang mungkin terkait
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time()-1000);
        setcookie($name, '', time()-1000, '/');
    }
}

// Hancurkan session
session_destroy();

// Setelah session_destroy()
echo '<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    window.location.href = "index.php?logout=1";
</script>';
exit();

// Hapus variabel session dari memory
unset($_SESSION);

error_log("LOGOUT: Session destroyed, redirecting to index.php");
header("Location: index.php");
exit();
?>