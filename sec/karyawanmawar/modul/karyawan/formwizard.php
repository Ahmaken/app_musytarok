<?php include'../../session/level3.php'; ?>
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
                                <header class="panel-heading"><b>Karyawan</b></header>
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
                                    <div class="stepy-tab">
                                        <ul id="default-titles" titles clearfix">
                                            <li id="default-title-0" class="current-step">
                                                <div>Personal Identity</div>
                                            </li>
                                            <li id="default-title-1" class="">
                                                <div>Personal Adress</div>
                                            </li>
                                            <li id="default-title-2" class="">
                                                <div>Step 3</div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="form">
                                        <form id="tambahkaryawan" class="cmxform form-horizontal tasi-form" method="post" action="prospegawai.php?type=save">
                                            <fieldset title="1" class="step" id="default-step-0">
                                                <legend>Identitas Personal</legend>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">NO KTP</label>
                                                <div class="col-sm-6">
                                                    <input id="ktp" name="ktp" autofocus="" placeholder="Nomor KTP" class="form-control" required="" type="text" value="" maxlength="16"/>
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nama</label>
                                                <div class="col-sm-10">
                                                    <input id="nama" name="nama" autofocus="" placeholder="Nama" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tempat Lahir</label>
                                                <div class="col-sm-10">
                                                    <input id="tempatlahir" placeholder="Tempat Lahir" name="tempatlahir" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tanggal Lahir</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" placeholder="hh-bb-tttt" data-mask="99-99-9999" required name="tanggallahir" id="tanggallahir">

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tinggi / Berat Badan</label>
                                                <div class="col-sm-3">
                                                    <input id="tinggi" placeholder="Tinggi" name="tinggi" class="form-control" type="text" /> cm
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="berat" placeholder="Berat" name="berat" class="form-control" type="text" /> kg  

                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Jumlah Anak</label>
                                                <div class="col-sm-3">
                                                    <input placeholder="Jumlah Anak" name="jumlahanak" class="form-control" type="text" /> 
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status Alumni</label>
                                                <div class="col-sm-2">
                                                    <div class="radio">
                                                        <input  name="statusalumni" type="radio" value="1" checked=""/> PPS
                                                    </div>
                                                </div>
                                                <div class="col-sm2">
                                                    <div class="radio">
                                                        <input  name="statusalumni" type="radio"value="0" /> Non-PPS
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tahun Masuk</label>
                                                <div class="col-sm-2">
                                                    <input type="text" class="form-control" placeholder="tttt" data-mask="9999" required name="tahunmasuk" id="tahunmasuk">
                                                </div>
                                            </div>
                                            </fieldset>
                                            <!--                                            alamat-->
                                            <fieldset class="step" id="default-step-1" title="2">
                                                <legend>Alamat Personal</legend>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Alamat</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Alamat" name="alamat" class="form-control" type="text" /> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Desa" name="desa" class="form-control" required="" type="text" value="" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Kecamatan" name="kecamatan" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label"></label>
                                                 <div class="col-sm-5">
                                                    <input id="alamat" placeholder="Kota" name="kota" class="form-control" required="" type="text" value="" />
                                                </div>
                                                <div class="col-sm-5">
                                                    <input id="alamat" placeholder="Provinsi" name="provinsi" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>
                                           <div class="form-group">
                                                <label class="col-sm-2 control-label">Jarak Kantor</label>
                                                <div class="col-sm-2">
                                                    <input placeholder="Jarak Kantor" name="jarakkantor" class="form-control" type="text" /> km
                                                </div>

                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Np HP 1</label>
                                                <div class="col-sm-3">
                                                    <input id="hp" placeholder="No Hanphone 1" name="hp1" class="form-control" required="" type="text" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">HP</label>
                                                <div class="col-sm-4">
                                                    <input id="hp" placeholder="No Handphone 2" name="hp2" class="form-control" type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-8">
                                                    <input id="email" placeholder="Email" name="email" class="form-control"  type="email" value="" />
                                                </div>
                                            </div>
                                            </fieldset>
                                            <fieldset class="step" id="default-step-2" Title="3">
                                                <legend>Pendidikan dan Pengalaman</legend>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Pendidikan Pesantren</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Pesantren 1" name="pesantren1" class="form-control" type="text" /> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Pesantren 2" name="pesantren2" class="form-control"  type="text" value="" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Pesantren 3" name="pesantren3" class="form-control"  type="text" value="" />
                                                </div>
                                            </div>
                                                  <div class="form-group">
                                                <label class="col-sm-2 control-label">Pendidikan Umum</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Umum 1 " name="umum1" class="form-control" type="text" /> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Umum 2" name="umum2" class="form-control"  type="text" value="" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Umum 3" name="umum3" class="form-control"  type="text" value="" />
                                                </div>
                                            </div>
                                                  <div class="form-group">
                                                <label class="col-sm-2 control-label">Peng Organisasi</label>
                                                <div class="col-sm-4">
                                                    <input placeholder="Organisasi 1" name="organisasi1" class="form-control" type="text" /> 
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Organisasi 2" name="organisasi2" class="form-control"  type="text" value="" />
                                                </div>
                                                <div class="col-sm-3">
                                                    <input id="alamat" placeholder="Organisasi 3" name="organisasi3" class="form-control"  type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Bidang</label>
                                                <div class="col-sm-8">
                                                    <input id="bidang" placeholder="Bidang" name="bidang" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
                                                </div>
                                            </div>


                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-6">
                                                    <button type="submit" class="btn btn-danger">Simpan</button>
                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                </div>
                                            </div>
                                            </fieldset>
                                            <input type="submit" class="finish btn btn-danger" value="Finish"/>
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
    <script src="../../js/jquery.stepy.js" ></script>
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

        $('#menupegawai').attr('class', 'active');
        $('#submenupegawai').attr('class', 'active');
    </script>
    <script>
        
      $(function() {
          $('#tambahkaryawan').stepy({
              backLabel: 'Previous',
              block: true,
              nextLabel: 'Next',
              titleClick: true,
              titleTarget: '.stepy-tab'
          });
      });
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
