<?php

include'../../session/level3.php';

$type = $_GET['type'];
require '../../config/kon.php';
//echo $type;
switch ($type) {
    case 'bynik':
        bynik();
        break;

    default:
        ditolak();
        break;
}

function bynik() {

#setting judul laporan dan header tabel
    $judul = "CV KARYAWAN";
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
            $this->Cell(0, 8, 'CV Karyawan', 0, 0, 'L');
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
    $nik = $_POST['nik'];
    $sql = mysql_query("select * from karyawan_master where status=1 and nik='$nik'");
    $no = 1;
//foreach data table
    $row = mysql_fetch_array($sql);
    $nik = $row['nik'];
    $noktp = $row['noktp'];
    $nama = $row['nama'];
    $tanggal = $row['tgllahir'];
    $tanggal = explode('-', $tanggal);
    $tanggal = $tanggal['2'] . '/' . $tanggal['1'] . '/' . $tanggal['0'];
    if ($row[statusalumni == 1]) {
        $alumni = 'Alumni PPS';
    } else {
        $alumni = 'Non PPS';
    }
    //cek hape
    if ($row['hp2' != ""]) {
        $hp2 = ', ' . $row['hp2'];
    } else {
        $hp2 = '';
    }


    //pesonal identiti
    $pdf->Ln();
    $pdf->SetFont('Courier', 'B');

    $pdf->Cell(50, 6, 'Personal Identity', 0, 0, 'L', true);
    $pdf->SetFont('Courier', '');
    $pdf->Ln();
    $pdf->Cell(50, 6, 'NIK', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $nik, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'KTP', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $noktp, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $nama, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'TTL', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['tempatlahir'] . ", " . $tanggal, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Tinggi Badan', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['tinggibadan'] . "cm. Berat Badan: " . $row['beratbadan'] . " kg", 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Status Pernikahan', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['statusnikah'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Ibu Kandung', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['ibukandung'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Jumlah Anak', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['jumlahanak'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Status Alumni', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $alumni, 0, 0, 'L', true);

    //address identiti
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Courier', 'B');

    $pdf->Cell(50, 6, 'Personal Addreses', 0, 0, 'L', true);
    $pdf->SetFont('Courier', '');
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Alamat', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['alamat'] . ' ' . $row['desa'] . ' ' . $row['kecamatan'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, '', 0, 0, 'L', true);
    $pdf->Cell(0, 6, '  ' . $row['kota'] . ' ' . $row['provinsi'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Jarak ke Kantor', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['jarakkantor'] . ' km', 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Handphone', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['hp1'] . '' . $hp2, 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Email', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['email'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Rekening Bank', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['bank'] . " No Rek " . $row['rekening'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama Pemilik Rek', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['namarekening'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Nama Kerabat', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['namakerabat'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Status Kerabat', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['statuskerabat'], 0, 0, 'L', true);
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Handphone Kerabat', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': ' . $row['hpkerabat'], 0, 0, 'L', true);

    //address identiti
    $pdf->Ln();
    $pdf->Ln();
    $pdf->SetFont('Courier', 'B');

    $pdf->Cell(50, 6, 'Personal Academics & Experiences', 0, 0, 'L', true);
    $pdf->SetFont('Courier', '');
    $pdf->Ln();
    $pdf->Cell(50, 6, 'Riwayat Pesantren', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': 1.' . $row['pesantren1'], 0, 0, 'L', true);
    //cek jika kosong
    if ($row['pesantren2'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  2.' . $row['pesantren2'], 0, 0, 'L', true);
    }
    if ($row['pesantren3'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  3.' . $row['pesantren3'], 0, 0, 'L', true);
    }
     $pdf->Ln();
    $pdf->Cell(50, 6, 'Riwayat Pend Umum', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': 1.' . $row['umum1'], 0, 0, 'L', true);
    //cek jika kosong
    if ($row['umum2'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  2.' . $row['umum2'], 0, 0, 'L', true);
    }
    if ($row['umum3'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  3.' . $row['umum3'], 0, 0, 'L', true);
    }
       $pdf->Ln();
    $pdf->Cell(50, 6, 'Peng. Organisasi', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': 1.' . $row['organisasi1'], 0, 0, 'L', true);
    //cek jika kosong
    if ($row['organisasi2'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  2.' . $row['organisasi2'], 0, 0, 'L', true);
    }
    if ($row['organisasi3'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  3.' . $row['organisasi3'], 0, 0, 'L', true);
    }
      $pdf->Ln();
    $pdf->Cell(50, 6, 'Sert/Kompetensi', 0, 0, 'L', true);
    $pdf->Cell(0, 6, ': 1.' . $row['kompetensi1'], 0, 0, 'L', true);
    //cek jika kosong
    if ($row['kompetensi2'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  2.' . $row['kompetensi2'], 0, 0, 'L', true);
    }
    if ($row['kompetensi3'] != "") {
        $pdf->Ln();
        $pdf->Cell(50, 6, '', 0, 0, 'L', true);
        $pdf->Cell(0, 6, '  3.' . $row['kompetensi3'], 0, 0, 'L', true);
    }
    

//
#output file PDF
    $pdf->Output('RekapInventarisBaru' . $nama, 'I');
    exit();
}

function ditolak() {
    echo "Access denied...";
}

#tampilkan judul filter
?>