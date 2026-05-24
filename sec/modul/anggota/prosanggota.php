<?php

include '../../session/level3.php';
include '../../config/kon.php';
include '../../config/security.php';
$type = isset($_GET['type']) ? $_GET['type'] : "";

switch ($type) {
    case 'save':
        save();
        break;
    case 'update':
        update();
        break;
    case 'delete':
        hapus();
        break;
    case 'topps':
        topps();
        break;
     case 'tolpps':
        topps();
        break;
}

function save() {
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $tempatlahir = $_POST['tempatlahir'];
    $tgllhr = explode("-", $_POST['tanggallahir']);
    $tgllahir = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $tgldaftar = explode("-", $_POST['tanggaldaftar']);
    $tgl_daftar = $tgldaftar[2] . "-" . $tgldaftar[1] . "-" . $tgldaftar[0];
    $ibukandung = $_POST['ibukandung'];
    $statusanggota = $_POST['statusanggota'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $kodepos = $_POST['kodepos'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp1'];
    $email = $_POST['email'];
    $bank = $_POST['bank'];
    $norekening = $_POST['norekening'];
    $atasnama = $_POST['atasnamarekening'];
    $ahliwaris = $_POST['ahliwaris'];
    $hubungan = $_POST['hubunganahliwaris'];
    $telpahliwaris = $_POST['telpahliwaris'];
    $idlama = $_POST['idlama'];
    $keterangan = $_POST['keterangan'];

    //echo $tgllahir;
    //echo $_SESSION['username'];
//get urutan
    $noktp = mysql_query("select ktp from anggota_master where idlama='$idlama'");
    $noktp = mysql_num_rows($noktp);
    if ($noktp > 0) {
        echo "<script>
	  window.alert('Maaf, ID Lama sudah pernah dipake... hee');
	  location.href = 'index.php';</script>";
    } else {
        $n = mysql_query("SELECT count(id) AS id FROM anggota_master WHERE tanggaldaftar like '$tgldaftar[2]%'");
        $rowid= mysql_fetch_array($n);
        $barisid = $rowid['id']; 
        $nomor = $barisid + 1;
        $urut = str_pad($nomor, 3, "0", STR_PAD_LEFT);        
//awal dbukanya pendafatarn
        $selisih = $tgldaftar[2]-2008;
        $selisih = str_pad($selisih, 2, "0", STR_PAD_LEFT);
        $kode = $statusanggota. "" . $tgldaftar[2] . "" . $selisih . "" . $urut;
        $operator = $_SESSION['username'];
        $q = mysql_query("insert into anggota_master(kode,ktp, nama, tempatlahir, tanggallahir, alamat, desa, "
                . "kecamatan, kabupaten, provinsi, kodepos, hp1, hp2, email, ahliwaris_nama, ahliwaris_status, "
                . "ahliwaris_hp, status_anggota, status, ibukandung, bank_nama, bank_rekening, bank_atasnama, idlama, "
                . "keterangan, tanggaldaftar, TIMESTAMP, operator) value ('$kode', '$ktp', '$nama', '$tempatlahir', '$tgllahir',"
                . "'$alamat', '$desa', '$kecamatan', '$kota', '$provinsi', '$kodepos', '$hp1', '$hp2', '$email', '$ahliwaris',"
                . "'$hubungan', '$telpahliwaris', '$statusanggota', '1', '$ibukandung', '$bank', '$norekening', '$atasnama',"
                . "'$idlama', '$keterangan', '$tgl_daftar', now(), '$operator')");
        
        if ($q) {
            $id=md5(kode);
            $kodeencrypt= urlencode(encryptIt($kode));
            header('Location: ../simpanan/index.php?modul=penambahan&'.$id.'='.$kodeencrypt);
        } else {
            $error = mysql_error();
            header('Location: index.php?status=gagal&ket=' . $error);
        }
    }
}

function update() {
    $id = $_POST['id'];
    $kode = $_POST['kode'];
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $tempatlahir = $_POST['tempatlahir'];
    $tgllhr = explode("-", $_POST['tanggallahir']);
    $tgllahir = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $tgldaftar = explode("-", $_POST['tanggaldaftar']);
    $tgl_daftar = $tgldaftar[2] . "-" . $tgldaftar[1] . "-" . $tgldaftar[0];
    $ibukandung = $_POST['ibukandung'];
    $statusanggota = $_POST['statusanggota'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $kodepos = $_POST['kodepos'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp1'];
    $email = $_POST['email'];
    $bank = $_POST['bank'];
    $norekening = $_POST['norekening'];
    $atasnama = $_POST['atasnamarekening'];
    $ahliwaris = $_POST['ahliwaris'];
    $hubungan = $_POST['hubunganahliwaris'];
    $telpahliwaris = $_POST['telpahliwaris'];
    $idlama = $_POST['idlama'];
    $keterangan = $_POST['keterangan'];
    $operator = $_SESSION['username'];
    $q = mysql_query("update anggota_master set kode='$kode',ktp='$ktp', nama='$nama', tempatlahir='$tempatlahir', tanggallahir='$tgllahir', alamat='$alamat', desa='$desa', "
                . "kecamatan='$kecamatan', kabupaten='$kota', provinsi='$provinsi', kodepos='$kodepos', hp1='$hp1', hp2='$hp2', email='$email', ahliwaris_nama='$ahliwaris', ahliwaris_status='$hubungan', "
                . "ahliwaris_hp='$telpahliwaris', status_anggota='$statusanggota', ibukandung='$ibukandung', bank_nama='$nama', bank_rekening='$norekening', bank_atasnama='$atasnama', idlama='$idlama', "
                . "keterangan='$keterangan', tanggaldaftar='$tgl_daftar', TIMESTAMP='now()', operator='$operator' where id='$id'");

    if ($q) {
        header('Location: anggota.php?status=update');
    } else {
        $error = mysql_error();
        header('Location: anggota.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $id = $_GET['id'];
    $q = mysql_query("UPDATE anggota_master set status=0 where id = $id");
    if (q) {
        header('Location: anggota.php?status=sukses');
    } else {
        header('Location: anggota.php?status=gagal');
    }
}


?>