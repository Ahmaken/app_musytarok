<?php
include'../../session/level3.php';
if (isset($_POST['instansi2'])){
    include '../../config/kon.php';
    $kode=$_POST['kode'];
    $instansi=$_POST['instansiasal'];
    $instansi2=$_POST['instansi2'];
    $user= $_SESSION['username'];
    $tanggal= date('Y-m-d');
    //$instansi=$_POST['instansi2'];
    $up= mysql_query("UPDATE inv_inventaris set instansi='$instansi2' where kode='$kode'");
    //$add= mysql_query ("insert into inv_mutasi values ('','$kode', '$instansi','$instansi2', '$tanggal', '1')");
    
    if ($up){
         $add= mysql_query ("insert into inv_mutasi values ('','$kode', '$instansi','$instansi2', '$tanggal', '1')");
        header('Location: index.php?status=sukses');
    }else{
         $error = mysql_error();
        header('Location: index.php?status=gagal&ket=' . $error);
    }
    
}
?>