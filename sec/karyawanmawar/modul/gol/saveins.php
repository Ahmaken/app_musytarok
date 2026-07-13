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
}

function save() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $golongan = isset($_POST['golongan']) ? $_POST['golongan'] : '';
    $q = mysql_query("insert into inv_golongan(id, golongan) values ('$kode','$golongan')");
    if ($q) {
        header('Location: golongan.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: golongan.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $instansi = isset($_POST['golongan']) ? $_POST['golongan'] : '';
    $q = mysql_query("update inv_golongan set golongan='$instansi' where id='$kode'");
    if ($q) {
        header('Location: golongan.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: golongan.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $q = mysql_query("delete from inv_golongan where id='$kode'");
    if ($q) {
        header('Location: golongan.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: golongan.php?status=gagal&ket=' . $error);
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

