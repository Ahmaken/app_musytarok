<?php
error_log("=== LOGOUT DEBUG ===");
error_log("Script: " . __FILE__);
error_log("Session status: " . session_status());

ob_start();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
    error_log("Session started");
}

error_log("Session ID: " . session_id());
error_log("Session data before destroy: " . print_r($_SESSION, true));

$_SESSION = array();

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
    error_log("Cookie destroyed");
}

session_destroy();
error_log("Session destroyed");

ob_end_clean();

header("Location: ../index.php");
error_log("Redirecting to index.php");
exit();
?>