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
    $keterangan = $_POST['keterangan'];
    
    
    //password default
   $q = mysql_query("INSERT INTO umum_area(kode,namaarea, keterangan) values ('$kode','$nama', '$keterangan');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $idarea = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    $keterangan = $_POST['keterangan'];
    
    //password default
   $q = mysql_query("UPDATE umum_area SET kode='$kode',namaarea='$nama', keterangan='$keterangan' where id='$idarea'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("DELETE from umum_area where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>