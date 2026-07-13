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
    $judul = "REKAPITULASI INVENTARIS PERKOORDINATOR";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";
    $periode = "Sampai Bulan " . $blnindo . " " . $thn;
    $header = array(
        array("label" => "KODE", "h" => 8, "length" => 14, "align" => "C"),
        array("label" => "KOORDINATOR", "h" => 8, "length" => 60, "align" => "C"),
        array("label" => "HARGA", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "PENYUSUTAN", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "AKUMULASI", "h" => 8, "length" => 45, "align" => "C"),
        array("label" => "NILAI BUKU", "h" => 8, "length" => 45, "align" => "C")
    );



#sertakan library FPDF dan bentuk objek
    require ("../../fpdf/fpdf.php");

    class PDF extends FPDF {

        function Footer() {
            //atur posisi 1.5 cm dari bawah
            $this->SetY(-15);
            //buat garis horizontal
            //$this->Line(10, $this->GetY(), 300, $this->GetY());
            //Arial italic 9
            $this->SetFont('Arial', 'I', 9);
            //nomor halaman
            $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Penyusutan Perkoordinator', 0, 0, 'L');
            $this->Cell(0, 10, 'Tanggal ' .  date('d-m-Y H:i:s'), 0, 0, 'R');
        }

    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
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

    function filterInstansi($instansi) {
        //echo "$instansi";
        require '../../config/kon.php';
        //require ("../../fpdf/fpdf.php");
        global $pdf;
        global $tglsql;
        $where = "status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
        $filter = "a.tglperolehan < '$tglsql' and a.instansi LIKE '$instansi%'";
        $sql = mysql_query("SELECT a.harga
                   , b.instansi
                   , a.penyusutan
                   , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                   , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                   , harga-@akhir AS nilaibuku
                   FROM inv_inventaris a, umum_instansi b
                   WHERE a.instansi=b.id AND " . $where . " AND " . $filter . "ORDER BY a.kode ASC");
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
        // $printinstansi= $instansi;
        //menghitung konter
        $conter = mysql_query("SELECT COUNT(instansi) as instansi FROM inv_inventaris WHERE tglperolehan < '$tglsql' and instansi LIKE '$instansi%'");
        while ($contampil = mysql_fetch_array($conter)) {
            $princonter = $contampil['instansi'];
            //echo $idgol;
        }

        switch ($instansi) {
            case 1 : {
                    $instansi = 'KETUA I';
                    $printinstansi = '100';
                }break;
            case 2 : {
                    $instansi = 'KETUA II';
                    $printinstansi = '200';
                }break;
            case 3 : {
                    $instansi = 'KETUA III';
                    $printinstansi = '300';
                }break;
            case 4 : {
                    $instansi = 'KETUA IV';
                    $printinstansi = '400';
                }break;
            case 5 : {
                    $instansi = 'BENDAHARA UMUM';
                    $printinstansi = '500';
                }break;
            case 6 : {
                    $instansi = "SEKRETARIS UMUM";
                    $printinstansi = '600';
                }break;
            case 7 : {
                    $instansi = 'WAKIL KETUA UMUM';
                    $printinstansi = '700';
                }break;
            case 8 : {
                    $instansi = 'KETUA UMUM';
                    $printinstansi = '800';
                }break;
        }
        //$printinstansi=$instansi;
        if ($princonter == 0) {
            
        } else {

            $pdf->Ln();
            $pdf->Cell(14, 8, $printinstansi, 1, 0, 'L', true);
            $pdf->Cell(60, 8, $instansi, 1, 0, 'L', true);
            $pdf->Cell(45, 8, number_format(array_sum($harga), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($penyusutan), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($akhir), 2), 1, 0, 'R', true);
            $pdf->Cell(45, 8, number_format(array_sum($nilaibuku), 2), 1, 0, 'R', true);
        }
    }

//tampilkan data with looping
    for ($index = 1; $index < 10; $index++) {
        filterInstansi($index);
}
    //select total sub   
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

//cek apabila array kosong
    if (empty($harga)) {
        $pdf->Ln();
        $pdf->Cell(254, 8, "Maaf data tidak ada...", 1, 0, 'C', true);
    } else {
//filtergol('4');
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', '10');
        $pdf->Cell(74, 8, "Subtotal s.d " . $blnindo . " " . $thn, 1, 0, 'C', true);
        $pdf->Cell(45, 8, number_format(array_sum($harga), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($penyusutan), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($akhir), 2), 1, 0, 'R', true);
        $pdf->Cell(45, 8, number_format(array_sum($nilaibuku), 2), 1, 0, 'R', true);
#output file PDF
    }
    $pdf->Output();
    exit();
} else {
    echo "Access denied...";
}
?>