<?php

include'../../session/level3.php';
include '../../config/kon.php';

$type = isset($_GET['type']) ? $_GET['type'] : "";
switch ($type) {
    case "kode":
        kode();
        break;
    case "data":
        data();
    break;
}

function kode() {
    $q = strtolower($_GET["q"]);
    //$sql = mysql_query("SELECT * from pegawai");
    $sql = mysql_query("SELECT kode from karyawan_jabatan where kode LIKE '$q%'");

    while ($r = mysql_fetch_array($sql)) {
        $kode = $r['kode'];
        echo "$kode \n";
    }
}

function data(){
    
    $kode = $_POST['kode'];
//   get kode anggaran
    
    $sql = mysql_query("SELECT kode, jabatan from karyawan_jabatan where kode = '$kode'");
    $row = mysql_num_rows($sql);
    if ($row > 0) {
        $r = mysql_fetch_array($sql);
        $data['kode'] = $r['kode'];
        $data['jabatan'] = $r['jabatan'];
        echo json_encode($data);
    } else {
        $data['kode'] = '';
        $data['jabatan'] = '';
        echo json_encode($data);
    }
}

?>