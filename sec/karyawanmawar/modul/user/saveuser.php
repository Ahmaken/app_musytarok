<?php

$tipe = isset($_GET['jenis']) ? $_GET['jenis'] : '';
include '../../config/kon.php';

switch ($tipe) {
    case "save":
        save();
        break;
    case "update":
        update();
        break;

    case "del":
        delete();
        break;
    case "edit":
        edit();
        break;
     case "aktif":
        aktif();
        break;
}

function save() {
   // $username = $_POST['username'];
    $password = $_POST['password'];
    $nik = $_POST['nik'];
    $otorisasi = $_POST['otorisasi'];
    $operator = $_SESSION['username'];
    //$jabatan = $_POST['jabatan'];
    $qthn = mysql_fetch_array(mysql_query("SELECT LEFT(tgllahir, 4) AS thnlhr FROM karyawan_master WHERE nik='$nik';"));
    
    
    $password = md5($qthn['thnlhr']);
    
    $q = mysql_query("insert into umum_user(nik, password, otorisasi, timestamp, status, operator) values "
            . "('$nik','$password', '$otorisasi', now(), '1', '$operator')");
    if ($q) {
        header('Location: index.php?status=sukses&un='.$username);
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $username = $_POST['nik'];

    $otorisasi = $_POST['otorisasi'];
    
    $q = mysql_query("update umum_user set otorisasi='$otorisasi' where nik='$username'");
    if ($q) {
        header('Location: index.php?status=sukses&un='.$username);
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $kode = isset($_GET['kode']) ? $_GET['kode'] : '';
    $q = mysql_query("update umum_user set status=0 where nik='$kode'");
    if ($q) {
        header('Location: index.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function aktif() {
    $kode = isset($_GET['kode']) ? $_GET['kode'] : '';
    $q = mysql_query("update umum_user set status=1 where nik='$kode'");
    if ($q) {
        header('Location: index.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function edit() {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $q = mysql_query("update umum_user set password='$password' where nik=$username");
    if ($q) {
        echo "<script>
	  window.alert('Ganti password berhasil. Anda harus login kembali.');
	  location.href = '../login/logout.php';</script>";
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

