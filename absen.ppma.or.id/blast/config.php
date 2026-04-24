<?php
// Konfigurasi Database
$db_host = 'localhost';
$db_user = 'quic1934_Admin123'; // Ganti sesuai user db Anda
$db_pass = '.A7991h80d70.';     // Ganti sesuai pass db Anda
$db_name = 'quic1934_absensi_online';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// File JSON untuk menyimpan setting agar tidak perlu tabel baru
$setting_file = __DIR__ . '/settings.json';
if (!file_exists($setting_file)) {
    $default_settings = [
        'is_active' => '1',
        'msg_template' => "Assalamu'alaikum Ust. {nama},\n\nMengingatkan jadwal mengajar {mapel} pada hari ini pukul {jam}. Terima kasih."
    ];
    file_put_contents($setting_file, json_encode($default_settings));
}

$settings = json_decode(file_get_contents($setting_file), true);
?>