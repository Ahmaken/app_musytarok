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
    $cabang = $_POST['kodecabang'];
    $tglawal = explode("-", $_POST['tglawal']);
    $tglawal = $tglawal[2] . "" . $tglawal[1] . "" . $tglawal[0];
    $status = $_POST['status'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    $cekstatus = mysql_query("SELECT status FROM karyawan_master WHERE nik ='$kode' limit 1");
    $getcekstatus = mysql_fetch_array($cekstatus);
    $cekstatus = $getcekstatus['status'];
    if ($cekstatus == $status) {
        echo "<script>
	  window.alert('Maaf, tidak bisa update status yang sama!');
	  location.href = 'index.php';</script>";
    } else {
        $q = mysql_query("INSERT INTO karyawan_mutasistatus(nik, tanggal, status, landasan, keterangan, timestamp, operator)"
                . " values ('$kode','$tglawal', '$status', '$landasan', '$keterangan', now(),'$operator');");

        if ($q) {
            $q = mysql_query("update karyawan_master set status='$status' where nik='$kode'");
            header('Location: index.php?status=sukses');
        } else {
            $error = mysql_error();
            header('Location: index.php?status=gagal&ket=' . $error);
        }
    }

    //password default
}

function update() {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $cabang = $_POST['kodecabang'];
    $tglawal = explode("-", $_POST['tglawal']);
    $tglawal = $tglawal[2] . "" . $tglawal[1] . "" . $tglawal[0];
    $status = $_POST['status'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    //password default
    $q = mysql_query("UPDATE karyawan_mutasistatus SET status='$status',tglawal='$tglawal', landasan='$landasan', keterangan='$keterangan', timestamp=now(), operator='$operator' where id='$id'");

    if ($q) {
        $q = mysql_query("update karyawan_master set status='$status' where nik='$kode'");
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("delete from karyawan_mutasistatus where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>