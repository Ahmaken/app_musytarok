<?php
include'../../session/level3.php';
if (isset($_POST['tanggal'])) {
//koneksi ke database
    require '../../config/kon.php';
    $pecah = explode('/', $_POST['tanggal']);
    $hariindo = $pecah[0];
    $blnindo = $pecah [1];
    $thn = $pecah[2];
    $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
//$type= isset($_POST['rekap'])? $_POST['rekap'] : "" ;



    require '../../config/tanggal.php';
    global $tglsql;

#setting judul laporan dan header tabel
    $judul = "REKAPITULASI INVENTARIS PERGOLONGAN";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";
    $periode = "Sampai Bulan " . $blnindo . " " . $thn;
    $header = array(
        array("label" => "KODE", "h" => 8, "length" => 14, "align" => "C"),
        array("label" => "GOLONGAN", "h" => 8, "length" => 60, "align" => "C"),
        array("label" => "HARGA", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "PENYUSUTAN", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "AKUMULASI", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "NILAI BUKU", "h" => 8, "length" => 45, "align" => "C")
    );
    require ("../../fpdf/fpdf.php");
 class PDF extends FPDF {

            function Footer() {
                global $instansi;
                global $printinstansi;
                        
                //atur posisi 1.5 cm dari bawah
                $this->SetY(-15);
                //buat garis horizontal
                //$this->Line(10, $this->GetY(), 300, $this->GetY());
                //Arial italic 9
                $this->SetFont('Arial', 'I', 9);
                //nomor halaman
                $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Penyusutan Pergolongan', 0, 0, 'L');
                $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
            }

        }

        $pdf = new FPDF();
        $pdf = new PDF();
        $pdf->AliasNbPages();

#sertakan library FPDF dan bentuk objek
   
    $pdf->AddPage('L', 'A4');
    $pdf->SetMargins('20', '20');

#tampilkan judul laporan
    $pdf->SetFont('Arial', 'B', '16');
    $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
    $pdf->SetFont('Arial', 'B', '14');
    $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

#tampilkan judul laporan
    $pdf->SetFont('Arial', 'B', '12');
    $pdf->Cell(0, 10, $periode, '0', 1, 'C');

#buat header tabel
    $pdf->SetFont('Arial', 'B', '10');
    $pdf->SetFillColor(0, 0, 0);
    $pdf->SetTextColor(255);
    $pdf->SetDrawColor(0, 0, 0);
    foreach ($header as $kolom) {
        $pdf->Cell($kolom['length'], 8, $kolom['label'], 1, '0', $kolom['align'], true);
    }

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

    function filterGolongan($gol) {
        //echo "$gol";
        require '../../config/kon.php';
        //require ("../../fpdf/fpdf.php");
        global $pdf;
        global $tglsql;
        $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
        $filter = " AND a.tglperolehan < '" . $tglsql . "' and a.golongan='$gol'";
        $sql = mysql_query("SELECT a.harga
                   , b.golongan
                   , a.penyusutan
                   , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                   , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                   , harga-@akhir AS nilaibuku
                   FROM inv_inventaris a, inv_golongan b
                   WHERE a.golongan=b.id" . $where . "" . $filter . "ORDER BY a.kode ASC");
        $no = 1;
        //foreach data table
        while ($row = mysql_fetch_array($sql)) {

            $harga[] = $row['harga'];
            $penyusutan[] = $row['penyusutan'];
            $akhir[] = $row['akhir'];
            $nilaibuku[] = $row['nilaibuku'];
            $no++;
        }
        //tampilkan golongan
        $idgol = mysql_query("SELECT UPPER(golongan) AS golongan FROM inv_golongan where id= $gol");
        while ($goltampil = mysql_fetch_array($idgol)) {
            $printgol = $goltampil['golongan'];
            //echo $idgol;
        }
        //menghitung konter


        if (empty($harga)) {
            
        } else {

            $pdf->Ln();
            $pdf->Cell(14, 8, $gol, 1, 0, 'L', true);
            $pdf->Cell(60, 8, $printgol, 1, 0, 'L', true);
            $pdf->Cell(45, 8, number_format(array_sum($harga), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($penyusutan), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($akhir), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($nilaibuku), 2), 1, 0, 'R', true);
        }
    }

//tampilkan data with looping
    for ($index = 0; $index < 10; $index++) {
        filterGolongan($index);
    }

    // select total sub   
    $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
    $filter = " AND a.tglperolehan < '" . $tglsql . "'";
    $sql = mysql_query("SELECT a.harga
                   , b.golongan
                   , a.penyusutan
                   , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                   , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                   , harga-@akhir AS nilaibuku
                   FROM inv_inventaris a, inv_golongan b
                   WHERE a.golongan=b.id" . $where . "" . $filter . "ORDER BY a.kode ASC");
    $no = 1;
    //foreach data table
    while ($row = mysql_fetch_array($sql)) {

        $harga[] = $row['harga'];
        $penyusutan[] = $row['penyusutan'];
        $akhir[] = $row['akhir'];
        $nilaibuku[] = $row['nilaibuku'];
        $no++;
    }

    //cek konter  jika kosong agar tidak muncul errro
    if (empty($harga)) {
        $pdf->Ln();
        $pdf->SetFont('Arial', '', '10');
        $pdf->SetTextColor(194, 8, 8);
        $pdf->Cell(254, 8, "Maaf, data tidak ada... ", 1, 0, 'C', true);
    } else {

        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', '10');
        $pdf->Cell(74, 8, "Subtotal s.d " . $blnindo . " " . $thn, 1, 0, 'C', true);
        $pdf->Cell(45, 8, number_format(array_sum($harga), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($penyusutan), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($akhir), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($nilaibuku), 2), 1, 0, 'R', true);
    }

//filtergol('4');


    $pdf->Output('Rekappergolongan' . $tglsql, 'I');
    exit();
} else {
    echo "Access denied...";
}
?>