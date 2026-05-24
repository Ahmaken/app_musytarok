<?php
	session_start();
	require_once("../../config/kon.php");
        $q=  mysql_query("UPDATE umum_user set lastlogout= now() where nik=".$_SESSION['username']);
	session_destroy();
	header("location:../login/");
?>

