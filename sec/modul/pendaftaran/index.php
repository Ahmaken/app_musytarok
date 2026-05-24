<?php
$modul = isset($_GET['modul']) ? $_GET['modul'] : "";
switch ($modul  ) {
    case "penambahan":
        include './penambahan.php';
        break;
    case "penarikan":
        include './penarikan.php';;
        break;
    case "editpenambahan":
        include './editpenambahan.php';;
        break;
    case "editpenarikan":
        include './editpenarikan.php';;
        break;
    case "input":
        include './input.php';;
        break;
}
?>