<?php
include'../../session/level2.php';
require_once '../../config/kon.php';
require_once '../../config/security.php';
$iddescryp = md5('id');
$iddescryp = $_GET[$iddescryp];

if (isset($iddescryp)) {
    $idbaris = decryptIt($iddescryp);
    $q = mysql_query("SELECT * from sekretariat_datasantri where id='$idbaris' and status=1");
    $qalamat = mysql_query("SELECT a.provinsi, b.provinsiNama, a.kabupaten, c.kabupatenNama, a.kecamatan, d.kecamatanNama FROM sekretariat_datasantri a, provinsi b, kabupaten c, kecamatan d WHERE a.provinsi=b.provinsiId AND a.kabupaten=c.kabupatenId AND a.kecamatan=d.kecamatanId AND a.id='$idbaris'");
    $hasil = mysql_fetch_array($q);
    $hasilalamat = mysql_fetch_array($qalamat);
    $tgllhr = date("d-m-Y", strtotime($hasil['tanggallahir']));
    $tgldaftar = date("d-m-Y", strtotime($hasil['tanggaldaftar']));
    if ($hasil['status_anggota'] == 0) {
        $cekumum = "checked";
    } else if ($hasil['status_anggota'] == 1) {
        $cekbasmalah = 'checked';
    }
    //echo $idbaris;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">

        <title>Pondok Pesantern Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->

        <?php include '../../template/head.php'; ?>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>

        <section id="container" >
            <!--header start-->

            <!--header end-->
            <!--sidebar start-->
            <?php
            include'../../template/header.php';
            include'../../template/side.php';
            include './getalamat.php';
            ?>

            <!--sidebar end-->
            <!--main content start-->
            <section id="main-content">
                <section class="wrapper">
                    <!--state overview start-->
                    <div class="row state-overview">

                        <div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading"><b>Edit Pendaftar</b></header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    if ($status == "sukses") {
                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        <p>Data baru berhasil disimpan...</p>
                                    </div>";
                                    } else if ($status == "gagal") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutuperror' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon--sign'></i>
                                            Gagal
                                        </h4>
                                        <p>Data tidak bisa disimpan</p>
                                        <p>Error:" . $ket . "</p>
                                    </div>";
                                    }
                                    ?>

                                    <div class="form">
                                        <form id="formanggota" class="cmxform form-horizontal tasi-form" method="post" action="prosanggota.php?type=update">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Kode Pendaftaran</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="kode"  readonly placeholder="Kode Pendaftaran" class="form-control" required="" type="text" value="<?php echo $hasil['NomorDaftar'] ?>"/>
                                                    <input name="id" type="hidden" value="<?php echo $hasil['id'] ?>"/>
                                                </div>
                                            </div>  
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nomor Induk</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="idmawar"  readonly placeholder="Nomor Induk" class="form-control" required="" type="text" value="<?php echo $hasil['IDMAWAR'] ?>"/>
                                                    <input name="id" type="hidden" value="<?php echo $hasil['id'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tanggal Daftar</label>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" autofocus="" placeholder="hh-bb-tttt" data-mask="99-99-9999" required name="tanggaldaftar" id="tgldaftar" value="<?php echo $tgldaftar ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">NO KTP</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="ktp"  placeholder="Nomor KTP" class="form-control" required="" type="text" maxlength="16" value="<?php echo $hasil['ktp'] ?>"/>
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nama</label>
                                                <div class="col-sm-10">
                                                    <input id="nama" name="nama" autofocus="" placeholder="Nama" class="form-control" required="" type="text" value="<?php echo $hasil['nama'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tempat Lahir</label>
                                                <div class="col-sm-4">
                                                    <input id="tempatlahir" placeholder="Tempat Lahir" name="tempatlahir" class="form-control" required="" type="text" value="<?php echo $hasil['tempatlahir'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input type="text" class="form-control" placeholder="hh-bb-tttt" data-mask="99-99-9999" required name="tanggallahir" id="tanggallahir" value="<?php echo $tgllhr ?>">

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Ibu Kandung</label>
                                                <div class="col-sm-3">
                                                    <input placeholder="Ibu Kandung" name="ibukandung" class="form-control" type="text" value="<?php echo $hasil['ibukandung'] ?>"/> 
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Alamat</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Alamat" name="alamat" class="form-control" type="text" value="<?php echo $hasil['alamat'] ?>" /> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Desa" name="desa" class="form-control" required="" type="text" value="<?php echo $hasil['desa'] ?>" />
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label>
                                                <div class="col-sm-5">
                                                    <select id="provinsi"  name="provinsi" class="form-control" required="">
                                                        <option value="">Provinsi:</option>
                                                        <option selected value="<?php echo $hasilalamat['provinsi'] ?>"><?php echo $hasilalamat['provinsiNama'] ?></option>
                                                        <?php
                                                        foreach ($arrpropinsi as $kodeprov => $namaprov) {
                                                            echo "<option value='$kodeprov'>$namaprov</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-5">
                                                    <select id="kabupaten" name="kota" class="form-control" required="">
                                                        <option value="">Kota/Kabupaten:</option>
                                                        <option selected value="<?php echo $hasilalamat['kabupaten'] ?>"><?php echo $hasilalamat['kabupatenNama'] ?></option>
                                                    </select>

                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label>

                                                <div class="col-sm-3">
                                                    <select id="kecamatan" name="kecamatan" class="form-control" required="">
                                                        <option value="">Kecamatan</option>
                                                        <option selected value="<?php echo $hasilalamat['kecamatan'] ?>"><?php echo $hasilalamat['kecamatanNama'] ?></option>
                                                    </select>
                                                </div><div class="col-sm-2">
                                                    <input id="kodepos" placeholder="Kode Pos" name="kodepos" class="form-control" type="text" value="<?php echo $hasil['kodepos'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">No HP 1</label>
                                                <div class="col-sm-3">
                                                    <input id="hp" placeholder="No Hanphone 1" name="hp1" class="form-control" required="" type="text" value="<?php echo $hasil['hp1'] ?>"/>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="hp" placeholder="No Handphone 2" name="hp2" class="form-control" type="text" value="<?php echo $hasil['hp2'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-8">
                                                    <input id="email" placeholder="Email" name="email" class="form-control"  type="email" value="<?php echo $hasil['email'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Rekening Bank</label>
                                                <div class="col-sm-3">
                                                    <input id="email" placeholder="Bank" name="bank" class="form-control"  type="text" value="<?php echo $hasil['bank_nama'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input placeholder="Nomor Rekening" name="norekening" class="form-control" type="text" value="<?php echo $hasil['bank_rekening'] ?>"/>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input  placeholder="Atas Nama" name="atasnamarekening" class="form-control" type="text" value="<?php echo $hasil['bank_atasnama'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status Keanggotaan</label>

                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <label> <input  name="statusanggota" type="radio" value="0" <?php echo $cekumum ?>/>Umum</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <label><input  name="statusanggota" type="radio" value="1" <?php echo $cekbasmalah ?> />Basmalah</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Ahli Waris</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Nama" name="ahliwaris" class="form-control" type="text" value="<?php echo $hasil['ahliwaris_nama'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input placeholder="Hubungan" name="hubunganahliwaris" class="form-control" type="text" value="<?php echo $hasil['ahliwaris_status'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input  placeholder="Telepon" name="telpahliwaris" class="form-control" type="text" value="<?php echo $hasil['ahliwaris_hp'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">ID Lama</label>
                                                <div class="col-sm-2">
                                                    <input placeholder="ID LAMA" name="idlama" class="form-control" type="text" value="<?php echo $hasil['idlama'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"><?php echo $hasil['keterangan'] ?></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-6">
                                                    <button type="submit" class="btn btn-danger">Simpan</button>
                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                </div>
                                        </form>
                                    </div>
                                </div>

                            </section>

                        </div>
                    </div>
                </section>
                </div>
                </div>
                </div>
            </section>
        </section>
        <!--main content end-->
        <!--footer start-->
        <?php include '../../template/foot.php' ?>
        <!--footer end-->
    </section>
    <script src="../../js/jquery.js"></script>
    <script src="../../js/jquery-1.8.3.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="../../js/jquery.scrollTo.min.js"></script>
    <script src="../../js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="../../js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="../../assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="../../js/owl.carousel.js" ></script>
    <script src="../../js/jquery.customSelect.min.js" ></script>
    <script src="../../js/respond.min.js" ></script>
    <script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
    <script class="include" type="text/javascript" src="../../js/nominal.js"></script>

    <!--common script for all pages-->
    <script src="../../js/common-scripts.js"></script>

    <!--script for this page-->
    <script type="text/javascript" src="../../js/jquery.validate.min.js"></script>
    <script src="../../js/form-validation-script.js"></script>
		<script src="../../js/jquery.maskedinput.min.js"></script>


    <script>
	jQuery(function($){
	  $("#tgldaftar").mask("99-99-9999");
	  $("#tanggallahir").mask("99-99-9999");
	});
        $(function() {
            $('input').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
            $('#email').keyup(function() {
                this.value = this.value.toLocaleLowerCase();
            });
            $('#keterangan').keyup(function() {
                this.value = this.value.toLocaleUpperCase();
            });
        });
        function jenidentitas() {
            $('#jenisidentitas').attr('required', '');
        }

        setInterval(function() {
            $("#tutup").click();
        }, 3000);

        $('#menuanggota').attr('class', 'active');
        $('#submenuanggota').attr('class', 'active');
    </script>
    <script>

        //owl carousel

        $(document).ready(function() {
            $('#provinsi').change(function() {
                $.getJSON('getalamat.php', {action: 'getKab', kode_prop: $(this).val()}, function(json) {
                    $('#kabupaten').html('');
                     $('#kabupaten').append("<option value=''>Kabupaten/Kota:</option>");
                    $.each(json, function(index, row) {
                        $('#kabupaten').append('<option value=' + row.kodekab + '>' + row.namakab + '</option>');
                    });
                });
            });

            $('#kabupaten').change(function() {
                $.getJSON('getalamat.php', {action: 'getKec', kode_kab: $(this).val()}, function(json) {
                    $('#kecamatan').html('');
                    $('#kecamatan').append("<option value=''>Kecamatan:</option>");
                    $.each(json, function(index, row) {
                        $('#kecamatan').append('<option value=' + row.kodeKec + '>' + row.namaKec + '</option>');

                    });
                });
            });
            $('#kecamatan').change(function() {
                $.getJSON('getalamat.php', {action: 'getPos', kode_kec: $(this).val()}, function(json) {
                    //$('#kodepos').html('');
                    $.each(json, function(index, row) {
                        //$('#kecamatan').append('<option value=' + row.kodeKec + '>' + row.namaKec + '</option>');
                        $('#kodepos').val(row.kodepos);
                    });
                });
            });
            $("#owl-demo").owlCarousel({
                navigation: true,
                slideSpeed: 300,
                paginationSpeed: 400,
                singleItem: true,
                autoPlay: true

            });
        });

        //custom select box

        $(function() {
            $('select.styled').customSelect();
        });

    </script>

</body>
</html>
