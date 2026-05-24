<?php
$host="localhost";
$user="ppmawaro";
$password="zl3g39mEN8";
$db="ppmawaro_sec";
$koneksi=mysql_connect("$host","$user","$password") or die('gagal konek'.  mysql_error());
mysql_select_db($db);
?>