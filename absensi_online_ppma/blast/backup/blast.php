<?php
// Set zona waktu agar deteksi hari sesuai dengan lokasi Anda
date_default_timezone_set('Asia/Jakarta');

require_once 'wa.php'; 

// Cek keberadaan file setting
if (!file_exists('blast_settings.json')) {
    die("Error: File blast_settings.json tidak ditemukan.");
}

$settings = json_decode(file_get_contents('blast_settings.json'), true);

// 1. Cek apakah status sedang aktif
if ($settings['status'] !== 'aktif') {
    die("Blast dibatalkan: Status saat ini sedang Libur/Nonaktif.");
}

// 2. Koneksi Database
$host = 'localhost';
$db   = 'quic1934_absensi_online';
$user = 'quic1934_Admin123'; 
$pass = '.A7991h80d70.';     
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// 3. Mapping Hari Indonesia
$hari_inggris = date('l');
$map_hari = [
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
    'Sunday'    => 'Ahad'
];
$hari_ini = $map_hari[$hari_inggris];
$tanggal_sekarang = date('Y-m-d');

// 4. Ambil Jadwal Hari Ini (Sudah diperbaiki pada bagian JOIN)
$sql = "SELECT j.jadwal_id, j.kelas_madin_id, k.nama_kelas, g.nama, g.no_hp 
        FROM jadwal_madin j
        JOIN guru g ON j.guru_id = g.guru_id
        JOIN kelas_madin k ON j.kelas_madin_id = k.kelas_id 
        WHERE j.hari = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$hari_ini]);
$jadwal_list = $stmt->fetchAll();

if (empty($jadwal_list)) {
    echo "Tidak ada jadwal mengajar untuk hari $hari_ini.";
    exit;
}

// 5. Proses Blast
foreach ($jadwal_list as $row) {
    $nama_guru = $row['nama'];
    $no_hp = trim($row['no_hp']); // Trim untuk hapus spasi tak sengaja
    $nama_kelas = $row['nama_kelas'];
    
    // Validasi nomor HP (Hanya kirim jika no_hp tidak kosong)
    if (empty($no_hp)) {
        echo "Skip: $nama_guru tidak memiliki nomor HP.\n";
        continue;
    }

    // Susun Link Absensi
    $link_absen = "http://absen.quizb.my.id/pages/absensi.php?active_tab=pelajaran&filter=1&tanggal=$tanggal_sekarang&kelas_id={$row['kelas_madin_id']}&jadwal_id={$row['jadwal_id']}&filter=";

    // Replace Placeholder di Template
    $pesan = str_replace(
        ['{{nama}}', '{{kelas}}', '{{link}}'],
        [$nama_guru, $nama_kelas, $link_absen],
        $settings['template']
    );

    // Kirim via WA
    $hasil = kirimWA($no_hp, $pesan);
    echo "Terkirim ke: $nama_guru ($no_hp) | Status: $hasil\n";
}