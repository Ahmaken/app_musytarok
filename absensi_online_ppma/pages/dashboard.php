<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PENTING: Pindahkan require_once init.php KE ATAS
require_once '../includes/init.php';

// PERBAIKAN: Cek autentikasi setelah init
if (!check_auth()) {
    header("Location: ../index.php");
    exit();
}

// PERBAIKAN: Cek apakah user memiliki akses ke halaman ini berdasarkan role
$allowed_roles = ['admin', 'staff', 'guru'];
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
    header("Location: ../dashboard.php");
    exit();
}

// === PERBAIKAN: SET DEFAULT VALUES SETELAH CHECK_AUTH ===
// Default values untuk avoid notice - SETELAH check_auth
$_SESSION['role'] = $_SESSION['role'] ?? 'guest';
$_SESSION['guru_id'] = $_SESSION['guru_id'] ?? null;
$_SESSION['user_id'] = $_SESSION['user_id'] ?? null;
$_SESSION['username'] = $_SESSION['username'] ?? 'User';

// Filter data untuk role guru
$guru_id = null;
if ($_SESSION['role'] === 'guru' && isset($_SESSION['guru_id'])) {
    $guru_id = $_SESSION['guru_id'];
}

// PERBAIKAN: HAPUS DUPLIKASI INISIALISASI $guru_id
// ===== FILTER DATA UNTUK ROLE GURU =====
if ($_SESSION['role'] === 'guru' && !$guru_id) {
    $sql_guru = "SELECT guru_id FROM guru WHERE user_id = ?";
    $stmt_guru = $conn->prepare($sql_guru);
    $stmt_guru->bind_param("i", $_SESSION['user_id']);
    $stmt_guru->execute();
    $result_guru = $stmt_guru->get_result();
    
    if ($result_guru->num_rows > 0) {
        $guru_data = $result_guru->fetch_assoc();
        $guru_id = $guru_data['guru_id'];
        $_SESSION['guru_id'] = $guru_id; // Simpan di session
        error_log("Guru ID ditemukan: " . $guru_id);
    } else {
        error_log("Guru ID tidak ditemukan untuk user_id: " . $_SESSION['user_id']);
    }
    $stmt_guru->close();
}

// ===== PERBAIKAN: DEFINSIKAN VARIABEL $today DI AWAL =====
$today = date('Y-m-d');

// ===== INISIALISASI VARIABEL DI AWAL =====
$jadwal_hari_ini = [];
$jadwal_quran_hari_ini = [];
$jadwal_kegiatan_hari_ini = [];
$notifikasi_jadwal_belum_isi = []; 
$pelanggaran_terbaru = [];
$perizinan_terbaru = [];

// Include fungsi hijriyah
require_once '../includes/hijri_functions.php';

// Ambil tanggal Hijriyah dengan error handling
try {
    $tanggal_hijriyah = get_hijri_date_kemenag($today);
    
    $_SESSION['hijri_date_nav'] = $tanggal_hijriyah;
    $_SESSION['hijri_date_cache'] = [
        'date' => $today,
        'hijri_date' => $tanggal_hijriyah
    ];
    
} catch (Exception $e) {
    error_log("Error getting hijri date: " . $e->getMessage());
    $tanggal_hijriyah = date('d M Y') . ' H';
}

$day_name = date('l');

// Di sekitar baris 76-84, ganti array day_map menjadi bahasa arab:
$day_map = [
    'Monday' => 'الاثنين',
    'Tuesday' => 'الثلاثاء',
    'Wednesday' => 'الأربعاء',
    'Thursday' => 'الخميس',
    'Friday' => 'الجمعة',
    'Saturday' => 'السبت',
    'Sunday' => 'الأحد'
];
$hari_ini = $day_map[$day_name] ?? $day_name;

// TAMBAHKAN mapping untuk query database (jika kolom hari masih berisi bahasa Indonesia)
$day_map_for_query = [
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa', 
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu',
    'Sunday' => 'Ahad'
];
$hari_untuk_query = $day_map_for_query[$day_name] ?? $day_name;

// ===== CEK STATUS BLAST WHATSAPP =====
$blast_settings_file = '../blast/blast_settings.json';
$blast_config_file = '../blast/blast_config.json';
$blast_status = 'nonaktif';
$blast_template = '';
$blast_config = ['waktu_notifikasi' => 30, 'jam_awal' => '06:00', 'jam_akhir' => '22:00', 'multiple_notifikasi' => []];

if (file_exists($blast_settings_file)) {
    $blast_settings = json_decode(file_get_contents($blast_settings_file), true);
    $blast_status = $blast_settings['status'] ?? 'nonaktif';
    $blast_template = $blast_settings['template'] ?? '';
}

if (file_exists($blast_config_file)) {
    $blast_config = json_decode(file_get_contents($blast_config_file), true);
}

// Hitung jadwal dengan notifikasi
$jadwal_dengan_notifikasi = [];
$semua_jadwal = array_merge(
    array_map(function($j) { $j['jenis'] = 'Madin'; return $j; }, $jadwal_hari_ini),
    array_map(function($j) { $j['jenis'] = 'Quran'; return $j; }, $jadwal_quran_hari_ini),
    array_map(function($j) { $j['jenis'] = 'Kegiatan'; return $j; }, $jadwal_kegiatan_hari_ini)
);

// Sort by waktu
usort($semua_jadwal, function($a, $b) {
    return strcmp($a['jam_mulai'], $b['jam_mulai']);
});

foreach ($semua_jadwal as $jadwal) {
    $jam_mulai = $jadwal['jam_mulai'];
    $waktu_notifikasi_list = [];
    
    foreach ($blast_config['multiple_notifikasi'] as $notif) {
        $waktu_notif = date('H:i', strtotime("$today $jam_mulai -{$notif['menit_sebelum']} minutes"));
        $waktu_notifikasi_list[] = [
            'waktu' => $waktu_notif,
            'label' => $notif['label']
        ];
    }
    
    $jadwal['waktu_notifikasi'] = $waktu_notifikasi_list;
    $jadwal_dengan_notifikasi[] = $jadwal;
}

// Tambahkan fungsi ini di awal dashboard.php setelah koneksi database
function table_exists($conn, $table_name) {
    $result = $conn->query("SHOW TABLES LIKE '$table_name'");
    return $result && $result->num_rows > 0;
}

// Fungsi untuk mengurutkan array multidimensi berdasarkan kolom tertentu
function sort_array_by_column(&$array, $column, $direction = SORT_ASC) {
    usort($array, function($a, $b) use ($column, $direction) {
        if ($direction === SORT_ASC) {
            return strcasecmp($a[$column], $b[$column]);
        } else {
            return strcasecmp($b[$column], $a[$column]);
        }
    });
}

// Fungsi untuk mengurutkan natural sort (A-Z lalu 1-100)
function natural_sort_array_by_column(&$array, $column, $secondary_column = null) {
    usort($array, function($a, $b) use ($column, $secondary_column) {
        // Jika ada kolom sekunder untuk pengurutan bertingkat
        if ($secondary_column) {
            // Bandingkan kolom utama terlebih dahulu
            $cmp = strnatcasecmp($a[$column], $b[$column]);
            if ($cmp == 0) {
                // Jika sama, bandingkan kolom sekunder
                return strnatcasecmp($a[$secondary_column], $b[$secondary_column]);
            }
            return $cmp;
        }
        // Bandingkan kolom tunggal
        return strnatcasecmp($a[$column], $b[$column]);
    });
}

// Fungsi untuk ekstrak angka dari string untuk pengurutan yang lebih baik
function extract_number_from_string($str) {
    preg_match('/\d+/', $str, $matches);
    return isset($matches[0]) ? (int)$matches[0] : 9999;
}

// Fungsi untuk mengurutkan campuran huruf dan angka
function sort_mixed_alphanumeric(&$array, $column, $secondary_column = null) {
    usort($array, function($a, $b) use ($column, $secondary_column) {
        // Ekstrak bagian huruf dan angka
        preg_match('/([A-Za-z]+)(\d*)/', $a[$column], $matches_a);
        preg_match('/([A-Za-z]+)(\d*)/', $b[$column], $matches_b);
        
        $letter_a = $matches_a[1] ?? '';
        $number_a = isset($matches_a[2]) && $matches_a[2] !== '' ? (int)$matches_a[2] : 0;
        
        $letter_b = $matches_b[1] ?? '';
        $number_b = isset($matches_b[2]) && $matches_b[2] !== '' ? (int)$matches_b[2] : 0;
        
        // Bandingkan huruf terlebih dahulu
        $letter_cmp = strcasecmp($letter_a, $letter_b);
        if ($letter_cmp != 0) {
            return $letter_cmp;
        }
        
        // Jika huruf sama, bandingkan angka
        if ($number_a != $number_b) {
            return $number_a - $number_b;
        }
        
        // Jika masih sama dan ada kolom sekunder, bandingkan kolom sekunder
        if ($secondary_column) {
            return strnatcasecmp($a[$secondary_column], $b[$secondary_column]);
        }
        
        return 0;
    });
}

// Fungsi get_jadwal_hari_ini dengan filter guru dan pengurutan A-Z
function get_jadwal_hari_ini($conn, $hari, $table, $join_table, $id_field, $name_field, $guru_id = null, $order_column = null) {
    $jadwal = [];
    
    // Tentukan kolom untuk ORDER BY
    $order_by = "ORDER BY ";
    if ($order_column) {
        $order_by .= "$order_column ASC";
    } else {
        // Default order berdasarkan kolom yang sesuai
        if ($table == 'jadwal_madin') {
            $order_by .= "nama_kelas ASC, mata_pelajaran ASC";
        } elseif ($table == 'jadwal_quran') {
            $order_by .= "nama_kelas ASC, mata_pelajaran ASC";
        } elseif ($table == 'jadwal_kegiatan') {
            $order_by .= "nama_kamar ASC, nama_kegiatan ASC";
        } else {
            $order_by .= "jam_mulai ASC";
        }
    }
    
    $sql = "SELECT j.*, k.$name_field 
            FROM $table j 
            JOIN $join_table k ON j.{$id_field} = k.{$id_field}
            WHERE j.hari = ?";
    
    if ($guru_id) {
        if ($table == 'jadwal_madin') {
            $sql .= " AND (j.guru_id = ? OR k.guru_id = ?)";
        } elseif ($table == 'jadwal_quran') {
            $sql .= " AND (j.guru_id = ? OR k.guru_id = ?)";
        } elseif ($table == 'jadwal_kegiatan') {
            $sql .= " AND (j.guru_id = ? OR k.guru_id = ?)";
        }
    }

    $sql .= " $order_by";
    
    try {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }

        if ($guru_id) {
            $stmt->bind_param("sii", $hari, $guru_id, $guru_id);
        } else {
            $stmt->bind_param("s", $hari);
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $jadwal[] = $row;
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
    
    return $jadwal;
}

// Fungsi untuk mengambil statistik absensi dengan filter guru
function get_attendance_stats($conn, $table, $date, $guru_id = null) {
    // PERBAIKAN: Cek apakah tabel exist
    if (!table_exists($conn, $table)) {
        error_log("Table $table does not exist");
        return ['stats' => ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0], 'total' => 0];
    }
    
    $stats = ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
    $total = 0;
    
    $sql = "SELECT a.status, COUNT(*) as total 
            FROM $table a";
    
    if ($guru_id && $table == 'absensi') {
        $sql .= " JOIN jadwal_madin jm ON a.jadwal_madin_id = jm.jadwal_id
                  JOIN kelas_madin km ON jm.kelas_madin_id = km.kelas_id
                  WHERE a.tanggal = ? AND (jm.guru_id = ? OR km.guru_id = ?)";
    } elseif ($guru_id && $table == 'absensi_quran') {
        $sql .= " JOIN jadwal_quran jq ON a.jadwal_quran_id = jq.id
                  JOIN kelas_quran kq ON jq.kelas_quran_id = kq.id
                  WHERE a.tanggal = ? AND (jq.guru_id = ? OR kq.guru_id = ?)";
    } elseif ($guru_id && $table == 'absensi_kegiatan') {
        $sql .= " JOIN jadwal_kegiatan jk ON a.kegiatan_id = jk.kegiatan_id
                  JOIN kamar k ON jk.kamar_id = k.kamar_id
                  WHERE a.tanggal = ? AND (jk.guru_id = ? OR k.guru_id = ?)";
    } else {
        $sql .= " WHERE a.tanggal = ?";
    }
    
    $sql .= " GROUP BY a.status ORDER BY a.status ASC";
    
    try {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Error preparing stats statement: " . $conn->error);
        }

        if ($guru_id) {
            $stmt->bind_param("sii", $date, $guru_id, $guru_id);
        } else {
            $stmt->bind_param("s", $date);
        }

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $stats[$row['status']] = $row['total'];
                    $total += $row['total'];
                }
            }
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log($e->getMessage());
    }
    
    return ['stats' => $stats, 'total' => $total];
}

// ===== DEBUG: CEK DATA TERFILTER =====
if ($_SESSION['role'] === 'guru') {
    error_log("=== DEBUG GURU FILTER ===");
    error_log("Guru ID: " . $guru_id);
    error_log("Jadwal Madin: " . count($jadwal_hari_ini));
    error_log("Jadwal Quran: " . count($jadwal_quran_hari_ini)); 
    error_log("Jadwal Kegiatan: " . count($jadwal_kegiatan_hari_ini));
    error_log("Pelanggaran: " . count($pelanggaran_terbaru));
    error_log("Perizinan: " . count($perizinan_terbaru));
    error_log("==========================");
}

// ===== PERBAIKAN: AMBIL JADWAL DENGAN URUTAN A-Z =====
// Jadwal Madin dengan filter guru - DIURUTKAN secara alfanumerik
$sql_madin = "SELECT jm.*, km.nama_kelas 
              FROM jadwal_madin jm 
              JOIN kelas_madin km ON jm.kelas_madin_id = km.kelas_id
              WHERE jm.hari = ?";
              
if ($guru_id) {
    $sql_madin .= " AND (jm.guru_id = ? OR km.guru_id = ?)";
}
$sql_madin .= " ORDER BY 
    CAST(SUBSTRING_INDEX(km.nama_kelas, ' ', 1) AS UNSIGNED),
    km.nama_kelas ASC, 
    jm.mata_pelajaran ASC";

$stmt_madin = $conn->prepare($sql_madin);
if ($stmt_madin) {
    if ($guru_id) {
        $stmt_madin->bind_param("sii", $hari_untuk_query, $guru_id, $guru_id); // GANTI di sini
    } else {
        $stmt_madin->bind_param("s", $hari_untuk_query); // GANTI di sini
    }
    $stmt_madin->execute();
    $result_madin = $stmt_madin->get_result();
    while ($row = $result_madin->fetch_assoc()) {
        $jadwal_hari_ini[] = $row;
    }
    $stmt_madin->close();
    
    // PERBAIKAN: Urutkan dengan natural sort setelah query
    sort_mixed_alphanumeric($jadwal_hari_ini, 'nama_kelas', 'mata_pelajaran');
} else {
    error_log("Error preparing jadwal madin statement: " . $conn->error);
}

