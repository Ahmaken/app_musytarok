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
   // $username = $_POST['username'];
    $info = $_POST['info'];
    $tgl = date('Y-m-d');
    
    $q = mysql_query("insert into umum_info (info, tanggal, status) values ('$info','$tgl', 1)");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
   $idInfo = $_POST['idinfo'];
   $info = $_POST['info'];
   $status = $_POST['status'];
    //$tgl = date('Y-m-d');
    
    $q = mysql_query("update umum_info set info='$info', status='$status' where id='$idInfo'");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function delete() {
    $id = isset($_POST['kode']) ? $_POST['kode'] : '';
   // echo $id;
    $q = mysql_query("delete from umum_info where id='$id'");
    if ($q) {
        header('Location: index.php?status=hapus');
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

