<?php
include '../../session/level2.php';
$tipe = isset($_GET['jenis']) ? $_GET['jenis'] : '';
include '../../config/kon.php';

switch ($tipe) {
    case "save":
        save();
        break;
    case "update":
        update();
        break;

    case "hapus":
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
    $password = md5($_POST['password']);
    $nik = $_POST['nik'];
    $otorisasi = $_POST['otorisasi'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $operator = $_SESSION['username'];

    $q = mysql_query("INSERT INTO USER(username, PASSWORD, otorisasi, noktp, nama, STATUS, TIMESTAMP, operator) values "
            . "('$username','$password', '$otorisasi', '$nik', '$nama', 1, now(), '$operator')");
    if ($q) {
        header('Location: index.php?status=sukses&un=' . $username);
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    //$password = md5($_POST['password']);
    $nik = $_POST['nik'];
    $otorisasi = $_POST['otorisasi'];
    $username = $_POST['username'];
    $nama = $_POST['nama'];
    $operator = $_SESSION['username'];

    $q = mysql_query("update user set noktp='$nik', nama='$nama', username='$username', otorisasi='$otorisasi' where username='$username'");
    if ($q) {
        header('Location: index.php?status=sukses&un=' . $username);
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $q = mysql_query("update user set status=0 where username='$username'");
    if ($q) {
        header('Location: index.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function aktif() {
    $username = isset($_GET['username']) ? $_GET['username'] : '';
    $q = mysql_query("update user set status=1 where username='$username'");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function edit() {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $q = mysql_query("update user set password='$password' where username='$username'");
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

