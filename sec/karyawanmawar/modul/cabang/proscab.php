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
    //$kodearea = $_POST['kodearea'];
    $kodekab = $_POST['kodekab'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $usaha = $_POST['usaha'];
      $tgllhr = explode("-", $_POST['berdiri']);
    $berdiri = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $npp = $_POST['npp'];
    $keterangan = $_POST['keterangan'];
    
    
    //password default
   $q = mysql_query("INSERT INTO umum_cabang(kode,nama, alamat, telepon,email, usaha, tanggalberdiri, kodekab, keterangan) values "
           . "('$kode','$nama', '$alamat', '$telepon', '$email', '$usaha','$berdiri', '$kodekab', '$npp', '$keterangan');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $idcab = $_POST['id'];
    $kode = $_POST['kode'];
    $nama = $_POST['nama'];
    //$kodearea = $_POST['kodearea'];
    $kodekab = $_POST['kodekab'];
    $alamat = $_POST['alamat'];
    $email = $_POST['email'];
    $telepon = $_POST['telepon'];
    $usaha = $_POST['usaha'];
      $tgllhr = explode("-", $_POST['berdiri']);
    $berdiri = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $npp = $_POST['npp'];
    $keterangan = $_POST['keterangan'];
    
    //password default
   $q = mysql_query("UPDATE umum_cabang SET kode='$kode', nama='$nama',kodekab='$kodekab', alamat='$alamat', email='$email', telepon='$telepon', usaha='$usaha', tanggalberdiri='$berdiri', npp='$npp', keterangan='$keterangan' where id='$idcab'");

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