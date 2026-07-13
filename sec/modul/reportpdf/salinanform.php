<?php

include'../../session/level3.php';
require '../../fpdf/code128.php';

$type = $_GET['type'];
require '../../config/kon.php';
//echo $type;
switch ($type) {
    case 'bykode':
        bykode();
        break;

    default:
        ditolak();
        break;
}

function bykode() {

#setting judul laporan dan header tabel
    $judul = "FORMULIR PENDAFTARAN SANTRI BARU";
    $judul2 = "Pondok Pesantern Matholi'ul Anwar";

#sertakan library FPDF dan bentuk objek
    //   require ("../../fpdf/fpdf.php");

//    class PDF extends FPDF {
//
//        function Footer() {
//            global $tmpgol;
//            //atur posisi 1.5 cm dari bawah
//            $this->SetY(-15);
//            //buat garis horizontal
//            //$this->Line(10, $this->GetY(), 300, $this->GetY());
//            //Arial italic 9
//            $this->SetFont('Courier', 'I', 9);
//            //nomor halaman
//            $this->Cell(0, 8, 'Pendaftar Santri Baru', 0, 0, 'L');
//            $this->Cell(0, 8, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
//        }
//
////        function SetDash($black = false, $white = false) {
////            if ($black and $white)
////                $s = sprintf('[%.3f %.3f] 0 d', $black * $this->k, $white * $this->k);
////            else
////                $s = '[] 0 d';
////            $this->_out($s);
////        }
//    }

    // $pdf = new FPDF();
    $pdf = new PDF_Code128();
    $pdf->AliasNbPages();
    $pdf->AddPage('P', 'A4');

#tampilkan judul laporan
    // $pdf->SetMargins('0', '0');
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Ln();
    $pdf->Cell(0, 10, '', '0', 1, 'C');
    $pdf->Ln();
    $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

    $pdf->SetMargins('30', '0');
//$pdf->Ln();
#tampilkan data tabelnya
    $pdf->SetFillColor(255, 254, 255);
    $pdf->SetTextColor(0);
    $pdf->SetFont('');
    $fill = false;

//foreach ($data as $baris) {
//    $i = 0;
//    foreach ($baris as $cell) {
//        $pdf->Cell($header[$i]['length'], 5, $cell, 1, '0', $alig, $fill);
//        $i++;
//    }
//    $fill = !$fill;
//    $pdf->Ln();
//}
//require '../../config/kon.php';
//require ("../../fpdf/fpdf.php");
    $nik = $_GET['kodereg'];
    $sql = mysql_query("SELECT a.id, a.nomordaftar, a.idmawar, a.ktp, a.nama,  CONCAT(a.tempatlahir, ', ', DATE_FORMAT(a.tanggallahir, '%d/%m/%Y')) AS ttl, a.jeniskelamin, a.rencanastatusdomisili, a.wnama, a.wstatus, a.namaayah, a.namagadisibu, a.status AS STATUS,
                                                        CONCAT(a.dusun, ' ', a.desa) AS alamat, d.kecamatanNama, c.kabupatenNama, e.provinsiNama, CONCAT(a.wkontakhp1,', ', a.wkontakhp2) AS hp, a.wemail,  
                                                        DATE_FORMAT(a.tanggaldaftar, '%d/%m/%Y') AS tanggaldaftar FROM sekretariat_datasantri a, kabupaten c, kecamatan d, provinsi e WHERE a.status=1 AND a.provinsi=e.provinsiId AND a.kecamatan=d.kecamatanId AND a.kabupaten=c.kabupatenId
                                                       AND a.NomorDaftar='$nik';");
    $no = 1;
//foreach data table
    $row = mysql_fetch_array($sql);
    $kode = $row['idmawar'];
    $noktp = $row['ktp'];
    $nama = $row['nama'];
    $tanggal = $row['ttl'];
    $tanggal = explode('-', $tanggal);
    $tanggal = $tanggal['2'] . '/' . $tanggal['1'] . '/' . $tanggal['0'];

    //cek hape
    if ($row['wkontakhp1' != ""]) {
        $hp2 = ', ' . $row['wkontakhp2'];
    } else {
        $hp2 = '';
    }


    //pesonal identiti
    $pdf->Ln();
    $pdf->SetFont('Courier', 'B', '11');

    $pdf->Cell(50, 6, 'Identitas Pribadi', 0, 0, 'L', true);
    $pdf->SetFont('Courier', '');
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nomor Daftar', 0, 0, 'L', true);
    $pdf->Cell(30, 6, ': ' . $row['nomordaftar'], 0, 0, 'L', true);


    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nomor Induk', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $kode, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'KTP', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $noktp, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $nama, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'TTL', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['ttl'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Jenis Kelamin', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['jeniskelamin'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Rencana Domisili', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['rencanastatusdomisili'] . " Pondok", 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama Wali', 0, 0, 'L', true);
    $pdf->Cell(50, 6, ': ' . $row['wnama'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Hubungan  Wali', 0, 0, 'L', true);
    $pdf->Cell(50, 6, ': ' . $row['wstatus'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama Ayah', 0, 0, 'L', true);
    $pdf->Cell(50, 6, ': ' . $row['namaayah'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama Ayah', 0, 0, 'L', true);
    $pdf->Cell(50, 6, ': ' . $row['namagadisibu'], 0, 0, 'L', true);
    //address identiti
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Courier', 'B', '11');

    $pdf->Cell(50, 6, 'Identitas Alamat', 0, 0, 'L', true);
    $pdf->SetFont('Courier', '');
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Alamat', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['alamat'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Kecamatan', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ':' . strtoupper($row['kecamatanNama']), 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Kabupaten/Kota', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . strtoupper($row['kabupatenNama']), 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Provinsi', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . strtoupper($row['provinsiNama']), 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Handphone', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['hp'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Email', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['wemail'], 0, 0, 'L', true);


    $pdf->Ln();
    $pdf->Cell(100, 6, '', 0, 0, 'L', true);
    $pdf->Cell(0, 6, 'Lamongan, ' . $row['tanggaldaftar'], 0, 0, 'L', true);
    $pdf->Ln();

    $pdf->Cell(100, 6, '', 0, 0, 'L', true);
    $pdf->Cell(0, 6, 'Panitia Penerimaan', 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(100, 6, '', 0, 0, 'L', true);
    $pdf->Cell(0, 6, 'Santri/Murid Baru', 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $operator = $_SESSION['nama'];
    $pdf->SetFont('Courier', 'B', '11');
    $pdf->Cell(100, 6, '', 0, 0, 'L', true);
    $pdf->Cell(0, 6, $operator, 0, 0, 'L', true);
    $pdf->ln();
    $pdf->Code128(30, 6, "1234", 0, 0, 'L', true);
//
#output file PDF
    $pdf->Output('FormPendaftaran ' . $nama, 'I');
    exit();
}

function ditolak() {
    echo "Access denied...";
}

#tampilkan judul filter
?>