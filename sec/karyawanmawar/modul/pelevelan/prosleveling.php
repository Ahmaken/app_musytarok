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
    $level = $_POST['level'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    $ceklevel = mysql_query("SELECT level FROM karyawan_leveling WHERE nik ='$kode' order by id desc limit 1");
    $getceklevel = mysql_fetch_array($ceklevel);
    $ceklevel = $getceklevel['level'];
    if ($ceklevel == $level) {
        echo "<script>
	  window.alert('Maaf, tidak bisa menempatkan di Level yang sama!');
	  location.href = 'index.php';</script>";
    } else {
        $q = mysql_query("INSERT INTO karyawan_leveling(nik, level,  landasan, keterangan, status, timestamp, operator)"
                . " values ('$kode','$level', '$landasan', '$keterangan','1', now(),'$operator');");

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
       $kode = $_POST['kode'];
    $level = $_POST['level'];
    $landasan = $_POST['landasan'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];

    //password default
    $q = mysql_query("UPDATE karyawan_leveling SET nik='$kode', level='$level', landasan='$landasan', keterangan='$keterangan', timestamp=now(), operator='$operator' where id='$id'");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_GET['kode'];
    $q = mysql_query("update karyawan_leveling set status='0' where id = $kode");
    if (q) {
        header('Location: index.php?status=delete');
    } else {
        header('Location: index.php?status=gagal');
    }
}

?>