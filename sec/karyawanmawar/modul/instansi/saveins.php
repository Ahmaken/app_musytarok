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
    $instansi = isset($_POST['instansi']) ? $_POST['instansi'] : '';
    $gedung = isset($_POST['gedung']) ? $_POST['gedung'] : '';
    $q = mysql_query("insert into umum_instansi (id, instansi, gedung) values ('$kode','$instansi', '$gedung')");
    if ($q) {
        header('Location: instansi.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: instansi.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $instansi = isset($_POST['instansi']) ? $_POST['instansi'] : '';
    $instansi = isset($_POST['gedung']) ? $_POST['gedung'] : '';
    $q = mysql_query("update umum_instansi set instansi='$instansi', $gedung='$gedung' where id=$kode");
    if ($q) {
        header('Location: instansi.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: instansi.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $kode = isset($_POST['kode']) ? $_POST['kode'] : '';
    $q = mysql_query("delete from umum_instansi where id='$kode'");
    if ($q) {
        header('Location: instansi.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: instansi.php?status=gagal&ket=' . $error);
    }
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

