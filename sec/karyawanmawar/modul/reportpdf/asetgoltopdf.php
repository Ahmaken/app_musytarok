<?php
include'../../session/level3.php';
if (!empty($_POST['filterby'])) {
    if ($_POST['filterby'] == 'all') {
        require '../../config/kon.php';
        $pecah = explode('/', $_POST['tanggal1']);
        $hariindo = $pecah[0];
        $tglangka = $pecah[0];
        $blnindo = $pecah [1];
        $thn = $pecah[2];
        $tanggalutuh = $tglangka . "/" . $blnindo . "/" . $thn;
        $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
//PENGECEKAN TANGGAL PERIODE
        if (empty($_POST['tanggal2'])) {
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
        require_once ("../../fpdf/fpdf.php");
#setting judul laporan dan header tabel
        $judul = "DATA INVENTARIS PERGOLONGAN";
        $judul2 = "Pondok Pesantren Matholi'ul Anwar";

        class PDF extends FPDF {

            function Footer() {
                //global $instansi;
                //global $printinstansi;
                //atur posisi 1.5 cm dari bawah
                $this->SetY(-15);
                //buat garis horizontal
                //$this->Line(10, $this->GetY(), 300, $this->GetY());
                //Arial italic 9
                $this->SetFont('Arial', 'I', 9);
                //nomor halaman
                $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Inventaris', 0, 0, 'L');
                $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
            }

        }

        $pdf = new FPDF();
        $pdf = new PDF();
        $pdf->AliasNbPages();
        $pdf->AddPage('P', 'A4');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '16');
        $pdf->Cell(0, 5, $judul, '0', 1, 'C');
#tampilkan judul2
        $pdf->SetFont('Arial', 'B', '14');
        $pdf->Cell(0, 5, $judul2, '0', 1, 'C');

#tampilkan judul laporan
        $pdf->SetFont('Arial', 'B', '12');
        $pdf->Cell(0, 10, $periode, '0', 1, 'C');

        //fungtion
        function kumpulInstansi($instansi) {
            // require '../../fpdf/fpdf.php';
#buat header tabel
            global $pdf;
            global $header;
            global $filtertgl;
            //echo $instansi;
#tampilkan data tabelnya
#ambil data di tabel dan masukkan ke array
            $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
            $filterins = " AND a.golongan = " . $instansi;
            //query for header
            $no = 1;
            $sql = mysql_query("SELECT a.kode, UPPER(a.nama) AS nama, c.id, UPPER(c.golongan)as golongan, UPPER(a.spesifikasi) as spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%m/%Y') AS tglperolehan,"
                    . "a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b, inv_golongan c WHERE a.instansi=b.id and a.golongan=c.id"
                    . $where . "" . $filterins . "" . $filtertgl);

            while ($row = mysql_fetch_array($sql)) {
                $barishead[] = $row['kode'];
                $stringist = $row['golongan'];
                $kodegol = $row['id'];
            }
            //header

            if (empty($barishead)) {
                
            } else {
                #tampilkan judul koordinasi
                $pdf->SetFont('Arial', '', '12');
                $pdf->Cell(5, 5, "(".$kodegol.") ".$stringist, '0', 1, 'L');
                $pdf->SetFont('Arial', 'B', '10');
                $pdf->SetFillColor(0, 0, 0);
                $pdf->SetTextColor(255);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->Cell(10, 5, "NO", 1, 0, 'L', true);
                $pdf->Cell(25, 5, "KODE", 1, 0, 'L', true);
                $pdf->Cell(55, 5, "NAMA", 1, 0, 'L', true);
                $pdf->Cell(45, 5, "SPESIFIKASI", 1, 0, 'L', true);
                $pdf->Cell(20, 5, "TGL", 1, 0, 'L', true);
                $pdf->Cell(35, 5, "HARGA", 1, 0, 'C', true);
            }
            // query untuk table
            $sql = mysql_query("SELECT a.kode, UPPER(a.nama) AS nama, UPPER(a.spesifikasi) as spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%m/%Y') AS tglperolehan,"
                    . "a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id"
                    . $where . "" . $filterins . "" . $filtertgl. " ORDER BY a.instansi, a.id ASC");

            while ($hasil = mysql_fetch_array($sql)) {
//$fill = !$fill;
                //fill isi
                $pdf->SetFillColor(255, 254, 255);
                $pdf->SetTextColor(0);
                $pdf->SetFont('');
                $fill = false;
                $pdf->Ln();
                $pdf->Cell(10, 5, $no++, 1, 0, 'L', true);
                $pdf->Cell(25, 5, $hasil["kode"], 1, 0, 'L', true);
                $pdf->Cell(55, 5, $hasil["nama"], 1, 0, 'L', true);
                $pdf->Cell(45, 5, $hasil["spesifikasi"], 1, 0, 'L', true);
                $pdf->Cell(20, 5, $hasil["tglperolehan"], 1, 0, 'L', true);
                $pdf->Cell(35, 5, number_format($hasil["harga"], 2), 1, 0, 'R', true);
                //$pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'L', true);
                $totharga[] = $hasil['harga'];

                //echo $hasil['kode'];
            }

//cek agar array tidak error
            if (empty($totharga)) {
//                $pdf->Ln();
//                $pdf->Cell(190, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
            } else {
                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', '10');
                $pdf->Cell(155, 5, 'TOTAL', 1, 0, 'R', true);
                $pdf->Cell(35, 5, number_format(array_sum($totharga), 2), 1, 0, 'R', true);
                $pdf->Ln();
                $pdf->Ln();
            }
        }

        //query untuk menampilkan aktiva group by instansi
        //include '../../config/kon.php';
        $q = mysql_query("SELECT id from inv_golongan");
        while ($row = mysql_fetch_array($q)) {
            kumpulInstansi($row['id']);
            // echo $row['id']."<br>";
        }
        $pdf->Output();
        exit();
    } else {
        pecah();
    }

// jika type golongan
} else {
    echo "Access denied... :)";
}