// Jadwal Quran dengan filter guru - DIURUTKAN secara alfanumerik
$sql_quran = "SELECT jq.*, kq.nama_kelas 
              FROM jadwal_quran jq 
              JOIN kelas_quran kq ON jq.kelas_quran_id = kq.id
              WHERE jq.hari = ?";
              
if ($guru_id) {
    $sql_quran .= " AND (jq.guru_id = ? OR kq.guru_id = ?)";
}
$sql_quran .= " ORDER BY 
    -- Urutkan berdasarkan angka lalu huruf
    CAST(REGEXP_SUBSTR(kq.nama_kelas, '[0-9]+') AS UNSIGNED),
    -- Kemudian urutkan berdasarkan huruf
    kq.nama_kelas ASC, 
    -- Kolom sekunder
    jq.mata_pelajaran ASC";

$stmt_quran = $conn->prepare($sql_quran);
if ($stmt_quran) {
    if ($guru_id) {
        $stmt_quran->bind_param("sii", $hari_untuk_query, $guru_id, $guru_id); // GANTI di sini
    } else {
        $stmt_quran->bind_param("s", $hari_untuk_query); // GANTI di sini
    }
    $stmt_quran->execute();
    $result_quran = $stmt_quran->get_result();
    while ($row = $result_quran->fetch_assoc()) {
        $jadwal_quran_hari_ini[] = $row;
    }
    $stmt_quran->close();
    
    // PERBAIKAN: Urutkan dengan natural sort setelah query
    sort_mixed_alphanumeric($jadwal_quran_hari_ini, 'nama_kelas', 'mata_pelajaran');
} else {
    error_log("Error preparing jadwal quran statement: " . $conn->error);
}

// Query yang lebih fleksibel untuk berbagai format nama
$sql_kegiatan = "SELECT jk.*, k.nama_kamar 
                 FROM jadwal_kegiatan jk 
                 JOIN kamar k ON jk.kamar_id = k.kamar_id
                 WHERE jk.hari = ?";
                 
if ($guru_id) {
    $sql_kegiatan .= " AND (jk.guru_id = ? OR k.guru_id = ?)";
}
$sql_kegiatan .= " ORDER BY 
    -- Urutkan berdasarkan huruf pertama
    SUBSTRING(k.nama_kamar, 1, 1) ASC,
    -- Urutkan berdasarkan angka setelah huruf (jika ada)
    CAST(REGEXP_SUBSTR(k.nama_kamar, '[0-9]+') AS UNSIGNED) ASC,
    -- Jika tidak ada angka, urutkan berdasarkan string lengkap
    k.nama_kamar ASC,
    -- Kolom sekunder
    jk.nama_kegiatan ASC";

$stmt_kegiatan = $conn->prepare($sql_kegiatan);
if ($stmt_kegiatan) {
    if ($guru_id) {
        $stmt_kegiatan->bind_param("sii", $hari_untuk_query, $guru_id, $guru_id); // GANTI di sini
    } else {
        $stmt_kegiatan->bind_param("s", $hari_untuk_query); // GANTI di sini
    }
    $stmt_kegiatan->execute();
    $result_kegiatan = $stmt_kegiatan->get_result();
    while ($row = $result_kegiatan->fetch_assoc()) {
        $jadwal_kegiatan_hari_ini[] = $row;
    }
    $stmt_kegiatan->close();
    
    // PERBAIKAN: Urutkan dengan natural sort setelah query
    sort_mixed_alphanumeric($jadwal_kegiatan_hari_ini, 'nama_kamar', 'nama_kegiatan');
} else {
    error_log("Error preparing jadwal kegiatan statement: " . $conn->error);
}

// Ambil statistik absensi dengan filter guru
$stats_madin = get_attendance_stats($conn, 'absensi', $today, $guru_id);
$stats_quran_data = get_attendance_stats($conn, 'absensi_quran', $today, $guru_id);
$stats_kegiatan_data = get_attendance_stats($conn, 'absensi_kegiatan', $today, $guru_id);

// Hitung persentase
function calculate_percentages($stats, $total) {
    if ($total > 0) {
        return [
            'Hadir' => ($stats['Hadir'] / $total) * 100,
            'Sakit' => ($stats['Sakit'] / $total) * 100,
            'Izin' => ($stats['Izin'] / $total) * 100,
            'Alpa' => ($stats['Alpa'] / $total) * 100
        ];
    }
    return ['Hadir' => 0, 'Sakit' => 0, 'Izin' => 0, 'Alpa' => 0];
}

$percentages_madin = calculate_percentages($stats_madin['stats'], $stats_madin['total']);
$percentages_quran = calculate_percentages($stats_quran_data['stats'], $stats_quran_data['total']);
$percentages_kegiatan = calculate_percentages($stats_kegiatan_data['stats'], $stats_kegiatan_data['total']);

// ===== AMBIL STATISTIK ABSENSI GURU =====
$stats_guru = [
    'hari_ini' => [
        'hadir_hari_ini' => 0,
        'sakit_hari_ini' => 0,
        'izin_hari_ini' => 0,
        'alpa_hari_ini' => 0,
        'total_hari_ini' => 0
    ],
    'total_guru' => 0
];

// Di bagian statistik absensi guru (sekitar baris 303)
if (in_array($_SESSION['role'], ['admin', 'staff'])) {
    // Hitung total guru - PERBAIKAN: Hapus kondisi WHERE status = 'aktif' jika kolom tidak ada
    $sql_total_guru = "SELECT COUNT(*) as total FROM guru ORDER BY nama ASC"; // Urutkan A-Z
    $result_total_guru = $conn->query($sql_total_guru);
    if ($result_total_guru && $result_total_guru->num_rows > 0) {
        $row_total = $result_total_guru->fetch_assoc();
        $stats_guru['total_guru'] = $row_total['total'];
    }

    // PERBAIKAN: Pastikan tabel absensi_guru ada sebelum query
    $table_exists = $conn->query("SHOW TABLES LIKE 'absensi_guru'")->num_rows > 0;
    
    if ($table_exists) {
        // Hitung statistik absensi guru hari ini - urutkan berdasarkan status
        $sql_absensi_guru = "SELECT 
            COUNT(CASE WHEN status = 'Hadir' THEN 1 END) as hadir_hari_ini,
            COUNT(CASE WHEN status = 'Sakit' THEN 1 END) as sakit_hari_ini,
            COUNT(CASE WHEN status = 'Izin' THEN 1 END) as izin_hari_ini,
            COUNT(CASE WHEN status = 'Alpa' THEN 1 END) as alpa_hari_ini,
            COUNT(*) as total_hari_ini
            FROM absensi_guru 
            WHERE tanggal = ?";
        
        $stmt_absensi_guru = $conn->prepare($sql_absensi_guru);
        if ($stmt_absensi_guru) {
            $stmt_absensi_guru->bind_param("s", $today);
            $stmt_absensi_guru->execute();
            $result_absensi_guru = $stmt_absensi_guru->get_result();
            
            if ($result_absensi_guru && $result_absensi_guru->num_rows > 0) {
                $row_absensi = $result_absensi_guru->fetch_assoc();
                $stats_guru['hari_ini'] = [
                    'hadir_hari_ini' => $row_absensi['hadir_hari_ini'] ?? 0,
                    'sakit_hari_ini' => $row_absensi['sakit_hari_ini'] ?? 0,
                    'izin_hari_ini' => $row_absensi['izin_hari_ini'] ?? 0,
                    'alpa_hari_ini' => $row_absensi['alpa_hari_ini'] ?? 0,
                    'total_hari_ini' => $row_absensi['total_hari_ini'] ?? 0
                ];
            }
            $stmt_absensi_guru->close();
        }
    }
}

// ===== PERUBAHAN: PELANGGARAN TERBARU dengan filter 3 HARI =====
$pelanggaran_terbaru = [];
$sql_pelanggaran = "SELECT p.*, m.nama, km.nama_kelas 
                    FROM pelanggaran p
                    JOIN murid m ON p.murid_id = m.murid_id
                    LEFT JOIN kelas_madin km ON m.kelas_madin_id = km.kelas_id
                    WHERE p.tanggal >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
                    
if ($guru_id) {
    $sql_pelanggaran .= " AND (km.guru_id = ? OR m.kelas_quran_id IN (
                            SELECT id FROM kelas_quran WHERE guru_id = ?
                         ) OR m.kamar_id IN (
                            SELECT kamar_id FROM kamar WHERE guru_id = ?
                         ))";
}
$sql_pelanggaran .= " ORDER BY 
    CAST(SUBSTRING_INDEX(km.nama_kelas, ' ', 1) AS UNSIGNED),
    km.nama_kelas ASC, 
    m.nama ASC, 
    p.tanggal DESC 
    LIMIT 20";

$stmt_pelanggaran = $conn->prepare($sql_pelanggaran);
if ($stmt_pelanggaran) {
    if ($guru_id) {
        $stmt_pelanggaran->bind_param("iii", $guru_id, $guru_id, $guru_id);
    }
    $stmt_pelanggaran->execute();
    $result_pelanggaran = $stmt_pelanggaran->get_result();
    if ($result_pelanggaran && $result_pelanggaran->num_rows > 0) {
        while ($row = $result_pelanggaran->fetch_assoc()) {
            $pelanggaran_terbaru[] = $row;
        }
    }
    $stmt_pelanggaran->close();
} else {
    error_log("Error preparing pelanggaran statement: " . $conn->error);
}

// ===== PERUBAHAN: PERIZINAN TERBARU dengan filter 3 HARI =====
$perizinan_terbaru = [];
$sql_perizinan = "SELECT p.*, m.nama, km.nama_kelas 
                  FROM perizinan p
                  JOIN murid m ON p.murid_id = m.murid_id
                  LEFT JOIN kelas_madin km ON m.kelas_madin_id = km.kelas_id
                  WHERE p.tanggal >= DATE_SUB(CURDATE(), INTERVAL 3 DAY)";
                  
if ($guru_id) {
    $sql_perizinan .= " AND (km.guru_id = ? OR m.kelas_quran_id IN (
                            SELECT id FROM kelas_quran WHERE guru_id = ?
                         ) OR m.kamar_id IN (
                            SELECT kamar_id FROM kamar WHERE guru_id = ?
                         ))";
}
$sql_perizinan .= " ORDER BY 
    CAST(SUBSTRING_INDEX(km.nama_kelas, ' ', 1) AS UNSIGNED),
    km.nama_kelas ASC, 
    m.nama ASC, 
    p.tanggal DESC 
    LIMIT 20";


$stmt_perizinan = $conn->prepare($sql_perizinan);
if ($stmt_perizinan) {
    if ($guru_id) {
        $stmt_perizinan->bind_param("iii", $guru_id, $guru_id, $guru_id);
    }
    $stmt_perizinan->execute();
    $result_perizinan = $stmt_perizinan->get_result();
    if ($result_perizinan && $result_perizinan->num_rows > 0) {
        while ($row = $result_perizinan->fetch_assoc()) {
            $perizinan_terbaru[] = $row;
        }
    }
    $stmt_perizinan->close();
} else {
    error_log("Error preparing perizinan statement: " . $conn->error);
}

// Di bagian setelah session_start() di dashboard.php, tambahkan:
if (isset($_GET['clear_hijri_cache'])) {
    unset($_SESSION['hijri_date_cache']);
    unset($_SESSION['hijri_date_cache_nav']);
    echo '<script>window.location.href = "dashboard.php";</script>';
    exit();
}

// ===== FUNGSI UNTUK MENGAMBIL PENGATURAN NOTIFIKASI =====
function get_pengaturan_notifikasi($conn, $nama_pengaturan) {
    $sql = "SELECT nilai FROM pengaturan_notifikasi WHERE nama_pengaturan = ? ORDER BY nama_pengaturan ASC";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("s", $nama_pengaturan);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['nilai'];
        }
    }
    return null;
}

// Ambil pengaturan notifikasi
$notifikasi_aktif = get_pengaturan_notifikasi($conn, 'notifikasi_aktif') ?? '1';
$waktu_tampil_jam = intval(get_pengaturan_notifikasi($conn, 'waktu_tampil_notifikasi') ?? '1');
$batas_waktu_jam = intval(get_pengaturan_notifikasi($conn, 'batas_waktu_notifikasi') ?? '24');
$refresh_otomatis_menit = intval(get_pengaturan_notifikasi($conn, 'refresh_otomatis') ?? '5');

// Konversi ke detik
$waktu_tampil_detik = $waktu_tampil_jam * 3600;
$batas_waktu_detik = $batas_waktu_jam * 3600;

// ===== FUNGSI UNTUK NOTIFIKASI =====
function cek_jadwal_sudah_diisi($conn, $tanggal, $jenis_jadwal, $id_jadwal) {
    $table_map = [
        'madin' => 'absensi',
        'quran' => 'absensi_quran',
        'kegiatan' => 'absensi_kegiatan'
    ];
    
    $id_field_map = [
        'madin' => 'jadwal_madin_id',
        'quran' => 'jadwal_quran_id',
        'kegiatan' => 'kegiatan_id'
    ];
    
    $table = $table_map[$jenis_jadwal] ?? '';
    $id_field = $id_field_map[$jenis_jadwal] ?? '';
    
    if (!$table || !$id_field) return false;
    
    // PERBAIKAN: Cek apakah tabel exist
    $table_exists = $conn->query("SHOW TABLES LIKE '$table'")->num_rows > 0;
    
    // Jika tabel kegiatan belum ada, anggap belum diisi
    if (!$table_exists && $table == 'absensi_kegiatan') {
        return false;
    }
    
    try {
        $sql = "SELECT COUNT(*) as total FROM $table 
                WHERE tanggal = ? AND $id_field = ?";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("si", $tanggal, $id_jadwal);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
            return $row['total'] > 0;
        }
    } catch (Exception $e) {
        error_log("Error in cek_jadwal_sudah_diisi: " . $e->getMessage());
        return false;
    }
    
    return false;
}

// PERBAIKAN: Tambahkan parameter $today ke fungsi
function perlu_tampilkan_notifikasi($jam_mulai, $waktu_tampil_detik, $batas_waktu_detik, $today) {
    $sekarang = time();
    $waktu_jadwal = strtotime($today . ' ' . $jam_mulai);
    $waktu_tampil = $waktu_jadwal + $waktu_tampil_detik;
    $waktu_batas = $waktu_jadwal + $batas_waktu_detik;
    
    return ($sekarang >= $waktu_tampil && $sekarang <= $waktu_batas);
}

