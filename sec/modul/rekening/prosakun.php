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
}

function save() {
    $kodeakun =$_POST['kodeakun'];
    $namaakun = strtoupper($_POST['namaakun']);
    $kodeparent = isset ($_POST['kodeparent'])? $_POST['kodeparent'] : "";
    $grup = isset ($_POST['grup']) ? $_POST['grup'] : "";
    $grup = $grup+1;
    $operator= $_SESSION['username'];
   
  
//    //insert into
    $q = mysql_query("INSERT INTO ak_akun (kode, nama, grup, parent, timestamp, status, operator) values "
            . "('$kodeakun', '$namaakun', '$grup', '$kodeparent', now(), 1, '$operator' )");

    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function update() {
    $id =$_POST['id'];
    $kodeakun =$_POST['kodeakun'];
    $namaakun = strtoupper($_POST['namaakun']);
    $kodeparent = isset ($_POST['kodeparent'])? $_POST['kodeparent'] : "";
    $grup = isset ($_POST['grup']) ? $_POST['grup'] : "";
    $grup = $grup+1;
    $operator= $_SESSION['username'];

    //echo $kode.".". $instansi.".". $program."." .$operator;
    $q = mysql_query("update ak_akun set kode= '$kodeakun', nama='$namaakun', grup='$grup', parent='$kodeparent', operator='$operator', timestamp=now() where id='$id'");
    if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $kode = $_POST['kode'];
    $q = mysql_query("delete from ak_akun where kode=" . $kode);
   if ($q) {
        header('Location: index.php?status=hapus');
    } else {
        $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
}

?>