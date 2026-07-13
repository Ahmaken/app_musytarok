<?php
  
    switch($hariindo){      
        case 0 : {
                    $hariindo='Ahad';
                }break;
        case 1 : {
                    $hariindo='Senin';
                }break;
        case 2 : {
                    $hariindo='Selasa';
                }break;
        case 3 : {
                    $hariindo='Rabu';
                }break;
        case 4 : {
                    $hariindo='Kamis';
                }break;
        case 5 : {
                    $hariindo="Jumat";
                }break;
        case 6 : {
                    $hariindo='Sabtu';
                }break;
        default: {
                    $hariindo='UnKnown';
                }break;
    }
     
switch($blnindo){       
        case 1 : {
                    $blnindo='Januari';
                }break;
        case 2 : {
                    $blnindo='Februari';
                }break;
        case 3 : {
                    $blnindo='Maret';
                }break;
        case 4 : {
                    $blnindo='April';
                }break;
        case 5 : {
                    $blnindo='Mei';
                }break;
        case 6 : {
                    $blnindo="Juni";
                }break;
        case 7 : {
                    $blnindo='Juli';
                }break;
        case 8 : {
                    $blnindo='Agustus';
                }break;
        case 9 : {
                    $blnindo='September';
                }break;
        case 10 : {
                    $blnindo='Oktober';
                }break;     
        case 11 : {
                    $blnindo='November';
                }break;
        case 12 : {
                    $blnindo='Desember';
                }break;
        default: {
                    $blnindo='UnKnown';
                }break;
    }
//$sekarang="Hari ".$hariindo." : Tanggal ".$tgl." ".$blnindo." ".$thn;
//echo $sekarang;
?>