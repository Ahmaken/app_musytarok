<?php

include '../../session/level3.php';
include '../../config/kon.php';
include '../../config/security.php';
//include '../../config/tahunajaran.php';
$type = isset($_GET['type']) ? $_GET['type'] : "";

switch ($type) {
    case 'pembayaran':
        pembayaran();
        break;
    case 'simpaninput':
        input();
        break;
    case 'editpenambahan':
        editpenambahan();
        break;
    case 'editpenarikan':
        editpenarikan();
        break;
    case 'proses':
        proses();
        break;
}

function pembayaran() {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $tanggal = explode('/', $_POST['tanggal']);
    $tglsql = $tanggal[2] . "-" . $tanggal[1] . "-" . $tanggal[0];
    $jam = date('H:i:s');
    $administrasi = str_replace('.', '', $_POST['administrasi']);
    $pangkal = str_replace('.', '', $_POST['uangpangkal']);
    $gedung = str_replace('.', '', $_POST['uanggedung']);
    $keterangan = $_POST['keterangan'];
    $verifikasi = $_POST['verifikasi'];
    $operator = $_SESSION['username'];
    //generate kode
    $yearNow = date('Y');
    $cekkode = mysql_fetch_array(mysql_query("select count(kode) as kode from reg_pembayaran where kode like '$yearNow%'"));
    $barisnya = $cekkode['kode'] + 1;
    $barisnya = str_pad($barisnya, 4, "0", STR_PAD_LEFT);
    $kode = "$yearNow" . $barisnya;
    $q = mysql_query("INSERT INTO reg_pembayaran(kode, namawali, alamat, administrasi, pangkal, gedung, STATUS, proses, tanggal, TIMESTAMP, operator) VALUES "
            . "('$kode','$nama','$alamat', '$administrasi', '$pangkal', '$gedung', '1', '1', '$tglsql', NOW(), '$operator')");
    if ($q) {

        header('Location: index.php?modul=penambahan&status=sukses&kode=' . $kode);
    } else {
        $error = mysql_error();
        header('Location: index.php?modul=penambahan&status=gagal&ket=' . $error);
    }
}

function input() {
    $kodedaftar = $_POST['kodedaftar'];
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $tempatlahir = $_POST['tempatlahir'];
    $tgllhr = explode("-", $_POST['tanggallahir']);
    $tgllahir = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $tgldaftar = explode("-", $_POST['tanggaldaftar']);
    $tgl_daftar = $tgldaftar[2] . "-" . $tgldaftar[1] . "-" . $tgldaftar[0];
    $jeniskelamin = $_POST['jeniskelamin'];
    $rencanamukim = $_POST['rencanamukim'];
    $goldarah = $_POST['goldarah'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $kodepos = $_POST['kodepos'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp2'];
    $email = $_POST['email'];
    $ktpwali = $_POST['ktpwali'];
    $namawali = $_POST['namawali'];
    $hubwali = $_POST['hubunganwali'];
    $namaayah = $_POST['namaayah'];
    $namaibu = $_POST['namaibu'];
    $pekerjaan = $_POST['pekerjaan'];
    $keterangan = $_POST['keterangan'];
    $kodemawar = date("Y") . "" . substr($tgllhr[2], -2);
    //echo $tgllahir;
    //echo $_SESSION['username'];
//get urutan
    $noktp = mysql_query("select ktp from sekretariat_datasantri where ktp='$ktp'");
    $noktp = mysql_num_rows($noktp);
    if ($noktp > 0) {
        echo "<script>
	  window.alert('Maaf, ID Lama sudah pernah dipakai');
	  location.href = 'index.php?modul=input';</script>";
    } else {
        $n = mysql_query("SELECT count(id) AS id FROM sekretariat_datasantri'  where IDMAWAR like '$tgldaftar[2]'");
        $rowid = mysql_fetch_array($n);
        $barisid = $rowid['id'];
        $nomor = $barisid + 1;
        $urut = str_pad($nomor, 4, "0", STR_PAD_LEFT);
        $kode = $kodemawar . "" . $urut;
        $operator = $_SESSION['username'];
        $q = mysql_query("INSERT INTO sekretariat_datasantri(IDMAWAR, NomorDaftar, TanggalDaftar, ktp, Nama, Dusun, Desa, "
                . "Kecamatan, Kabupaten, Provinsi, KodePos, GolonganDarah, JenisKelamin, Tempatlahir, TanggalLahir, RencanaStatusDomisili, "
                . "NamaAyah, NamaGadisIbu, KTPWali, WNama, WDusun, WDesa, WKecamatan, WKabupaten, WProvinsi, WStatus, WPekerjaan, WKontakHP1, "
                . "WKontakHP2, WEmail, Keterangan, status, operator) values ('$kode', '$kodedaftar', '$tgl_daftar', '$ktp', '$nama', '$alamat', '$desa', '$kecamatan', '$kota',"
                . "'$provinsi', '$kodepos', '$goldarah', '$jeniskelamin', '$tempatlahir', '$tgllahir', '$rencanamukim', '$namaayah', '$namaibu',"
                . "'$ktpwali', '$namawali', '$alamat', '$desa', '$kecamatan', '$kota', '$provinsi', '$hubwali', '$pekerjaan', '$hp1', '$hp2', '$email', '$keterangan', 1, '$operator')");

        if ($q) {
            $id = md5(kode);
            $kodeencrypt = urlencode(encryptIt($kode));
            $qupdate = mysql_query("update reg_pembayaran set proses=2 where kode='$kodedaftar'");
            header('Location: index.php?modul=input&' . $id . '=' . $kodeencrypt);
        } else {
            $error = mysql_error();
            header('Location: index.php?modul=input&status=gagal&ket=' . $error);
        }
    }
}

function editpenambahan() {
    
}

function editpenarikan() {
    
}

?>