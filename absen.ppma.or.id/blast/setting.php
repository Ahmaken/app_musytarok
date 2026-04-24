<?php
require_once 'config.php';

// Cek dan update template jika file setting baru dibuat
$setting_file = __DIR__ . '/settings.json';
if (!file_exists($setting_file)) {
    $default_settings = [
        'is_active' => '1',
        'msg_template' => "Assalamu'alaikum Ust/Ustd. {nama},\n\nMengingatkan jadwal mengajar *{mapel}* pada hari ini ({hari}) pukul {jam}.\n\nMohon untuk mengisi absensi pada link di bawah ini:\n{link_absen}\n\nTerima kasih."
    ];
    file_put_contents($setting_file, json_encode($default_settings));
}

$settings = json_decode(file_get_contents($setting_file), true);

$message = "";
if (isset($_POST['save'])) {
    $new_settings = [
        'is_active' => $_POST['is_active'],
        'msg_template' => $_POST['msg_template']
    ];
    file_put_contents($setting_file, json_encode($new_settings));
    $settings = $new_settings;
    $message = "<div style='color:green; margin-bottom:15px;'>✅ Pengaturan berhasil disimpan!</div>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setting Blast WhatsApp</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 40px; background: #f0f2f5; color: #333; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 700px; margin: auto; }
        h2 { color: #1a73e8; margin-top: 0; }
        label { font-weight: bold; display: block; margin-bottom: 8px; }
        select, textarea { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #ddd; 
            border-radius: 6px; 
            box-sizing: border-box;
            font-size: 14px;
        }
        textarea { height: 180px; font-family: monospace; resize: vertical; }
        .hint { background: #e8f0fe; padding: 10px; border-radius: 6px; font-size: 13px; margin-bottom: 15px; border-left: 4px solid #1a73e8; }
        button { 
            background: #1a73e8; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            border-radius: 6px; 
            cursor: pointer; 
            font-size: 16px; 
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover { background: #1557b0; }
        .var-tag { color: #d93025; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>⚙️ Pengaturan Blast Jadwal</h2>
        <?php echo $message; ?>
        
        <form method="POST">
            <label>Status Fitur Blast:</label>
            <select name="is_active" style="margin-bottom: 20px;">
                <option value="1" <?php echo $settings['is_active'] == '1' ? 'selected' : ''; ?>>✅ Aktif (Kirim Pesan)</option>
                <option value="0" <?php echo $settings['is_active'] == '0' ? 'selected' : ''; ?>>❌ Non-Aktif</option>
            </select>

            <label>Template Pesan WhatsApp:</label>
            <div class="hint">
                <strong>Tag Variabel:</strong><br>
                <span class="var-tag">{nama}</span> : Nama Guru | 
                <span class="var-tag">{mapel}</span> : Mata Pelajaran | 
                <span class="var-tag">{hari}</span> : Hari Mengajar | 
                <span class="var-tag">{jam}</span> : Jam Mulai | 
                <span class="var-tag">{link_absen}</span> : Link Absensi Otomatis
            </div>
            <textarea name="msg_template" placeholder="Tulis pesan di sini..."><?php echo htmlspecialchars($settings['msg_template']); ?></textarea>
            
            <p style="font-size: 12px; color: #666;">* Link absensi akan digenerate otomatis menyertakan tanggal hari ini dan ID jadwal sesuai database.</p>
            
            <button type="submit" name="save">Simpan Pengaturan</button>
        </form>
    </div>

    <div class="card">
        <p><a href="https://absen.quizb.my.id/blast/cron_madin.php">Coba Madin</a></p>
        <p><a href="https://absen.quizb.my.id/blast/cron_quran.php">Coba Qiraati</a></p> 
    </div>
    
</body>
</html>