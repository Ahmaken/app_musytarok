<?php
session_start();
require_once '../includes/init.php';

// Hanya admin dan staff yang bisa mengakses
if (!check_auth() || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: ../index.php");
    exit();
}

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$hari_indo = getHariIndonesia(date('l'));

// Query untuk monitoring absensi guru
$sql = "SELECT 
            ag.*,
            g.nama as nama_guru,
            g.no_hp,
            TIMEDIFF(ag.waktu_absensi, ag.deadline_absensi) as selisih_waktu,
            CASE 
                WHEN ag.status = 'Hadir' AND ag.waktu_absensi <= ag.deadline_absensi THEN 'Tepat Waktu'
                WHEN ag.status = 'Hadir' AND ag.waktu_absensi > ag.deadline_absensi THEN 'Terlambat'
                WHEN ag.status = 'Alpa' THEN 'Alpa'
                ELSE 'Belum Absen'
            END as status_kehadiran
        FROM absensi_guru ag
        JOIN guru g ON ag.guru_id = g.guru_id
        WHERE ag.tanggal = ?
        ORDER BY g.nama";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $tanggal);
$stmt->execute();
$absensi_guru = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

function getHariIndonesia($hariInggris) {
    $day_map = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 
        'Sunday' => 'Ahad'
    ];
    return $day_map[$hariInggris] ?? $hariInggris;
}

require_once '../includes/navigation.php';
?>

<!DOCTYPE html>
<html lang="id" data-bs-theme="<?= $dark_mode ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor Absensi Guru - Sistem Absensi Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2><i class="bi bi-clock-history me-1"></i> Monitor Absensi Guru</h2>
        </div>
        
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">Filter Tanggal</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-7 mb-2">
                        <input type="date" class="form-control" name="tanggal" value="<?= $tanggal ?>">
                    </div>
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-primary">Tampilkan</button>
                        <a href="monitor_absensi_guru.php" class="btn btn-secondary">Hari Ini</a>
                    </div>
                    <div class="col-md-3 mb-2">
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        
                            <a href="absensi_guru.php" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali ke Absensi Guru
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <?php if ($tanggal): ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    Status Absensi Guru - <?= $tanggal ?> (<?= $hari_indo ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama Guru</th>
                                <th>Status</th>
                                <th>Waktu Absensi</th>
                                <th>Deadline</th>
                                <th>Keterlambatan</th>
                                <th>Notifikasi</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($absensi_guru as $absensi): ?>
                            <tr>
                                <td><?= htmlspecialchars($absensi['nama_guru']) ?></td>
                                <td>
                                    <span class="badge bg-<?= 
                                        $absensi['status'] == 'Hadir' ? 'success' : 
                                        ($absensi['status'] == 'Alpa' ? 'danger' : 'warning')
                                    ?>">
                                        <?= $absensi['status'] ?>
                                    </span>
                                </td>
                                <td><?= $absensi['waktu_absensi'] ?: '-' ?></td>
                                <td><?= $absensi['deadline_absensi'] ?: '-' ?></td>
                                <td>
                                    <?php if ($absensi['selisih_waktu'] && $absensi['status_kehadiran'] == 'Terlambat'): ?>
                                        <span class="text-danger"><?= $absensi['selisih_waktu'] ?></span>
                                    <?php elseif ($absensi['status_kehadiran'] == 'Tepat Waktu'): ?>
                                        <span class="text-success">Tepat Waktu</span>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($absensi['notifikasi_terkirim']): ?>
                                        <span class="badge bg-success">Terkirim</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Belum</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($absensi['keterangan']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if (empty($absensi_guru)): ?>
                <div class="alert alert-info">Tidak ada data absensi guru untuk tanggal ini.</div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>