<?php include'../../session/level3.php';?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">

        <title>Mutasi | Pondok Pesantren Matholi'ul Anwar</title>

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
                                <header class="panel-heading">Rekapitulasi Laporan Inventaris</header>
                                <div class="panel-body">
                                    <div class="form">
                                        <form id="frmrekap" class="cmxform form-horizontal tasi-form" method="post" target="_blank">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Rekap By</label>

                                                <div class="col-sm-4">
                                                    <select onchange="cekRekap()" class="form-control" id="rekap" name="rekap" required>
                                                        <option value="">Pilih Rekap: </option>>
                                                        <option value="1">Inv Baru </option>>
                                                        <option value="2">Golongan </option>>
<!--                                                        <option value="3">Koordinator </option>>-->
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tanggal</label>
                                                <div class="col-md-3 col-xs-11">
                                                    <input placeholder="Tanggal" class="form-control form-control-inline input-medium default-date-picker"  required name="tanggal" id="tanggal" size="16" type="text" value="" />
                                                    
                                                </div>
                                                <div class="col-md-3 col-xs-11">
                                                    
                                                    <input placeholder="Tanggal Akhir" class="form-control form-control-inline input-medium default-date-picker"  name="tanggal2" id="tanggal2" size="16" type="text" value="" style="visibility: hidden;" />
                                                </div>
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
    <script src="../../js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="../../js/typeahead.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="../../js/jquery.scrollTo.min.js"></script>
    <script src="../../js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="../../js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="../../js/common-scripts.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <script type="text/javascript" src="../../assets/jquery-multi-select/js/jquery.multi-select.js"></script>
    <script src="../../js/advanced-form-components.js"></script>
    <script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
    <script src="../../js/jquery.customSelect.min.js" ></script>
    <script>

    </script>
    <script>
        //cek rekap
        function cekRekap() {
            var value = document.getElementById("rekap").value;
            //var baru = 'baru'
            //alert(value);
//            alert(rekap);
//            var koor = 'koordinator';
//            var gol= 'gol';
            switch (value) {
                case "1":
                    //alert('koordinator');
                    $('#frmrekap').removeAttr('action');
                    $('#frmrekap').attr('action', '../reportpdf/rekapbaru.php');
                    $('#tanggal2').attr('required','');
                    $('#tanggal2').css('visibility', 'visible');
                    break;
                case "2":
                    //alert('gol');
                    $('#frmrekap').removeAttr('action');
                    $('#frmrekap').attr('action', '../reportpdf/rekappergolongan.php');
                    $('#tanggal2').removeAttr('required');
                    $('#tanggal2').css('visibility', 'hidden');                     
                    break;
                case "3":
                   // alert('baru');
                    $('#frmrekap').removeAttr('action');
                    $('#frmrekap').attr('action', '../reportpdf/rekapperkoordinator.php');
                   $('#tanggal2').css('visibility', 'hidden');
                   $('#tanggal2').removeAttr('required');
                    break;
            }

        }

        $('#tanggal').datepicker({
            format: "dd/mm/yyyy"
        });
        $('#tanggal2').datepicker({
            format: "dd/mm/yyyy"
        });
        //$('#tanggal').datepicker(type.format);


        $('#rekapmenu').attr('class', 'active');
        $('#laporan').attr('class', 'active');
        setInterval(function() {
            $("#tutup").click();
        }, 3000);
        //owl carousel

        $(document).ready(function() {
            $('#kode').typeahead({
                name: 'kode',
                local: [<?php
        include '../../config/kon.php';
        $kode = mysql_query('SELECT kode FROM inv_inventaris WHERE STATUS=1');
        while ($result = mysql_fetch_array($kode)) {
            echo "'" . $result['kode'] . "',";
        }
        ?>]
            });
//        $('#kode').typeahead({
//            name: 'allcountry',
//            prefetch: 'getdate.php?tipe=1&query=%QUERY'
//        });

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

        function cari(teks)
        {
            var kode = teks.value;
            if (!kode)
                return;
            xmlhttp.open('get', 'getdata.php?tipe=2&kode=' + kode, true);
            xmlhttp.onreadystatechange = function() {
                if ((xmlhttp.readyState == 4) && (xmlhttp.status == 200))
                {
                    var r = xmlhttp.responseXML.getElementsByTagName('data');
                    document.getElementById("nama").value = r[0].firstChild.data;
                    document.getElementById("spesifikasi").value = r[1].firstChild.data;
                    document.getElementById("instansiasal").value = r[2].firstChild.data;
                    document.getElementById("instansi1").value = r[3].firstChild.data;
                    document.getElementById("prg").style.visibility = "hidden";
                }
                return false;
            }
            xmlhttp.send(null);
            document.getElementById("prg").style.visibility = "visible";
        }
    </script>

</body>
</html>
