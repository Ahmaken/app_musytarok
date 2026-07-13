<?php

session_start();
if (isset($_SESSION['otoritas'])) {
    $oto = $_SESSION['otoritas'];
    if ($oto <= 4) {
        
    } else if ($oto > 4) {
         echo "<script>
	  window.alert('Maaf Anda tidak boleh mengakses halaman ini');
	  location.href = '../../modul/dashboard/';</script>";
    }
} else {

    header('Location:../login/');
}
?>