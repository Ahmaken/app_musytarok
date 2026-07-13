<?php
require_once 'config.php';
require_once 'wa.php';

// --- FUNGSI UTAMA ---

// Fungsi untuk mengacak teks (Spinning Text) agar pesan tidak seragam
function spinText($text) {
    return preg_replace_callback('/\{([^{}]*)\}/', function($matches) {
        $choices = explode('|', $matches[1]);
        return $choices[array_rand($choices)];
    }, $text);
}

// Handler untuk AJAX Request (dipanggil oleh JavaScript)
if (isset($_GET['action']) && $_GET['action'] == 'send_ajax') {
    $no_hp = $_POST['no_hp'];
    $pesan = $_POST['pesan'];
    
    // Tambahkan delay acak antara 5 - 12 detik sebelum kirim
    sleep(rand(5, 12)); 
    
    $res = kirimWA($no_hp, $pesan);
    
    echo json_encode(['status' => 'success', 'message' => "Terkirim ke $no_hp: $res"]);
    exit;
}

// Default Template dengan format Spinning Text
$default_msg = "{Assalamu'alaikum|Sugeng injing|Selamat pagi|Mugi sami wilujeng|السلام عليكم} Ust/Ustd. {nama},\{Berikut ini adalah|Kami lampirkan|Ini adalah|Kami haturkan|Niki} akun login Anda untuk sistem Absensi Online:\nUsername: {username}\nPassword: {password}\{Silakan|Mohon|Monggo|Anda dapat} login di: https://absen.quizb.my.id\n{Terima kasih|Syukron}.";

// Ambil data Guru
$query_guru = "SELECT g.guru_id, g.nama, g.no_hp, u.username, u.password 
               FROM guru g 
               LEFT JOIN users u ON g.guru_id = u.id 
               WHERE (u.role = 'guru' OR g.guru_id IS NOT NULL) AND g.guru_id > 114";
$data_guru = $conn->query($query_guru);
$gurus = [];
while($row = $data_guru->fetch_assoc()) { $gurus[] = $row; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Blast Aman - Zainul Hakim</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f4f7f6; padding: 20px; color: #333; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 6px; display: none; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; display: block; }
        textarea { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #ddd; font-size: 14px; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #eee; padding: 12px; text-align: left; }
        th { background: #f9f9f9; font-weight: 600; }
        .btn { padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-success { background: #28a745; color: white; width: 100%; font-size: 16px; }
        .btn-success:hover { background: #218838; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }
        .pending { background: #ffeeba; color: #856404; }
        .sending { background: #b8daff; color: #004085; }
        .done { background: #c3e6cb; color: #155724; }
        #progress-container { margin-top: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; display: none; }
    </style>
</head>
<body>

<div class="container">
    <h2>🚀 Smart Blast Akun Guru</h2>
    <p style="font-size: 14px; color: #666;">Fitur: Anti-Banned (Random Delay & Spinning Text) via AJAX.</p>

    <div class="info alert">
        <strong>Format Spinning:</strong> Gunakan <code>{Assalamu'alaikum|Sugeng injing|Selamat pagi|Mugi sami wilujeng|السلام عليكم}</code> untuk merandom kata.
    </div>

    <form id="form-blast">
        <label><strong>Template Pesan Global:</strong></label>
        <textarea id="template_global" name="template_global" rows="5"><?php echo $default_msg; ?></textarea>
        <br><br>
        <button type="button" id="start-blast" class="btn btn-success">⚡ Mulai Blast Semua Guru</button>
    </form>

    <div id="progress-container">
        <strong>Status Pengiriman:</strong> <span id="current-status">Menunggu...</span>
        <div style="width: 100%; background: #eee; height: 10px; border-radius: 5px; margin-top: 10px;">
            <div id="progress-bar" style="width: 0%; background: #28a745; height: 10px; border-radius: 5px; transition: 0.5s;"></div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Guru</th>
                <th>WhatsApp</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody id="guru-list">
            <?php foreach ($gurus as $row) : ?>
            <tr id="row-<?php echo $row['guru_id']; ?>" class="guru-row" 
                data-id="<?php echo $row['guru_id']; ?>" 
                data-nama="<?php echo htmlspecialchars($row['nama']); ?>" 
                data-no="<?php echo $row['no_hp']; ?>"
                data-user="<?php echo $row['username']; ?>"
                data-pass="<?php echo $row['password']; ?>">
                <td><?php echo $row['guru_id']; ?></td>
                <td><strong><?php echo $row['nama']; ?></strong></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td><span class="status-badge pending">Menunggu</span></td>
                <td><small>Siap kirim</small></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Fungsi Spinning Text di sisi Client (JavaScript)
    function spinTextJS(text) {
        return text.replace(/\{([^{}]+)\}/g, function(match, choices) {
            var words = choices.split('|');
            return words[Math.floor(Math.random() * words.length)];
        });
    }

    $('#start-blast').click(function() {
        if (!confirm('Mulai kirim pesan massal? Proses ini akan memakan waktu karena menggunakan delay aman.')) return;

        $(this).prop('disabled', true).text('Sedang Memproses...');
        $('#progress-container').show();
        
        let rows = $('.guru-row');
        let total = rows.length;
        let index = 0;

        function sendNext() {
            if (index >= total) {
                $('#current-status').text('Selesai! Semua pesan terkirim.');
                $('#start-blast').text('Selesai').addClass('done');
                return;
            }

            let currentRow = $(rows[index]);
            let id = currentRow.data('id');
            let nama = currentRow.data('nama');
            let no_hp = currentRow.data('no');
            let username = currentRow.data('user');
            let password = currentRow.data('pass');
            let template = $('#template_global').val();

            // Replace variabel
            let pesan = template.replace('{nama}', nama).replace('{username}', username).replace('{password}', password);
            // Apply Spinning
            pesan = spinTextJS(pesan);

            // Update UI status
            currentRow.find('.status-badge').removeClass('pending').addClass('sending').text('Mengirim...');
            $('#current-status').text('Mengirim ke ' + nama + ' (' + (index + 1) + '/' + total + ')...');

            $.ajax({
                url: 'index.php?action=send_ajax',
                method: 'POST',
                data: { no_hp: no_hp, pesan: pesan },
                success: function(response) {
                    currentRow.find('.status-badge').removeClass('sending').addClass('done').text('Terkirim');
                    index++;
                    
                    // Update Progress Bar
                    let percent = (index / total) * 100;
                    $('#progress-bar').css('width', percent + '%');

                    // Panggil fungsi berikutnya
                    sendNext();
                },
                error: function() {
                    currentRow.find('.status-badge').text('Gagal');
                    index++;
                    sendNext();
                }
            });
        }

        sendNext();
    });
});
</script>

</body>
</html>