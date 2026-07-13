<?php

include'../../session/level4.php';

$type = $_GET['type'];
require '../../config/kon.php';
//echo $type;
switch ($type) {
    case 'percabang':
        percabang();
        break;

    default:
        ditolak();
        break;
}

function percabang() {

#setting judul laporan dan header tabel
    $judul = "Data Karyawan";
    $judul2 = "PT Pondok Pesantren Matholi'ul Anwar";

#sertakan library FPDF dan bentuk objek
    require ("../../fpdf/fpdf.php");

    class PDF extends FPDF {

        function Footer() {
            global $tmpgol;
            //atur posisi 1.5 cm dari bawah
            $this->SetY(-15);
            //buat garis horizontal
            //$this->Line(10, $this->GetY(), 300, $this->GetY());
            //Arial italic 9
            $this->SetFont('Courier', 'I', 9);
            //nomor halaman
            $this->Cell(0, 8, 'Data Karyawan', 0, 0, 'L');
            $this->Cell(0, 8, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
        }

//        function SetDash($black = false, $white = false) {
//            if ($black and $white)
//                $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
//            else
//                $s = '[] 0 d';
//            $this->_out($s);
//        }
    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('L', 'A4');

#tampilkan judul laporan
    // $pdf->SetMargins('0', '0');
    $pdf->SetFont('Arial', 'B', '12');
    $pdf->Ln();
    $pdf->Cell(0, 3, '', '0', 1, 'C');

    $pdf->Cell(0, 6, $judul, '0', 1, 'C');
#tampilkan judul2
    $pdf->SetFont('Arial', 'B', '12');
    $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

//    $pdf->SetFont('Arial', '', '11');
//    $pdf->Cell(5, 5, $stringist, '0', 1, 'L');
$pdf->Ln();
    $pdf->SetFont('Arial', 'B', '8');

    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Cell(10, 5, "NO", 1, 0, 'L', true);
    $pdf->Cell(30, 5, "NIK", 1, 0, 'L', true);
    $pdf->Cell(50, 5, "NAMA", 1, 0, 'L', true);
    $pdf->Cell(70, 5, "ALAMAT", 1, 0, 'L', true);
    $pdf->Cell(50, 5, "TELEPON", 1, 0, 'L', true);
    $pdf->Cell(40, 5, "EMAIL", 1, 0, 'L', true);
    $pdf->Cell(25, 5, "CABANG", 1, 0, 'L', true);
    $pdf->Ln();

#tampilkan data tabelnya
    $pdf->SetFillColor(255, 254, 255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('');
    $fill = false;
    
    $getcabang = $_POST['cabang'];
    $getkoor = $_POST['koordinator'];
    $wherecab = "";
    $wherekoor = "";
    if ($getcabang != "all" and $getcabang != "") {
        $wherecab = " AND b.cabang = '$getcabang'";
    }
    if ($getkoor != "all" and $getkoor != "") {
        $wherekoor = " AND d.kodekoordinator = '$getkoor'";
    }
    $no = 1;
    $qgetdata = mysql_query("SELECT a.nik, a.noktp, UPPER(a.nama) AS nama, CONCAT(a.tempatlahir, ', ', "
            . "DATE_FORMAT(a.tgllahir, '%d/%m/%Y')) AS ttl, CONCAT(a.alamat, ' ', a.desa, ' ', a.kecamatan, "
            . "' ', a.kota) AS alamat, CONCAT(a.hp1, ', ', a.hp2) AS hp,a.email,  a.ibukandung, b.cabang, "
            . "d.jabatan FROM karyawan_master a JOIN max_penempatan b ON a.nik=b.nik JOIN umum_cabang c ON "
            . "b.cabang=c.kode LEFT JOIN max_mutasijabatan d ON a.nik=d.nik WHERE "
            . "a.status=1" . $wherecab . "" . $wherekoor . " ORDER BY a.id ASC");
    $no = 1;
//foreach data table
    while ($row = mysql_fetch_array($qgetdata)) {
        $pdf->Cell(10, 5, $no++, 1, 0, 'L', true);
        $pdf->Cell(20, 5, $row['nik'], 1, 0, 'L', true);
        $pdf->Cell(50, 5, $row['nama'], 1, 0, 'L', true);
        $pdf->Cell(70, 5, $row['alamat'], 1, 0, 'L', true);
        $pdf->Cell(50, 5, $row['hp'], 1, 0, 'L', true);
        $pdf->Cell(50, 5, $row['email'], 1, 0, 'L', true);
        $pdf->Cell(25, 5, $row['cabang'], 1, 0, 'L', true);
        $pdf->Ln();
    }



//
#output file PDF
    $pdf->Output('datakaryawan', 'I');
    exit();
}

function ditolak() {
    echo "Access denied...";
}

#tampilkan judul filter
?>