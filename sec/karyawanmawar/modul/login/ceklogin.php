<?php

session_start();
//tes login

$username = $_POST['username'];
$password = $_POST['password'];
if ($username == 'admin' and $password == 'adminmawar2015') {
    $_SESSION['username'] = "admin";
    $_SESSION['nama'] = "Administrator";
    $_SESSION['otoritas'] = "1";
    $_SESSION['jabatan'] = "Admin";

// login_validate();
    header("location:../dashboard/");
} else {

//define('INCLUDE_CHECK',true);
    require '../../config/kon.php';
//require_once '../config/fpengatur.php';

    $nik = mysql_real_escape_string($_POST['nik']);
    $password = mysql_real_escape_string($_POST['password']);
    $password = md5($password);
//echo $username."<br>".$password;
// Escaping all input data
    $query = mysql_query("SELECT a.nik, b.nama, a.otorisasi, a.status FROM umum_user a, karyawan_master b WHERE a.nik=b.nik AND a.status='1' AND a.password='$password'");
    $row = mysql_fetch_array($query);
    $cek = mysql_num_rows($query);
//echo $cek;
    if ($cek > 0) {
        $status = $row['status'];
        if ($status == "0") {
            echo "<script>
	  window.alert('account anda sudah tidak aktif,silakan hubungi administrator');
	  location.href = 'index.php';</script>";
        } else {
            $_SESSION['username'] = $row['nik'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['otoritas'] = $row['otorisasi'];
            $nik = $row['nik'];
            $q = mysql_query("UPDATE umum_user set lastlogin = now() where nik='$nik'");
//$_SESSION['jabatan'] = $row['jabatan'];
// login_validate();
            header("location:../dashboard/");
        }
    } else {
// header("location:../dashboard/");
        header("location:index.php?error=1");
    }
}
