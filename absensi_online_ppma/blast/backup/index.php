<?php
// index.php di folder blast
session_start();

// Cek apakah ada session dark mode, default light
if (!isset($_SESSION['dark_mode'])) {
    $_SESSION['dark_mode'] = false;
}

$file_settings = 'blast_settings.json';
$file_config = 'blast_config.json';

// Load settings
if (!file_exists($file_settings)) {
    // Create default settings
    $default_settings = [
        'status' => 'nonaktif',
        'template' => "« السلام عليكم ورحمة الله »\n\nYang terhormat Bapak/Ibu/Saudara/i {{nama}} 🌹,\n\nJadwal {{kelas}} akan dimulai pada pukul {{jam_mulai}}.\n\nMohon kesediaannya untuk melakukan absensi pada tautan berikut:\n{{link}}\n\nusername :\nguru1\n\npassword :\nguru123\n\nJazakumullah khairan katsiran atas waktu dan kedisiplinan yang baik.\n\n« والسلام عليكم ورحمة الله »"
    ];
    file_put_contents($file_settings, json_encode($default_settings, JSON_PRETTY_PRINT));
}

$settings = json_decode(file_get_contents($file_settings), true);

// Load config
if (!file_exists($file_config)) {
    $default_config = [
        'waktu_notifikasi' => 30,
        'jam_awal' => '06:00',
        'jam_akhir' => '22:00',
        'multiple_notifikasi' => [
            ['menit_sebelum' => 60, 'label' => '1 jam sebelum'],
            ['menit_sebelum' => 30, 'label' => '30 menit sebelum']
        ]
    ];
    file_put_contents($file_config, json_encode($default_config, JSON_PRETTY_PRINT));
}

$config = json_decode(file_get_contents($file_config), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings['status'] = $_POST['status'];
    $settings['template'] = $_POST['template'];
    file_put_contents($file_settings, json_encode($settings, JSON_PRETTY_PRINT));
    
    // Update config
    $config['waktu_notifikasi'] = intval($_POST['waktu_notifikasi']);
    $config['jam_awal'] = $_POST['jam_awal'];
    $config['jam_akhir'] = $_POST['jam_akhir'];
    
    // Handle multiple notifications
    $multiple_notifikasi = [];
    if (isset($_POST['multiple_menit']) && is_array($_POST['multiple_menit'])) {
        foreach ($_POST['multiple_menit'] as $index => $menit) {
            if (!empty($menit) && !empty($_POST['multiple_label'][$index])) {
                $multiple_notifikasi[] = [
                    'menit_sebelum' => intval($menit),
                    'label' => $_POST['multiple_label'][$index]
                ];
            }
        }
    }
    
    // Jika tidak ada multiple, gunakan default single
    if (empty($multiple_notifikasi)) {
        $multiple_notifikasi[] = [
            'menit_sebelum' => intval($_POST['waktu_notifikasi']),
            'label' => $_POST['waktu_notifikasi'] . ' menit sebelum'
        ];
    }
    
    $config['multiple_notifikasi'] = $multiple_notifikasi;
    file_put_contents($file_config, json_encode($config, JSON_PRETTY_PRINT));
    
    $success = "Pengaturan berhasil disimpan!";
}

