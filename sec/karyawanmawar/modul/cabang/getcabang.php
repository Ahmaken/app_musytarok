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
    $sql = mysql_query("SELECT kode from umum_cabang where kode LIKE '$q%'");

    while ($r = mysql_fetch_array($sql)) {
        $kode = $r['kode'];
        echo "$kode \n";
    }
}

function data(){
    
    $kode = $_POST['kode'];
//   get kode anggaran
    
    $sql = mysql_query("SELECT kode, nama from umum_cabang where kode = '$kode'");
    $row = mysql_num_rows($sql);
    if ($row > 0) {
        $r = mysql_fetch_array($sql);
        $data['kode'] = $r['kode'];
        $data['nama'] = $r['nama'];
        echo json_encode($data);
    } else {
        $data['kode'] = '';
        $data['nama'] = '';
        echo json_encode($data);
    }
}

?>