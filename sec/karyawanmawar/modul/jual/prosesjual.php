<?php
include '../../session/level2.php';
if (isset($_POST['kode'])) {
    $kode= $_POST['kode'];
    $nilaibuku= str_replace('.', '', $_POST['nilaibuku']);
    $hargajual= str_replace('.', '', $_POST['dijual']);
    $keterangan= str_replace('.', '', $_POST['keterangan']);
    $tgljual= date('Y-m-d');
    $opr = "admin";
    $penyusutan = 1 * (($harga - $sisa) / $umur);
    require '../../config/kon.php';
//query insert
    $q = mysql_query("INSERT INTO inv_dijual (tgljual, kode, nilaibuku, hargajual, keterangan, user) VALUES ('$tgljual','$kode','$nilaibuku','$hargajual','$keterangan','$opr')"); // 1= status aktif
    $qmutasi = mysql_query("UPDATE inv_inventaris SET status=4 WHERE kode= '$kode'");

//query insert
if ($q) {
        header('Location: index.php?status=sukses');
    } else {
        $error = mysql_error();
        echo "<script>
	  window.alert('Maaf Anda tidak boleh mengakses halaman ini. Error: $error');
	  location.href = '../../modul/dashboard/';</script>";
    }
} else {
    Echo 'Access denied...';
}
?>
