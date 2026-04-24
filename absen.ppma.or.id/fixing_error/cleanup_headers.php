<?php
// cleanup_headers.php
// Script untuk membersihkan masalah headers

ob_start();

echo "<h1>Cleanup Headers Issues</h1>";

// Cek file dengan BOM (Byte Order Mark)
function checkBOM($filename) {
    if (!file_exists($filename)) return false;
    
    $handle = fopen($filename, 'r');
    $bom = fread($handle, 3);
    fclose($handle);
    
    return $bom === "\xEF\xBB\xBF";
}

// File yang perlu dicek
$files = glob('*.php');
$files = array_merge($files, glob('../*.php'));
$files = array_merge($files, glob('../pages/*.php'));
$files = array_merge($files, glob('../includes/*.php'));

$has_issues = false;

foreach ($files as $file) {
    if (checkBOM($file)) {
        echo "WARNING: $file has BOM (Byte Order Mark)<br>";
        $has_issues = true;
        
        // Remove BOM
        $content = file_get_contents($file);
        if (substr($content, 0, 3) == "\xEF\xBB\xBF") {
            $content = substr($content, 3);
            file_put_contents($file, $content);
            echo "Fixed: BOM removed from $file<br>";
        }
    }
    
    // Cek whitespace di awal file
    $content = file_get_contents($file);
    if (preg_match('/^\s+<\?php/', $content)) {
        echo "WARNING: $file has whitespace before &lt;?php<br>";
        $has_issues = true;
        
        // Remove leading whitespace
        $content = ltrim($content);
        file_put_contents($file, $content);
        echo "Fixed: Leading whitespace removed from $file<br>";
    }
}

if (!$has_issues) {
    echo "No header issues found!";
}

ob_end_flush();
?>