<?php
if (isset($_SESSION['otoritas'])) {
    $oto = $_SESSION['otoritas'];
    if ($oto == 1) {
        include 'menusuperadmin.php';
    } else if ($oto == 2) {
        include 'menuadmin.php';
    } else if ($oto == 3) {
        include '../../template/menusupervisor.php';
    } else if ($oto == 4) {
        include 'menuuser.php';
    }
}
?>