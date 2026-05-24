<?php

require('code128.php');
mysql_connect("localhost", "root", "");
mysql_select_db("ppsdb");

$sql = mysql_query("SELECT kode,nama,DATE_FORMAT(tglperolehan,'%d/%m/%Y')AS tglperolehan FROM inv_inventaris WHERE id<20");

$pdf = new PDF_Code128('L', 'mm', array(50, 22));
$pdf->SetAutoPageBreak("", 0);

while ($row = mysql_fetch_array($sql)) {
    $pdf->AddPage();
    $kode = $row['kode'];
    $nama = $row['nama'];
    $tglprint= $row['tglperolehan'];
    $pdf->SetXY(5, 1);
    $pdf->SetFont('Arial', 'B', 8);
    $pdf->Cell(40, 11, "PONDOK PESANTREN SIDOGIRI", 0, 0, "C");
    $pdf->SetXY(5, 1);
    $pdf->SetFont('Arial', '', 8);
    $pdf->Cell(40, 18, $kode." - ".$tglprint, 0, 0, "C");
    $pdf->Code128(5, 12, $kode, 40, 5);
    $pdf->SetXY(5, 0);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(40, 38, $nama, 0, 0, "C");
    $pdf->SetFont('Arial', '', 5);
//    $pdf->SetXY(1, 0);
//    $pdf->Text(2, 21, "Bendahara PPS");
//    $pdf->Text(35, 21, "Tgl: ".$tglprint);
    //$pdf->Cell(5, 35, "Tgl: ".$tglprint, 0, 0, "R");
}
$pdf->Output();
?>