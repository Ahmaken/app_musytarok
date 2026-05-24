<?php

include '../../session/level2.php';
include '../../config/kon.php';
$type = isset($_GET['type']) ? $_GET['type'] : "";

switch ($type) {
    case 'save':
        save();
        break;
    case 'update':
        update();
        break;
    case 'delete':
        hapus();
        break;
    case 'ubahpassword':
        ubahpassword();
        break;
}

function save() {
    $level = $_POST['level'];
    $pokok = intval($_POST['pokok']);
    $jabatan = intval($_POST['jabatan']);
    $operasional = intval($_POST['operasional']);
    $kinerja = intval($_POST['kinerja']);
    $perjam = intval($_POST['perjam']);
    $keterangan = $_POST['keterangan'];
    //$kodearea = $_POST['kodearea'];
    //password default
    $q = mysql_query("INSERT INTO karyawan_level(LEVEL, bisyarah_pokok, tunjangan_jabatan, tunjangan_operasional, tunjangan_kinerja, tarif_jam, keterangan) "
            . "VALUES ('$level', '$pokok', '$jabatan', '$operasional', '$kinerja', '$perjam', '$keterangan');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $id = $_POST['id'];
    $level = $_POST['level'];
    $pokok = intval($_POST['pokok']);
    $jabatan = intval($_POST['jabatan']);
    $operasional = intval($_POST['operasional']);
    $kinerja = intval($_POST['kinerja']);
    $perjam = intval($_POST['perjam']);
    $keterangan = $_POST['keterangan'];

    //password default
    $q = mysql_query("UPDATE karyawan_level set level='$level', bisyarah_pokok='$pokok', tunjangan_jabatan='$jabatan', "
            . "tunjangan_operasional='$operasional', tunjangan_kinerja='$kinerja', tarif_jam='$perjam', keterangan='$keterangan' where id='$id'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("DELETE from karyawan_level where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>