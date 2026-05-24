<?php include'../../session/level3.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />
        <link rel="shortcut icon" href="img/favicon.png">

        <title>Cetak | Pondok Pesantern Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->

        <?php include '../../template/head.php'; ?>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
        <!--[if lt IE 9]>
          <script src="js/html5shiv.js"></script>
          <script src="js/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">
            .tt-hint{
                font-size: 14px;
            }
            .tt-dropdown-menu {
                width: 200px;
                margin-top: 5px;
                padding: 8px 12px;
                background-color: #fff;
                border: 1px solid #ccc;
                border: 1px solid rgba(0, 0, 0, 0.2);
                border-radius: 8px 8px 8px 8px;
                font-size: 15px;
                color: #111;
                background-color: #F1F1F1;
            }
        </style>
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

                        <div class="col-lg-8">
                            <section class="panel">
                                <header class="panel-heading">Cetak Sertifikat</header>   
                                <header class="panel-heading tab-bg-dark-navy-blue ">
                                    <ul class="nav nav-tabs">
                                        <li class="active">
                                            <a data-toggle="tab" href="#pertanggal">Kode Anggota</a>
                                        </li>
                                        <!--                                        <li class="">
                                                                                    <a data-toggle="tab" href="#perkode">Wilayah</a>
                                                                                </li>
                                                                                <li class="">
                                                                                    <a data-toggle="tab" href="#pergolongan">Seluruh</a>
                                                                                </li>
                                                                                <li class="">
                                                                                    <a data-toggle="tab" href="#perinstansi">#</a>
                                                                                </li>-->
                                    </ul>
                                </header>
                                <div class="panel-body">
                                    <div class="tab-content">
                                        <div id="pertanggal" class="tab-pane active">
                                            <div class="form-group">
                                                <div class="panel-body">
                                                    <div class="form">
                                                        <form id="frmrekap" class="cmxform form-horizontal tasi-form" method="POST" action="../reportpdf/sertifikat.php?type=bynik" target="_blank">
                                                            <label class="col-sm-2 control-label">Kode Anggota</label>
                                                            <div class="col-sm-4">
                                                                <input id="kodeanggota" name="kode" autofocus="" placeholder="NIK" class="form-control" required="" type="text" value="" />
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <input id="namaanggota" name="nama"  placeholder="Nama" class="form-control"   type="text"  />
                                                            </div>
                                                            <div class="form-group" id="inputan">
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-lg-offset-2 col-lg-6">
                                                                    <button type="submit"  class="btn btn-danger">Proses</button>
                                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="perkode" class="tab-pane">
                                            <div class="form-group">
                                                <div class="panel-body">
                                                    <div class="form">
                                                        <form id="frmrekap" class="cmxform form-horizontal tasi-form" method="POST" action="../../fpdf/printlabel.php?type=perkode"  target="_blank">
                                                            <label class='col-sm-2 control-label' id='labeltgl'>Kode Inv</label>
                                                            <div class='col-md-3 col-xs-11'>
                                                                <input placeholder='Kode' class='form-control form-control-inline input-medium'  required name='kode' id='tanggal' size='20' type='text' value='' />
                                                            </div>
                                                            <div class="form-group" id="inputan">

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-lg-offset-2 col-lg-6">
                                                                    <button type="submit"  class="btn btn-danger">Proses</button>
                                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="pergolongan" class="tab-pane">
                                            <div class="form-group">
                                                <div class="panel-body">
                                                    <div class="form">
                                                        <form id="frmrekap" class="cmxform form-horizontal tasi-form" method="POST" action="../../fpdf/printlabel.php?type=pergolongan" target="_blank">
                                                            <label class='col-sm-2 control-label' id='labeltgl'>Koordinator</label>
                                                            <div class='col-md-3 col-xs-11'>
                                                                <select  class="form-control" id="golongan" name="golongan" placeholder="Golongan" required>
                                                                    <option selected value="">GOLONGAN</option>
                                                                    <?php
                                                                    include '../../config/kon.php';
                                                                    if (isset($_GET['golongan'])) {
                                                                        $getgol = $_GET['golongan'];
                                                                        if ($getgol != "all") {
                                                                            $gol = mysql_query("select id, upper(golongan) as golongan from inv_golongan where id='$getgol' ");
                                                                            while ($hasil = mysql_fetch_array($gol)) {
                                                                                echo "<option selected value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                                            }
                                                                        }
                                                                    }
                                                                    $gol = mysql_query("select id, upper(golongan)as golongan from inv_golongan");
                                                                    while ($hasil = mysql_fetch_array($gol)) {
                                                                        echo "<option value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                                    }
                                                                    ?>

                                                                </select>
                                                            </div>
                                                            <div class="form-group" id="inputan">

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-lg-offset-2 col-lg-6">
                                                                    <button type="submit"  class="btn btn-danger">Proses</button>
                                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div id="perinstansi" class="tab-pane">
                                            <div class="form-group">
                                                <div class="panel-body">
                                                    <div class="form">
                                                        <form id="frmrekap" class="cmxform form-horizontal tasi-form" method="POST" action="../../fpdf/printlabel.php?type=instansi" target="_blank">
                                                            <label class='col-sm-2 control-label' id='labeltgl'>Koordinator</label>
                                                            <div class='col-md-3 col-xs-11'>
                                                                <select class="form-control" id="instansi" name="instansi" placeholder="instansi" required>
                                                                    <option selected value="">Instansi</option>
                                                                    <?php
                                                                    if (isset($_GET['instansi'])) {
                                                                        $getins = $_GET['instansi'];
                                                                        if ($getins != "all") {
                                                                            $ins = mysql_query("select * from umum_instansi where id='$getins' ");
                                                                            while ($hasil = mysql_fetch_array($ins)) {
                                                                                echo "<option selected value=" . $hasil['id'] . ">" . $hasil['instansi'] . "</option>";
                                                                            }
                                                                        }
                                                                    }
                                                                    $ins = mysql_query("select * from umum_instansi");
                                                                    while ($tampil = mysql_fetch_array($ins)) {
                                                                        echo "<option value=" . $tampil['id'] . ">" . $tampil['instansi'] . "</option>";
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                            <div class="form-group" id="inputan">

                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-lg-offset-2 col-lg-6">
                                                                    <button type="submit"  class="btn btn-danger">Proses</button>
                                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
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
        <script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.dataTables.js"></script>
        <script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
        <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
        <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
        <script class="include" type="text/javascript" src="../../js/nominal.js"></script>
        <script src="../../js/jquery.autocomplete.js"></script>

        <!--common script for all pages-->
        <script src="../../js/common-scripts.js"></script>

        <!--script for this page-->
        <script type="text/javascript" src="../../js/jquery.validate.min.js"></script>
        <script src="../../js/form-validation-script.js"></script>
        <script type="text/javascript" src="../../assets/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>

        <script>
            //cek rekap


            $('#subsertifikat').attr('class', 'active');
            $('#laporan').attr('class', 'active');
            setInterval(function() {
                $("#tutup").click();
            }, 3000);
            //owl carousel

            $(document).ready(function() {
                $("#kodeanggota").autocomplete("../../config/getanggota.php?type=kode", {
                    width: 250
                });

                $("#kodeanggota").result(function(event, data, formatted) {
                    var kode = formatted;
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function(data) {
                            $("#kodeanggota").val(data.kode);
                            $("#namaanggota").val(data.nama);
                        }
                    });
                });
                $("#kodeanggota").keyup(function() {
                    var kode = $('#kodeanggota').val();
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function(data) {
                            //  $("#kodeanggota").val(data.kode);
                            $("#namaanggota").val(data.nama);
                        }
                    });
                });

            });
            //custom select box

            $(function() {
                $('select.styled').customSelect();
            });

            //auto fill form
            function createRequestObject() {
                var ro;
                var browser = navigator.appName;
                if (browser == "Microsoft Internet Explorer") {
                    ro = new ActiveXObject("Microsoft.XMLHTTP");
                } else {
                    ro = new XMLHttpRequest();
                }
                return ro;
            }

            var xmlhttp = createRequestObject();


        </script>

    </body>
</html>
