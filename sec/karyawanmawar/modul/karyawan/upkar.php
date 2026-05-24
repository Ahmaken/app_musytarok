<?php
include'../../session/level3.php';
require_once '../../config/kon.php';
if (isset($_GET['id'])) {
    $q = mysql_query("SELECT * from karyawan_master where id=" . $_GET['id']);
    $hasil = mysql_fetch_array($q);
    $tgllhr = $hasil['tgllahir'];
    $tgllhr = date("d-m-Y", strtotime($tgllhr));
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

        <title>Pondok Pesantren Matholi'ul Anwar</title>

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
            <?php include'../../template/header.php'; ?>
            <?php include'../../template/side.php'; ?>
            <!--sidebar end-->
            <!--main content start-->
            <section id="main-content">
                <section class="wrapper">
                    <!--state overview start-->
                    <div class="row state-overview">

                        <div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading"><b>Pegawai</b></header>
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
                                        <form id="formkaryawan" class="cmxform form-horizontal tasi-form" method="post" action="proskaryawan.php?type=update">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">NIK</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="nik" readonly="" class="form-control" required="" type="text" value="<?php echo $hasil['nik']?>"/>
                                                    <input id="ktp" name="id" class="form-control" type="hidden" value="<?php echo $_GET['id'] ?>"/>
                                                </div>
                                            </div>  
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">NO KTP</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="ktp" autofocus="" placeholder="Nomor KTP" class="form-control" required="" type="text" value="<?php echo $hasil['noktp']?>" maxlength="16"/>
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nama</label>
                                                <div class="col-sm-10">
                                                    <input id="nama" name="nama" autofocus="" placeholder="Nama" class="form-control" required="" type="text" value="<?php echo $hasil['nama']?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tempat Lahir</label>
                                                <div class="col-sm-10">
                                                    <input id="tempatlahir" placeholder="Tempat Lahir" name="tempatlahir" class="form-control" required="" type="text" value="<?php echo $hasil['tempatlahir']?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tanggal Lahir</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" placeholder="hh-bb-tttt" data-mask="99-99-9999" required name="tanggallahir" id="tanggallahir" value="<?php $tgllhr= explode("-", $hasil['tgllahir']);  $tglbdr = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0]; echo $tglbdr;?>">

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tinggi / Berat Badan</label>
                                                <div class="col-sm-3">
                                                    <input id="tinggi" placeholder="Tinggi" name="tinggi" class="form-control" type="text"  value="<?php echo $hasil['tinggibadan']?>"/> cm
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="berat" placeholder="Berat" name="berat" class="form-control" type="text" value="<?php echo $hasil['beratbadan']?>" /> kg  
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Ibu Kandung</label>
                                                <div class="col-sm-3">
                                                    <input placeholder="Ibu Kandung" name="ibukandung" class="form-control" type="text" value="<?php echo $hasil['ibukandung']?>" /> 
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status Pernikahan</label>
                                                <?php 
                                                $snikah=$hasil['statusnikah'];
                                                if ($snikah=='Belum'){
                                                    $belum='checked';
                                                } elseif($snikah=='Menikah'){
                                                    $menikah='checked';
                                                }else{
                                                    $duda='checked';
                                                }?>
                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <input  name="statusnikah" type="radio" value="Belum" <?php echo $belum ?>/> Belum
                                                    </div>
                                                </div>
                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <input  name="statusnikah" type="radio"value="Menikah" <?php echo $menikah ?>/> Menikah
                                                    </div>
                                                </div>
                                                 <div class="col-sm-2">
                                                    <div class="radio">
                                                        <input  name="statusnikah" type="radio" value="Duda" <?php echo $duda ?>/> Duda
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                              
                                                <label class="col-sm-2 control-label">Jumlah Anak</label>
                                                <div class="col-sm-3">
                                                    <input placeholder="Jumlah Anak" name="jumlahanak" class="form-control" type="text" value="<?php echo $hasil['jumlahanak']?>"/> 
                                                </div>

                                            </div>
                                              <?php
//                                                 $salumni=$hasil['statusalumni'];
//                                                if ($salumni=='1'){
//                                                    $alumni='checked';
//                                                } else{
//                                                    $nonalumni='checked';
//                                                }
                                                ?>
<!--                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status Alumni</label>
                                                <div class="col-sm-1">
                                                    <div class="radio">
                                                        <input  name="statusalumni" type="radio" value="1" <?php echo $alumni ?>/> PPS
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="radio">
                                                        <input  name="statusalumni" type="radio"value="0" <?php echo $nonalumni ?>/> Non-PPS
                                                    </div>
                                                </div>
                                            </div>-->
<!--                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tahun Masuk</label>
                                                <div class="col-sm-2">
                                                    <input type="text" class="form-control" placeholder="tttt" data-mask="9999" required name="tahunmasuk" id="tahunmasuk">
                                                </div>
                                            </div>-->
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Alamat</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Alamat" name="alamat" class="form-control" type="text" value="<?php echo $hasil['alamat'] ?>"/> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Desa" name="desa" class="form-control" required="" type="text" value="<?php echo $hasil['desa'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Kecamatan" name="kecamatan" class="form-control" required="" type="text" value="<?php echo $hasil['kecamatan'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label>
                                                <div class="col-sm-5">
                                                    <input id="alamat" placeholder="Kota" name="kota" class="form-control" required="" type="text" value="<?php echo $hasil['kota'] ?>"/>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input id="alamat" placeholder="Provinsi" name="provinsi" class="form-control" required="" type="text" value="<?php echo $hasil['provinsi'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Jarak Kantor</label>
                                                <div class="col-sm-2">
                                                    <input placeholder="Jarak Kantor" name="jarakkantor" class="form-control" type="text" value="<?php echo $hasil['jarakkantor'] ?>"/> km
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Np HP </label>
                                                <div class="col-sm-3">
                                                    <input id="hp" placeholder="No Hanphone 1" name="hp1" class="form-control" required="" type="text" value="<?php echo $hasil['hp1'] ?>"/>
                                                </div>
                                                <div class="col-sm-4">
                                                    <input id="hp" placeholder="No Handphone 2" name="hp2" class="form-control" type="text" value="<?php echo $hasil['hp2'] ?>"/>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-8">
                                                    <input id="email" placeholder="Email" name="email" class="form-control"  type="email"  value="<?php echo $hasil['email'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Rekening Bank</label>
                                                <div class="col-sm-2">
                                                    <select class="form-control" name="bank">
                                                        <option value="<?php echo $hasil['bank']?>"><?php echo $hasil['bank']?></option>
                                                        <option value="BNI">BNI</option>
                                                        <option value="Mandiri">Mandiri</option>
                                                        <option value="BSM">BSM</option>
                                                        <option value="BRI">BRI</option>
                                                        <option value="BCA">BCA</option>
                                                    </select>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input placeholder="Nomor Rekening" name="norekening" class="form-control"  value="<?php echo $hasil['rekening'] ?>"  />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input  placeholder="Atas Nama" name="atasnamarekening" class="form-control" type="text" value="<?php echo $hasil['namarekening'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Kerabat</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Nama Kerabat" name="kerabat" class="form-control" type="text" value="<?php echo $hasil['namakerabat'] ?>"  />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input placeholder="Hubungan" name="hubungankerabat" class="form-control" type="text"   value="<?php echo $hasil['statuskerabat'] ?>"/>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input  placeholder="Telepon" name="telpkerabat" class="form-control" type="text" value="<?php echo $hasil['hpkerabat'] ?>" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Pendidikan Pesantren</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Pesantren 1" name="pesantren1" class="form-control" type="text" value="<?php echo $hasil['pesantren1'] ?>"/> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Pesantren 2" name="pesantren2" class="form-control"  type="text" value="<?php echo $hasil['pesantren2'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Pesantren 3" name="pesantren3" class="form-control"  type="text" value="<?php echo $hasil['pesantren3'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Pendidikan Umum</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Umum 1 " name="umum1" class="form-control" type="text" value="<?php echo $hasil['umum1'] ?>"/> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Umum 2" name="umum2" class="form-control"  type="text" value="<?php echo $hasil['umum2'] ?>" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Umum 3" name="umum3" class="form-control"  type="text" value="<?php echo $hasil['umum3'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Peng Organisasi</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Organisasi 1" name="organisasi1" class="form-control" type="text" value="<?php echo $hasil['organisasi1'] ?>"/> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Organisasi 2" name="organisasi2" class="form-control"  type="text" value="<?php echo $hasil['organisasi2'] ?>"/>
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Organisasi 3" name="organisasi3" class="form-control"  type="text" value="<?php echo $hasil['organisasi3'] ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"><?php echo $hasil['keterangan']?></textarea>
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
    <script type="text/javascript" src="../../assets/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

    <script>
                                                        function jenidentitas() {
                                                            $('#jenisidentitas').attr('required', '');
                                                        }

                                                        setInterval(function() {
                                                            $("#tutup").click();
                                                        }, 3000);

                                                        $('#baru').attr('class', 'active');
                                                        $('#inventaris').attr('class', 'active');
    </script>
    <script>

        //owl carousel

        $(document).ready(function() {
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
