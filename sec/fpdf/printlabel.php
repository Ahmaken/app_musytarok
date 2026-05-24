<?php
//define('FPDF_FONTPATH','../../fpdf/font/');
require('code128.php');
require('../config/kon.php');
//error_reporting(0);

if(isset($_GET['type'])){
$type = $_GET['type'];
}
switch ($type) {
    case "pertanggal":
        pertanggal();
        break;
    case "perkode":
        perkode();
        break;
    case "pergolongan":
        pergolongan();
        break;
    case "instansi":
        perinstansi();
        break;
}

function pertanggal() {
    $tanggal1 = explode("/", $_POST['tanggal']);
    $tanggal2 = explode("/", $_POST['tanggal2']);
    $tanggalsql1 = $tanggal1[2] . "-" . $tanggal1[1] . "-" . $tanggal1[0];
    $tanggalsql2 = $tanggal2[2] . "-" . $tanggal2[1] . "-" . $tanggal2[0];

    $sql = mysql_query("SELECT kode, instansi, upper(nama) as nama,DATE_FORMAT(tglperolehan,'%d/%m/%Y')AS tglperolehan FROM inv_inventaris WHERE status=1 and tglperolehan between '$tanggalsql1' and '$tanggalsql2'");
    $pdf = new PDF_Code128('L', 'mm', array(50, 22));
    $pdf->SetAutoPageBreak("", 0);

    while ($row = mysql_fetch_array($sql)) {
        $pdf->AddPage();
        $kode = $row['kode'];
        $nama = $row['nama'];
        $instansi = $row['instansi'];
        $tglprint = $row['tglperolehan'];
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(40, 11, "PONDOK PESANTREN SIDOGIRI", 0, 0, "C");
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(40, 18, $kode . "-" . $instansi."-".$tglprint, 0, 0, "C");
        $pdf->Code128(5, 12, $kode, 40, 5);
        $pdf->SetXY(5, 0);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 38, $nama, 0, 0, "C");
        $pdf->SetFont('Arial', '', 5);
    }
    $pdf->Output();
}

function perkode() {
    $kode = $_POST['kode'];
    $sql = mysql_query("SELECT kode, instansi, upper(nama) as nama,DATE_FORMAT(tglperolehan,'%d/%m/%Y')AS tglperolehan FROM inv_inventaris WHERE status=1 and kode='$kode'");
    $pdf = new PDF_Code128('L', 'mm', array(50, 22));
    $pdf->SetAutoPageBreak("", 0);

    while ($row = mysql_fetch_array($sql)) {
        $pdf->AddPage();
        $kode = $row['kode'];
        $nama = $row['nama'];
        $instansi = $row['instansi'];
        $tglprint = $row['tglperolehan'];
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(40, 11, "PONDOK PESANTREN SIDOGIRI", 0, 0, "C");
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(40, 18, $kode . "-" . $instansi."-".$tglprint, 0, 0, "C");
        $pdf->Code128(5, 12, $kode, 40, 5);
        $pdf->SetXY(5, 0);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 38, $nama, 0, 0, "C");
        $pdf->SetFont('Arial', '', 5);
    }
    $pdf->Output();
}

function pergolongan() {
    $golongan = $_POST['golongan'];
    $sql = mysql_query("SELECT kode, instansi,upper(nama) as nama,DATE_FORMAT(tglperolehan,'%d/%m/%Y')AS tglperolehan FROM inv_inventaris WHERE status=1 and golongan='$golongan'");
    $pdf = new PDF_Code128('L', 'mm', array(50, 22));
    $pdf->SetAutoPageBreak("", 0);

    while ($row = mysql_fetch_array($sql)) {
        $pdf->AddPage();
        $kode = $row['kode'];
        $nama = $row['nama'];
        $instansi = $row['instansi'];
        $tglprint = $row['tglperolehan'];
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(40, 11, "PONDOK PESANTREN SIDOGIRI", 0, 0, "C");
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(40, 18, $kode . "-" . $instansi."-".$tglprint, 0, 0, "C");
        $pdf->Code128(5, 12, $kode, 40, 5);
        $pdf->SetXY(5, 0);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 38, $nama, 0, 0, "C");
        $pdf->SetFont('Arial', '', 5);
    }
    $pdf->Output();
}
function perinstansi() {
    $instansi = $_POST['instansi'];
    $sql = mysql_query("SELECT kode, instansi,upper(nama) as nama,DATE_FORMAT(tglperolehan,'%d/%m/%Y')AS tglperolehan FROM inv_inventaris WHERE status=1 and instansi='$instansi'");
    $pdf = new PDF_Code128('L', 'mm', array(50, 22));
    $pdf->SetAutoPageBreak("", 0);

    while ($row = mysql_fetch_array($sql)) {
        $pdf->AddPage();
        $kode = $row['kode'];
        $nama = $row['nama'];
        $instansi = $row['instansi'];
        $tglprint = $row['tglperolehan'];
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(40, 11, "PONDOK PESANTREN SIDOGIRI", 0, 0, "C");
        $pdf->SetXY(5, 1);
        $pdf->SetFont('Arial', '', 8);
        $pdf->Cell(40, 18, $kode . "-" . $instansi."-".$tglprint, 0, 0, "C");
        $pdf->Code128(5, 12, $kode, 40, 5);
        $pdf->SetXY(5, 0);
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 38, $nama, 0, 0, "C");
        $pdf->SetFont('Arial', '', 5);
    }
    $pdf->Output();
}

?>