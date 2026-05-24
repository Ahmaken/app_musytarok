<?php

function golongan($golongan) {
    include '../../config/kon.php';
    $idgol = mysql_query("SELECT * FROM inv_golongan where id= $golongan");
    while ($goltampil = mysql_fetch_array($idgol)) {
        echo $goltampil['golongan'];
         //echo $idgol;
    }
   
}

?>
