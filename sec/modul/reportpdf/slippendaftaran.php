<?php

include'../../session/level3.php';
include '../../config/security.php';
require '../../config/kon.php';


function terbilang($satuan) {
    $huruf = array("", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas");
    if ($satuan < 12)
        return " " . $huruf[$satuan];
    elseif ($satuan < 20)
        return Terbilang($satuan - 10) . " Belas";
    elseif ($satuan < 100)
        return Terbilang($satuan / 10) . " Puluh" . Terbilang($satuan % 10);
    elseif ($satuan < 200)
        return " Seratus" . Terbilang($satuan - 100);
    elseif ($satuan < 1000)
        return Terbilang($satuan / 100) . " Ratus" . Terbilang($satuan % 100);
    elseif ($satuan < 2000)
        return " Seribu" . Terbilang($satuan - 1000);
    elseif ($satuan < 1000000)
        return Terbilang($satuan / 1000) . " Ribu" . Terbilang($satuan % 1000);
    elseif ($satuan < 1000000000)
        return Terbilang($satuan / 1000000) . " Juta" . Terbilang($satuan % 1000000);
    elseif ($satuan <= 1000000000)
        echo "Maaf Tidak Dapat di Prose Karena Jumlah Uang Terlalu Besar ";
}

$kodex = md5('kode');
if (isset($_GET[$kodex])) {
    $kodetrans = decryptIt($_GET[$kodex]);
//echo $getNoPengajuan;
#setting judul laporan dan header tabel
    $judul = "BUKTI PEMBAYARAN PENDAFTARAN";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";

    $header = array(
        array("label" => "NO", "h" => 8, "length" => 10, "align" => "C"),
        array("label" => "KODE", "h" => 8, "length" => 25, "align" => "C"),
        array("label" => "NO ANGGARAN", "h" => 8, "length" => 35, "align" => "C"),
        array("label" => "URAIAN", "h" => 8, "length" => 78, "align" => "C"),
        array("label" => "NOMINAL", "h" => 8, "length" => 35, "align" => "C"),
    );



#sertakan library FPDF dan bentuk objek
    require ("../../fpdf/fpdf.php");

    class PDF extends FPDF {
        
    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->SetTopMargin(10);
    $pdf->AddPage('L', array(210, 120));
    $pdf->SetMargins('13', '20');

#tampilkan judul laporan
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Cell(0, 5, $judul2, '0', 1, 'C');
//query get data
    $sql = mysql_query("SELECT DATE_FORMAT(tanggal, '%d/%m/%Y') AS tanggal, kode, namawali, alamat, administrasi, pangkal, gedung FROM reg_pembayaran where kode='$kodetrans'");
    $getKwitansi = mysql_fetch_array($sql);
    $instansiKw = $getKwitansi['instansi'];
    $pdf->Ln();
    $pdf->SetFont('Courier', '', '12');
    $pdf->Cell(17, 7, "", 0, 0, 'L');
    $pdf->Cell(30, 7, "Telah diterima uang dari", 0, 0, 'L');
   #tampilkan judul filter
    $pdf->Ln();
    $pdf->SetFont('Courier', '', '12');
    $pdf->SetLeftMargin(10);
    $pdf->Cell(17, 7, "", 0, 0, 'L');
    $pdf->Cell(35, 7, "Nama Wali", 0, 0, 'L');
    $pdf->Cell(100, 7, ": " . $getKwitansi['namawali'], 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 7, "", 0, 0, 'L');
    $pdf->Cell(35, 7, "Alamat", 0, 0, 'L');
    $pdf->Cell(100, 7, ": " . $getKwitansi['alamat'], 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 7, "", 0, 0, 'L');
    $pdf->Cell(35, 7, "Nominal", 0, 0, 'L');
    $pdf->Cell(100, 7, ": ". number_format($getKwitansi['administrasi']+$getKwitansi['pangkal']+$getKwitansi['gedung'],0,'.','.'), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 7, "", 0, 0, 'L');
    $pdf->Cell(0, 7, "Untuk biaya pendaftaran Santri/Siswa baru No Reg " .$kodetrans, 0, 0, 'L');
    

   //tanggal
    $pdf->Ln();
    $pdf->SetFont('Courier', '', '12');
    $pdf->Cell(130, 7, "", 0, 0, 'R');
    $pdf->Cell(65, 7,  date('d/m/Y h:i:s'), 0, 0, 'L');
//tanda tangan
    $pdf->Ln();
    $pdf->Cell(25, 5, "", 0, 0, '');
    $pdf->Cell(45, 5, "Penerima", 0, 0, 'C');
    $pdf->Cell(50, 5, "", 0, 0, '');
    $pdf->Cell(50, 5, "Yang Menyerahkan", 0, 0, 'C');
// identitas instansi
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    //$pdf->Ln();
    //$pdf->Ln();
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Cell(25, 7, "", 0, 0, '');
    $pdf->Cell(50, 7, strtoupper($_SESSION['nama']), 0, 0, 'C');
    $pdf->Cell(50, 7, "", 0, 0, '');
    $pdf->Cell(50, 7, strtoupper($getKwitansi['namawali']), 0, 0, 'C');
    
    $pdf->PageBreakTrigger = 5; 
//outpu set
    $pdf->Output('Kwitansi ' . $getNoPengajuan, 'I');
    exit();
} else {
    echo "Access denied...! :)";
}
?>