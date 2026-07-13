<?php
mysql_connect("localhost","root","");
mysql_select_db("ppsdb");
 
$sql=mysql_query("select * from inv_inventaris where id<20");
$data=mysql_fetch_array($sql);
 
$nama="$data[kode]";
$gambar=$data['foto'];
 
//define('FPDF_FONTPATH', 'fpdf/font/');
require '../../fpdf/code128.php';

 
$tgl = date('D,d-F-Y');
 
$pdf = new PDF_Code128();
 
$pdf->Open();
$pdf->addPage(L);
 
$pdf->setFont('Arial','',20);
 
//$pdf->Image('foto/' . $gambar,10,10,80);
 $code="12345";
$pdf->Code128(100, 30, $code);
 
//$pdf->Code39(100, 30, 'Test');
 
$pdf->Output();