<?php
// blast_realtime.php
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

// 2. Koneksi Database - GUNAKAN YANG SAMA DENGAN blast.php
$host = 'localhost';
$db   = 'quic1934_absensi_online';
$user = 'quic1934_Admin123'; 
$pass = '.A7991h80d70.';     
$charset = 'utf8mb4';

// Membuat koneksi MySQLi
$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset($charset);

// 3. Load konfigurasi tambahan
$config_file = 'blast_config.json';
if (file_exists($config_file)) {
    $config = json_decode(file_get_contents($config_file), true);
    $waktu_notifikasi_menit = $config['waktu_notifikasi'] ?? 30;
    $jam_awal = $config['jam_awal'] ?? '06:00';
    $jam_akhir = $config['jam_akhir'] ?? '22:00';
    $multiple_notifikasi = $config['multiple_notifikasi'] ?? [];
} else {
    $waktu_notifikasi_menit = 30;
    $jam_awal = '06:00';
    $jam_akhir = '22:00';
    $multiple_notifikasi = [];
}

// 4. Mapping Hari Indonesia
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
$waktu_sekarang = date('H:i:s');

// 5. Cek apakah dalam jam aktif
if ($waktu_sekarang < $jam_awal || $waktu_sekarang > $jam_akhir) {
    die("Luar jam aktif ($jam_awal - $jam_akhir). Tidak mengirim notifikasi.");
}

// 6. Ambil semua jadwal hari ini untuk notifikasi multiple
$jadwal_list = [];

// Jadwal Madin
$sql_madin = "SELECT jm.jadwal_id, jm.kelas_madin_id, km.nama_kelas, g.nama, g.no_hp, 
                     'madin' as jenis, jm.jam_mulai, jm.jam_selesai
              FROM jadwal_madin jm
              JOIN guru g ON jm.guru_id = g.guru_id
              JOIN kelas_madin km ON jm.kelas_madin_id = km.kelas_id
              WHERE jm.hari = ?";
              
$stmt_madin = $conn->prepare($sql_madin);
$stmt_madin->bind_param("s", $hari_ini);
$stmt_madin->execute();
$result_madin = $stmt_madin->get_result();
while ($row = $result_madin->fetch_assoc()) {
    $jadwal_list[] = $row;
}

// Jadwal Quran
$sql_quran = "SELECT jq.id as jadwal_id, jq.kelas_quran_id as kelas_id, 
                     kq.nama_kelas, g.nama, g.no_hp, 'quran' as jenis, 
                     jq.jam_mulai, jq.jam_selesai
              FROM jadwal_quran jq
              JOIN guru g ON jq.guru_id = g.guru_id
              JOIN kelas_quran kq ON jq.kelas_quran_id = kq.id
              WHERE jq.hari = ?";
              
$stmt_quran = $conn->prepare($sql_quran);
$stmt_quran->bind_param("s", $hari_ini);
$stmt_quran->execute();
$result_quran = $stmt_quran->get_result();
while ($row = $result_quran->fetch_assoc()) {
    $jadwal_list[] = $row;
}

// // Jadwal Kegiatan (jika ada)
// $sql_kegiatan = "SELECT jk.kegiatan_id, jk.kamar_id as kelas_id, 
//                         k.nama_kamar as nama_kelas, g.nama, g.no_hp, 
//                         'kegiatan' as jenis, jk.jam_mulai, jk.jam_selesai
//                  FROM jadwal_kegiatan jk
//                  JOIN guru g ON jk.guru_id = g.guru_id
//                  JOIN kamar k ON jk.kamar_id = k.kamar_id
//                  WHERE jk.hari = ?";
                 
// $stmt_kegiatan = $conn->prepare($sql_kegiatan);
// $stmt_kegiatan->bind_param("s", $hari_ini);
// $stmt_kegiatan->execute();
// $result_kegiatan = $stmt_kegiatan->get_result();
// while ($row = $result_kegiatan->fetch_assoc()) {
//     $jadwal_list[] = $row;
// }

if (empty($jadwal_list)) {
    die("Tidak ada jadwal untuk hari $hari_ini.");
}

// 7. Cek apakah sudah pernah dikirim hari ini
$log_file = 'blast_log_' . $tanggal_sekarang . '.txt';
$log_data = [];

if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $log_data = explode("\n", trim($log_content));
}

