<?php
// fix_all_sessions.php
ob_start();

echo "<h1>Session Fix Tool</h1>";

// Cek dan perbaiki semua file
$files_to_check = [
    '../logout.php',
    '../includes/init.php',
    'session_fix.php',
    '../index.php',
    '../pages/dashboard.php'
];

foreach ($files_to_check as $file) {
    echo "<h3>Checking: $file</h3>";
    
    if (!file_exists($file)) {
        echo "File tidak ditemukan<br>";
        continue;
    }
    
    $content = file_get_contents($file);
    
    // Cek apakah ada output sebelum session_start
    $lines = explode("\n", $content);
    $found_session_start = false;
    $output_before_session = false;
    
    foreach ($lines as $line_num => $line) {
        $line = trim($line);
        
        if (stripos($line, 'session_start()') !== false) {
            $found_session_start = true;
            echo "session_start() found at line " . ($line_num + 1) . "<br>";
        }
        
        // Cek jika ada output sebelum session_start
        if (!$found_session_start && !empty($line) && 
            !preg_match('/^<\?php/', $line) && 
            !preg_match('/^\/\//', $line) && 
            !preg_match('/^#/', $line) && 
            !preg_match('/^\s*\*/', $line) &&
            !preg_match('/^\/\*/', $line)) {
            
            if (preg_match('/^echo|^print|^<|^\s*[a-zA-Z]/', $line)) {
                $output_before_session = true;
                echo "WARNING: Possible output before session_start at line " . ($line_num + 1) . ": $line<br>";
            }
        }
    }
    
    if (!$found_session_start) {
        echo "No session_start() found<br>";
    }
    
    if (!$output_before_session && $found_session_start) {
        echo "OK: No output before session_start<br>";
    }
    
    echo "<hr>";
}

// Test current session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

echo "<h2>Current Session Test</h2>";
echo "Session Status: " . session_status() . "<br>";
echo "Session ID: " . session_id() . "<br>";

if (isset($_GET['test'])) {
    $_SESSION['fix_test'] = date('Y-m-d H:i:s');
    echo "Test session value set!<br>";
}

echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<br><a href='fix_all_sessions.php?test=1'>Set Test Session</a> | ";
echo "<a href='logout.php'>Test Logout</a>";

ob_end_flush();
?>