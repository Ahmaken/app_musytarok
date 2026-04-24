<?php
session_start();
// session_fix.php - Untuk debugging masalah session
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan TIDAK ADA OUTPUT SEBELUM session_start()!
// Semua kode PHP harus di atas, HTML di bawah

if (session_status() == PHP_SESSION_NONE) {
    $domain = $_SERVER['HTTP_HOST'];
    $domain_parts = explode('.', $domain);
    $domain_count = count($domain_parts);
    
    if ($domain_count >= 3) {
        $main_domain = '.' . $domain_parts[$domain_count-2] . '.' . $domain_parts[$domain_count-1];
        ini_set('session.cookie_domain', $main_domain);
    }
    
    // Mulai session SEBELUM output apapun
    // session_start();
}

// Handle actions
if (isset($_GET['login'])) {
    $_SESSION['test_user'] = 'test_user_' . time();
    $_SESSION['logged_in'] = true;
    header("Location: session_fix.php");
    exit;
}

if (isset($_GET['logout'])) {
    // Hapus semua data session
    $_SESSION = array();
    
    // Hapus cookie session
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
    header("Location: session_fix.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Session Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        pre { background: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>Session Debug</h1>
    
    <p><strong>Session ID:</strong> <?php echo session_id(); ?></p>
    <p><strong>Domain:</strong> <?php echo $_SERVER['HTTP_HOST']; ?></p>
    <p><strong>Cookie Domain Setting:</strong> <?php echo ini_get('session.cookie_domain'); ?></p>
    
    <h2>Current Session Data:</h2>
    <pre><?php print_r($_SESSION); ?></pre>
    
    <h2>Cookies:</h2>
    <pre><?php print_r($_COOKIE); ?></pre>
    
    <h2>Session Status:</h2>
    <p><strong>Status:</strong> <?php echo session_status(); ?> 
    (0 = PHP_SESSION_DISABLED, 1 = PHP_SESSION_NONE, 2 = PHP_SESSION_ACTIVE)</p>
    
    <h2>Actions:</h2>
    <p><a href="session_fix.php?login=1">Create Test Session</a></p>
    <p><a href="session_fix.php?logout=1">Destroy Session</a></p>
    
    <hr>
    
    <h2>Test Logout from Main App:</h2>
    <p><a href="../logout.php" target="_blank">Test Logout (main app)</a></p>
    
    <h2>Debug Info:</h2>
    <pre>
PHP Version: <?php echo phpversion(); ?>

Session Name: <?php echo session_name(); ?>

Session Save Path: <?php echo session_save_path(); ?>

Session Cookie Params:
<?php print_r(session_get_cookie_params()); ?>

HTTP Headers (partial):
<?php 
$headers = headers_list();
foreach ($headers as $header) {
    echo $header . "\n";
}
?>
    </pre>
</body>
</html>