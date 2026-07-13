<?php
// fix_sessions.php - Script untuk memperbaiki masalah session
$files = ['../index.php', 'session_fix.php'];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Hapus karakter sebelum <?php
        $content = preg_replace('/^[\s\t\n\r]*/', '', $content);
        
        // Pastikan diawali dengan <?php
        if (strpos($content, '<?php') !== 0) {
            $content = '<?php' . "\n" . $content;
        }
        
        // Tambahkan session_start() setelah <?php jika belum ada
        if (strpos($content, 'session_start()') === false) {
            $content = str_replace('<?php', '<?php' . "\n" . 'session_start();', $content);
        }
        
        file_put_contents($file . '.fixed', $content);
        echo "Fixed version saved to: $file.fixed\n";
    }
}