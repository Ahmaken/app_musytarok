<?php
include'../../session/level3.php';
if (isset($_POST['filterby'])) {
//koneksi ke database
    $filterby = $_POST['filterby'];
    $koordinator = $_POST['filterby'];
    require '../../config/kon.php';
//$golongan= '4';
//$instansi= '1';
    $pecah = explode('/', $_POST['tanggal1']);
//echo $exp;
//$pecah = explode('/', $exp);
    $hariindo = $pecah[0];
    $tglangka = $pecah[0];
    $blnindo = $pecah [1];
    $thn = $pecah[2];
    $tanggalutuh = $tglangka . "/" . $blnindo . "/" . $thn;
    $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
    //PENGECEKAN TANGGAL PERIODE
    if ($_POST['tanggal2'] == "") {
        $periode = "Per Tanggal " . $tglangka . "/" . $blnindo . "/" . $thn;
        $filtertgl = " AND a.tglperolehan < '" . $tglsql . "'";
    } else {
        $pecah2 = explode('/', $_POST['tanggal2']);
        $hariindo2 = $pecah2[0];
        $blnindo2 = $pecah2[1];
        $thn2 = $pecah2[2];
        $tanggalutuh2 = $hariindo2 . "/" . $blnindo2 . "/" . $thn2;
        $periode = "Periode Tanggal " . $tanggalutuh . " s.d " . $tanggalutuh2;
        $tglsql2 = $thn2 . "/" . $blnindo2 . "/" . $hariindo2;
        $filtertgl = " AND a.tglperolehan BETWEEN '" . $tglsql . "' AND '" . $tglsql2 . "'";
    }

    require '../../config/tanggal.php';
//akhir koneksi
//$data = array();
//while ($row = mysql_fetch_assoc($sql)) {
//    array_push($data, $row);
//}
#setting judul laporan dan header tabel
    $judul = "LAPORAN PENYUSUTAN INVENTARIS";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";

    $header = array(
        array("label" => "KODE", "length" => 20, "align" => "L"),
        array("label" => "NAMA", "length" => 40, "align" => "L"),
        array("label" => "INSTANSI", "length" => 35, "align" => "L"),
        array("label" => "HARGA", "length" => 30, "align" => "L"),
        array("label" => "RESIDU", "length" => 30, "align" => "L"),
        array("label" => "UMUR", "length" => 15, "align" => "C"),
        array("label" => "SISA", "length" => 15, "align" => "L"),
        array("label" => "PENYUSUTAN", "length" => 30, "align" => "L"),
        array("label" => "AKUMULASI", "length" => 30, "align" => "L"),
        array("label" => "NILAI BUKU", "length" => 30, "align" => "L")
    );
    // pengecekan jkondisi golongan atau koordinator

    if ($_GET['type'] == 'koordinator') {
//cek koordinator
        switch ($koordinator) {
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

#sertakan library FPDF dan bentuk objek
        require_once ("../../fpdf/fpdf.php");

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
                $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Inventaris Koordinasi '.$instansi.' ('.$printinstansi.')', 0, 0, 'L');
                $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
            }

        }

        $pdf = new FPDF();
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage('L', 'A4');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '16');
        $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
        $pdf->SetFont('Arial', 'B', '14');
        $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '12');
        $pdf->Cell(0, 10, $periode, '0', 1, 'C');
#tampilkan judul koordinasi
        $pdf->SetFont('Arial', 'B', '12');
        $pdf->Cell(0, 10, 'KOORDINASI ' . $instansi . ' (' . $printinstansi . ')', '0', 1, 'C');

#buat header tabel
        $pdf->SetFont('Arial', 'B', '10');
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0, 0, 0);
        foreach ($header as $kolom) {
            $pdf->Cell($kolom['length'], 5, $kolom['label'], 1, '0', $kolom['align'], true);
        }


