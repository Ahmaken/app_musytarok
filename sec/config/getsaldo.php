<?php

include'../session/level3.php';
include './kon.php';
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
    if (!$q)
        return;
    $sql = mysql_query("SELECT kodekumpul FROM anggota_pencarian WHERE kodekumpul LIKE '%$q%'");
    while ($r = mysql_fetch_array($sql)) {
        $kode_akun = $r['kodekumpul'];
        echo "$kode_akun \n";
    }
}

function data() {
    $kode = explode('-', $_POST['kode']);
    $kode = $kode[0];
    $sql = mysql_query("SELECT b.kode_anggota as kode, a.nama, b.saldo FROM anggota_master a, anggota_saldo b WHERE a.kode=b.kode_anggota AND b.kode_anggota='$kode'");
    $row = mysql_num_rows($sql);
    if ($row > 0) {
        $r = mysql_fetch_array($sql);
        $data['kode'] = $r['kode'];
        $data['nama'] = $r['nama'];
        $data['saldo'] = $r['saldo'];
        echo json_encode($data);
    } else {
        $data['kode'] = '';
        $data['nama'] = '';
        $data['saldo'] = '';
        echo json_encode($data);
    }
}

?>