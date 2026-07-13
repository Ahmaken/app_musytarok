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
    $gedung = isset($_POST['gedung']) ? $_POST['gedung'] : '';
    $q = mysql_query("insert into umum_gedung(kode, gedung) values ('$kode','$gedung')");
    if ($q) {
        header('Location: gedung.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: gedung.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $instansi = isset($_POST['gedung']) ? $_POST['gedung'] : '';
    $q = mysql_query("update umum_gedung set gedung='$instansi' where kode='$kode'");
    if ($q) {
        header('Location: gedung.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: gedung.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $q = mysql_query("delete from umum_instansi where kode='$kode'");
    if ($q) {
        header('Location: gedung.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: gedung.php?status=gagal&ket=' . $error);
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

