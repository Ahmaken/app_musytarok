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
    $nama = $_POST['nama'];
    $kodearea = $_POST['kodearea'];
    $keterangan = $_POST['keterangan'];
    
    
    //password default
   $q = mysql_query("INSERT INTO umum_kabupaten(kode,namakabupaten, kodearea, keterangan) values ('$kode','$nama', '$kodearea', '$keterangan');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $idkab = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $kodearea = $_POST['kodearea'];
    $keterangan = $_POST['keterangan'];
    
    //password default
   $q = mysql_query("UPDATE umum_kabupaten SET kode='$kode', namakabupaten='$nama', keterangan='$keterangan', kodearea='$kodearea' where id='$idkab'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("DELETE from umum_kabupaten where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}


?>