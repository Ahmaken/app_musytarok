<?php

include '../../session/level2.php';
include '../../config/kon.php';
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
    $idpps = $_POST['idpps'];
    $idalumni = $_POST['idalumni'];
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $tempatlahir = $_POST['tempatlahir'];
    $tgllhr = explode("-", $_POST['tanggallahir']);
    $tgllahir = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $tinggi = $_POST['tinggi'];
    $berat = $_POST['berat'];
    $ibukandung = $_POST['ibukandung'];
    $statusnikah = $_POST['statusnikah'];
    $jumlahanak = $_POST['jumlahanak'];
    $statusalumni = $_POST['statusalumni'];
    $tahunmasuk = $_POST['tahunmasuk'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $jarakkantor = $_POST['jarakkantor'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp1'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $jarakkantor = $_POST['jarakkantor'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp2'];
    $email = $_POST['email'];
    $bank = $_POST['bank'];
    $norekening = $_POST['norekening'];
    $atasnama = $_POST['atasnamarekening'];
    $kerabat = $_POST['kerabat'];
    $hubungan = $_POST['hubungankerabat'];
    $telpkerabat = $_POST['telpkerabat'];
    $pesantren1 = $_POST['pesantren1'];
    $pesantren2 = $_POST['pesantren2'];
    $pesantren3 = $_POST['pesantren3'];
    $umum1 = $_POST['umum1'];
    $umum2 = $_POST['umum2'];
    $umum3 = $_POST['umum3'];
    $organisasi1 = $_POST['organisasi1'];
    $organisasi2 = $_POST['organisasi2'];
    $organisasi3 = $_POST['organisasi3'];
    $keterangan = $_POST['keterangan'];

    //echo $tgllahir;
    //echo $_SESSION['username'];
//get urutan
    $noktp = mysql_query("select noktp from karyawan_master where noktp = '$ktp'");
    $noktp = mysql_num_rows($noktp);
    if ($noktp > 0) {
        echo "<script>
	  window.alert('Data dengan no KTP " . $ktp . " sudah terdaftar');
	  location.href = 'index.php';</script>";
    } else {
        $n = mysql_query("SELECT count(tahunmasuk) AS tahun FROM karyawan_master WHERE tahunmasuk='$tahunmasuk'");
        while ($row = mysql_fetch_array($n)) {
            $nomor = $row['tahun'];
        }
        $nomor = $nomor + 1;
        $urut = str_pad($nomor, 3, "0", STR_PAD_LEFT);
        $thnlahir = substr($tgllhr[2], -2);
        $nik = $tahunmasuk . "" . $statusalumni . "" . $thnlahir . "" . $urut;
//
//
        $user = $_SESSION['username'];
        $q = mysql_query("INSERT INTO karyawan_master(nik, idpps, idalumni, noktp, nama, tempatlahir, tgllahir, tinggibadan, beratbadan, "
                . "ibukandung, statusnikah, tahunmasuk, statusalumni, jumlahanak, alamat, desa, kecamatan, kota, provinsi, "
                . "jarakkantor, hp1, hp2, email, bank, rekening, namarekening, namakerabat, statuskerabat, hpkerabat, "
                . "pesantren1, pesantren2, pesantren3, umum1, umum2, umum3, organisasi1, organisasi2, organisasi3, STATUS, TIMESTAMP, operator) "
                . "VALUES ('$nik', '$idpps', '$idalumni', '$ktp', '$nama', '$tempatlahir', '$tgllahir', '$tinggi', '$berat', "
                . "'$ibukandung', '$statusnikah', '$tahunmasuk', '$statusalumni', '$jumlahanak', '$alamat', '$desa', '$kecamatan', '$kota', '$provinsi',"
                . "'$jarakkantor', '$hp1', '$hp2', '$email', '$bank', '$norekening', '$atasnama', '$kerabat', '$hubungan', '$telpkerabat',"
                . "'$pesantren1','$pesantren2', '$pesantren3', '$umum1', '$umum2', '$umum3', '$organisasi1', '$organisasi2', '$organisasi3', 1, now(), '$user')");

        if ($q) {
            header('Location: index.php?status=sukses');
        } else {
            $error = mysql_error();
            header('Location: index.php?status=gagal&ket=' . $error);
        }
    }
}

