<?php
$modul = isset($_GET['modul']) ? $_GET['modul'] : "";
switch ($modul  ) {
    case "mutasianggota":
        include './mutasianggota.php';
        break;
    case "penarikan":
        include './penarikan.php';;
        break;
}
?>