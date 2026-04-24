<?php
// test_session.php
ob_start();

// Simple session test
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['set'])) {
    $_SESSION['test_time'] = date('Y-m-d H:i:s');
    $_SESSION['test_user'] = 'test_user';
    echo "Session set!<br>";
}

if (isset($_GET['clear'])) {
    session_destroy();
    echo "Session destroyed!<br>";
}

echo "Session ID: " . session_id() . "<br>";
echo "Session Data: <pre>";
print_r($_SESSION);
echo "</pre>";

echo "<br><a href='test_session.php?set=1'>Set Session</a> | ";
echo "<a href='test_session.php?clear=1'>Clear Session</a>";
?>