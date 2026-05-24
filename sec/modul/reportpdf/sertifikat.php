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

if (isset($_POST['kode'])) {
    $kodeanggota = $_POST['kode'];
//echo $getNoPengajuan;
#setting judul laporan dan header tabel
    $judul = "SERTIFIKAT SIMPANAN";
    $judul2 = "Kopontren Sidogiri";

#sertakan library FPDF dan bentuk objek
    require ("../../fpdf/fpdf.php");

    class PDF extends FPDF {
        
    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->SetTopMargin(10);
    $pdf->AddPage('L', 'A5');
    $pdf->SetMargins('10', '20');
    
    #tampilkan judul laporan
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->Cell(0, 5, $judul, '0', 1, 'C');
    
    //query get data
    $sql = mysql_query("SELECT a.kode, a.nama, a.ttl, CONCAT(a.alamat,' ', a.kecamatanNama) as alamat, a.kabupatenNama, b.saldo FROM anggota_alamatdetail a, anggota_saldo b WHERE a.kode=b.kode_anggota and a.kode='$kodeanggota'");
    $getDataAnggota = mysql_fetch_array($sql);

    //getsimpanan
    $qsimpanan = mysql_query("select * from simpanan_ketentuan where status=1");
    $getSimpanan = mysql_fetch_array($qsimpanan);
    $khusus = $getDataAnggota['saldo'] - $getSimpanan['pokok'] - $getSimpanan['wajib'];
    //getpengurus
    $qpengurus = mysql_query("select * from pengurus where status=1");
    $getpengurus = mysql_fetch_array($qpengurus);
    
    $pdf->SetFont('Courier', '', '11');
    $pdf->Ln();
    $pdf->MultiCell(0, 7,"Pengurus Pondok Pesantern Matholi'ul Anwar menyatakan:", '0', 'L');
#tampilkan judul filter

    
    $pdf->SetLeftMargin(10);
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(40, 5, "Kode Anggota", 0, 0, 'L');
    $pdf->Cell(100, 5, ": " . $getDataAnggota['kode'], 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(40, 5, "Nama", 0, 0, 'L');
    $pdf->Cell(100, 5, ": " . $getDataAnggota['nama'], 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(40, 5, "Tmpt Tgl Lahir", 0, 0, 'L');
    $pdf->Cell(100, 5, ": " . $getDataAnggota['ttl'], 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(40, 5, "Alamat", 0, 0, 'L');
    $pdf->Cell(100, 5, ": " . strtoupper($getDataAnggota['alamat']), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(40, 5, "Kabupaten/Kota", 0, 0, 'L');
    $pdf->Cell(100, 5, ": " . strtoupper($getDataAnggota['kabupatenNama']), 0, 0, 'L');
    $pdf->Ln();
    $pdf->Ln();
    $pdf->MultiCell(0, 5, "adalah Anggota Pondok Pesantern Matholi'ul Anwar dengan memiliki Simpanan Anggota tahun ".$getSimpanan['tahun'].", dengan rincian sebagai berikut:", '0', 'L');
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(50, 5, "1. Simpanan Pokok", 0, 0, 'L');
    $pdf->Cell(5, 5, ": ", 0, 0, 'L');
    $pdf->Cell(30, 5, number_format($getSimpanan['pokok'],0,'.','.'), 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(50, 5, "2. Simpanan Wajib", 0, 0, 'L');
    $pdf->Cell(5, 5, ": ", 0, 0, 'L');
    $pdf->Cell(30, 5, number_format($getSimpanan['wajib'],0,'.','.'), 0, 0, 'R');
    $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(50, 5, "3. Simpanan Khusus", 0, 0, 'L');
    $pdf->Cell(5, 5, ": ", 0, 0, 'L');
    $pdf->Cell(30, 5, number_format($khusus,0,'.','.'), 0, 0, 'R');
    $pdf->SetFont('Courier', 'B', '11');
     $pdf->Ln();
    $pdf->Cell(20, 5, "", 0, 0, 'L');
    $pdf->Cell(50, 5, "JUMLAH", 0, 0, 'L');
    $pdf->Cell(5, 5, ": ", 0, 0, 'L');
    $pdf->Cell(30, 7, number_format($getDataAnggota['saldo'],0,'.','.'), 0, 0, 'R');
//tanggal
    $pdf->SetFont('Courier', '', '11');
    $pdf->Ln();
    $pdf->Cell(120, 5, "", 0, 0, 'R');
    $pdf->Cell(65, 5, "Sidogiri, " . date('d/m/Y'), 0, 0, 'L');
    $pdf->Ln();
    //$pdf->Cell(120, 7, "", 0, 0, 'R');
    $pdf->Cell(170, 5, "Pengurus Pondok Pesantern Matholi'ul Anwar", 0, 0, 'C');
//tanda tangan
    $pdf->Ln();
    $pdf->Cell(15, 5, "", 0, 0, '');
    $pdf->Cell(45, 5, "Ketua", 0, 0, 'C');
    $pdf->Cell(50, 5, "", 0, 0, '');
    $pdf->Cell(50, 5, "Sekretaris", 0, 0, 'C');
// identitas instansi
    $pdf->Ln();
    $pdf->Ln();
    $pdf->Ln();
    
    $pdf->SetFont('Courier', 'B', '11');
    $pdf->Cell(15, 5, "", 0, 0, '');
    $pdf->Cell(50, 5, strtoupper($getpengurus['ketua']), 0, 0, 'C');
    $pdf->Cell(50, 5, "", 0, 0, '');
    $pdf->Cell(50, 5, strtoupper($getpengurus['bendahara']), 0, 0, 'C');
    $pdf->ln();
    $pdf->SetFont('Courier', 'B', '12');
    $pdf->SetLeftMargin(10);
    $pdf->Cell(120, 7, "", 0, 0, 'C');
    $tglberlaku = $getSimpanan['tahun']+1;
    $pdf->Cell(60, 7, "Berlaku sd. 31-12-".$tglberlaku, 1, 0, 'C');
   $pdf->SetAutoPageBreak();
    //outpu set
    $pdf->Output('Sertifikat ' . $getDataAnggota['kode'], 'I');
    exit();
} else {
    echo "Access denied...! :)";
}
?>