function update() {
    $idkar = $_POST['id'];
    $nik = $_POST['nik'];
    $ktp = $_POST['ktp'];
    $nama = $_POST['nama'];
    $tempatlahir = $_POST['tempatlahir'];
    $tgllhr = explode("-", $_POST['tanggallahir']);
    $tgllahir = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0];
    $tinggi = $_POST['tinggi'];
    $berat = $_POST['berat'];
    $ibukandung = $_POST['ibukandung'];
    $statusnikah = $_POST['statusnikah'];
    $jumlahanak = $_POST['jumlahanak'];
    // $statusalumni = $_POST['statusalumni'];
    // $tahunmasuk = $_POST['tahunmasuk'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $jarakkantor = $_POST['jarakkantor'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp1'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];
    $desa = $_POST['desa'];
    $kecamatan = $_POST['kecamatan'];
    $kota = $_POST['kota'];
    $provinsi = $_POST['provinsi'];
    $jarakkantor = $_POST['jarakkantor'];
    $hp1 = $_POST['hp1'];
    $hp2 = $_POST['hp2'];
    $email = $_POST['email'];
    $bank = $_POST['bank'];
    $norekening = $_POST['norekening'];
    $atasnama = $_POST['atasnamarekening'];
    $kerabat = $_POST['kerabat'];
    $hubungan = $_POST['hubungankerabat'];
    $telpkerabat = $_POST['telpkerabat'];
    $pesantren1 = $_POST['pesantren1'];
    $pesantren2 = $_POST['pesantren2'];
    $pesantren3 = $_POST['pesantren3'];
    $umum1 = $_POST['umum1'];
    $umum2 = $_POST['umum2'];
    $umum3 = $_POST['umum3'];
    $organisasi1 = $_POST['organisasi1'];
    $organisasi2 = $_POST['organisasi2'];
    $organisasi3 = $_POST['organisasi3'];
    $keterangan = $_POST['keterangan'];
    $user = $_SESSION['username'];

    $q = mysql_query("UPDATE karyawan_master SET nik='$nik', noktp='$ktp', nama='$nama', tempatlahir='$tempatlahir', tgllahir='$tgllahir', tinggibadan='$tinggi', beratbadan='$berat', "
            . "ibukandung='$ibukandung', statusnikah='$statusnikah', jumlahanak='$jumlahanak', alamat='$alamat', desa='$desa', kecamatan='$kecamatan', kota='$kota', provinsi='$provinsi', "
            . "jarakkantor='$jarakkantor', hp1='$hp1', hp2='$hp2', email='$email', bank='$bank', rekening='$norekening', namarekening='$atasnama', namakerabat='$kerabat', statuskerabat='$hubungan', hpkerabat='$telpkerabat', "
            . "pesantren1='$pesantren1', pesantren2='$pesantren2', pesantren3='$pesantren3', umum1='$umum1', umum2='$umum2', umum3='$umum3', organisasi1='$organisasi1', organisasi2='$organisasi2', organisasi3='$organisasi3', TIMESTAMP=now(), operator='$user' where id='$idkar'");

    if ($q) {
        header('Location: karyawan.php?status=update');
    } else {
        $error = mysql_error();
        header('Location: karyawan.php?status=gagal&ket=' . $error);
    }
}

function hapus() {
    $id = $_GET['id'];
    $q = mysql_query("UPDATE karyawan_master set status=0 where id = $id");
    if (q) {
        header('Location: karyawan.php?status=sukses');
    } else {
        header('Location: karyawan.php?status=gagal');
    }
}
function topps() {
    $id = $_GET['id'];
    $idpps = $_GET['idpps'];
    $q = mysql_query("UPDATE karyawan_master set idpps='$idpps' where id = $id");
    if (q) {
        header('Location: karyawan.php?status=sukses');
    } else {
        header('Location: karyawan.php?status=gagal');
    }
}
function tolpps() {
    $id = $_GET['id'];
    $idpps = $_GET['idpps'];
    $q = mysql_query("UPDATE karyawan_master set idpps='$idpps' where id = $id");
    if (q) {
        header('Location: karyawan.php?status=sukses');
    } else {
        header('Location: karyawan.php?status=gagal');
    }
}

?>