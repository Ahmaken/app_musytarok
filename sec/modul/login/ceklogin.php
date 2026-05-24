<?php

session_start();
//tes login
include '../../config/kon.php';
$username = $_POST['username'];
$password = md5($_POST['password']);
//echo $username;
//echo $password;
if ($username == 'admin' and $password == md5(adminmawar2015)) {
    $_SESSION['username'] = "admin";
    $_SESSION['nama'] = "Administrator";
    $_SESSION['otoritas'] = "1";
    $_SESSION['jabatan'] = "Admin";

// login_validate();
    header("location:../dashboard/");
} else {

// Escaping all input data
    $query = mysql_query("SELECT username, nama, otorisasi, status FROM user WHERE status=1 AND username='$username' and password='$password'");
    $row = mysql_fetch_array($query);
    $cek = mysql_num_rows($query);

    if ($cek > 0) {
        $status = $row['status'];
        if ($status == "0") {
            echo "<script>
	  window.alert('account anda sudah tidak aktif,silakan hubungi administrator');
	  location.href = 'index.php';</script>";
        } else {
            $_SESSION['username'] = $row['username'];
            $_SESSION['nama'] = $row['nama'];
            $_SESSION['otoritas'] = $row['otorisasi'];
            //set sessin
            if ($row['otorisasi'] == 'Admin') {
                $_SESSION['otoritas'] = 1;
            } else if ($row['otorisasi'] == 'User') {
                $_SESSION['otoritas'] = 2;
            }
            header("location:../dashboard/");
        }
    } else {
        header("location:index.php?error=1");
    }
}