// Dark mode toggle
if (isset($_GET['dark_mode'])) {
    $_SESSION['dark_mode'] = $_GET['dark_mode'] == '1';
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id" data-bs-theme="<?= $_SESSION['dark_mode'] ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan WhatsApp Otomatis - Sistem Absensi Online</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .btn-success {
            background-color: #198754;
            border-color: #198754;
        }
        
        .btn-success:hover {
            background-color: #0d5037;
            border-color: #0d5037;
        }
        
        [data-bs-theme="dark"] .badge.bg-success {
            background-color: #20c997 !important;
            color: #000;
        }
        
        .alert-info {
            background-color: rgba(25, 135, 84, 0.1);
            border-color: rgba(25, 135, 84, 0.3);
            color: #0a3622;
        }
        
        [data-bs-theme="dark"] .alert-info {
            background-color: rgba(32, 201, 151, 0.15);
            border-color: rgba(32, 201, 151, 0.3);
            color: #a3e9d9;
        }
        
        .notification-item {
            border-left: 4px solid #198754;
            padding-left: 12px;
            margin-bottom: 10px;
        }
        
        [data-bs-theme="dark"] .notification-item {
            border-left-color: #20c997;
        }
    </style>
</head>
<body>
    <div class="container mt-4">

        <div class="text-end d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-whatsapp me-2"></i> Pengaturan Blast WhatsApp</h2>
            
        </div>
        
        <div class="text-end mb-4">
            <a href="../pages/pengaturan_notifikasi.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Pengaturan Notif
            </a>
            
            <button class="btn btn-sm btn-outline-secondary" id="darkModeToggle">
                <i class="bi bi-moon" id="darkModeIcon"></i>
            </button>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Pengaturan Blast WhatsApp -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="bi bi-sliders me-1"></i> Konfigurasi Blast Wa</h5>
                <span class="badge bg-light text-dark">
                    <?= $settings['status'] == 'aktif' ? 'Status: Aktif' : 'Status: Libur' ?>
                </span>
            </div>
            <div class="card-body">
                <form method="POST" id="blastForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Status Blast</label>
                            <select name="status" class="form-select">
                                <option value="aktif" <?= $settings['status'] == 'aktif' ? 'selected' : '' ?>>Aktif (Kirim Pesan)</option>
                                <option value="nonaktif" <?= $settings['status'] == 'nonaktif' ? 'selected' : '' ?>>Libur (Matikan Blast)</option>
                            </select>
                            <small class="form-text text-muted">Aktifkan atau nonaktifkan pengiriman pesan WhatsApp otomatis</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jam Aktif Blast</label>
                            <div class="row">
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-sun"></i></span>
                                        <input type="time" name="jam_awal" class="form-control" value="<?= $config['jam_awal'] ?>">
                                    </div>
                                    <small class="form-text text-muted">Mulai</small>
                                </div>
                                <div class="col-6">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-moon"></i></span>
                                        <input type="time" name="jam_akhir" class="form-control" value="<?= $config['jam_akhir'] ?>">
                                    </div>
                                    <small class="form-text text-muted">Selesai</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Multiple Notifications -->
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Waktu Notifikasi</label>
                            <div id="multipleNotifications">
                                <?php foreach ($config['multiple_notifikasi'] as $index => $notif): ?>
                                <div class="row mb-2 notification-item">
                                    <div class="col-5">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                            <input type="number" name="multiple_menit[]" class="form-control" 
                                                   placeholder="Menit sebelum" min="1" max="240" 
                                                   value="<?= $notif['menit_sebelum'] ?>">
                                            <span class="input-group-text">menit</span>
                                        </div>
                                    </div>
                                    <div class="col-5">
                                        <input type="text" name="multiple_label[]" class="form-control" 
                                               placeholder="Label (contoh: 1 jam sebelum)" 
                                               value="<?= htmlspecialchars($notif['label']) ?>">
                                    </div>
                                    <div class="col-2">
                                        <?php if($index > 0): ?>
                                        <button type="button" class="btn btn-danger btn-sm remove-notification">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" id="addNotification">
                                <i class="bi bi-plus-circle"></i> Tambah Waktu Notifikasi
                            </button>
                            <small class="form-text text-muted d-block">Contoh: Notifikasi 60 menit dan 30 menit sebelum jadwal</small>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Template Pesan</label>
                            <textarea name="template" class="form-control" rows="8" placeholder="Masukkan template pesan di sini..."><?= htmlspecialchars($settings['template']) ?></textarea>
                            <div class="mt-2">
                                <small class="text-muted">Gunakan placeholder: 
                                    <span class="badge bg-secondary">{{nama}}</span>
                                    <span class="badge bg-secondary">{{kelas}}</span>
                                    <span class="badge bg-secondary">{{jam_mulai}}</span>
                                    <span class="badge bg-secondary">{{waktu_notifikasi}}</span>
                                    <span class="badge bg-secondary">{{link}}</span>
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Contoh Template:</strong><br>
                        "Assalamualaikum {{nama}} 🌹<br>
                        Pengingat {{waktu_notifikasi}} untuk jadwal {{kelas}} yang dimulai pukul {{jam_mulai}}.<br>
                        Silakan absen di: {{link}}<br>
                        Terima kasih."
                    </div>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" class="btn btn-outline-success">
                            <i class="bi bi-save me-1"></i> Simpan Pengaturan
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                        <a href="blast_realtime.php?manual=1" class="btn btn-outline-primary" target="_blank">
                            <i class="bi bi-send me-1"></i> Test Kirim realtime
                        </a>
                        <a href="blast.php?manual=1" class="btn btn-outline-warning" target="_blank">
                            <i class="bi bi-send-check me-1"></i> Test Blast Sekarang
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Informasi Sistem -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="card-title mb-0"><i class="bi bi-info-circle me-1"></i> Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6><i class="bi bi-check-circle text-success me-2"></i>Fitur Utama</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-dot me-1"></i>Pengiriman pesan otomatis sesuai jadwal</li>
                            <li><i class="bi bi-dot me-1"></i>Multiple notifikasi (contoh: 1 jam & 30 menit sebelum)</li>
                            <li><i class="bi bi-dot me-1"></i>Template pesan yang dapat disesuaikan</li>
                            <li><i class="bi bi-dot me-1"></i>Support placeholders dinamis</li>
                            <li><i class="bi bi-dot me-1"></i>Mode aktif/libur</li>
                            <li><i class="bi bi-dot me-1"></i>Atur jam aktif pengiriman</li>
                        </ul>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6><i class="bi bi-exclamation-triangle text-warning me-2"></i>Catatan Penting</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-dot me-1"></i>Pastikan cron job diatur untuk menjalankan blast_realtime.php setiap menit</li>
                            <li><i class="bi bi-dot me-1"></i>Pastikan koneksi WhatsApp Business aktif</li>
                            <li><i class="bi bi-dot me-1"></i>Periksa template sebelum menyimpan</li>
                            <li><i class="bi bi-dot me-1"></i>Simpan perubahan sebelum meninggalkan halaman</li>
                        </ul>
                    </div>
                </div>
                <div class="alert alert-warning">
                    <h6><i class="bi bi-terminal me-2"></i>Instruksi Cron Job</h6>
                    <p>Tambahkan baris berikut di cron job Anda (cPanel):</p>
                    <code>* * * * * php /home/quic1934/public_html/absen.quizb.my.id/blast/blast_realtime.php >> /home/quic1934/public_html/absen.quizb.my.id/blast/cron_log.txt 2>&1</code>
                    <p class="mb-0 mt-2">Atau setiap 5 menit:</p>
                    <code>*/5 * * * * php /home/quic1934/public_html/absen.quizb.my.id/blast/blast_realtime.php >> /home/quic1934/public_html/absen.quizb.my.id/blast/cron_log.txt 2>&1</code>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <a href="../pages/dashboard.php" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dark Mode Toggle
        document.getElementById('darkModeToggle').addEventListener('click', function() {
            const html = document.documentElement;
            const icon = document.getElementById('darkModeIcon');
            
            if (html.getAttribute('data-bs-theme') === 'dark') {
                html.setAttribute('data-bs-theme', 'light');
                icon.className = 'bi bi-moon';
                fetch('?dark_mode=0');
            } else {
                html.setAttribute('data-bs-theme', 'dark');
                icon.className = 'bi bi-sun';
                fetch('?dark_mode=1');
            }
        });

        // Fungsi reset form
        function resetForm() {
            if (confirm('Apakah Anda yakin ingin mengembalikan pengaturan ke default?')) {
                document.querySelector('select[name="status"]').value = 'aktif';
                document.querySelector('input[name="jam_awal"]').value = '06:00';
                document.querySelector('input[name="jam_akhir"]').value = '22:00';
                
                // Reset multiple notifications
                const container = document.getElementById('multipleNotifications');
                container.innerHTML = `
                    <div class="row mb-2 notification-item">
                        <div class="col-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="number" name="multiple_menit[]" class="form-control" placeholder="Menit sebelum" min="1" max="240" value="60">
                                <span class="input-group-text">menit</span>
                            </div>
                        </div>
                        <div class="col-5">
                            <input type="text" name="multiple_label[]" class="form-control" placeholder="Label (contoh: 1 jam sebelum)" value="1 jam sebelum">
                        </div>
                        <div class="col-2"></div>
                    </div>
                    <div class="row mb-2 notification-item">
                        <div class="col-5">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                <input type="number" name="multiple_menit[]" class="form-control" placeholder="Menit sebelum" min="1" max="240" value="30">
                                <span class="input-group-text">menit</span>
                            </div>
                        </div>
                        <div class="col-5">
                            <input type="text" name="multiple_label[]" class="form-control" placeholder="Label (contoh: 30 menit sebelum)" value="30 menit sebelum">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger btn-sm remove-notification">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                
                // Reattach remove event listeners
                attachRemoveListeners();
                
                document.querySelector('textarea[name="template"]').value = '« السلام عليكم ورحمة الله »\n\nYang terhormat Bapak/Ibu/Saudara/i {{nama}} 🌹,\n\nJadwal {{kelas}} akan dimulai pada pukul {{jam_mulai}}.\n\nMohon kesediaannya untuk melakukan absensi pada tautan berikut:\n{{link}}\n\nusername :\nguru1\n\npassword :\nguru123\n\nJazakumullah khairan katsiran atas waktu dan kedisiplinan yang baik.\n\n« والسلام عليكم ورحمة الله »';
            }
        }

        // Update badge status saat form berubah
        document.querySelector('select[name="status"]').addEventListener('change', function() {
            const badge = document.querySelector('.card-header .badge');
            badge.textContent = this.value === 'aktif' ? 'Status: Aktif' : 'Status: Libur';
            badge.className = this.value === 'aktif' ? 'badge bg-light text-dark' : 'badge bg-warning text-dark';
        });

        // Multiple notifications functionality
        document.getElementById('addNotification').addEventListener('click', function() {
            const container = document.getElementById('multipleNotifications');
            const newItem = document.createElement('div');
            newItem.className = 'row mb-2 notification-item';
            newItem.innerHTML = `
                <div class="col-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-clock"></i></span>
                        <input type="number" name="multiple_menit[]" class="form-control" placeholder="Menit sebelum" min="1" max="240">
                        <span class="input-group-text">menit</span>
                    </div>
                </div>
                <div class="col-5">
                    <input type="text" name="multiple_label[]" class="form-control" placeholder="Label (contoh: 15 menit sebelum)">
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger btn-sm remove-notification">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(newItem);
            attachRemoveListeners();
        });

        function attachRemoveListeners() {
            document.querySelectorAll('.remove-notification').forEach(button => {
                button.addEventListener('click', function() {
                    this.closest('.notification-item').remove();
                });
            });
        }

        // Initialize remove listeners
        attachRemoveListeners();
        
        // Validasi form sebelum submit
        document.getElementById('blastForm').addEventListener('submit', function(e) {
            const menitInputs = document.querySelectorAll('input[name="multiple_menit[]"]');
            let isValid = true;
            
            menitInputs.forEach((input, index) => {
                const value = parseInt(input.value);
                if (isNaN(value) || value < 1 || value > 240) {
                    alert('Waktu notifikasi harus antara 1-240 menit');
                    isValid = false;
                    e.preventDefault();
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>