//global declatation
//funtion satuan

function pecah() {
    global $pdf;
    $instansi = $_POST['filterby'];
    require '../../config/kon.php';
    $pecah = explode('/', $_POST['tanggal1']);
    $hariindo = $pecah[0];
    $tglangka = $pecah[0];
    $blnindo = $pecah [1];
    $thn = $pecah[2];
    $tanggalutuh = $tglangka . "/" . $blnindo . "/" . $thn;
    $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
//PENGECEKAN TANGGAL PERIODE
    if (empty($_POST['tanggal2'])) {
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

#setting judul laporan dan header tabel
    $judul = "DATA INVENTARIS";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";

    $header = array(
        array("label" => "NO", "length" => 10, "align" => "C"),
        array("label" => "KODE", "length" => 25, "align" => "C"),
        array("label" => "NAMA", "length" => 55, "align" => "C"),
        array("label" => "SPESIFIKASI", "length" => 45, "align" => "C"),
        array("label" => "TANGGAL", "length" => 20, "align" => "C"),
        array("label" => "HARGA", "length" => 35, "align" => "C"),
            // array("label" => "UMUR", "length" => 15, "align" => "L"),
    );
// pengecekan jkondisi golongan atau koordinator
//echo $instansi;
    $ins = mysql_query("select golongan from inv_golongan where id=$instansi");
    while ($result = mysql_fetch_array($ins)) {
        $printinstansi = $result['golongan'];
    }
#sertakan library FPDF dan bentuk objek
    require_once ("../../fpdf/fpdf.php");

    // include ("../../fpdf/fpdf.php");

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
            $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Inventaris Koordinasi ' . $instansi . ' (' . $printinstansi . ')', 0, 0, 'L');
            $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
        }

    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('P', 'A4');

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
    $pdf->SetFont('Arial', '', '12');
    $pdf->Cell(5, 10, 'Inventaris: ' . $printinstansi . ' (' . $instansi . ')', '0', 1, 'L');
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
//$filtertgl = $filtertgl;
    $filterins = " AND a.golongan = " . $instansi;
//variable untuk pengecekan all

    $no = 1;
    $sql = mysql_query("SELECT a.kode, UPPER(a.nama) AS nama, UPPER(a.spesifikasi) as spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%m/%Y') AS tglperolehan,"
            . "a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id"
            . $where . "" . $filterins . "" . $filtertgl. " ORDER BY a.instansi, a.id ASC");
//echo mysql_error();
    while ($hasil = mysql_fetch_array($sql)) {
        //$fill = !$fill;
        $pdf->Ln();
        $pdf->Cell(10, 5, $no++, 1, 0, 'L', true);
        $pdf->Cell(25, 5, $hasil["kode"], 1, 0, 'L', true);
        $pdf->Cell(55, 5, $hasil["nama"], 1, 0, 'L', true);
        $pdf->Cell(45, 5, $hasil["spesifikasi"], 1, 0, 'L', true);
        $pdf->Cell(20, 5, $hasil["tglperolehan"], 1, 0, 'L', true);
        $pdf->Cell(35, 5, number_format($hasil["harga"], 2), 1, 0, 'R', true);
        //$pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'L', true);
        $totharga[] = $hasil['harga'];

        //echo $hasil['kode'];x
    }

//cek agar array tidak error
    if (empty($totharga)) {
        $pdf->Ln();
        $pdf->Cell(195, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
    } else {
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', '10');
        $pdf->Cell(155, 5, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(35, 5, number_format(array_sum($totharga), 2), 1, 0, 'R', true);
    }
#output file PDF
    $pdf->Output();
    exit();
}

function seluruh() {

    require '../../config/kon.php';
    $pecah = explode('/', $_POST['tanggal1']);
    $hariindo = $pecah[0];
    $tglangka = $pecah[0];
    $blnindo = $pecah [1];
    $thn = $pecah[2];
    $tanggalutuh = $tglangka . "/" . $blnindo . "/" . $thn;
    $tglsql = $thn . "/" . $blnindo . "/" . $hariindo;
//PENGECEKAN TANGGAL PERIODE
    if (empty($_POST['tanggal2'])) {
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

#setting judul laporan dan header tabel
    $judul = "DATA INVENTARIS";
    $judul2 = "Pondok Pesantren Matholi'ul Anwar";

    $header = array(
        array("label" => "NO", "length" => 10, "align" => "C"),
        array("label" => "KODE", "length" => 25, "align" => "C"),
        array("label" => "NAMA", "length" => 50, "align" => "C"),
        array("label" => "SPESIFIKASI", "length" => 45, "align" => "C"),
        array("label" => "TANGGAL", "length" => 20, "align" => "C"),
        array("label" => "HARGA", "length" => 35, "align" => "C"),
            // array("label" => "UMUR", "length" => 15, "align" => "L"),
    );
// pengecekan jkondisi golongan atau koordinator
//echo $instansi;
//   $ins = mysql_query("select instansi from umum_instansi where id=$instansi");
//    while ($result = mysql_fetch_array($ins)) {
//        $printinstansi = $result['instansi'];
//    }
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
            $this->Cell(0, 10, 'Halaman ' . $this->PageNo() . ' dari {nb} | Laporan Inventaris Koordinasi ' . $instansi . ' (' . $printinstansi . ')', 0, 0, 'L');
            $this->Cell(0, 10, 'Tanggal ' . date('d-m-Y H:i:s'), 0, 0, 'R');
        }

    }

    $pdf = new FPDF();
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage('P', 'A4');

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
    $pdf->SetFont('Arial', '', '12');
    $pdf->Cell(5, 10, 'Seluruh Instansi', '0', 1, 'L');
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
//$filtertgl = $filtertgl;
    // $filterins = " AND a.instansi = " . $instansi;
//variable untuk pengecekan all

    $no = 1;
    $sql = mysql_query("SELECT a.kode, UPPER(a.nama) AS nama, upper(a.spesifikasi) as spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%m/%Y') AS tglperolehan,"
            . "a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id"
            . $where . "" . $filtertgl . " ORDER BY a.instansi, a.id ASC");
//echo mysql_error();
    while ($hasil = mysql_fetch_array($sql)) {
        //$fill = !$fill;
        $pdf->Ln();
        $pdf->Cell(10, 5, $no++, 1, 0, 'L', true);
        $pdf->Cell(25, 5, $hasil["kode"], 1, 0, 'L', true);
        $pdf->Cell(50, 5, $hasil["nama"], 1, 0, 'L', true);
        $pdf->Cell(45, 5, $hasil["spesifikasi"], 1, 0, 'L', true);
        $pdf->Cell(20, 5, $hasil["tglperolehan"], 1, 0, 'L', true);
        $pdf->Cell(35, 5, number_format($hasil["harga"], 2), 1, 0, 'R', true);
        //$pdf->Cell(15, 5, $hasil["umur"], 1, 0, 'L', true);
        $totharga[] = $hasil['harga'];

        //echo $hasil['kode'];
    }

//cek agar array tidak error
    if (empty($totharga)) {
        $pdf->Ln();
        $pdf->Cell(190, 5, 'Maaf data tidak ada...', 1, 0, 'C', true);
    } else {
        $pdf->Ln();
        $pdf->SetFont('Arial', 'B', '10');
        $pdf->Cell(150, 5, 'TOTAL', 1, 0, 'C', true);
        $pdf->Cell(35, 5, number_format(array_sum($totharga), 2), 1, 0, 'R', true);
    }
#output file PDF
    $pdf->Output();
    exit();
}

?>