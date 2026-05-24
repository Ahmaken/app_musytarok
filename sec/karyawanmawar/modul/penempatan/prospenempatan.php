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
    $tglakhir = explode("-", $_POST['tglakhir']);
    $tglakhir = $tglakhir[2] . "" . $tglakhir[1] . "" . $tglakhir[0];
    //$kodearea = $_POST['kodearea'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    $cekcabang = mysql_query("SELECT cabang FROM karyawan_penempatan WHERE nik ='$kode' order by id desc limit 1");
    $getcekcabang = mysql_fetch_array($cekcabang);
    $cekcabang = $getcekcabang['cabang'];
    if ($cekcabang == $cabang) {
        echo "<script>
	  window.alert('Maaf, tidak bisa menempatkan di tempat yang sama!');
	  location.href = 'index.php';</script>";
    } else {
        $q = mysql_query("INSERT INTO karyawan_penempatan(nik, cabang, tglawal, tglakhir, landasan, keterangan, status, timestamp, operator)"
                . " values ('$kode','$cabang', '$tglawal', '$tglakhir', '$landasan', '$keterangan','1', now(),'$operator');");

        if ($q) {
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
    $nik = $_POST['kode'];
    $cabang = $_POST['kodecabang'];
    $tglawal = explode("-", $_POST['tglawal']);
    $tglawal = $tglawal[2] . "" . $tglawal[1] . "" . $tglawal[0];
    $tglakhir = explode("-", $_POST['tglakhir']);
    $tglakhir = $tglakhir[2] . "" . $tglakhir[1] . "" . $tglakhir[0];
    //$kodearea = $_POST['kodearea'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    //password default
    $q = mysql_query("UPDATE karyawan_penempatan SET nik='$nik', cabang='$cabang',tglawal='$tglawal', tglakhir='$tglakhir', landasan='$landasan', keterangan='$keterangan', timestamp=now(), operator='$operator' where id='$id'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("update karyawan_penempatan set status='0' where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>