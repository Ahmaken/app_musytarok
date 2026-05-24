<?php
/**
 * API Bridge untuk Aplikasi Absensi Next.js
 * File ini berfungsi untuk menjembatani data dari database lama (sec) 
 * ke aplikasi Next.js secara real-time.
 */

// Mengizinkan akses dari aplikasi Next.js (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Memasukkan koneksi database bawaan aplikasi mitra
// Pastikan path ke kon.php sudah benar (sesuaikan jika file ini diletakkan di dalam subfolder)
include './config/kon.php';

// Menangkap parameter action dari URL (misal: ?action=get_santri)
$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'get_santri':
        getSantriAktif();
        break;
    
    case 'get_pembayaran':
        // getPembayaran(); // Bisa ditambahkan jika sudah tahu nama tabel pembayarannya
        echo json_encode(array("message" => "Endpoint pembayaran belum dikonfigurasi."));
        break;

    case 'check_tables':
        // Endpoint sementara untuk melihat struktur tabel apa saja yang ada (untuk development)
        checkTables();
        break;

    default:
        echo json_encode(array(
            "status" => "error",
            "message" => "Aksi tidak valid atau tidak ditemukan. Gunakan ?action=get_santri"
        ));
        break;
}

// ==========================================
// FUNGSI-FUNGSI API
// ==========================================

function getSantriAktif() {
    // Asumsi: tabel data santri bernama 'sekretariat_datasantri' berdasarkan temuan di uppendaftar.php
    // Dan kolom status=1 menandakan aktif.
    $sql = "SELECT * FROM sekretariat_datasantri WHERE status=1";
    
    // Karena aplikasi ini pakai mysql lama, kita ikuti formatnya
    $result = mysql_query($sql);
    
    if (!$result) {
        http_response_code(500);
        echo json_encode(array(
            "status" => "error",
            "message" => "Gagal mengambil data dari database: " . mysql_error()
        ));
        return;
    }

    $data_santri = array();
    while ($row = mysql_fetch_assoc($result)) {
        // Membersihkan data jika perlu
        $data_santri[] = $row;
    }

    echo json_encode(array(
        "status" => "success",
        "total" => count($data_santri),
        "data" => $data_santri
    ));
}

function checkTables() {
    // Endpoint ini berguna untuk kita menganalisis tabel apa yang menyimpan data pembayaran
    $sql = "SHOW TABLES";
    $result = mysql_query($sql);
    
    $tables = array();
    while ($row = mysql_fetch_array($result)) {
        $tables[] = $row[0];
    }
    
    echo json_encode(array(
        "status" => "success",
        "total_tables" => count($tables),
        "tables" => $tables
    ));
}
?>
