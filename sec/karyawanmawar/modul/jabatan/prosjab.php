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
    $kode = $_POST['kode'];
    $jabatan = $_POST['jabatan'];
    $departemen = $_POST['departemen'];
    $koordinator = $_POST['koordinator'];
    $keterangan = $_POST['keterangan'];
    //$kodearea = $_POST['kodearea'];
    //password default
    $q = mysql_query("INSERT INTO karyawan_jabatan(kode,jabatan, departemen, koordinator,keterangan) VALUES ('$kode','$jabatan', '$departemen', '$koordinator', '$keterangan');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $idjab = $_POST['id'];
    $kode = $_POST['kode'];
    $jabatan = $_POST['jabatan'];
    $departemen = $_POST['departemen'];
    $koordinator = $_POST['koordinator'];
    $keterangan = $_POST['keterangan'];

    //password default
    $q = mysql_query("UPDATE karyawan_jabatan SET kode='$kode', jabatan='$jabatan',departemen='$departemen', koordinator='$koordinator', keterangan='$keterangan' where id='$idjab'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("DELETE from karyawan_jabatan where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>