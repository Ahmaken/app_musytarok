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
    $sql = mysql_query("SELECT * FROM ak_akun WHERE STATUS=1 AND kode LIKE '$q%'");
    while ($r = mysql_fetch_array($sql)) {
        $kode_akun = $r['kode'];
        echo "$kode_akun \n";
    }
}

function data() {
    $kode = $_POST['kode'];
    $sql = mysql_query("SELECT * FROM ak_akun WHERE STATUS=1 AND kode='$kode'");
    $row = mysql_num_rows($sql);
    if ($row > 0) {
        $r = mysql_fetch_array($sql);
        $data['id'] = $r['id'];
        $data['kode'] = $r['kode'];
        $data['nama'] = $r['nama'];
        $data['grup'] = $r['grup'];
        echo json_encode($data);
    } else {
        $data['id'] = 'Data Tidak Ditemukan';
        $data['kode'] = 'Data Tidak Ditemukan';
        $data['nama'] = 'Data Tidak Ditemukan';
        $data['grup'] = 'Data Tidak Ditemukan';
        echo json_encode($data);
    }
}

?>