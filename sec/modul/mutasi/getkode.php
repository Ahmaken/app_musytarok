<?php
include'../../session/level3.php';
include '../../config/kon.php';
if (!empty($_GET['q'])) {
    include '../../config/kon.php';
    $q = strtolower($_GET["q"]);
    if (!$q)
        return;

    $sql = mysql_query("select kode from inv_inventaris where status=1 and kode LIKE '$q%'");
    while ($r = mysql_fetch_array($sql)) {
        $kode_barang = $r['kode'];
        echo "$kode_barang \n";
    }
}
?>