<?php

include'../../session/level3.php';
include '../../config/kon.php';

$type = isset($_GET['type']) ? $_GET['type'] : "";
switch ($type) {
    case "nik":
        nip();
        break;
    case "data":
        data();
    break;
}

function nip() {
    $q = strtolower($_GET["q"]);
    //$sql = mysql_query("SELECT * from pegawai");
    $sql = mysql_query("SELECT nik from karyawan_master where status=1 and nik LIKE '$q%'");

    while ($r = mysql_fetch_array($sql)) {
        $nip = $r['nik'];
        echo "$nip \n";
    }
}

function data(){
    
    $nip = $_POST['nik'];
//   get kode anggaran
    
    $sql = mysql_query("SELECT nik, nama from karyawan_master where nik = '$nip'");
    $row = mysql_num_rows($sql);
    if ($row > 0) {
        $r = mysql_fetch_array($sql);
        $data['nik'] = $r['nik'];
        $data['nama'] = $r['nama'];
        echo json_encode($data);
    } else {
        $data['nik'] = '';
        $data['nama'] = '';
        echo json_encode($data);
    }
}

?>