<?php
require_once 'config.php';
require_once 'wa.php';

if ($settings['is_active'] !== '1') exit("Fitur Blast Non-Aktif.");

$hari_ini_eng = date('l');
$hari_map = [
    'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 
    'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Ahad'
];
$nama_hari = $hari_map[$hari_ini_eng];
$tanggal_sekarang = date('Y-m-d');

// Query mengambil jadwal Madin dan data Guru terkait
$sql = "SELECT j.jadwal_id, j.mata_pelajaran, j.jam_mulai, j.hari, g.nama, g.no_hp 
        FROM jadwal_madin j 
        JOIN guru g ON j.guru_id = g.guru_id 
        WHERE j.hari = '$nama_hari'";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Membuat link absensi dinamis
        $link_absen = "https://absen.quizb.my.id/pages/absensi.php?filter&tanggal=$tanggal_sekarang&jadwal_id=" . $row['jadwal_id'] . "&active_tab=pelajaran#pelajaran";
        
        $pesan = str_replace(
            ['{nama}', '{mapel}', '{hari}', '{jam}', '{link_absen}'], 
            [$row['nama'], $row['mata_pelajaran'], $row['hari'], $row['jam_mulai'], $link_absen], 
            $settings['msg_template']
        );
        
        kirimWA($row['no_hp'], $pesan);
    }
}
?>