//$pdf->Ln();
#tampilkan data tabelnya
        $pdf->SetFillColor(255, 254, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
        $fill = false;


#ambil data di tabel dan masukkan ke array
        $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
        $filtertgl = $filtertgl;
        $filterkoor = " AND a.instansi like '" . $filterby . "%'";
//variable untuk pengecekan all


        $sql = mysql_query("SELECT a.kode
                                                                                            , a.nama
                                                                                            , a.spesifikasi
                                                                                            , b.instansi
                                                                                            , a.harga
                                                                                            , a.penyusutan
                                                                                            , a.nilaisisa AS residu
                                                                                            , a.umur
                                                                                            , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                                                                                            , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                                                                                            , @akhir-penyusutan AS awal
                                                                                            , harga-@akhir AS nilaibuku

                                                        FROM
                                                            inv_inventaris a, umum_instansi b
                                                            WHERE a.instansi=b.id" . $where . "" . $filtertgl . "" . $filterkoor . " ORDER BY a.instansi, a.id ASC");
        while ($hasil = mysql_fetch_array($sql)) {
            //$fill = !$fill;
            if ($hasil["sisaumur"]<0){
                $sisaumur="0";
            } else {
                $sisaumur = $hasil['sisaumur'];
            }
            $pdf->Ln();
            $pdf->Cell(20, 5, $hasil["kode"], 1, 0, 'L', true);
            $pdf->Cell(40, 5, $hasil["nama"], 1, 0, 'L', true);
            $pdf->Cell(35, 5, $hasil["instansi"], 1, 0, 'L', true);
            $pdf->Cell(30, 5, number_format($hasil["harga"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["residu"], 2), 1, 0, 'R', true);
            $pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'C', true);
            $pdf->Cell(15, 5, $hasil["sisaumur"], 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format($hasil["penyusutan"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["akhir"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["nilaibuku"], 2), 1, 0, 'R', true);
            $totharga[] = $hasil['harga'];
            $totresidu[] = $hasil['residu'];
            $totpenyusutan[] = $hasil['penyusutan'];
            $totakhir[] = $hasil['akhir'];
            $totbuku[] = $hasil['nilaibuku'];
            //echo $hasil['kode'];
        }

        //cek agar array tidak error
        if (empty($totharga)) {
            $pdf->Ln();
            $pdf->Cell(275, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
        } else {
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Cell(95, 5, 'TOTAL', 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format(array_sum($totharga), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totresidu), 2), 1, 0, 'R', true);
            $pdf->Cell(15, 5, '-', 1, 0, 'C', true);
            $pdf->Cell(15, 5, '-', 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format(array_sum($totpenyusutan), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totakhir), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totbuku), 2), 1, 0, 'R', true);
        }

#output file PDF
        $pdf->Output('PenyusutanKoordinator' .$instansi."". date('dmY'), 'I');
        exit();

        // jika type golongan
    } if ($_GET['type'] == 'golongan') {
        //echo $filterby;
        $q = mysql_query("select upper(golongan) as gol from inv_golongan where id=" . $filterby);
        while ($row = mysql_fetch_array($q)) {
            $tmpgol = $row["gol"];
        }
        #sertakan library FPDF dan bentuk objek
        require_once ("../../fpdf/fpdf.php");

        class PDF extends FPDF {

            function Footer() {
                global $tmpgol;
                //atur posisi 1.5 cm dari bawah
                $this->SetY(-15);
                //buat garis horizontal
                //$this->Line(10, $this->GetY(), 300, $this->GetY());
                //Arial italic 9
                $this->SetFont('Arial', 'I', 9);
                //nomor halaman
                $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Inventaris Golongan ' .$tmpgol, 0, 0, 'L');
                $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
            }

        }

        $pdf = new FPDF();
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage('L', 'A4');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '16');
        $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
        $pdf->SetFont('Arial', 'B', '14');
        $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '12');
        $pdf->Cell(0, 10, $periode, '0', 1, 'C');
#tampilkan judul koordinasi
        $pdf->SetFont('Arial', 'B', '12');
        $pdf->Cell(0, 10, 'GOLONGAN ' . $tmpgol, '0', 1, 'C');

#buat header tabel
        $pdf->SetFont('Arial', 'B', '9');
        $pdf->SetFillColor(0, 0, 0);
        $pdf->SetTextColor(255);
        $pdf->SetDrawColor(0, 0, 0);
        foreach ($header as $kolom) {
            $pdf->Cell($kolom['length'], 5, $kolom['label'], 1, '0', $kolom['align'], true);
        }


//$pdf->Ln();
#tampilkan data tabelnya
        $pdf->SetFillColor(255, 254, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('Arial', '', '9');
        $fill = false;


#ambil data di tabel dan masukkan ke array
        $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
        $filtertgl = $filtertgl;
        $filterkoor = " AND a.kode like '" . $filterby . "%'";
//variable untuk pengecekan all


        $sql = mysql_query("SELECT a.kode
                                                                                            , a.nama
                                                                                            , a.spesifikasi
                                                                                            , b.instansi
                                                                                            , a.harga
                                                                                            , a.penyusutan
                                                                                            , a.nilaisisa AS residu
                                                                                            , a.umur
                                                                                            , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                                                                                            , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                                                                                            , @akhir-penyusutan AS awal
                                                                                            , harga-@akhir AS nilaibuku
                                                        FROM
                                                            inv_inventaris a, umum_instansi b
                                                            WHERE a.instansi=b.id" . $where . "" . $filtertgl . "" . $filterkoor . " ORDER BY a.instansi, a.id ASC");
        while ($hasil = mysql_fetch_array($sql)) {
            //$fill = !$fill;
            if ($hasil["sisaumur"]<0){
                $sisaumur="0";
            } else {
                $sisaumur= $hasil['sisaumur'];
            }
            $pdf->Ln();
            $pdf->Cell(20, 5, $hasil["kode"], 1, 0, 'L', true);
            $pdf->Cell(40, 5, $hasil["nama"], 1, 0, 'L', true);
            $pdf->Cell(35, 5, $hasil["instansi"], 1, 0, 'L', true);
            $pdf->Cell(30, 5, number_format($hasil["harga"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["residu"], 2), 1, 0, 'R', true);
            $pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'C', true);
            $pdf->Cell(15, 5, $sisaumur, 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format($hasil["penyusutan"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["akhir"], 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format($hasil["nilaibuku"], 2), 1, 0, 'R', true);
            $totharga[] = $hasil['harga'];
            $totresidu[] = $hasil['residu'];
            $totpenyusutan[] = $hasil['penyusutan'];
            $totakhir[] = $hasil['akhir'];
            $totbuku[] = $hasil['nilaibuku'];
            //echo $hasil['kode'];
        }

        //cek agar array tidak error
        if (empty($totharga)) {
            $pdf->Ln();
            $pdf->Cell(275, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
        } else {
            $pdf->Ln();
            $pdf->SetFont('Arial', 'B', '10');
            $pdf->Cell(95, 5, 'TOTAL', 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format(array_sum($totharga), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totresidu), 2), 1, 0, 'R', true);
            $pdf->Cell(15, 5, '-', 1, 0, 'C', true);
            $pdf->Cell(15, 5, '-', 1, 0, 'C', true);
            $pdf->Cell(30, 5, number_format(array_sum($totpenyusutan), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totakhir), 2), 1, 0, 'R', true);
            $pdf->Cell(30, 5, number_format(array_sum($totbuku), 2), 1, 0, 'R', true);
        }
         $pdf->Output('PenyusutanGolongan' . $tmpgol ."". date('dmY'), 'I');
        exit();
    }
} else {
    echo "Access denied... :)";
}
?>