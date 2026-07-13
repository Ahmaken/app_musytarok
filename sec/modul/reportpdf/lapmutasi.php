<?php

include'../../session/level4.php';
require '../../config/kon.php';
if (!empty($_POST['tanggal'])) {

    $pecah = explode('/', $_POST['tanggal']);
    $hariindo = $pecah[0];
    $tglangka = $pecah[0];
    $blnindo = $pecah [1];
    $thn = $pecah[2];
    $tanggalutuh = $tglangka . "/" . $blnindo . "/" . $thn;
    $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
    $tanggal2 = explode('/', $_POST['tanggal2']);
    $tanggal2utuh = $_POST['tanggal2'];
    $tgl2sql = $tanggal2[2]."-".$tanggal2[1]."-".$tanggal2[0];
//PENGECEKAN TANGGAL PERIODE

    require '../../config/tanggal.php';
    require_once ("../../fpdf/fpdf.php");
#setting judul laporan dan header tabel
    $judul = "Laporan Mutasi Simpanan";
    $judul2 = "Pondok Pesantern Matholi'ul Anwar";

    class PDF extends FPDF {

        function Footer() {
            
            global $tanggalutuh;
            global $tanggal2utuh;
            //global $printinstansi;
            //atur posisi 1.5 cm dari bawah
            $this->SetY(-15);
            //buat garis horizontal
            //$this->Line(10, $this->GetY(), 300, $this->GetY());
            //Arial italic 9
            $this->SetFont('Arial', 'I', 9);
            //nomor halaman
            $this->Cell(0, 10, "Halaman " . $this->PageNo() . " dari {nb} | Peneriman Pembayaran Tgl $tanggalutuh s.d $tanggal2utuh", 0, 0, 'L');
            $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
        }

    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('P', 'A4');

#tampilkan judul laporan
    $pdf->SetFont('Arial', 'B', '12');
    $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
    $pdf->SetFont('Arial', 'B', '12');
    $pdf->Cell(0, 5, $judul2, '0', 1, 'C');
   
    if(!empty($_POST['$tanggal2'])){
        $datenow = "Tanggal: ".$tanggalutuh;
    }else{
        $datenow = "Tanggal: ".$tanggalutuh." s.d ".$tanggal2utuh;
    }
    $pdf->Ln();
    $pdf->SetFont('Arial', '', '10');
    $pdf->Cell(153, 5, $datenow, 0, 0, 'L');
    $pdf->Ln();
    $pdf->SetFont('Arial', 'B', '9');
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->Cell(10, 5, "NO", 1, 0, 'C', true);
    //$pdf->Cell(35, 5, "NO TRANSAKSI", 1, 0, 'L', true);
    $pdf->Cell(28, 5, "TANGGAL", 1, 0, 'C', true);
    $pdf->Cell(9, 5, "SND", 1, 0, 'C', true);
    $pdf->Cell(27, 5, "NO TRANSAKSI", 1, 0, 'C', true);
    $pdf->Cell(23, 5, "KODE", 1, 0, 'C', true);
    $pdf->Cell(45, 5, "NAMA", 1, 0, 'C', true);
    $pdf->Cell(25, 5, "DEBET", 1, 0, 'C', true);
    $pdf->Cell(25, 5, "KREDIT", 1, 0, 'C', true);

     if (isset($_POST['filter'])) {
         $operator = $_SESSION['username'];
        $whereuser = " AND a.operator ='$operator'";
    }
    // query untuk table
    $sql = mysql_query("SELECT CONCAT(DATE_FORMAT(a.tanggal, '%d-%m-%Y'),' ' ,DATE_FORMAT(a.jam, '%H:%i')) AS tanggal, a.jenis, a.kode, a.kode_anggota, b.nama, a.debet, a.kredit  FROM anggota_simpanan a, anggota_master b WHERE a.kode_anggota=b.kode ".$whereuser." and a.tanggal BETWEEN '$tglsql' and '$tgl2sql' ORDER BY a.tanggal, a.jam ASC");
    $no = 1;
    while ($hasil = mysql_fetch_array($sql)) {
//$fill = !$fill;
        //fill isi
        $pdf->SetFillColor(255, 254, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $fill = false;
        $pdf->Ln();
        $pdf->Cell(10, 5, $no++, 1, 0, 'L', true);
        //   $pdf->Cell(35, 5, $hasil["kode"], 1, 0, 'L', true);
        $pdf->Cell(28, 5, $hasil["tanggal"], 1, 0, 'C', true);
        $pdf->Cell(9, 5, $hasil["jenis"], 1, 0, 'L', true);
        $pdf->Cell(27, 5, $hasil["kode"], 1, 0, 'L', true);
        $pdf->Cell(23, 5, $hasil["kode_anggota"], 1, 0, 'L', true);
        $pdf->Cell(45, 5, $hasil["nama"], 1, 0, 'L', true);
        $pdf->Cell(25, 5, number_format($hasil["debet"], 0, '.', '.'), 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format($hasil["kredit"], 0, '.', '.'), 1, 0, 'R', true);
        //$pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'L', true);
        $totaldebet[] = $hasil['debet'];
        $totalkredit[] = $hasil['kredit'];

        //echo $hasil['kode'];
    }

//cek agar array tidak error
    if (empty($totdebet)&&empty($totalkredit)) {
//                $pdf->Ln();
//                $pdf->Cell(190, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
    } else {
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', '9');
        $pdf->Cell(142, 5, 'Jumlah', 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format(array_sum($totaldebet), 0, '.', '.'), 1, 0, 'R', true);
        $pdf->Cell(25, 5, number_format(array_sum($totalkredit), 0, '.', '.'), 1, 0, 'R', true);
        $pdf->Ln();
        $pdf->Cell(142, 5, 'SALDO', 1, 0, 'R', true);
        $pdf->Cell(50, 5, number_format(array_sum($totaldebet)-array_sum($totalkredit), 0, '.', '.'), 1, 0, 'R', true);
       
        $pdf->Ln();
    }

    $pdf->Output();
    exit();


// jika type golongan
} else {
    echo "Access denied... :)";
}
?>