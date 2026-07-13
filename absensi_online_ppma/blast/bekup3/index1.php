<?php
require_once 'config.php';
require_once 'wa.php';

// --- FUNGSI INTI ---

// Fungsi Spinning Text untuk variasi pesan (Anti-Spam)
function spinText($text) {
    return preg_replace_callback('/\{([^{}]*)\}/', function($matches) {
        $choices = explode('|', $matches[1]);
        return $choices[array_rand($choices)];
    }, $text);
}

// Handler AJAX Request
if (isset($_GET['action']) && $_GET['action'] == 'send_ajax') {
    $no_hp = $_POST['no_hp'];
    $pesan = $_POST['pesan'];
    
    // Delay acak 5-12 detik untuk meniru perilaku manusia
    sleep(rand(5, 12)); 
    
    $res = kirimWA($no_hp, $pesan);
    
    echo json_encode(['status' => 'success', 'message' => "Respons: $res"]);
    exit;
}

// Template Default Hakimz Project
$default_msg = "{Assalamu'alaikum|Halo|Selamat pagi} Ust/Ustd. {nama},\n\n{Berikut kami sampaikan|Ini adalah|Info} rincian akun Anda untuk sistem Absensi Online:\n\n👤 Username: {username}\n🔑 Password: {password}\n\n🌐 Login: https://absen.quizb.my.id\n\n{Terima kasih|Syukron|Salam}, \nAdmin IT Mawar";

// Ambil data Guru sesuai kriteria (ID > 63)
$query_guru = "SELECT g.guru_id, g.nama, g.no_hp, u.username, u.password 
               FROM guru g 
               LEFT JOIN users u ON g.guru_id = u.id 
               WHERE (u.role = 'guru' OR g.guru_id IS NOT NULL) AND g.guru_id > 78";
