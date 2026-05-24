<?php include'../../session/level2.php';?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">

        <title>Jual | Pondok Pesantren Matholi'ul Anwar</title>

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
                                <header class="panel-heading">Jual Inventaris</header>
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
                                        <form id="frmjual" class="cmxform form-horizontal tasi-form" method="post" action="prosesjual.php">
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
                                                    <input type="text" readonly="" class="form-control" name="nama" required id="nama" placeholder="Nama">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Spesifikasi</label>
                                                <div class="col-sm-5">
                                                    <input type="text" readonly="" class="form-control" name="spesifikasi" required id="spesifikasi" placeholder="Spesifikasi">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Instansi</label>
                                                <div class="col-sm-5">
                                                    <input type="text" readonly="" class="form-control" name="instansi1" required id="instansi1" placeholder="Instansi">
                                                    <input type="hidden" class="form-control" name="instansiasal" id="instansiasal">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Harga</label>
                                                <div class="col-sm-5">
                                                    <input type="text" readonly="" class="form-control" name="harga" required id="harga" placeholder="Harga">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nilai Buku</label>
                                                <div class="col-sm-5">
                                                    <input type="text" readonly="" class="form-control" name="nilaibuku" required id="nilaibuku" placeholder="Nilai Buku">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Harga Jual</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" onkeydown="return numbersonly(this,event)" onkeyup="javascript: tandaPemisahTitik(this)" name="dijual" required id="dijual" placeholder="Harga Jual">
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-2 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" class="form-control"  name="keterangan" id="keterangan" placeholder="Keterangan"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-2 col-lg-6">
                                                    <button id="tombol" class="btn btn-danger" type="submit">Jual</button>
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

            <!-- Modal -->
            
            <!-- modal -->
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
        <script class="include" type="text/javascript" src="../../js/nominal.js"></script>

</script>
<script>
function kirimjual(){
    $('#frmjual').submit();
}
$(document).ready(function() {
    $("#kode").autocomplete("../mutasi/getkode.php", {
        width: 150
    });

    $("#kode").result(function(event, data, formatted) {
        var kode = formatted;
        $.ajax({
            type: "POST",
            data: "kode=" + kode,
            url: "getJual.php",
            dataType: "json",
            success: function(data) {
                $("#nama").val(data.nama);
                $("#spesifikasi").val(data.spesifikasi);
                $("#instansi1").val(data.instansi);
                $("#instansiasal").val(data.kodeins);
                $("#harga").val(data.harga);
                $("#nilaibuku").val(data.nilaibuku);

            }
        });
    });
    $("#kode").keyup(function() {
        var kode = $('#kode').val();
        $.ajax({
            type: "POST",
            data: "kode=" + kode,
            url: "getJual.php",
            dataType: "json",
            success: function(data) {
                $("#nama").val(data.nama);
                $("#spesifikasi").val(data.spesifikasi);
                $("#instansi1").val(data.instansi);
                $("#instansiasal").val(data.kodeins);
                $("#harga").val(data.harga);
                $("#nilaibuku").val(data.nilaibuku);

            }
        });
    });

});
setInterval(function() {
    $("#tutup").click();
}, 3000);

$('#jualmenu').attr('class', 'active');
$('#inventaris').attr('class', 'active');
//owl carousel
function idConfirm() {
    var kode = $('#kode').val();
    var nama = $('#nama').val();
    $('#alas').val(alas);
    $('#idconfirm').val(kode);
    $('#pesan').text('Anda yakin akan MENJUAL Inventaris dengan KODE ' + kode + ' > ' + nama + '');
    // alert(idcon);
}

</script>

</body>
</html>
