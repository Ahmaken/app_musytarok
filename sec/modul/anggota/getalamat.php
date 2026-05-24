<?php
include '../../config/kon.php';
$qprov= mysql_query("SELECT provinsiId as kode, provinsiNama as nama FROM provinsi ORDER BY provinsiId;");
$arrpropinsi = array();
while ($row = mysql_fetch_assoc($qprov)) {
    $arrpropinsi [$row['kode']] = $row['nama'];
}

#action get Kabupaten
if (isset($_GET['action']) && $_GET['action'] == "getKab") {
    $kode_prop = $_GET['kode_prop'];
//ambil data kabupaten
    $query = mysql_query("SELECT kabupatenId as kodekab, kabupatenNama as namakab FROM kabupaten WHERE provinsiId = '$kode_prop' ORDER BY kabupatenNama;");
    //$sql = mysqli_query($conn, $query);
    $arrkab = array();
    while ($row = mysql_fetch_assoc($query)) {
        array_push($arrkab, $row);
    }
    echo json_encode($arrkab);
    exit;
}
if (isset($_GET['action']) && $_GET['action'] == "getKec") {
    $kode_kab = $_GET['kode_kab'];
//ambil data kabupaten
   $query = mysql_query("SELECT a.kecamatanId AS kodeKec, a.kecamatanNama AS namaKec FROM kecamatan a WHERE kabupatenId='$kode_kab' ORDER BY a.kecamatanNama;");
    //$sql = mysqli_query($conn, $query);
    $arrkab = array();
    while ($row = mysql_fetch_assoc($query)) {
        array_push($arrkab, $row);
    }
    echo json_encode($arrkab);
    exit;
}
if (isset($_GET['action']) && $_GET['action'] == "getPos") {
    $kode_kec = $_GET['kode_kec'];
//ambil data kabupaten
   $query = mysql_query("SELECT kodepos FROM desa WHERE kecamatanId='$kode_kec' LIMIT 1;");
    //$sql = mysqli_query($conn, $query);
    $arrkab = array();
    while ($row = mysql_fetch_assoc($query)) {
        array_push($arrkab, $row);
    }
    echo json_encode($arrkab);
    exit;
}

?>