$data_guru = $conn->query($query_guru);
$gurus = [];
$total_data = 0;
while($row = $data_guru->fetch_assoc()) { 
    $gurus[] = $row; 
    $total_data++;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blast Manager | IT Mawar</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
            --success-color: #27ae60;
            --bg-color: #f8f9fa;
            --card-bg: #ffffff;
        }

        body { 
            font-family: 'Inter', -apple-system, sans-serif; 
            background: var(--bg-color); 
            margin: 0; 
            padding: 20px; 
            color: var(--primary-color);
        }

        .container { 
            max-width: 1000px; 
            margin: auto; 
        }

        /* Header Style */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
        }

        .header h2 { margin: 0; font-weight: 800; letter-spacing: -1px; }
        .header span { color: var(--accent-color); }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            text-align: center;
        }

        .stat-card h3 { margin: 0; font-size: 24px; color: var(--accent-color); }
        .stat-card p { margin: 5px 0 0; color: #7f8c8d; font-size: 14px; }

        /* Main Form & Content */
        .main-card {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        label { font-weight: 600; display: block; margin-bottom: 10px; }

        textarea { 
            width: 100%; 
            padding: 15px; 
            border: 1px solid #ddd; 
            border-radius: 10px; 
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            transition: border 0.3s;
        }

        textarea:focus { border-color: var(--accent-color); outline: none; }

        .btn-blast { 
            background: var(--success-color); 
            color: white; 
            border: none; 
            padding: 15px 30px; 
            border-radius: 10px; 
            font-weight: 700; 
            cursor: pointer; 
            width: 100%; 
            font-size: 16px;
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-blast:disabled { background: #bdc3c7; cursor: not-allowed; }

        /* Progress Bar */
        #progress-wrapper {
            margin-top: 25px;
            display: none;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .progress-bg { background: #eee; height: 12px; border-radius: 10px; overflow: hidden; }
        #progress-fill { 
            width: 0%; 
            background: linear-gradient(90deg, #2ecc71, #27ae60); 
            height: 100%; 
            transition: width 0.5s ease;
        }

        /* Table Style */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { text-align: left; padding: 15px; color: #7f8c8d; font-size: 12px; text-transform: uppercase; }
        td { padding: 15px; border-top: 1px solid #f1f1f1; font-size: 14px; }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .bg-pending { background: #f3f4f6; color: #6b7280; }
        .bg-sending { background: #e0f2fe; color: #0369a1; }
        .bg-done { background: #dcfce7; color: #166534; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Hakimz <span>Project</span></h2>
        <div style="font-size: 13px; background: #fff; padding: 5px 15px; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
            <i class="fa-solid fa-circle-check" style="color: #27ae60;"></i> WhatsApp Anti-Banned Active
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3 id="stat-total"><?php echo $total_data; ?></h3>
            <p>Total Antrean</p>
        </div>
        <div class="stat-card">
            <h3 id="stat-sent">0</h3>
            <p>Berhasil Terkirim</p>
        </div>
        <div class="stat-card">
            <h3 id="stat-wait"><?php echo $total_data; ?></h3>
            <p>Sisa Antrean</p>
        </div>
    </div>

    <div class="main-card">
        <label><i class="fa-solid fa-pen-to-square"></i> Template Pesan Global</label>
        <textarea id="template_global" rows="6"><?php echo $default_msg; ?></textarea>
        
        <div id="progress-wrapper">
            <div class="progress-info">
                <span id="progress-text">Memproses pengiriman...</span>
                <span id="progress-percent">0%</span>
            </div>
            <div class="progress-bg">
                <div id="progress-fill"></div>
            </div>
        </div>

        <button type="button" id="start-btn" class="btn-blast">
            <i class="fa-solid fa-paper-plane"></i> Jalankan Blast Massal
        </button>
    </div>

    <div class="main-card">
        <label><i class="fa-solid fa-list-check"></i> Monitoring Pengiriman</label>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Penerima</th>
                        <th>No. WhatsApp</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gurus as $row) : ?>
                    <tr class="guru-item" 
                        data-id="<?php echo $row['guru_id']; ?>" 
                        data-nama="<?php echo htmlspecialchars($row['nama']); ?>" 
                        data-no="<?php echo $row['no_hp']; ?>"
                        data-user="<?php echo $row['username']; ?>"
                        data-pass="<?php echo $row['password']; ?>">
                        <td>#<?php echo $row['guru_id']; ?></td>
                        <td><strong><?php echo $row['nama']; ?></strong></td>
                        <td><?php echo $row['no_hp']; ?></td>
                        <td><span class="badge bg-pending status-label">Menunggu</span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Fungsi Spinning Text JS
    function applySpin(text) {
        return text.replace(/\{([^{}]+)\}/g, function(match, choices) {
            var words = choices.split('|');
            return words[Math.floor(Math.random() * words.length)];
        });
    }

    $('#start-btn').click(function() {
        if (!confirm('Konfirmasi: Mulai kirim akun ke semua guru?')) return;

        $(this).prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Sedang Mengirim...');
        $('#progress-wrapper').fadeIn();
        
        let items = $('.guru-item');
        let total = items.length;
        let successCount = 0;
        let index = 0;

        function processQueue() {
            if (index >= total) {
                $('#start-btn').html('<i class="fa-solid fa-check-double"></i> Selesai Terkirim');
                $('#progress-text').text('Proses Selesai!');
                return;
            }

            let current = $(items[index]);
            let data = current.data();
            let rawTemplate = $('#template_global').val();

            // Replace data & apply spinning
            let msg = rawTemplate.replace('{nama}', data.nama)
                                 .replace('{username}', data.user)
                                 .replace('{password}', data.pass);
            let finalMsg = applySpin(msg);

            // UI Update
            current.find('.status-label').removeClass('bg-pending').addClass('bg-sending').text('Mengirim...');
            
            $.ajax({
                url: 'index.php?action=send_ajax',
                method: 'POST',
                data: { no_hp: data.no, pesan: finalMsg },
                success: function() {
                    current.find('.status-label').removeClass('bg-sending').addClass('bg-done').text('Terkirim');
                    successCount++;
                    index++;

                    // Update Statistik & Progress
                    let percent = Math.round((index / total) * 100);
                    $('#stat-sent').text(successCount);
                    $('#stat-wait').text(total - index);
                    $('#progress-fill').css('width', percent + '%');
                    $('#progress-percent').text(percent + '%');
                    $('#progress-text').text('Mengirim ke: ' + data.nama);

                    processQueue();
                },
                error: function() {
                    current.find('.status-label').text('Gagal');
                    index++;
                    processQueue();
                }
            });
        }

        processQueue();
    });
});
</script>

</body>
</html>