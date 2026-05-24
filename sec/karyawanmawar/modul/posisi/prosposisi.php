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
    $jabatan = $_POST['kodejabatan'];
    $tglawal = explode("-", $_POST['tglawal']);
    $tglawal= $tglawal[2]."".$tglawal[1]."".$tglawal[0];
    $tglakhir = explode("-", $_POST['tglakhir']);
    $tglakhir= $tglakhir[2]."".$tglakhir[1]."".$tglakhir[0];
    //$kodearea = $_POST['kodearea'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];
    
    //password default
   $q = mysql_query("INSERT INTO karyawan_mutasijabatan(nik, jabatan, tglawal, tglakhir, landasan, keterangan, status, timestamp, operator)"
           . " values ('$kode','$jabatan', '$tglawal', '$tglakhir', '$landasan', '$keterangan','1', now(),'$operator');");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
     $id = $_POST['id'];
    $nik = $_POST['kode'];
    $jabatan = $_POST['kodejabatan'];
    $tglawal = explode("-", $_POST['tglawal']);
    $tglawal = $tglawal[2] . "" . $tglawal[1] . "" . $tglawal[0];
    $tglakhir = explode("-", $_POST['tglakhir']);
    $tglakhir = $tglakhir[2] . "" . $tglakhir[1] . "" . $tglakhir[0];
    //$kodearea = $_POST['kodearea'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    //password default
    $q = mysql_query("UPDATE karyawan_mutasijabatan SET nik='$nik', jabatan='$jabatan',tglawal='$tglawal', tglakhir='$tglakhir', landasan='$landasan', keterangan='$keterangan', timestamp=now(), operator='$operator' where id='$id'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("UPDATE karyawan_mutasijabatan set status='0' where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}


?>