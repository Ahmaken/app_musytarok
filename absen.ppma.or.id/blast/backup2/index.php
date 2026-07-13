<?php
require_once 'config.php';
require_once 'wa.php';

// Default Template
$default_msg = "Assalamu'alaikum Ust/Ustd. {nama},\n\nBerikut adalah akun login Anda untuk sistem Absensi Online:\nUsername: {username}\nPassword: {password}\n\nSilakan login di: https://absen.quizb.my.id\nTerima kasih.";

// Handle Pengiriman (Individual atau Masal)
$notif = "";
if (isset($_POST['send_wa'])) {
    $target_id = $_POST['guru_id'];
    $pesan_final = $_POST['pesan_wa'];
    $no_hp = $_POST['no_hp'];

    $res = kirimWA($no_hp, $pesan_final);
    $notif = "<div class='alert success'>Pesan dikirim ke $no_hp: $res</div>";
}

if (isset($_POST['blast_all'])) {
    $template = $_POST['template_global'];
    $query = "SELECT g.nama, g.no_hp, u.username, u.password 
              FROM guru g 
              JOIN users u ON g.guru_id = u.id
              WHERE u.role = 'guru' AND g.guru_id > 66"; // Tambahkan ini
    $result = $conn->query($query);
    
    $count = 0;
    while ($row = $result->fetch_assoc()) {
        $pesan = str_replace(
            ['{nama}', '{username}', '{password}'], 
            [$row['nama'], $row['username'], $row['password']], 
            $template
        );
        kirimWA($row['no_hp'], $pesan);
        $count++;
    }
    $notif = "<div class='alert success'>Berhasil blast ke $count Guru!</div>";
}

// Ambil data Guru dan User
$query_guru = "SELECT g.guru_id, g.nama, g.no_hp, u.username, u.password 
               FROM guru g 
               LEFT JOIN users u ON g.guru_id = u.id 
               WHERE u.role = 'guru' OR g.guru_id IS NOT NULL";
$data_guru = $conn->query($query_guru);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Blast Akun Guru</title>
    <style>
        body { font-family: sans-serif; background: #f4f7f6; padding: 20px; }
        .container { max-width: 1100px; margin: auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; }
        textarea { width: 100%; padding: 10px; border-radius: 5px; border: 1px solid #ccc; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 13px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background: #f8f9fa; }
        .btn { padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; color: white; font-weight: bold; }
        .btn-primary { background: #007bff; }
        .btn-success { background: #28a745; margin-bottom: 10px; }
        .btn-wa { background: #25D366; font-size: 11px; }
    </style>
</head>
<body>

<div class="container">
    <h2>🚀 Blast Username & Password Guru</h2>
    <?php echo $notif; ?>

    <form method="POST">
        <label><strong>1. Edit Template Pesan Global:</strong></label>
        <div style="font-size: 12px; color: #666; margin-bottom: 5px;">Gunakan variabel: {nama}, {username}, {password}</div>
        <textarea name="template_global" rows="5"><?php echo $default_msg; ?></textarea>
        <br><br>
        <button type="submit" name="blast_all" class="btn btn-success" onclick="return confirm('Kirim akun ke SEMUA guru?')">⚡ Blast Semua Guru</button>
    </form>

    <hr>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Guru</th>
                <th>WhatsApp</th>
                <th>Username/Pass</th>
                <th>Edit Pesan Individual</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $data_guru->fetch_assoc()) : 
                // Generate pesan individual untuk textarea per baris
                $pesan_individu = str_replace(
                    ['{nama}', '{username}', '{password}'], 
                    [$row['nama'], $row['username'], $row['password']], 
                    $default_msg
                );
            ?>
            <tr>
                <td><?php echo $row['guru_id']; ?></td>
                <td><strong><?php echo $row['nama']; ?></strong></td>
                <td><?php echo $row['no_hp']; ?></td>
                <td>
                    <small>U: <?php echo $row['username']; ?><br>P: <?php echo $row['password']; ?></small>
                </td>
                <td>
                    <form id="form-<?php echo $row['guru_id']; ?>" method="POST">
                        <input type="hidden" name="guru_id" value="<?php echo $row['guru_id']; ?>">
                        <input type="hidden" name="no_hp" value="<?php echo $row['no_hp']; ?>">
                        <textarea name="pesan_wa" rows="3"><?php echo $pesan_individu; ?></textarea>
                    </form>
                </td>
                <td>
                    <button type="submit" form="form-<?php echo $row['guru_id']; ?>" name="send_wa" class="btn btn-wa">Kirim WA</button>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>