// Handle PWA promotion dismissal
if (isset($_GET['dismiss_pwa_promo'])) {
    $_SESSION['pwa_promotion_dismissed'] = true;
    
    // Return JSON for AJAX requests
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode(['success' => true]);
        exit;
    } else {
        // Redirect back for non-AJAX requests
        header('Location: dashboard.php');
        exit;
    }
}

// Initialize PWA promotion session if not set
$_SESSION['pwa_promotion_dismissed'] = $_SESSION['pwa_promotion_dismissed'] ?? false;

// ===== FUNGSI FILTER JADWAL BERDASARKAN WAKTU =====
function filter_jadwal_berdasarkan_waktu($jadwal_array, $today, $waktu_tenggang_jam = 2) {
    $sekarang = time();
    $jadwal_terfilter = [];
    
    foreach ($jadwal_array as $jadwal) {
        $waktu_jadwal = strtotime($today . ' ' . $jadwal['jam_mulai']);
        
        // Hitung batas waktu (30 menit sebelum jadwal)
        $batas_awal = $waktu_jadwal - (30 * 60); // 30 menit sebelum
        
        // Hitung batas akhir (waktu tenggang setelah jadwal dimulai)
        $batas_akhir = $waktu_jadwal + ($waktu_tenggang_jam * 3600);
        
        // Tampilkan hanya jika:
        // - Waktu sekarang >= 30 menit sebelum jadwal
        // - Waktu sekarang <= waktu tenggang setelah jadwal dimulai
        if ($sekarang >= $batas_awal && $sekarang <= $batas_akhir) {
            $jadwal_terfilter[] = $jadwal;
        }
    }
    
    return $jadwal_terfilter;
}

// ===== AMBIL PENGATURAN WAKTU TENGGANG =====
$sql_waktu_tenggang = "SELECT nilai FROM pengaturan_absensi_otomatis WHERE nama_pengaturan = 'waktu_tenggang_absensi' ORDER BY nama_pengaturan ASC";
$result_waktu_tenggang = $conn->query($sql_waktu_tenggang);
$waktu_tenggang_jam = 2; // default 2 jam

if ($result_waktu_tenggang && $result_waktu_tenggang->num_rows > 0) {
    $row = $result_waktu_tenggang->fetch_assoc();
    $waktu_tenggang_jam = intval($row['nilai']);
}

// ===== FILTER JADWAL BERDASARKAN WAKTU =====
$jadwal_hari_ini = filter_jadwal_berdasarkan_waktu($jadwal_hari_ini, $today, $waktu_tenggang_jam);
$jadwal_quran_hari_ini = filter_jadwal_berdasarkan_waktu($jadwal_quran_hari_ini, $today, $waktu_tenggang_jam);
$jadwal_kegiatan_hari_ini = filter_jadwal_berdasarkan_waktu($jadwal_kegiatan_hari_ini, $today, $waktu_tenggang_jam);

// ===== CEK JADWAL YANG BELUM DIISI =====
// Cek jadwal Madin yang belum diisi
foreach ($jadwal_hari_ini as $jadwal) {
    if (!cek_jadwal_sudah_diisi($conn, $today, 'madin', $jadwal['jadwal_id'])) {
        if ($notifikasi_aktif == '1' && perlu_tampilkan_notifikasi($jadwal['jam_mulai'], $waktu_tampil_detik, $batas_waktu_detik, $today)) {
            $notifikasi_jadwal_belum_isi[] = [
                'jenis' => 'Madin',
                'mata_pelajaran' => $jadwal['mata_pelajaran'],
                'kelas' => $jadwal['nama_kelas'],
                'waktu' => $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai'],
                'jam_mulai' => $jadwal['jam_mulai'],
                'link' => "absensi.php?filter&tanggal={$today}&jadwal_id={$jadwal['jadwal_id']}&active_tab=pelajaran#pelajaran",
                'waktu_tampil' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $waktu_tampil_detik),
                'waktu_batas' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $batas_waktu_detik)
            ];
        }
    }
}

// Cek jadwal Quran yang belum diisi
foreach ($jadwal_quran_hari_ini as $jadwal) {
    if (!cek_jadwal_sudah_diisi($conn, $today, 'quran', $jadwal['id'])) {
        if ($notifikasi_aktif == '1' && perlu_tampilkan_notifikasi($jadwal['jam_mulai'], $waktu_tampil_detik, $batas_waktu_detik, $today)) {
            $notifikasi_jadwal_belum_isi[] = [
                'jenis' => 'Quran',
                'mata_pelajaran' => $jadwal['mata_pelajaran'],
                'kelas' => $jadwal['nama_kelas'],
                'waktu' => $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai'],
                'jam_mulai' => $jadwal['jam_mulai'],
                'link' => "absensi.php?filter_quran&tanggal_quran={$today}&jadwal_quran_id={$jadwal['id']}&active_tab=quran#quran",
                'waktu_tampil' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $waktu_tampil_detik),
                'waktu_batas' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $batas_waktu_detik)
            ];
        }
    }
}

// Cek jadwal Kegiatan yang belum diisi
foreach ($jadwal_kegiatan_hari_ini as $jadwal) {
    if (!cek_jadwal_sudah_diisi($conn, $today, 'kegiatan', $jadwal['kegiatan_id'])) {
        if ($notifikasi_aktif == '1' && perlu_tampilkan_notifikasi($jadwal['jam_mulai'], $waktu_tampil_detik, $batas_waktu_detik, $today)) {
            $notifikasi_jadwal_belum_isi[] = [
                'jenis' => 'Kegiatan',
                'mata_pelajaran' => $jadwal['nama_kegiatan'],
                'kelas' => $jadwal['nama_kamar'],
                'waktu' => $jadwal['jam_mulai'] . ' - ' . $jadwal['jam_selesai'],
                'jam_mulai' => $jadwal['jam_mulai'],
                'link' => "absensi.php?filter&tanggal_kegiatan={$today}&kegiatan_id={$jadwal['kegiatan_id']}&active_tab=kegiatan#kegiatan",
                'waktu_tampil' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $waktu_tampil_detik),
                'waktu_batas' => date('H:i', strtotime($today . ' ' . $jadwal['jam_mulai']) + $batas_waktu_detik)
            ];
        }
    }
}

// ===== PERBAIKAN: URUTKAN NOTIFIKASI DENGAN NATURAL SORT =====
if (count($notifikasi_jadwal_belum_isi) > 0) {
    // Pisahkan notifikasi berdasarkan jenis
    $notifikasi_madin = array_filter($notifikasi_jadwal_belum_isi, function($item) {
        return $item['jenis'] === 'Madin';
    });
    
    $notifikasi_quran = array_filter($notifikasi_jadwal_belum_isi, function($item) {
        return $item['jenis'] === 'Quran';
    });
    
    $notifikasi_kegiatan = array_filter($notifikasi_jadwal_belum_isi, function($item) {
        return $item['jenis'] === 'Kegiatan';
    });
    
    // Konversi ke array berindeks
    $notifikasi_madin = array_values($notifikasi_madin);
    $notifikasi_quran = array_values($notifikasi_quran);
    $notifikasi_kegiatan = array_values($notifikasi_kegiatan);
    
    // Untuk Quran: urutkan berdasarkan 'kelas' lalu 'mata_pelajaran'
    sort_mixed_alphanumeric($notifikasi_madin, 'kelas', 'mata_pelajaran');
    sort_mixed_alphanumeric($notifikasi_quran, 'kelas', 'mata_pelajaran');
    sort_mixed_alphanumeric($notifikasi_kegiatan, 'kelas', 'nama_kegiatan');
    
    // Gabungkan kembali dengan urutan: Kegiatan, Quran, Madin
    $notifikasi_jadwal_belum_isi = array_merge($notifikasi_kegiatan, $notifikasi_quran, $notifikasi_madin);
}

// Cek apakah ada jadwal yang muncul
$ada_jadwal = (count($jadwal_hari_ini) > 0 || count($jadwal_quran_hari_ini) > 0 || count($jadwal_kegiatan_hari_ini) > 0);

// Tentukan teks berdasarkan kondisi
$info_text = $ada_jadwal ? 
    "absensi jadwal telah tersedia, silahkan mengisi absen" : 
    "Jadwal akan ditampilkan antara 30 menit sebelum mulai sampai " . $waktu_tenggang_jam . " jam setelah mulai";

// Tentukan class tambahan berdasarkan kondisi
$card_class = $ada_jadwal ? 'pulsating-card has-schedule' : '';

require_once '../includes/navigation.php';
$dark_mode = $_SESSION['dark_mode'] ?? 0;

