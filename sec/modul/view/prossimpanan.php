<?php

include '../../session/level3.php';
include '../../config/kon.php';
//include '../../config/tahunajaran.php';
$type = isset($_GET['type']) ? $_GET['type'] : "";

switch ($type) {
    case 'penambahan':
        penambahan();
        break;
    case 'penarikan':
        penarikan();
        break;
    case 'delete':
        hapus();
        break;
    case 'proses':
        proses();
        break;
}

function penambahan() {
    $kodeanggota = $_POST['kodeanggota'];
    $tanggal = explode('/', $_POST['tanggal']);
    $tglsql = $tanggal[2] . "-" . $tanggal[1] . "-" . $tanggal[0];
    $jam = date('H:i:s');
    $nominal = str_replace('.', '', $_POST['nominal']);
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];
    //generate kode
    $yearNow = date('Y');
    $cekanggota = mysql_fetch_array(mysql_query("select count(kode) as kode from anggota_master where kode='$kodeanggota'"));
    if ($cekanggota['kode'] == 0) {
        header('Location: index.php?modul=penambahan&status=gagal&ket= Masukkan Kode Anggota yang valid');
    } else {
        $jenistrans = mysql_fetch_array(mysql_query("SELECT count(id) as id from anggota_simpanan where kode_anggota = '$kodeanggota'"));
        if ($jenistrans['id'] == 0) {
            $jenistrans = "BRU";
            $baris = mysql_fetch_array(mysql_query("SELECT COUNT(kode) as kode FROM anggota_simpanan WHERE kode LIKE 'BRU$yearNow%'"));
            $barisnya = $baris['kode'] + 1;
            $barisnya = str_pad($barisnya, 5, "0", STR_PAD_LEFT);
            $kode = "BRU$yearNow" . $barisnya;
        } else {
            $jenistrans = "PNB";
            $baris = mysql_fetch_array(mysql_query("SELECT COUNT(kode) as kode FROM anggota_simpanan WHERE kode LIKE 'PNB$yearNow%'"));
            $barisnya = $baris['kode'] + 1;
            $barisnya = str_pad($barisnya, 5, "0", STR_PAD_LEFT);
            $kode = "PNB$yearNow" . $barisnya;
        }
        $q = mysql_query("INSERT INTO anggota_simpanan(tanggal, jam, kode, kode_anggota, debet, kredit, jenis, keterangan, TIMESTAMP, operator) "
                . "VALUES ('$tglsql', '$jam', '$kode', '$kodeanggota', '$nominal', 0, '$jenistrans', '$keterangan', now(), '$operator')");
        if ($q) {
            header('Location: index.php?modul=penambahan&status=sukses&kode=' . $kode);
        } else {
            $error = mysql_error();
            header('Location: index.php?modul=penambahan&status=gagal&ket=' . $error);
        }
    }
}

function penarikan() {
    $kodeanggota = $_POST['kodeanggota'];
    $tanggal = explode('/', $_POST['tanggal']);
    $tglsql = $tanggal[2] . "-" . $tanggal[1] . "-" . $tanggal[0];
    $jam = date('H:i:s');
    $nominal = str_replace('.', '', $_POST['nominal']);
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];
    //generate kode
    $yearNow = date('Y');
    $ceksaldo = mysql_fetch_array(mysql_query("SELECT saldo FROM anggota_saldo WHERE kode_anggota='$kodeanggota'"));
    if ($ceksaldo['saldo'] < $nominal) {
        header('Location: index.php?modul=penarikan&status=gagal&ket= Saldo tidak mencukupi');
    } else {
        $baris = mysql_fetch_array(mysql_query("SELECT COUNT(kode) as kode FROM anggota_simpanan WHERE kode LIKE 'PNR$yearNow%'"));
        $barisnya = $baris['kode'] + 1;
        $barisnya = str_pad($barisnya, 5, "0", STR_PAD_LEFT);
        $kode = "PNR$yearNow" . $barisnya;

        $q = mysql_query("INSERT INTO anggota_simpanan(tanggal, jam, kode, kode_anggota, debet, kredit, jenis, keterangan, TIMESTAMP, operator) "
                . "VALUES ('$tglsql', '$jam', '$kode', '$kodeanggota', 0, '$nominal', '$jenistrans', '$keterangan', now(), '$operator')");
        if ($q) {
            header('Location: index.php?modul=penarikan&status=sukses&kode=' . $kode);
        } else {
            $error = mysql_error();
            header('Location: index.php?modul=penarikan&status=gagal&ket=' . $error);
        }
    }
}

function update() {
    $id = $_POST['id'];
    $kodeakun = $_POST['kodeakun'];
    $namaakun = strtoupper($_POST['namaakun']);
    $kodeparent = isset($_POST['kodeparent']) ? $_POST['kodeparent'] : "";
    $grup = isset($_POST['grup']) ? $_POST['grup'] : "";
    $grup = $grup + 1;
    $operator = $_SESSION['username'];

    //echo $kode.".". $instansi.".". $program."." .$operator;
    $q = mysql_query("update ak_akun set kode= '$kodeakun', nama='$namaakun', grup='$grup', parent='$kodeparent', operator='$operator', timestamp=now() where id='$id'");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_POST['kodex'];
    $q = mysql_query("UPDATE ak_jurnal set status=0, timestamp=now() where id=$kode");
    if ($q) {
        header('Location: index.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function proses() {
    $kodetrans = $_POST['kodetrans'];
    $q = mysql_query("UPDATE ak_jurnal set proses=1 where kode='$kodetrans'");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

?>