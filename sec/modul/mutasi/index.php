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

        <title>Mutasi | Pondok Pesantern Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->

        <link href="../../css/bootstrap.min.css" rel="stylesheet">
        <link href="../../css/bootstrap-reset.css" rel="stylesheet">
        <!--external css-->
        <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href="../../assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
        <link rel="stylesheet" href="../../css/owl.carousel.css" type="text/css">
        <!-- Custom styles for this template -->
        <link href="../../css/style.css" rel="stylesheet">
        <link href="../../css/style-responsive.css" rel="stylesheet" />
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />


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
                                <header class="panel-heading">Perubahan Ruang Pemegang Inventaris</header>
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
                                        <p>Perubahan berhasil disimpan...</p>
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
                                        <form id="inputinv" class="cmxform form-horizontal tasi-form" method="post" action="proses.php">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Kode</label>

                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="kode" name="kode" placeholder="Masukkan Kode Inventaris" required>
                                                    <span class="help-inline" id="prg" style="visibility: hidden; color: red; position: fixed; font-size: 12px;"> Loading...</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nama</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" disabled name="nama" required id="nama" placeholder="Nama">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Spesifikasi</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" disabled name="spesifikasi" required id="spesifikasi" placeholder="Spesifikasi">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Ruang Lama</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="instansi1" disabled="" required id="instansi1" placeholder="Ruang Lama">
                                                    <input type="hidden" class="form-control" name="instansiasal" id="instansiasal">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Ruang Baru</label>
                                                <div class="col-sm-5">
                                                    <select class="form-control" id="instansi2" required name="instansi2" placeholder="Ruang" required>
                                                        <option selected value="">Instansi</option>
                                                        <?php
                                                        include '../../config/kon.php';
                                                        $ins = mysql_query("select * from umum_instansi");
                                                        while ($tampil = mysql_fetch_array($ins)) {
                                                            echo "<option value=" . $tampil['id'] . ">" . $tampil['instansi'] . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-6">
                                                    <button type="submit" class="btn btn-danger">Simpan</button>
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

            </section>
        </section>
        <!--main content end-->
        <!--footer start-->
        <?php include '../../template/foot.php' ?>
        <!--footer end-->
    </section>
    <script src="../../js/jquery-1.8.3.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="../../js/jquery.scrollTo.min.js"></script>
    <script src="../../js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="../../js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="../../js/respond.min.js" ></script>
    <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
    <script src="../../js/common-scripts.js"></script>
    <script src="../../js/jquery.autocomplete.js"></script>
</script>
<script>
    $(document).ready(function() {
        $("#kode").autocomplete("getkode.php", {
            width: 150
        });

        $("#kode").result(function(event, data, formatted) {
            var kode = formatted;
            $.ajax({
                type: "POST",
                data: "kode=" + kode,
                url: "getdata.php",
                dataType: "json",
                success: function(data) {
                    $("#nama").val(data.nama);
                    $("#spesifikasi").val(data.spesifikasi);
                    $("#instansi1").val(data.instansi);
                    $("#instansiasal").val(data.kodeins);
                }
            });
        });
        $("#kode").keyup(function() {
            var kode = $('#kode').val();
            $.ajax({
                type: "POST",
                data: "kode=" + kode,
                url: "getdata.php",
                dataType: "json",
                success: function(data) {
                    $("#nama").val(data.nama);
                    $("#spesifikasi").val(data.spesifikasi);
                    $("#instansi1").val(data.instansi);
                    $("#instansiasal").val(data.kodeins);
                }
            });
        });

    });
    $('#pengalihan').attr('class', 'active');
    $('#inventaris').attr('class', 'active');
    setInterval(function() {
        $("#tutup").click();
    }, 3000);

</script>

</body>
</html>