// PERBAIKAN: Pindahkan DOCTYPE ke output buffer untuk menghindari Quirks Mode
ob_start();
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="<?= $dark_mode ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Absensi Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- PWA Manifest -->
    <link rel="manifest" href="../manifest.json">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#007bff">
    
    <!-- Icon untuk berbagai perangkat -->
    <link rel="icon" href="../assets/icons/icon-192x192.png" type="image/png">
    <link rel="apple-touch-icon" href="../assets/icons/icon-192x192.png">
    
    <style>
    /* ===== ANIMASI TEKS ===== */
    .arabic-font {
        font-family: 'Noto Naskh Arabic', serif !important;
        font-size: clamp(1.4rem, 4vw, 1.8rem) !important;
        font-weight: 600 !important;
        line-height: 1.8 !important;
        text-align: center !important;
        margin: 0 auto !important;
        display: block !important;
        width: 100% !important;
    }
    
    /* Container khusus untuk teks Arab */
    .arabic-container {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        width: 100% !important;
        margin: 0 auto 2rem auto !important;
        padding: 0 10px !important;
        min-height: 80px !important;
    }
    
    .arabic-text .word, .welcome-heading span {
        display: inline-block;
        opacity: 0;
        animation: fadeIn 0.8s forwards;
    }
    
    .arabic-text .space {
        display: inline-block !important;
        width: 0.3em !important;
        text-align: center !important;
    }
    
    .welcome-heading span {
        transform: translateY(30px);
    }
    
    /* Perbaikan untuk kata-kata Arab individual */
    .arabic-text .word {
        display: inline-block !important;
        margin: 0 1px !important;
        text-align: center !important;
        vertical-align: middle !important;
        line-height: 1.6 !important;
        transform: translateY(-30px);
    }
    
    .welcome-message span {
        display: inline-block;
        opacity: 0;
        filter: blur(8px);
        animation: fadeInBlur 1s forwards;
    }
    
    @keyframes fadeIn {
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeInBlur {
        to { opacity: 1; filter: blur(0); }
    }
    
    .arabic-text .word { animation-delay: calc(0.2s * var(--word-index)); }
    .welcome-heading span { animation-delay: calc(0.1s * var(--i)); }
    .welcome-message span { animation-delay: calc(0.03s * var(--i)); }
    
    .space { display: inline-block; width: 0.5em; }
    
    .welcome-dashboard-message {
        min-height: 24px;
        margin: 15px 0;
    }
    
    .welcome-dashboard-message span {
        display: inline-block;
        opacity: 0;
        filter: blur(5px);
        animation: fadeInBlur 2.5s forwards;
    }
    
    .welcome-dashboard-message .space { width: 0.3em; }
    
    /* ===== LAYOUT UTAMA ===== */
    .main-content {
        display: flex;
        gap: 20px;
        position: relative;
    }
    
    .jadwal-container {
        flex: 1;
    }
    
    .quick-access-container {
        width: 300px;
        position: sticky;
        top: 80px;
        height: fit-content;
        align-self: flex-start;
        z-index: 1000;
    }
    
    .chart-container {
        height: 200px;
        position: relative;
        margin-bottom: 20px;
    }
    
    /* ===== PERBAIKAN TAMPILAN TANGGAL ===== */
    .hijri-date-container {
        min-height: 60px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        width: 100%;
        margin: 0 auto;
        align-items: center;
        animation: fadeInDown 1s ease-out 0.5s both;
    }
    
    .hijri-date-official {
        margin-bottom: 8px;
    }
    
    .hijri-date-container .badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .hijri-date-container .badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* ===== STYLE BADGE TANGGAL RESPONSIF ===== */
    .custom-date-badge {
        white-space: normal;
        word-wrap: break-word;
        max-width: 95%;
        margin: 0 auto;
        display: inline-flex;
        flex-wrap: wrap;
        justify-content: center;
        align-items: center;
        line-height: 1.3;
        font-size: 0.875rem;
        padding: 0.5rem 1rem !important;
    }
    
    /* ===== DARK MODE SUPPORT ===== */
    [data-bs-theme="dark"] .arabic-font,
    [data-bs-theme="dark"] .welcome-heading span,
    [data-bs-theme="dark"] .welcome-dashboard-message span {
        color: #e0e0e0 !important;
    }
    
    [data-bs-theme="dark"] .progress {
        background-color: #333 !important;
    }
    
    [data-bs-theme="dark"] .hijri-date-container .badge {
        background: linear-gradient(135deg, #4a5568 0%, #2d3748 100%) !important;
        color: #e2e8f0 !important;
    }
    
    [data-bs-theme="dark"] .hijri-date-container .text-muted {
        color: #a0aec0 !important;
    }
    
    /* ===== PERBAIKAN STYLE NOTIFIKASI ===== */
    .notification-minimized {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 1060;
        background: rgba(255, 193, 7, 0.95);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        border: 3px solid #ffc107;
    }
    
    .notification-minimized:hover {
        transform: scale(1.1);
        background: rgba(255, 193, 7, 1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
    }
    
    .notification-bell {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        border-radius: 50%;
    }
    
    /* ===== PERBAIKAN ANIMASI NOTIFIKASI ===== */
    .notification-panel-card {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 9999;
        width: 450px;
        max-width: 90vw;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        border: 1px solid #ffc107;
        transition: all 0.3s ease-in-out;
        transform: translateX(0);
        opacity: 1;
    }
    
    .notification-panel-card.mobile {
        right: 10px;
        left: 10px;
        width: auto;
        max-width: calc(100vw - 20px);
    }
    
    /* Pastikan notifikasi selalu di atas elemen lain */
    .notification-minimized,
    .notification-panel-card {
        z-index: 9999 !important;
    }
    
    /* PERBAIKAN: Sembunyikan notifikasi jika tidak ada notifikasi */
    .notification-minimized.hidden,
    .notification-panel-card.hidden {
        display: none !important;
    }
    
    /* Pastikan panel notifikasi hidden by default dengan d-none */
    .notification-panel-card.d-none {
        display: none !important;
        opacity: 0;
        transform: translateX(100%);
    }
    
    /* Pastikan notification minimized visible ketika ada notifikasi */
    .notification-minimized:not(.d-none) {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Z-index yang lebih tinggi untuk memastikan di atas elemen lain */
    .notification-minimized {
        z-index: 9998 !important;
    }
    
    .notification-panel-card {
        z-index: 9999 !important;
    }
    
    /* Style untuk header notifikasi */
    .toast-header-warning {
        background: linear-gradient(45deg, #ffc107, #ffb300);
        color: #000;
        border-bottom: 1px solid #ffb300;
    }
    
    [data-bs-theme="dark"] .toast-header-warning {
        background: linear-gradient(45deg, #665800, #856d00);
        color: #fff;
        border-bottom: 1px solid #856d00;
    }
    
    .toast-body-scroll {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .notification-item {
        border-left: 4px solid #ffc107;
        padding-left: 12px;
        margin-bottom: 12px;
        background: rgba(255, 193, 7, 0.05);
        padding: 10px;
        border-radius: 4px;
    }
    
    [data-bs-theme="dark"] .notification-item {
        border-left-color: #856d00;
        background: rgba(133, 109, 0, 0.1);
    }
    
    [data-bs-theme="dark"] .notification-item {
        border-left-color: #856d00;
    }
    
    /* Pastikan notifikasi minimized selalu terlihat jika ada notifikasi */
    .notification-minimized:not(.d-none) {
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    .notification-panel-card:not(.d-none) {
        display: block !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* Animasi untuk notifikasi */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    .notification-bell .badge {
        animation: pulse 2s infinite;
        font-size: 0.7rem;
        min-width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .notification-panel-card:not(.d-none) {
        display: block !important;
        animation: slideInRight 0.3s ease-out;
    }
    
    /* Responsive design untuk notifikasi */
    @media (max-width: 768px) {
        .notification-minimized {
            top: 80px;
            right: 15px;
            width: 50px;
            height: 50px;
        }
        
        .notification-panel-card {
            top: 80px;
            right: 15px;
            width: calc(100vw - 30px);
        }
        
        .notification-bell i {
            font-size: 1.3rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .notification-minimized {
            top: 70px;
            right: 10px;
            width: 45px;
            height: 45px;
        }
        
        .notification-panel-card {
            top: 70px;
            right: 10px;
            left: 10px;
            width: calc(100vw - 20px);
        }
        
        .notification-bell i {
            font-size: 1.2rem !important;
        }
    }
    
    @media (max-width: 768px) {
        .display-4 { font-size: 1.8rem; }
        .arabic-font { font-size: 1.2rem; }
        .lead { font-size: 1rem; }
        
        /* Notifikasi mobile */
        .notification-minimized {
            top: 80px;
            right: 15px;
            width: 45px;
            height: 45px;
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        .notification-panel-card {
            top: 80px;
            right: 15px;
        }
        
        .notification-bell i {
            font-size: 1.3rem !important;
        }
    }
    
    /* ===== PERBAIKAN RESPONSIVE UNTUK MOBILE ZOOM ===== */
    @media (max-width: 576px) {
        html {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        
        body {
            min-width: 320px;
            overflow-x: hidden;
        }
        
        .container {
            padding-left: 10px;
            padding-right: 10px;
            max-width: 100%;
            overflow-x: hidden;
        }
        
        /* Perbaikan untuk tulisan Arab */
        .arabic-container {
            min-height: 60px !important;
            margin: 0 auto 1.5rem auto !important;
            padding: 0 5px !important;
        }
        
        .arabic-font {
            font-size: clamp(1.2rem, 5vw, 1.6rem) !important;
            line-height: 1.6 !important;
            padding: 0 5px;
        }
        
        .arabic-text h3 {
            line-height: 1.5 !important;
            padding: 0 !important;
            margin-bottom: 1rem !important;
        }
        
        /* Perbaikan untuk greeting */
        .display-4.welcome-heading {
            font-size: 1.8rem !important;
            line-height: 1.3 !important;
            margin-bottom: 0.5rem !important;
        }
        
        .display-7.welcome-heading {
            font-size: 1.4rem !important;
            line-height: 1.3 !important;
            margin-bottom: 1rem !important;
        }
        
        .welcome-dashboard-message {
            font-size: 0.9rem !important;
            line-height: 1.4 !important;
            padding: 0 8px;
            display: block !important;
        }
        
        .welcome-dashboard-message .break-mobile {
            display: block;
            width: 100%;
            height: 5px;
        }
        
        /* Perbaikan untuk badge tanggal */
        .custom-date-badge {
            font-size: 0.74rem !important;
            padding: 0.5rem 0.8rem !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 95%;
            margin: 0 auto;
        }
        
        .custom-date-badge .bi {
            font-size: 0.9em !important;
        }
        
        /* Perbaikan untuk alert PWA */
        #pwa-promotion {
            margin: 0 5px 20px 5px;
            border-radius: 8px;
        }
        
        #pwa-promotion .d-flex {
            flex-direction: column;
            text-align: center;
        }
        
        #pwa-promotion .btn {
            margin-top: 8px;
            width: 100%;
        }
        
        /* Perbaikan layout cards */
        .card {
            margin-left: 2px;
            margin-right: 2px;
        }
        
        .card-body {
            padding: 12px;
        }
        
        /* Notifikasi mobile kecil */
        .notification-minimized {
            top: 70px;
            right: 10px;
            width: 40px;
            height: 40px;
            display: flex !important;
            opacity: 1 !important;
            visibility: visible !important;
        }
        
        .notification-bell i {
            font-size: 1.2rem !important;
        }
        
        .notification-panel-card {
            top: 70px;
            right: 10px;
            left: 10px;
            width: auto;
        }
        
        .arabic-text .word {
            margin: 0 0.5px !important;
            line-height: 1.5 !important;
        }
        
        .arabic-text .space {
            width: 0.2em !important;
        }
        
        /* Perbaikan animasi untuk mobile */
        .welcome-heading span,
        .welcome-dashboard-message span,
        .arabic-text .word {
            animation-duration: 0.5s;
        }
        
        /* Perbaikan responsif untuk tanggal */
        .custom-date-badge {
            font-size: 0.7rem !important;
            padding: 0.4rem 0.6rem !important;
            line-height: 1.2;
        }
        
        .hijri-date-official {
            font-size: 0.85rem;
        }
        
        .hijri-date-container {
            padding: 0 5px;
        }
        
        /* Pastikan tanggal tidak terpotong */
        .custom-date-badge br {
            display: none;
        }
        
        /* Perbaikan untuk mencegah texts overflow */
        .welcome-dashboard-message {
            font-size: 0.85rem !important;
            line-height: 1.3 !important;
            padding: 0 8px;
        }
        
        .welcome-dashboard-message span {
            margin: 0 -0.3px;
            display: inline-block;
        }
        
        /* Container untuk greeting */
        .greeting-container {
            padding: 0 5px;
        }
        
        .display-4.welcome-heading {
            font-size: 1.6rem !important;
            line-height: 1.2 !important;
            margin-bottom: 0.3rem !important;
            word-spacing: -0.5px;
        }
        
        .display-7.welcome-heading {
            font-size: 1.2rem !important;
            line-height: 1.2 !important;
            margin-bottom: 0.5rem !important;
        }
        
        /* Perbaikan untuk animasi per huruf di mobile */
        .welcome-heading span {
            display: inline-block;
            margin: 0 -0.5px;
        }
    }
    
    /* ===== PERBAIKAN UNTUK TABLET ===== */
    @media (min-width: 577px) and (max-width: 768px) {
        .container {
            padding-left: 15px;
            padding-right: 15px;
        }
        
        .arabic-font {
            font-size: clamp(1.5rem, 4vw, 1.7rem) !important;
        }
        
        .display-4.welcome-heading {
            font-size: 2rem;
        }
        
        .custom-date-badge {
            font-size: 0.85rem !important;
            padding: 0.5rem 0.8rem !important;
        }
    }
    
    /* Untuk desktop - ukuran lebih besar */
    @media (min-width: 992px) {
        .custom-date-badge {
            font-size: 1.1rem !important;
            padding: 0.75rem 1.5rem !important;
        }
    }
    
    /* Untuk tablet - ukuran medium */
    @media (min-width: 768px) and (max-width: 991px) {
        .custom-date-badge {
            font-size: 1rem !important;
            padding: 0.6rem 1.2rem !important;
        }
    }
    
    /* Untuk mencegah layout break saat zoom */
    @media (max-width: 400px) {
        .arabic-font {
            font-size: 1.3rem !important;
            line-height: 1.5 !important;
        }
        
        .arabic-text h3 {
            line-height: 1.4 !important;
        }
    }
    
    /* ===== PERBAIKAN SPESIFIK UNTUK PWA PROMOTION ===== */
    #pwa-promotion {
        border-left: 4px solid #0dcaf0;
    }
    
    #pwa-promotion .d-flex {
        align-items: center;
    }
    
    @media (max-width: 576px) {
        #pwa-promotion .d-flex {
            align-items: stretch;
        }
        
        #pwa-promotion .bi-phone {
            margin-bottom: 10px;
        }
    }
    
    /* ===== PERBAIKAN UNTUK MENCEGAH TEXTS OVERFLOW ===== */
    .welcome-heading {
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .welcome-dashboard-message {
        line-height: 1.4;
        max-width: 100%;
        margin: 10px auto;
        padding: 0 10px;
        text-align: center;
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
    }
    
    /* ===== PERBAIKAN KHUSUS UNTUK TULISAN ARAB ===== */
    .arabic-text {
        line-height: 1.8 !important;
        padding: 0 10px !important;
        text-align: center !important;
        margin: 0 auto !important;
        max-width: 100% !important;
        display: block !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .arabic-text h3 {
        margin: 0 auto !important;
        max-width: 100% !important;
        display: block !important;
        text-align: center !important;
        line-height: 1.6 !important;
    }
    
    /* Zoom protection */
    .arabic-text h3 span[dir="rtl"] {
        transform: translateZ(0);
        backface-visibility: hidden;
        perspective: 1000;
        display: inline-block !important;
        text-align: center !important;
        margin: 0 auto !important;
        padding: 5px 0 !important;
        line-height: 1.8 !important;
        max-width: 100% !important;
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
    }
    
    /* Pastikan container parent tidak mempengaruhi layout Arab */
    .text-center.mb-8 {
        width: 100% !important;
        max-width: 100% !important;
        overflow: hidden !important;
        margin: 0 auto !important;
        padding: 0 5px !important;
    }
    
    .d-flex.flex-column.align-items-center {
        width: 100%;
        max-width: 100%;
    }
    
    /* Perbaikan untuk mencegah overflow */
    .text-center {
        max-width: 100%;
        overflow: hidden;
    }
    
    /* Dark mode support untuk notifikasi */
    [data-bs-theme="dark"] .notification-minimized {
        background: rgba(133, 109, 0, 0.95);
        border-color: #856d00;
    }
    
    [data-bs-theme="dark"] .notification-minimized:hover {
        background: rgba(133, 109, 0, 1);
    }
    
    /* ===== LOADING OVERLAY ===== */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99999;
        backdrop-filter: blur(5px);
    }
    
    .loading-content {
        text-align: center;
        color: white;
        padding: 2rem;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.1);
    }
    
    .spinner-border.loading-spinner {
        width: 4rem;
        height: 4rem;
        border-width: 0.3rem;
    }
    
    /* Animasi untuk logo yayasan */
    @keyframes pulse-logo {
        0% { transform: scale(1); opacity: 0.7; }
        50% { transform: scale(1.1); opacity: 1; }
        100% { transform: scale(1); opacity: 0.7; }
    }
    
    .logo-pulse {
        animation: pulse-logo 2s infinite;
        max-width: 150px;
        margin-bottom: 1rem;
    }
    
    /* Animasi untuk card info */
    .pulsating-card {
        position: relative;
        overflow: hidden !important;
        z-index: 1;
        animation: glowPulse 2s infinite ease-in-out;
        border: none !important;
    }
    
    /* Animasi denyut (detakan) */
    @keyframes glowPulse {
        0% {
            box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7),
                        0 0 0 0 rgba(255, 87, 34, 0.5);
            transform: scale(1);
        }
        70% {
            box-shadow: 0 0 0 15px rgba(255, 152, 0, 0),
                        0 0 0 30px rgba(255, 87, 34, 0);
            transform: scale(1.01);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(255, 152, 0, 0),
                        0 0 0 0 rgba(255, 87, 34, 0);
            transform: scale(1);
        }
    }
    
    /* Animasi untuk border luar */
    .pulsating-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 0;
        border-radius: inherit;
    }
    
    /* OPTION 1: Gradient berputar dengan animasi sudut */
    .rotating-gradient {
        background: linear-gradient(135deg, #45faae 0%, #0bb9a0 100%) !important;
        animation: rotateGradient 3s linear infinite;
    }
    
    @keyframes rotateGradient {
        0% {
            background: linear-gradient(0deg, #45faae 0%, #0bb9a0 100%) !important;
        }
        25% {
            background: linear-gradient(90deg, #45faae 0%, #0bb9a0 100%) !important;
        }
        50% {
            background: linear-gradient(180deg, #45faae 0%, #0bb9a0 100%) !important;
        }
        75% {
            background: linear-gradient(270deg, #45faae 0%, #0bb9a0 100%) !important;
        }
        100% {
            background: linear-gradient(360deg, #45faae 0%, #0bb9a0 100%) !important;
        }
    }
    
    /* OPTION 2: Gradient bergerak horizontal */
    .horizontal-moving-gradient {
        background: linear-gradient(
            90deg,
            #45faae 0%,
            #0bb9a0 25%,
            #45faae 50%,
            #0bb9a0 75%,
            #45faae 100%
        ) !important;
        background-size: 400% 100% !important;
        animation: moveGradient 4s linear infinite;
    }
    
    @keyframes moveGradient {
        0% {
            background-position: 0% 0%;
        }
        100% {
            background-position: 400% 0%;
        }
    }
    
    /* OPTION 3: Gradient berputar diagonal dengan efek melingkar */
    .spiral-gradient {
        background: 
            radial-gradient(circle at center, transparent 20%, #45faae 21%, #0bb9a0 40%, transparent 41%),
            radial-gradient(circle at center, transparent 30%, #0bb9a0 31%, #45faae 50%, transparent 51%),
            radial-gradient(circle at center, transparent 40%, #45faae 41%, #0bb9a0 60%, transparent 61%);
        background-size: 200% 200% !important;
        animation: spiralMove 8s linear infinite;
    }
    
    @keyframes spiralMove {
        0% {
            background-position: 0% 0%, 100% 100%, 50% 50%;
        }
        25% {
            background-position: 100% 0%, 0% 100%, 50% 50%;
        }
        50% {
            background-position: 100% 100%, 0% 0%, 50% 50%;
        }
        75% {
            background-position: 0% 100%, 100% 0%, 50% 50%;
        }
        100% {
            background-position: 0% 0%, 100% 100%, 50% 50%;
        }
    }
    
    /* OPTION 4: Gradient berputar seperti radar */
    .radar-gradient {
        background: 
            conic-gradient(
                from 0deg at 50% 50%,
                #45faae 0deg,
                #0bb9a0 90deg,
                #45faae 180deg,
                #0bb9a0 270deg,
                #45faae 360deg
            ) !important;
        background-size: 400% 400% !important;
        animation: radarRotate 3s linear infinite;
    }
    
    @keyframes radarRotate {
        0% {
            background-position: 0% 0%;
            transform: scale(1);
        }
        50% {
            background-position: 100% 100%;
            transform: scale(1.02);
        }
        100% {
            background-position: 0% 0%;
            transform: scale(1);
        }
    }
    
    /* OPTION 5: Gradient berputar dengan efek gelombang (pilihan terbaik) */
    .wave-gradient {
        background: 
            linear-gradient(45deg, 
                #45faae 0%, 
                #0bb9a0 25%, 
                #45faae 50%, 
                #0bb9a0 75%, 
                #45faae 100%
            ) !important;
        background-size: 400% 400% !important;
        animation: waveMove 6s ease-in-out infinite;
    }
    
    @keyframes waveMove {
        0%, 100% {
            background-position: 0% 50%;
            transform: scale(1);
        }
        25% {
            background-position: 100% 50%;
            transform: scale(1.01);
        }
        50% {
            background-position: 50% 100%;
            transform: scale(1);
        }
        75% {
            background-position: 50% 0%;
            transform: scale(1.01);
        }
    }
    
    /* OPTION 6: Gradient berputar dengan efek fire/flame */
    .fire-gradient {
        background: 
            linear-gradient(45deg, 
                #45faae 0%, 
                #0bb9a0 20%, 
                #45faae 40%, 
                #0bb9a0 60%, 
                #45faae 80%, 
                #0bb9a0 100%
            ) !important;
        background-size: 400% 400% !important;
        animation: fireMove 5s ease infinite;
    }
    
    @keyframes fireMove {
        0% {
            background-position: 0% 0%;
            filter: hue-rotate(0deg);
        }
        25% {
            background-position: 100% 0%;
            filter: hue-rotate(30deg);
        }
        50% {
            background-position: 100% 100%;
            filter: hue-rotate(60deg);
        }
        75% {
            background-position: 0% 100%;
            filter: hue-rotate(30deg);
        }
        100% {
            background-position: 0% 0%;
            filter: hue-rotate(0deg);
        }
    }
    
    /* OPTION 7: Gradient acak random movement */
    .random-gradient {
        background: 
            linear-gradient(45deg, 
                #45faae 0%, 
                #0bb9a0 33%, 
                #45faae 66%, 
                #0bb9a0 100%
            ) !important;
        background-size: 300% 300% !important;
        animation: randomMove 8s ease-in-out infinite;
    }
    
    @keyframes randomMove {
        0% {
            background-position: 0% 0%;
            background-size: 300% 300%;
        }
        25% {
            background-position: 100% 0%;
            background-size: 350% 350%;
        }
        50% {
            background-position: 100% 100%;
            background-size: 300% 300%;
        }
        75% {
            background-position: 0% 100%;
            background-size: 250% 250%;
        }
        100% {
            background-position: 0% 0%;
            background-size: 300% 300%;
        }
    }
    
    /* Animasi border luar untuk denyut */
    .pulsating-card::after {
        content: '';
        position: absolute;
        top: -4px;
        left: -4px;
        right: -4px;
        bottom: -4px;
        border: 2px solid transparent;
        border-radius: calc(0.375rem + 4px);
        animation: borderPulse 2s infinite ease-in-out;
        pointer-events: none;
        z-index: -1;
    }
    
    @keyframes borderPulse {
        0% {
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4);
            opacity: 1;
            transform: scale(1);
        }
        50% {
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 0 0 8px rgba(255, 255, 255, 0);
            opacity: 0.8;
            transform: scale(1.02);
        }
        100% {
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 0 0 rgba(255, 255, 255, 0);
            opacity: 1;
            transform: scale(1);
        }
    }
    
    /* Animasi teks untuk kondisi ada jadwal */
    .pulsating-card .info-text {
        animation: textPulse 3s infinite ease-in-out;
        display: inline-block;
        position: relative;
        z-index: 2;
    }
    
    @keyframes textPulse {
        0%, 100% {
            transform: scale(1);
            color: #000;
            text-shadow: 0 0 0 rgba(255, 255, 255, 0);
        }
        50% {
            transform: scale(1.05);
            color: #fff;
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
    }
    
    /* Dark mode support */
    [data-bs-theme="dark"] .pulsating-card .info-text {
        color: #fff;
    }
    
    [data-bs-theme="dark"] .pulsating-card::after {
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    [data-bs-theme="dark"] .pulsating-card::before {
        border-color: rgba(255, 255, 255, 0.3);
    }
    
    [data-bs-theme="dark"] .pulsating-card .info-text {
        color: #fff;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .pulsating-card {
            margin: 0 5px !important;
        }
        
        .pulsating-card .info-text {
            font-size: 0.9rem;
            display: block;
            margin-top: 5px;
        }
        
        /* Kurangi kecepatan animasi pada mobile untuk performa */
        .wave-gradient {
            animation-duration: 8s;
        }
        
        .fire-gradient {
            animation-duration: 6s;
        }
        
        @keyframes glowPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0.7),
                            0 0 0 0 rgba(255, 87, 34, 0.5);
                transform: scale(1);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(255, 152, 0, 0),
                            0 0 0 20px rgba(255, 87, 34, 0);
                transform: scale(1.005);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(255, 152, 0, 0),
                            0 0 0 0 rgba(255, 87, 34, 0);
                transform: scale(1);
            }
        }
    }
    
    /* Style untuk icon */
    .pulsating-card .bi-info-circle {
        animation: iconPulse 2s infinite ease-in-out;
        display: inline-block;
    }
    
    @keyframes iconPulse {
        0%, 100% {
            transform: scale(1) rotate(0deg);
        }
        25% {
            transform: scale(1.1) rotate(90deg);
        }
        50% {
            transform: scale(1) rotate(180deg);
        }
        75% {
            transform: scale(1.1) rotate(270deg);
        }
    }
    
    /* ===== PENAMBAHAN: SCROLLABLE LIST UNTUK PERIZINAN & PELANGGARAN ===== */
    .scrollable-list {
        max-height: 250px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .scrollable-list::-webkit-scrollbar {
        width: 6px;
    }
    .scrollable-list::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 3px;
    }
    [data-bs-theme="dark"] .scrollable-list::-webkit-scrollbar-thumb {
        background: #555;
    }
    
    </style>
</head>
<body>
    
    <!-- PERBAIKAN: Notifikasi dengan fungsi buka/tutup yang benar -->
    <?php if ($notifikasi_aktif == '1' && count($notifikasi_jadwal_belum_isi) > 0): ?>
        <!-- Ikon lonceng minimized - hanya tampil jika ada notifikasi -->
        <div class="notification-minimized" id="notificationMinimized">
            <div class="notification-bell position-relative" id="notificationBell" style="cursor: pointer;">
                <i class="bi bi-bell-fill" style="font-size: 1.5rem; color: <?= $dark_mode ? '#fff' : '#000' ?>;"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount">
                    <?= count($notifikasi_jadwal_belum_isi) ?>
                </span>
            </div>
        </div>
        
        <!-- Panel Notifikasi - dimulai dengan status d-none -->
        <div class="notification-panel-card card shadow-sm d-none" id="notificationPanel">
            <div class="card-header toast-header-warning modal-header justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bell-fill me-2"></i> Notifikasi Jadwal</h5>
                <button type="button" class="btn-close btn-sm" id="minimizeNotification"></button>
            </div>
            <div class="card-body toast-body-scroll" style="max-height: 400px; overflow-y: auto;">
                <p class="mb-3"><strong>Ada <?= count($notifikasi_jadwal_belum_isi) ?> jadwal yang belum diisi!</strong></p>
                
                <?php foreach ($notifikasi_jadwal_belum_isi as $index => $notif): ?>
                <div class="notification-item">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="flex-grow-1">
                            <small class="text-muted d-block"><?= $notif['jenis'] ?></small>
                            <strong class="d-block"><?= htmlspecialchars($notif['mata_pelajaran']) ?></strong>
                            <small class="text-muted d-block">
                                <?= htmlspecialchars($notif['kelas']) ?>
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i><?= $notif['waktu'] ?>
                            </small>
                        </div>
                        <a href="<?= $notif['link'] ?>" class="btn btn-sm btn-warning ms-2">
                            <i class="bi bi-pencil-square"></i> Isi
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="mt-3 pt-2 border-top">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i>
                        Notifikasi muncul <?= $waktu_tampil_jam ?> jam setelah jadwal dimulai 
                    </small>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="text-center mb-8">
            <div class="d-flex flex-column align-items-center">
                <!-- Tulisan Arab dengan animasi per kata -->
                <div class="arabic-container">
                    <div class="arabic-font arabic-text">
                        <h3 class="display-6">
                            <span dir="rtl" style="display: inline-block; text-align: center; margin: 0 auto;">
                            <?php
                            $arabicText = "« السلام عليكم ورحمة الله »";
                            $arabicWords = explode(' ', $arabicText);
                            foreach ($arabicWords as $wordIndex => $word) {
                                echo '<span class="word" style="--word-index: '.$wordIndex.';">'.$word.'</span>';
                                echo '<span class="space" style="--word-index: '.$wordIndex.';"> </span>';
                            }
                            ?>
                            </span>
                        </h3>
                    </div>
                </div>
                
                <!-- Greeting dengan animasi per huruf -->
                <div class="text-center">
                    <!-- Baris pertama: Salam -->
                    <h1 class="display-4 welcome-heading mb-4">
                        <?php
                        $greeting = get_greeting() . ' !';
                        $greetingChars = preg_split('//u', $greeting, -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($greetingChars as $i => $char) {
                            echo $char === ' ' ? 
                                '<span class="space" style="--i: '.$i.';">&nbsp;</span>' : 
                                '<span style="--i: '.$i.';">'.$char.'</span>';
                        }
                        ?>
                    </h1>
                    
                    <!-- Baris kedua: Nama pengguna -->
                    <h2 class="display-7 welcome-heading mb-4">
                        <?php
                        $username = (isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : "Pengguna") . '🌹';
                        $usernameChars = preg_split('//u', $username, -1, PREG_SPLIT_NO_EMPTY);
                        foreach ($usernameChars as $i => $char) {
                            echo $char === ' ' ? 
                                '<span class="space" style="--i: '.$i.';">&nbsp;</span>' : 
                                '<span style="--i: '.$i.';">'.$char.'</span>';
                        }
                        ?>
                        <div class="rose-icon">
                            <i class="fas fa-spa"></i>
                        </div>
                    </h2>
                </div>
            </div>
            
            <!-- Tampilkan Tanggal Masehi dan Hijriyah -->
            <div class="hijri-date-container mb-3">
                
                <!-- Tampilkan Tanggal Hijriyah Resmi -->
                <div class="text-center mb-1">
                    <div class="hijri-date-official">
                        <small class="ms-1 opacity-75 text-body">
                            <i class="bi bi-moon-stars-fill me-1"></i>
                            <span id="hijri-date-text"><?= htmlspecialchars($tanggal_hijriyah) ?></span>
                            <i class="bi bi-calendar2 me-1"></i>Versi Resmi Kemenag
                        </small>
                    </div>
                </div>
                
                <div class="badge bg-primary p-2 shadow-sm border-0 custom-date-badge" style="background: #1b5e20 !important;">    
                    <?= date('l, d F Y') ?> M | 
                    <span id="hijri-date-text" class="fw-bold">
                        <?= htmlspecialchars($tanggal_hijriyah) ?>
                    </span>
                </div>
                
            </div>
            
            <!-- Pesan selamat datang dengan animasi per huruf acak -->
            <p class="lead mt-3 welcome-dashboard-message" id="dashboardMessage">
                <?php
                // Baris pertama
                $welcomeMsg1 = "Selamat datang di Sistem Absensi Online";
                $welcomeChars1 = preg_split('//u', $welcomeMsg1, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($welcomeChars1 as $char) {
                    echo $char === ' ' ? 
                        '<span class="space">&nbsp;</span>' : 
                        '<span>'.$char.'</span>';
                }
                ?>
                
                <!-- Break untuk mobile -->
                <span class="break-mobile"></span>
                
                <?php
                // Baris kedua
                $welcomeMsg2 = "PP. Matholi'ul Anwar";
                $welcomeChars2 = preg_split('//u', $welcomeMsg2, -1, PREG_SPLIT_NO_EMPTY);
                foreach ($welcomeChars2 as $char) {
                    echo $char === ' ' ? 
                        '<span class="space">&nbsp;</span>' : 
                        '<span>'.$char.'</span>';
                }
                ?>
            </p>
        </div>
        
        <!-- Tambahkan di dashboard.php setelah welcome message -->
        <?php if (!isset($_SESSION['pwa_promotion_dismissed']) && $_SESSION['role'] !== 'guest'): ?>
        <div class="alert alert-info p-2 p-md-3 alert-dismissible fade show mx-2 mx-md-0" id="pwa-promotion">
            <div class="d-flex align-items-center flex-column flex-md-row">
                <div class="d-flex align-items-center mb-2 mb-md-0 me-md-3">
                    <i class="bi bi-phone me-2 me-md-3" style="font-size: 1.5rem;"></i>
                    <div class="flex-grow-1 text-center text-md-start">
                        <p class="mb-1" style="font-size: 0.875rem;">
                            <strong>Tambahkan ke Layar Utama!</strong>
                        </p>
                        <p class="mb-0" style="font-size: 0.8rem;">
                            Akses lebih cepat seperti aplikasi native. Tersedia untuk perangkat mobile dan desktop.
                        </p>
                    </div>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto align-items-center">
                    <button type="button" class="btn btn-primary btn-sm flex-fill" 
                            style="font-size: 0.8rem; padding: 0.4rem 0.8rem;" 
                            id="installPWA">
                        <i class="bi bi-download me-1"></i> Pasang Aplikasi
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm" 
                            onclick="showPWAInstructions()"
                            style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
                        <i class="bi bi-info-circle me-1"></i> Cara Pasang
                    </button>
                    <button type="button" class="btn-close align-self-center ms-2" 
                            data-bs-dismiss="alert" aria-label="Close" 
                            onclick="dismissPWAPromotion()"
                            style="font-size: 0.7rem;">
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Modal untuk instruksi PWA -->
        <div class="modal fade" id="pwaInstructionsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Cara Menambahkan ke Layar Utama</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Mobile -->
                            <div class="col-md-6 mb-3">
                                <h6><i class="bi bi-phone me-2"></i>Untuk Perangkat Mobile:</h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <strong>Android (Chrome):</strong>
                                        <ol class="mb-0 mt-1">
                                            <li>Buka menu browser (3 titik di pojok kanan atas)</li>
                                            <li>Pilih "Tambahkan ke Layar Utama"</li>
                                            <li>Konfirmasi dengan menekan "Tambahkan"</li>
                                        </ol>
                                    </div>
                                    <div class="list-group-item">
                                        <strong>iPhone/iPad (Safari):</strong>
                                        <ol class="mb-0 mt-1">
                                            <li>Tekan tombol "Bagikan" (kotak dengan panah)</li>
                                            <li>Scroll ke bawah, pilih "Tambahkan ke Layar Utama"</li>
                                            <li>Tekan "Tambahkan" di pojok kanan atas</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Desktop -->
                            <div class="col-md-6 mb-3">
                                <h6><i class="bi bi-laptop me-2"></i>Untuk Desktop:</h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item">
                                        <strong>Chrome/Edge:</strong>
                                        <ol class="mb-0 mt-1">
                                            <li>Klik ikon install (⚙️) di address bar</li>
                                            <li>Pilih "Install Sistem Absensi Online"</li>
                                            <li>Aplikasi akan terpasang di menu Start/dock</li>
                                        </ol>
                                    </div>
                                    <div class="list-group-item">
                                        <strong>Browser Lain:</strong>
                                        <ol class="mb-0 mt-1">
                                            <li>Buka menu browser (⋮ atau ≡)</li>
                                            <li>Cari opsi "Install App" atau serupa</li>
                                            <li>Konfirmasi pemasangan</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small>Pastikan browser Anda mendukung PWA. Chrome, Edge, Safari, dan Firefox versi terbaru sudah mendukung.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" onclick="checkPWAInstallation()">
                            <i class="bi bi-check-circle me-1"></i>Cek Kesiapan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="card border-warning p-2 p-md-3 justify-content-between align-items-center mx-2 mx-md-0 <?= $card_class ?>" 
             style="border-width: 2px !important;">
            <div class="text-center" style="padding: 10px 0; position: relative; z-index: 1;">
                <i class="bi bi-info-circle" style="font-size: 1.2rem;"></i>
                <span class="info-text ms-2" style="font-weight: 500;">
                    <?= htmlspecialchars($info_text) ?>
                </span>
            </div>
        </div>
        
        <div class="row mt-4">
            
            <?php if (in_array($_SESSION['role'], ['admin', 'staff'])): ?>
            <div class="col-lg-6 col-12 mb-4">
                <!-- Card Status Blast WhatsApp untuk desktop -->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-whatsapp me-2"></i> WhatsApp Blast
                            <small class="float-end" style="font-size: 0.9rem;"> Status :
                                <span class="badge bg-<?= $blast_status == 'aktif' ? 'success' : 'danger' ?>">
                                    <?= $blast_status == 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </small>
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text small">
                            <i class="bi bi-clock me-1"></i>
                            Notifikasi: 
                            <?php 
                            $notif_labels = [];
                            foreach ($blast_config['multiple_notifikasi'] as $notif) {
                                $notif_labels[] = $notif['label'];
                            }
                            echo implode(', ', $notif_labels);
                            ?>
                        </p>
                        <p class="card-text small">
                            <i class="bi bi-sun me-1"></i>
                            Jam aktif: <?= $blast_config['jam_awal'] ?> - <?= $blast_config['jam_akhir'] ?>
                        </p>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="../blast/index.php" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-sliders me-1"></i> Kelola Blast
                            </a>
                            <a href="../blast/blast_realtime.php?manual=1" class="btn btn-sm btn-outline-success" target="_blank">
                                <i class="bi bi-send me-1"></i> Test Kirim
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Card Akses Cepat untuk desktop -->
            <div class="col-lg-6 col-12 mb-4">
            <!--<div class="quick-access-container d-none d-lg-block">-->
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0"><i class="bi bi-speedometer2"></i> Akses Cepat</h5>
                    </div>
                    <div class="card-body mt-2 mb-2">
                        <div class="d-grid gap-1 d-md-flex justify-content-md-end mt-2 mb-2">
                            <a href="absensi.php" class="btn btn-lg btn-outline-primary text-start">
                                ____|  <i class="bi bi-clipboard-check me-0"></i> |____ Input Absen 
                            </a>
                            <a href="jadwal.php" class="btn btn-lg btn-outline-success text-start">
                                ____|  <i class="bi bi-calendar-week me-0"></i> |____ Lihat Jadwal
                            </a>
                            <a href="database.php" class="btn btn-lg btn-outline-secondary text-start">
                                ____|  <i class="bi bi-people me-0"></i> |____ Data Murid
                            </a>
                            <a href="rekapitulasi.php" class="btn btn-lg btn-outline-info text-start">
                                ____|  <i class="bi bi-bar-chart-line me-0"></i> |____ Rekapitulasi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Jadwal Notifikasi Wa Blast untuk desktop-->
            <?php if (in_array($_SESSION['role'], ['admin', 'staff'])): ?>    
                <?php if ($blast_status == 'aktif'): ?>
                <div class="col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bell me-2"></i> 
                                Jadwal Notifikasi WhatsApp Hari Ini - <?= $hari_ini ?> <!-- Akan tampil bahasa Arab -->
                                <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (count($jadwal_dengan_notifikasi) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Jenis</th>
                                            <th>Kelas/Kamar</th>
                                            <th>Jadwal Mulai</th>
                                            <th>Notifikasi dikirim</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jadwal_dengan_notifikasi as $jadwal): ?>
                                        <tr>
                                            <td><span class="badge bg-primary"><?= $jadwal['jenis'] ?></span></td>
                                            <td><?= htmlspecialchars($jadwal['nama_kelas'] ?? $jadwal['nama_kamar'] ?? '-') ?></td>
                                            <td><strong><?= $jadwal['jam_mulai'] ?></strong></td>
                                            <td>
                                                <?php foreach ($jadwal['waktu_notifikasi'] as $notif): ?>
                                                <span class="badge bg-warning text-dark me-1"><?= $notif['waktu'] ?></span>
                                                <?php endforeach; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success">Aktif</span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info mb-0">Tidak ada jadwal untuk Saat ini.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- PERBAIKAN: Jadwal Kegiatan - DIURUTKAN berdasarkan Kamar (A-Z) lalu Kegiatan (A-Z) -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i> 
                            Jadwal Kegiatan Hari Ini - <?= $hari_ini ?> <!-- Akan tampil bahasa Arab -->
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </h5>

                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (count($jadwal_kegiatan_hari_ini) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kamar</th>
                                    <th>Kegiatan</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Data sudah diurutkan dengan natural sort
                                foreach ($jadwal_kegiatan_hari_ini as $jadwal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jadwal['nama_kamar']) ?></td>
                                    <td><?= htmlspecialchars($jadwal['nama_kegiatan']) ?></td>
                                    <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
                                    <td>
                                        <a href="absensi.php?filter&tanggal_kegiatan=<?= $today ?>&kegiatan_id=<?= $jadwal['kegiatan_id'] ?>&active_tab=kegiatan#kegiatan" 
                                           class="btn btn-sm btn-secondary">Absen</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">Belum ada jadwal kegiatan saat ini.</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- PERBAIKAN: Jadwal Quran - DIURUTKAN berdasarkan Kelas (A-Z) lalu Mata Pelajaran (A-Z) -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i> 
                            Jadwal Qur'an Hari Ini  - <?= $hari_ini ?> <!-- Akan tampil bahasa Arab -->
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (count($jadwal_quran_hari_ini) > 0): ?>  <!-- PERBAIKAN: Gunakan $jadwal_quran_hari_ini -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kelas</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // PERBAIKAN: Gunakan $jadwal_quran_hari_ini, bukan $jadwal_hari_ini
                                foreach ($jadwal_quran_hari_ini as $jadwal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jadwal['nama_kelas']) ?></td>
                                    <td><?= htmlspecialchars($jadwal['mata_pelajaran']) ?></td>
                                    <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
                                    <td>
                                        <!-- PERBAIKAN: Link absen untuk Quran -->
                                        <a href="absensi.php?filter_quran&tanggal_quran=<?= $today ?>&jadwal_quran_id=<?= $jadwal['id'] ?>&active_tab=quran#quran" 
                                           class="btn btn-sm btn-secondary">Absen</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">Belum ada jadwal mengaji saat ini.</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- PERBAIKAN: Jadwal Mengajar (Madin) - DIURUTKAN berdasarkan Kelas (A-Z) lalu Mata Pelajaran (A-Z) -->
            <div class="col-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-calendar-event me-2"></i> 
                            Jadwal Madin Hari Ini - <?= $hari_ini ?> <!-- Akan tampil bahasa Arab -->
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </h5>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (count($jadwal_hari_ini) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Data sudah diurutkan berdasarkan Kelas (A-Z) lalu Mata Pelajaran (A-Z) dari query
                                foreach ($jadwal_hari_ini as $jadwal): ?>
                                <tr>
                                    <td><?= htmlspecialchars($jadwal['mata_pelajaran']) ?></td>
                                    <td><?= htmlspecialchars($jadwal['nama_kelas']) ?></td>
                                    <td><?= $jadwal['jam_mulai'] ?> - <?= $jadwal['jam_selesai'] ?></td>
                                    <td>
                                        <a href="absensi.php?filter&tanggal=<?= $today ?>&jadwal_id=<?= $jadwal['jadwal_id'] ?>&active_tab=pelajaran#pelajaran" 
                                           class="btn btn-sm btn-secondary">Absen</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info">Belum ada jadwal mengajar saat ini.</div>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- CARD STATISTIK ABSENSI GURU UNTUK DASHBOARD -->
        <?php if (in_array($_SESSION['role'], ['admin', 'staff'])): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-warning bg-opacity-10 border-warning">
                    <div class="card-body py-3">
                        <div class="card-header bg-info text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title text-white mb-0">
                                    <i class="bi bi-people-fill me-2"></i>Statistik Absensi Guru
                                </h5>
                                <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                            </div>
                        </div>
                        
                        <?php
                        // Hitung presentase untuk semua status
                        $total_hari_ini = $stats_guru['hari_ini']['total_hari_ini'];
                        $presentase_hadir = 0;
                        $presentase_sakit = 0;
                        $presentase_izin = 0;
                        $presentase_alpa = 0;
                        
                        if ($total_hari_ini > 0) {
                            $presentase_hadir = round(($stats_guru['hari_ini']['hadir_hari_ini'] / $total_hari_ini) * 100, 1);
                            $presentase_sakit = round(($stats_guru['hari_ini']['sakit_hari_ini'] / $total_hari_ini) * 100, 1);
                            $presentase_izin = round(($stats_guru['hari_ini']['izin_hari_ini'] / $total_hari_ini) * 100, 1);
                            $presentase_alpa = round(($stats_guru['hari_ini']['alpa_hari_ini'] / $total_hari_ini) * 100, 1);
                        }
                        
                        // Hitung presentase kehadiran keseluruhan
                        $presentase_kehadiran = 0;
                        if ($stats_guru['total_guru'] > 0) {
                            $presentase_kehadiran = round(($stats_guru['hari_ini']['hadir_hari_ini'] / $stats_guru['total_guru']) * 100, 1);
                        }
                        ?>
                        
                        <!-- Desktop View (6 kolom) -->
                        <div class="d-none d-md-block">
                            <div class="row mt-3 text-center">
                                
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-success bg-opacity-10">
                                        <div class="h5 mb-1 text-success"><?= $stats_guru['hari_ini']['hadir_hari_ini'] ?></div>
                                        <small class="text-muted">Hadir</small>
                                        <div class="small text-success fw-bold mt-1"><?= $presentase_hadir ?>%</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-warning bg-opacity-10">
                                        <div class="h5 mb-1 text-warning"><?= $stats_guru['hari_ini']['sakit_hari_ini'] ?></div>
                                        <small class="text-muted">Sakit</small>
                                        <div class="small text-warning fw-bold mt-1"><?= $presentase_sakit ?>%</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-info bg-opacity-10">
                                        <div class="h5 mb-1 text-info"><?= $stats_guru['hari_ini']['izin_hari_ini'] ?></div>
                                        <small class="text-muted">Izin</small>
                                        <div class="small text-info fw-bold mt-1"><?= $presentase_izin ?>%</div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="border rounded p-2 bg-danger bg-opacity-10">
                                        <div class="h5 mb-1 text-danger"><?= $stats_guru['hari_ini']['alpa_hari_ini'] ?></div>
                                        <small class="text-muted">Alpa</small>
                                        <div class="small text-danger fw-bold mt-1"><?= $presentase_alpa ?>%</div>
                                    </div>
                                </div>
                                <!-- Ringkasan Desktop -->
                                <div class="col-12 mt-3">
                                    <div class="border rounded p-2 bg-secondary bg-opacity-10">
                                        <small class="fw-bold">Total Absensi guru Hari Ini :</small>
                                        <div class="fw-bold mt-1">
                                            <small class="fw-bold"><?= $total_hari_ini ?> guru</small>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?= $presentase_hadir ?>%"></div>
                                            <div class="progress-bar bg-warning" style="width: <?= $presentase_sakit ?>%"></div>
                                            <div class="progress-bar bg-info" style="width: <?= $presentase_izin ?>%"></div>
                                            <div class="progress-bar bg-danger" style="width: <?= $presentase_alpa ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
                        <!-- Mobile View (bertumpuk) -->
                        <div class="d-block d-md-none">
                            <div class="row mt-3">
                                <!-- Hadir -->
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3 bg-success bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h4 mb-1 text-success"><?= $stats_guru['hari_ini']['hadir_hari_ini'] ?></div>
                                            <small class="text-muted">Hadir</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="h5 text-success"><?= $presentase_hadir ?>%</div>
                                            <small class="text-muted">dari <?= $total_hari_ini ?> absen</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Sakit -->
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3 bg-warning bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h4 mb-1 text-warning"><?= $stats_guru['hari_ini']['sakit_hari_ini'] ?></div>
                                            <small class="text-muted">Sakit</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="h5 text-warning"><?= $presentase_sakit ?>%</div>
                                            <small class="text-muted">dari <?= $total_hari_ini ?> absen</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Izin -->
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3 bg-info bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h4 mb-1 text-info"><?= $stats_guru['hari_ini']['izin_hari_ini'] ?></div>
                                            <small class="text-muted">Izin</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="h5 text-info"><?= $presentase_izin ?>%</div>
                                            <small class="text-muted">dari <?= $total_hari_ini ?> absen</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Alpa -->
                                <div class="col-12 mb-3">
                                    <div class="border rounded p-3 bg-danger bg-opacity-10 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="h4 mb-1 text-danger"><?= $stats_guru['hari_ini']['alpa_hari_ini'] ?></div>
                                            <small class="text-muted">Alpa</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="h5 text-danger"><?= $presentase_alpa ?>%</div>
                                            <small class="text-muted">dari <?= $total_hari_ini ?> absen</small>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            
                            <!-- Ringkasan Mobile -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-info py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="fw-bold">Total Absensi guru Hari Ini:</small>
                                            <small class="fw-bold"><?= $total_hari_ini ?> guru</small>
                                        </div>
                                        <div class="progress mt-1" style="height: 8px;">
                                            <div class="progress-bar bg-success" style="width: <?= $presentase_hadir ?>%"></div>
                                            <div class="progress-bar bg-warning" style="width: <?= $presentase_sakit ?>%"></div>
                                            <div class="progress-bar bg-info" style="width: <?= $presentase_izin ?>%"></div>
                                            <div class="progress-bar bg-danger" style="width: <?= $presentase_alpa ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Statistik Absensi Murid-->
        <div class="row mt-4">
            <!-- Statistik Absensi Quran -->
            <div class="col-md-4 mb-4 stat-card">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="bi bi-clipboard-data me-2"></i> Statistik Absensi Qur'an</h5>
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attendanceChartQuran"></canvas>
                        </div>
                        
                        <!-- Progress bars untuk Quran -->
                        <div class="mt-4">
                            <?php 
                            // Urutkan status secara alfabetis
                            $statuses = ['Alpa', 'Hadir', 'Izin', 'Sakit'];
                            sort($statuses);
                            
                            foreach ($statuses as $status): 
                                $color = [
                                    'Hadir' => 'success',
                                    'Sakit' => 'warning',
                                    'Izin' => 'info',
                                    'Alpa' => 'danger'
                                ][$status];
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= $status ?>: <?= $stats_quran_data['stats'][$status] ?> (<?= round($percentages_quran[$status], 1) ?>%)</span>
                                    <span><?= round($percentages_quran[$status], 1) ?>%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentages_quran[$status] ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Absensi Madin -->
            <div class="col-md-4 mb-4 stat-card">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="bi bi-clipboard-data me-2"></i> Statistik Absensi Madin</h5>
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attendanceChart"></canvas>
                        </div>
                        
                        <!-- Progress bars untuk Madin -->
                        <div class="mt-4">
                            <?php 
                            // Urutkan status secara alfabetis
                            $statuses = ['Alpa', 'Hadir', 'Izin', 'Sakit'];
                            sort($statuses);
                            
                            foreach ($statuses as $status): 
                                $color = [
                                    'Hadir' => 'success',
                                    'Sakit' => 'warning',
                                    'Izin' => 'info',
                                    'Alpa' => 'danger'
                                ][$status];
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= $status ?>: <?= $stats_madin['stats'][$status] ?> (<?= round($percentages_madin[$status], 1) ?>%)</span>
                                    <span><?= round($percentages_madin[$status], 1) ?>%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentages_madin[$status] ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistik Absensi Kegiatan -->
            <div class="col-md-4 mb-4 stat-card">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="bi bi-clipboard-data me-2"></i> Statistik Absensi Kegiatan</h5>
                            <small class="float-end" style="font-size: 0.7rem;"><?= date('d F Y') ?></small>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="attendanceChartKegiatan"></canvas>
                        </div>
                        <!-- Progress bars untuk Kegiatan -->
                        <div class="mt-4">
                            <?php 
                            // Urutkan status secara alfabetis
                            $statuses = ['Alpa', 'Hadir', 'Izin', 'Sakit'];
                            sort($statuses);
                            
                            foreach ($statuses as $status): 
                                $color = [
                                    'Hadir' => 'success',
                                    'Sakit' => 'warning',
                                    'Izin' => 'info',
                                    'Alpa' => 'danger'
                                ][$status];
                            ?>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span><?= $status ?>: <?= $stats_kegiatan_data['stats'][$status] ?> (<?= round($percentages_kegiatan[$status], 1) ?>%)</span>
                                    <span><?= round($percentages_kegiatan[$status], 1) ?>%</span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-<?= $color ?>" style="width: <?= $percentages_kegiatan[$status] ?>%"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Di bagian Statistik Pelanggaran dan Perizinan -->
        <div class="row mt-4">
            
            <!-- Card Baru: Perizinan Terbaru -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="bi bi-clipboard-check me-2"></i> Perizinan Terbaru </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="scrollable-list">
                            <div class="list-group">
                                <?php if (isset($perizinan_terbaru) && count($perizinan_terbaru) > 0): ?>
                                    <?php foreach ($perizinan_terbaru as $row): ?>
                                    <a href="pelanggaran.php" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?= htmlspecialchars($row['nama']) ?></h6>
                                            <small><?= $row['tanggal'] ?></small>
                                        </div>
                                        <p class="mb-1"><strong><?= htmlspecialchars($row['jenis']) ?>:</strong> 
                                        <?= htmlspecialchars($row['deskripsi']) ?></p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Kelas: <?= htmlspecialchars($row['nama_kelas']) ?></small>
                                            <span class="badge bg-<?= 
                                                $row['status_izin'] == 'Disetujui' ? 'success' : 
                                                ($row['status_izin'] == 'Ditolak' ? 'danger' : 'warning') 
                                            ?>">
                                                <?= $row['status_izin'] ?>
                                            </span>
                                        </div>
                                    </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">Tidak ada perizinan 3 hari terakhir.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Pelanggaran Terbaru -->
            <div class="col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0"><i class="bi bi-exclamation-triangle me-2"></i> Pelanggaran Terbaru </h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="scrollable-list">
                            <div class="list-group">
                                <?php if (isset($pelanggaran_terbaru) && count($pelanggaran_terbaru) > 0): ?>
                                    <?php foreach ($pelanggaran_terbaru as $row): ?>
                                    <a href="pelanggaran.php" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1"><?= htmlspecialchars($row['nama']) ?></h6>
                                            <small><?= $row['tanggal'] ?></small>
                                        </div>
                                        <p class="mb-1"><strong><?= htmlspecialchars($row['jenis']) ?>:</strong> 
                                        <?= htmlspecialchars($row['deskripsi']) ?></p>
                                        <small class="text-muted">Kelas: <?= htmlspecialchars($row['nama_kelas']) ?></small>
                                    </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="alert alert-info mb-0">Tidak ada pelanggaran 3 hari terakhir.</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    // ===== PERBAIKAN: KONTROL NOTIFIKASI YANG DIPERBAIKI =====
    function initializeNotificationSystem() {
        const notificationBell = document.getElementById('notificationBell');
        const notificationPanel = document.getElementById('notificationPanel');
        const minimizeBtn = document.getElementById('minimizeNotification');
        const notificationMinimized = document.getElementById('notificationMinimized');
    
        console.log('🔔 Initializing notification system...', {
            bell: !!notificationBell,
            panel: !!notificationPanel,
            minimizeBtn: !!minimizeBtn,
            minimized: !!notificationMinimized
        });
    
        // Jika elemen tidak ada, keluar
        if (!notificationBell || !notificationPanel || !minimizeBtn || !notificationMinimized) {
            console.error('❌ Notification elements not found');
            return;
        }
    
        let isNotificationOpen = false;
    
        // Fungsi untuk membuka notifikasi
        function openNotification() {
            console.log('📖 Opening notification panel');
            notificationPanel.classList.remove('d-none');
            isNotificationOpen = true;
            
            // Tambahkan class mobile jika perlu
            if (window.innerWidth < 768) {
                notificationPanel.classList.add('mobile');
            } else {
                notificationPanel.classList.remove('mobile');
            }
        }
        
        // Fungsi untuk menutup notifikasi
        function closeNotification() {
            console.log('📕 Closing notification panel');
            notificationPanel.classList.add('d-none');
            isNotificationOpen = false;
        }
    
        // Toggle panel notifikasi ketika ikon lonceng diklik
        notificationBell.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('🔔 Bell clicked, current state:', isNotificationOpen);
            
            if (isNotificationOpen) {
                closeNotification();
            } else {
                openNotification();
            }
        });
    
        // Minimize panel notifikasi ketika tombol close diklik
        minimizeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('❌ Close button clicked');
            closeNotification();
        });
    
        // Tutup panel ketika klik di luar area notifikasi
        document.addEventListener('click', function(e) {
            if (isNotificationOpen && 
                !notificationPanel.contains(e.target) && 
                !notificationMinimized.contains(e.target)) {
                console.log('🌍 Click outside, closing notification');
                closeNotification();
            }
        });
    
        // Handle escape key untuk menutup notifikasi
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isNotificationOpen) {
                console.log('⌨️ Escape key pressed');
                closeNotification();
            }
        });
    
        // Handle resize untuk responsive
        window.addEventListener('resize', function() {
            if (isNotificationOpen) {
                if (window.innerWidth < 768) {
                    notificationPanel.classList.add('mobile');
                } else {
                    notificationPanel.classList.remove('mobile');
                }
            }
        });
    
        console.log('✅ Notification system initialized successfully');
    }
    
    // PERBAIKAN: Tangani klik link di notifikasi dengan benar
    document.addEventListener('DOMContentLoaded', function() {
        // Inisialisasi sistem notifikasi yang sudah ada
        initializeNotificationSystem();
        
        // PERBAIKAN: Tambahkan event handler untuk link absen di notifikasi
        const notificationPanel = document.getElementById('notificationPanel');
        if (notificationPanel) {
            notificationPanel.addEventListener('click', function(e) {
                // Jika yang diklik adalah link absen (tombol dengan class btn-warning)
                if (e.target.closest('.btn-warning')) {
                    const link = e.target.closest('.btn-warning');
                    const href = link.getAttribute('href');
                    
                    // Tutup panel notifikasi
                    closeNotification();
                    
                    // Simpan status loading sebelum navigasi
                    showLoading();
                    
                    // Navigasi ke halaman absensi
                    setTimeout(() => {
                        window.location.href = href;
                    }, 300);
                    
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        }
    });
    
    // Fungsi untuk menampilkan loading
    function showLoading() {
        // Cek apakah sudah ada loading overlay
        if (!document.getElementById('loadingOverlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.id = 'loadingOverlay';
            loadingOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.7);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 99999;
            `;
            
            const spinner = document.createElement('div');
            spinner.innerHTML = `
                <div style="text-align: center; color: white;">
                    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">Memuat...</span>
                    </div>
                    <p class="mt-3">Mengarahkan ke halaman absensi...</p>
                </div>
            `;
            
            loadingOverlay.appendChild(spinner);
            document.body.appendChild(loadingOverlay);
        }
    }
    
    // Fungsi untuk menutup notifikasi (perlu didefinisikan di scope global)
    function closeNotification() {
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationMinimized = document.getElementById('notificationMinimized');
        
        if (notificationPanel) {
            notificationPanel.classList.add('d-none');
        }
        
        if (notificationMinimized) {
            notificationMinimized.style.display = 'flex';
        }
    }
    
    // ===== PWA Installation Handler =====
    class PWAInstaller {
        constructor() {
            this.deferredPrompt = null;
            this.isPWAInstalled = window.matchMedia('(display-mode: standalone)').matches;
            this.init();
        }
    
        init() {
            // Listen for beforeinstallprompt event
            window.addEventListener('beforeinstallprompt', (e) => {
                console.log('✅ beforeinstallprompt event fired');
                // Prevent Chrome 76+ from automatically showing the prompt
                e.preventDefault();
                // Stash the event so it can be triggered later
                this.deferredPrompt = e;
                
                // Update UI to notify the user they can install the PWA
                this.showInstallPromotion();
            });
    
            // Listen for appinstalled event
            window.addEventListener('appinstalled', (evt) => {
                console.log('🎉 PWA was installed');
                this.isPWAInstalled = true;
                this.hideInstallPromotion();
                this.showSuccessMessage();
            });
    
            // Check on page load if PWA is already installed
            this.checkInstallationStatus();
        }
    
        showInstallPromotion() {
            const pwaPromotion = document.getElementById('pwa-promotion');
            if (pwaPromotion && !this.isPWAInstalled) {
                pwaPromotion.style.display = 'block';
                console.log('📱 Showing PWA installation promotion');
            }
        }
    
        hideInstallPromotion() {
            const pwaPromotion = document.getElementById('pwa-promotion');
            if (pwaPromotion) {
                pwaPromotion.style.display = 'none';
            }
        }
    
        async installApp() {
            if (!this.deferredPrompt) {
                console.log('❌ No install prompt available');
                this.showManualInstructions();
                return;
            }
    
            try {
                // Show the install prompt
                this.deferredPrompt.prompt();
                
                // Wait for the user to respond to the prompt
                const { outcome } = await this.deferredPrompt.userChoice;
                
                console.log(`User response to the install prompt: ${outcome}`);
                
                if (outcome === 'accepted') {
                    console.log('✅ User accepted the install prompt');
                    this.showSuccessMessage();
                } else {
                    console.log('❌ User dismissed the install prompt');
                    this.showDismissMessage();
                }
                
                // We've used the prompt, and can't use it again, throw it away
                this.deferredPrompt = null;
            } catch (error) {
                console.error('Error during PWA installation:', error);
                this.showManualInstructions();
            }
        }
    
        checkInstallationStatus() {
            // Check if app is already installed
            if (this.isPWAInstalled) {
                console.log('📱 PWA is already installed');
                this.hideInstallPromotion();
                return true;
            }
    
            // Check for iOS standalone mode
            if (window.navigator.standalone === true) {
                console.log('📱 iOS PWA is installed');
                this.isPWAInstalled = true;
                this.hideInstallPromotion();
                return true;
            }
    
            return false;
        }
    
        showSuccessMessage() {
            // Show success toast or notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto"><i class="bi bi-check-circle me-2"></i>Berhasil!</strong>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        Aplikasi berhasil dipasang ke perangkat Anda! Anda dapat mengaksesnya dari layar utama.
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 5000);
        }
    
        showDismissMessage() {
            // Show instructions modal
            const modal = new bootstrap.Modal(document.getElementById('pwaInstructionsModal'));
            modal.show();
        }
    
        showManualInstructions() {
            // Show modal with manual instructions
            const modal = new bootstrap.Modal(document.getElementById('pwaInstructionsModal'));
            modal.show();
        }
    
        getInstallationStatus() {
            return this.isPWAInstalled;
        }
    }
    
    // Initialize PWA Installer when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Register service worker
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('../service-worker.js')
                .then(registration => {
                    console.log('✅ Service Worker registered with scope:', registration.scope);
                })
                .catch(error => {
                    console.error('❌ Service Worker registration failed:', error);
                });
        }
    
        // Initialize PWA installer
        window.pwaInstaller = new PWAInstaller();
    
        // Add event listener for install button
        const installButton = document.getElementById('installPWA');
        if (installButton) {
            installButton.addEventListener('click', () => {
                window.pwaInstaller.installApp();
            });
        }
    
        // Add meta tag for PWA if not exists
        if (!document.querySelector('meta[name="apple-mobile-web-app-capable"]')) {
            const meta1 = document.createElement('meta');
            meta1.name = 'apple-mobile-web-app-capable';
            meta1.content = 'yes';
            document.head.appendChild(meta1);
    
            const meta2 = document.createElement('meta');
            meta2.name = 'apple-mobile-web-app-status-bar-style';
            meta2.content = 'black-translucent';
            document.head.appendChild(meta2);
    
            const meta3 = document.createElement('meta');
            meta3.name = 'mobile-web-app-capable';
            meta3.content = 'yes';
            document.head.appendChild(meta3);
        }
    });
    
    // Fungsi untuk menampilkan modal instruksi
    function showPWAInstructions() {
        const modal = new bootstrap.Modal(document.getElementById('pwaInstructionsModal'));
        modal.show();
    }
    
    // Fungsi untuk mengecek kesiapan PWA
    function checkPWAInstallation() {
        const installer = window.pwaInstaller;
        
        // Check browser support
        const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
        const isStandalone = window.matchMedia('(display-mode: standalone)').matches;
        const isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
        
        let message = '';
        
        if (isStandalone) {
            message = '✅ Aplikasi sudah terpasang di perangkat Anda!';
        } else if (isIOS && isSafari) {
            message = '📱 Untuk iPhone/iPad: Gunakan Safari, tekan tombol Bagikan (📤) lalu pilih "Tambahkan ke Layar Utama"';
        } else if ('BeforeInstallPromptEvent' in window) {
            message = '✅ Browser Anda mendukung PWA. Klik "Pasang Aplikasi" untuk menambahkan.';
        } else {
            message = 'ℹ️ Browser Anda mungkin tidak mendukung PWA penuh. Gunakan Chrome, Edge, atau Safari versi terbaru.';
        }
        
        alert(message);
    }
    
    // Fungsi untuk dismiss promotion
    function dismissPWAPromotion() {
        fetch('?dismiss_pwa_promo=1')
            .then(response => {
                const promotion = document.getElementById('pwa-promotion');
                if (promotion) {
                    promotion.style.display = 'none';
                }
            })
            .catch(error => console.error('Error dismissing PWA promotion:', error));
    }
    
    // Panggil fungsi inisialisasi setelah DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Animasi untuk pesan dashboard
        const dashboardSpans = document.querySelectorAll('.welcome-dashboard-message span');
        dashboardSpans.forEach(span => {
            const randomDelay = Math.random() * 2;
            span.style.animationDelay = `${randomDelay}s`;
        });
        
        // Fungsi untuk membuat pie chart dengan label terurut A-Z
        const createPieChart = (elementId, data, colors, labels) => {
            const ctx = document.getElementById(elementId).getContext('2d');
            return new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        };
    
        // Urutkan label A-Z untuk chart
        const labels = ['Alpa', 'Hadir', 'Izin', 'Sakit'].sort();
        
        // Data untuk chart dengan urutan yang sesuai
        const dataMadin = [
            <?= $stats_madin['stats']['Alpa'] ?>, 
            <?= $stats_madin['stats']['Hadir'] ?>, 
            <?= $stats_madin['stats']['Izin'] ?>, 
            <?= $stats_madin['stats']['Sakit'] ?>
        ];
        
        const dataQuran = [
            <?= $stats_quran_data['stats']['Alpa'] ?>, 
            <?= $stats_quran_data['stats']['Hadir'] ?>, 
            <?= $stats_quran_data['stats']['Izin'] ?>, 
            <?= $stats_quran_data['stats']['Sakit'] ?>
        ];
        
        const dataKegiatan = [
            <?= $stats_kegiatan_data['stats']['Alpa'] ?>, 
            <?= $stats_kegiatan_data['stats']['Hadir'] ?>, 
            <?= $stats_kegiatan_data['stats']['Izin'] ?>, 
            <?= $stats_kegiatan_data['stats']['Sakit'] ?>
        ];
        
        // Warna sesuai urutan label
        const colors = ['#f44336', '#4caf50', '#2196f3', '#45faae'];
    
        // Statistik absensi utama
        createPieChart('attendanceChart', dataMadin, colors, labels);
    
        // Statistik absensi Quran
        createPieChart('attendanceChartQuran', dataQuran, colors, labels);
    
        // Statistik absensi Kegiatan
        createPieChart('attendanceChartKegiatan', dataKegiatan, colors, labels);
    
        // Inisialisasi sistem notifikasi
        initializeNotificationSystem();
    
        // Auto-refresh notifikasi berdasarkan pengaturan
        const refreshInterval = <?= $refresh_otomatis_menit ?> * 60 * 1000;
        
        if (refreshInterval > 0 && <?= count($notifikasi_jadwal_belum_isi) > 0 ? 'true' : 'false' ?>) {
            setInterval(function() {
                fetch('?check_notifications=1')
                    .then(response => response.json())
                    .then(data => {
                        if (data.has_unfilled) {
                            console.log('🔄 Auto-refreshing notifications...');
                            location.reload();
                        }
                    })
                    .catch(error => console.error('Error checking notifications:', error));
            }, refreshInterval);
        }
    });
    
    // Fungsi untuk menyesuaikan layout Arab saat zoom
    function adjustArabicLayout() {
        const arabicContainer = document.querySelector('.arabic-container');
        const arabicText = document.querySelector('.arabic-text h3 span[dir="rtl"]');
        
        if (arabicContainer && arabicText) {
            const containerWidth = arabicContainer.offsetWidth;
            const textWidth = arabicText.scrollWidth;
            
            // Jika teks lebih lebar dari container, sesuaikan font size
            if (textWidth > containerWidth * 0.9) {
                const scaleFactor = (containerWidth * 0.9) / textWidth;
                const currentFontSize = parseFloat(getComputedStyle(arabicText).fontSize);
                arabicText.style.fontSize = (currentFontSize * scaleFactor) + 'px';
            } else {
                arabicText.style.fontSize = ''; // Reset ke default
            }
        }
    }
    
    // Debug comprehensive untuk notifikasi
    function debugNotificationSystem() {
        const elements = {
            bell: document.getElementById('notificationBell'),
            panel: document.getElementById('notificationPanel'),
            minimizeBtn: document.getElementById('minimizeNotification'),
            minimized: document.getElementById('notificationMinimized')
        };
        
        console.group('🔔 Debug Notification System');
        console.log('Elements status:', elements);
        
        if (elements.panel) {
            console.log('Panel classes:', elements.panel.classList.toString());
            console.log('Panel display style:', window.getComputedStyle(elements.panel).display);
            console.log('Panel opacity:', window.getComputedStyle(elements.panel).opacity);
            console.log('Panel visibility:', window.getComputedStyle(elements.panel).visibility);
        }
        
        console.groupEnd();
    }
    
    // Panggil debug setelah DOM loaded
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(debugNotificationSystem, 1000);
    });
    
    // Panggil saat load dan resize
    document.addEventListener('DOMContentLoaded', function() {
        adjustArabicLayout();
        
        // Debounce untuk resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(adjustArabicLayout, 250);
        });
        
        // Juga panggil setelah animasi selesai
        setTimeout(adjustArabicLayout, 1000);
    });
    
    // Deteksi perubahan zoom
    let currentZoomLevel = window.devicePixelRatio;
    window.addEventListener('resize', function() {
        const newZoom = window.devicePixelRatio;
        
        if (Math.abs(newZoom - currentZoomLevel) > 0.1) {
            console.log('🔍 Zoom level changed, adjusting Arabic layout...');
            currentZoomLevel = newZoom;
            setTimeout(adjustArabicLayout, 100);
        }
    });
    
    // Deteksi perubahan ukuran viewport (termasuk zoom)
    window.addEventListener('resize', function() {
        const newZoom = window.devicePixelRatio;
        
        if (Math.abs(newZoom - currentZoomLevel) > 0.1) {
            console.log('🔍 Zoom level changed, adjusting layout...');
            currentZoomLevel = newZoom;
            
            // Force reflow untuk elemen tertentu
            const elements = document.querySelectorAll('.welcome-heading, .arabic-text, .hijri-date-container');
            elements.forEach(el => {
                el.style.display = 'none';
                void el.offsetHeight; // Trigger reflow
                el.style.display = '';
            });
        }
    });
    
    // Inisialisasi saat load
    document.addEventListener('DOMContentLoaded', function() {
        // Pastikan konten tetap dalam bounds
        const container = document.querySelector('.container');
        if (container) {
            container.style.minWidth = '320px';
            container.style.maxWidth = '100%';
        }
    });
    
    // Test function untuk notifikasi (opsional, hapus di production)
    function testNotification() {
        console.log('🧪 Testing notification system...');
        const bell = document.getElementById('notificationBell');
        if (bell) {
            bell.click();
            setTimeout(() => {
                const panel = document.getElementById('notificationPanel');
                console.log('Panel visible:', panel && !panel.classList.contains('d-none'));
            }, 500);
        }
    }
    
    // PERBAIKAN: Pastikan link notifikasi berfungsi dengan baik
    document.addEventListener('click', function(e) {
        // Jika yang diklik adalah link absen di notifikasi
        const notificationLink = e.target.closest('.notification-item a.btn-warning');
        if (notificationLink && !e.target.closest('.notification-bell')) {
            e.preventDefault();
            e.stopPropagation();
            
            const href = notificationLink.getAttribute('href');
            console.log('🔗 Navigasi ke:', href);
            
            // Tutup panel notifikasi
            const notificationPanel = document.getElementById('notificationPanel');
            if (notificationPanel) {
                notificationPanel.classList.add('d-none');
            }
            
            // Tampilkan loading
            showCustomLoading();
            
            // Tunggu sebentar untuk animasi, lalu navigasi
            setTimeout(() => {
                window.location.href = href;
            }, 500);
        }
    });
    
    // Fungsi loading custom
    function showCustomLoading() {
        // Hapus loading lama jika ada
        const oldLoading = document.getElementById('customLoading');
        if (oldLoading) oldLoading.remove();
        
        // Buat loading baru
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'customLoading';
        loadingDiv.className = 'loading-overlay';
        
        loadingDiv.innerHTML = `
            <div class="loading-content">
                <div class="logo-pulse">
                    <!-- Ganti dengan logo yayasan Anda -->
                    <i class="bi bi-house-heart-fill" style="font-size: 4rem; color: #4CAF50;"></i>
                </div>
                <div class="spinner-border loading-spinner text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 fs-5">Mengarahkan ke halaman absensi...</p>
                <small class="text-muted">Harap tunggu sebentar</small>
            </div>
        `;
        
        document.body.appendChild(loadingDiv);
    }
    
    // Fungsi untuk menghapus loading
    function hideCustomLoading() {
        const loading = document.getElementById('customLoading');
        if (loading) {
            loading.style.opacity = '0';
            setTimeout(() => {
                loading.remove();
            }, 300);
        }
    }
    
    // Deteksi jika halaman absensi selesai loading (untuk testing)
    window.addEventListener('load', function() {
        setTimeout(hideCustomLoading, 1000);
    });
    
    // PERBAIKAN: Endpoint untuk check notifikasi via AJAX
    <?php if (isset($_GET['check_notifications'])): ?>
    <?php
        header('Content-Type: application/json');
        echo json_encode([
            'has_unfilled' => count($notifikasi_jadwal_belum_isi) > 0,
            'notifikasi_aktif' => $notifikasi_aktif == '1',
            'count' => count($notifikasi_jadwal_belum_isi)
        ]);
        exit;
    ?>
    <?php endif; ?>
    
    // <!-- Tambahkan JavaScript untuk kontrol animasi -->
    document.addEventListener('DOMContentLoaded', function() {
        const pulsatingCard = document.querySelector('.pulsating-card');
        
        if (pulsatingCard) {
            // Mulai animasi dengan sedikit delay
            setTimeout(() => {
                pulsatingCard.style.animationPlayState = 'running';
            }, 500);
            
            // Hentikan animasi saat hover (opsional)
            pulsatingCard.addEventListener('mouseenter', function() {
                this.style.animationPlayState = 'paused';
            });
            
            pulsatingCard.addEventListener('mouseleave', function() {
                this.style.animationPlayState = 'running';
            });
            
            // Reset animasi setiap beberapa detik untuk menjaga konsistensi
            setInterval(() => {
                pulsatingCard.style.animation = 'none';
                void pulsatingCard.offsetWidth; // Trigger reflow
                pulsatingCard.style.animation = 'glowPulse 2s infinite ease-in-out';
            }, 10000);
        }
    });
    
    document.addEventListener('DOMContentLoaded', function() {
        const pulsatingCard = document.querySelector('.pulsating-card');
        
        if (pulsatingCard) {
            // Pilih secara acak salah satu animasi gradient
            const gradientOptions = [
                'wave-gradient',     // Opsi terbaik - efek gelombang
                'fire-gradient',     // Efek api/nyala
                'radar-gradient',   // Efek radar berputar
                'horizontal-moving-gradient', // Bergerak horizontal
                'random-gradient'   // Gerakan acak
            ];
            
            // Pilih secara acak (atau pilih salah satu yang Anda sukai)
            const selectedGradient = gradientOptions[Math.floor(Math.random() * gradientOptions.length)];
            
            // Tambahkan class gradient yang dipilih
            pulsatingCard.classList.add(selectedGradient);
            
            console.log(`🎨 Selected gradient animation: ${selectedGradient}`);
            
            // Mulai animasi dengan sedikit delay
            setTimeout(() => {
                pulsatingCard.style.animationPlayState = 'running';
            }, 500);
            
            // Hentikan animasi saat hover (opsional)
            pulsatingCard.addEventListener('mouseenter', function() {
                this.style.animationPlayState = 'paused';
            });
            
            pulsatingCard.addEventListener('mouseleave', function() {
                this.style.animationPlayState = 'running';
            });
            
            // Ganti animasi gradient secara berkala (setiap 30 detik)
            let currentGradientIndex = gradientOptions.indexOf(selectedGradient);
            
            setInterval(() => {
                // Hapus semua class gradient
                gradientOptions.forEach(gradient => {
                    pulsatingCard.classList.remove(gradient);
                });
                
                // Pilih gradient berikutnya
                currentGradientIndex = (currentGradientIndex + 1) % gradientOptions.length;
                const nextGradient = gradientOptions[currentGradientIndex];
                
                // Tambahkan class gradient baru
                pulsatingCard.classList.add(nextGradient);
                
                console.log(`🔄 Changed gradient to: ${nextGradient}`);
            }, 3000); // Ganti setiap 3 detik
        }
    });
    
    </script>
</body>
<?php
// Flush output buffer
ob_end_flush();
?>
</html>