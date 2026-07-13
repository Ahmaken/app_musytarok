<?php

include '../../session/level3.php';
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
    case "import":
        import();
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
        header('Location: index.php?status=sukses&un=' . $username);
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $username = $_POST['username'];

    $otorisasi = $_POST['otorisasi'];

    $q = mysql_query("update umum_user set otorisasi='$otorisasi' where nik='$username'");
    if ($q) {
        header('Location: index.php?status=sukses&un=' . $username);
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

function import() {
    include '../../config/excel_reader2.php';
    $bulan = $_POST['bulan'];
    $operator = $_SESSION['username'];
//membaca file excel yang diupload
    $data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);
//membaca jumlah baris dari data excel
    $baris = $data->rowcount($sheet_index = 0);

//nilai awal counter jumlah data yang sukses dan yang gagal diimport
    $sukses = 0;
    $gagal = 0;

//import data excel dari baris kedua, karena baris pertama adalah nama kolom
    for ($i = 2; $i <= $baris; $i++) {
        //membaca data nip (kolom ke-1)
        $nik = $data->val($i, 2);
        //membaca data nama depan (kolom ke-2)
        $jam = $data->val($i, 3);
        //membaca data nama belakang (kolom ke-3)
        //$nm_belakang = $data->val($i, 3);
//setelah data dibaca, sisipkan ke dalam tabel pegawai
        $query = mysql_query("INSERT INTO karyawan_rekapabsen(nik, bulan,jamabsen, timestamp, operator) values ('$nik', '$bulan','$jam', now(), '$operator')");
        //menambah counter jika berhasil atau gagal
        if ($query)
            $sukses++;
        else
            $gagal++;
    }
//tampilkan report hasil import
     if ($query) {
        header('Location: index.php?status=sukses&row=' . $sukses . '&gagal=' . $gagal);
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