// 8. Proses Blast untuk semua waktu notifikasi
// 8. Proses Blast untuk semua waktu notifikasi
foreach ($multiple_notifikasi as $notif_setting) {
    $menit_sebelum = $notif_setting['menit_sebelum'];
    $label = $notif_setting['label'];
    
    echo "Memeriksa notifikasi $label (menit sebelum: $menit_sebelum)\n";
    
    foreach ($jadwal_list as $row) {
        $nama_guru = $row['nama'];
        $no_hp = trim($row['no_hp']);
        $nama_kelas = $row['nama_kelas'];
        $jenis = $row['jenis'];
        $jam_mulai = $row['jam_mulai'];
        $jam_mulai_time = strtotime($jam_mulai);
        $jadwal_id = $row['jadwal_id'];
        
        // PERBAIKAN LOGIKA: Hitung waktu notifikasi
        $waktu_notifikasi = date('H:i', strtotime("-$menit_sebelum minutes", $jam_mulai_time));
        $waktu_sekarang = date('H:i');
        
        echo "Jadwal: $jam_mulai | Notif: $waktu_notifikasi (menit sebelum: $menit_sebelum) | Sekarang: $waktu_sekarang\n";
        
        // Cek jika waktu sekarang sama dengan waktu notifikasi
        if ($waktu_sekarang !== $waktu_notifikasi) {
            continue;
        }
        
        // Buat unique key untuk log
        $log_key = $jenis . '_' . $jadwal_id . '_' . $menit_sebelum . '_' . $tanggal_sekarang;
        
        // Skip jika sudah dikirim hari ini
        if (in_array($log_key, $log_data)) {
            echo "Skip: Notifikasi $label untuk $nama_guru ($jenis - $nama_kelas) sudah dikirim.\n";
            continue;
        }
        
        // Validasi dan format nomor HP
        if (empty($no_hp)) {
            echo "Skip: $nama_guru tidak memiliki nomor HP.\n";
            continue;
        }
        
        // Format nomor HP: jika dimulai dengan 0, ganti dengan 62
        if (substr($no_hp, 0, 1) == '0') {
            $no_hp = '62' . substr($no_hp, 1);
        }
        
        // Susun Link Absensi berdasarkan jenis jadwal
        $link_absen = "";
        if ($jenis == 'madin') {
            $kelas_field = 'kelas_madin_id';
            $link_absen = "http://absen.quizb.my.id/pages/absensi.php?active_tab=pelajaran&filter=1&tanggal=$tanggal_sekarang&kelas_id={$row[$kelas_field]}&jadwal_id={$jadwal_id}";
        } elseif ($jenis == 'quran') {
            $kelas_field = 'kelas_id';
            $link_absen = "http://absen.quizb.my.id/pages/absensi.php?active_tab=quran&filter_quran=1&tanggal_quran=$tanggal_sekarang&kelas_quran_id={$row[$kelas_field]}&jadwal_quran_id={$jadwal_id}";
        } 
        
        // elseif ($jenis == 'kegiatan') {
        //     $link_absen = "http://absen.quizb.my.id/pages/absensi.php?active_tab=kegiatan&filter=1&tanggal_kegiatan=$tanggal_sekarang&kegiatan_id={$jadwal_id}";
        // }

        // Replace Placeholder di Template
        $pesan = str_replace(
            ['{{nama}}', '{{kelas}}', '{{link}}', '{{jam_mulai}}', '{{waktu_notifikasi}}'],
            [$nama_guru, $nama_kelas, $link_absen, $jam_mulai, $label],
            $settings['template']
        );

        // Kirim via WA
        $hasil = kirimWA($no_hp, $pesan);
        echo "Terkirim ke: $nama_guru ($no_hp) - $jenis - $nama_kelas (Mulai: $jam_mulai) - $label | Status: $hasil\n";
        
        // Catat di log
        file_put_contents($log_file, $log_key . "\n", FILE_APPEND);
        $log_data[] = $log_key; // Update array log untuk iterasi selanjutnya
        
        // Delay antar pengiriman untuk menghindari spam
        sleep(2);
    }
}

// 9. Hapus log file kemarin (untuk menjaga kebersihan)
$kemarin = date('Y-m-d', strtotime('-1 day'));
$log_file_kemarin = 'blast_log_' . $kemarin . '.txt';
if (file_exists($log_file_kemarin)) {
    unlink($log_file_kemarin);
}

echo "Proses selesai pada " . date('Y-m-d H:i:s') . "\n";

// Jika diakses manual dari browser, tunggu sebelum close
if (isset($_SERVER['HTTP_USER_AGENT'])) {
    echo "<br><br><a href='index.php'>Kembali ke Pengaturan</a>";
}

$conn->close();
?>