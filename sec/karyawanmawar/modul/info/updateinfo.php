<?php include'../../session/level2.php'; ?>
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
                        <div class="col-lg-8">
                            <section class="panel">

                                <header class="panel-heading">
                                    Input Info
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $idInfo = isset($_GET['id']) ? $_GET['id'] : '';
                                    //$un = isset($_GET['un']) ? $_GET['un'] : '';
                                    if ($status == "sukses") {

                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        <p>Data baru berhasil disimpan...<br>
                                    </div>";
                                    } else if ($status == "gagal") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutuperror' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-remove'></i>
                                            Gagal
                                        </h4>
                                        <p>Proses gagal</p>
                                        <p>Error:" . $ket . "</p>
                                    </div>";
                                    } else if ($status == "hapus") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-signout'></i>
                                            Sukses
                                        </h4>
                                        <p>Data berhasil dihapus</p>
                                        </div>";
                                    }
                                    if (!empty($idInfo)) {
                                        $q = mysql_query("select * from umum_info where id='$idInfo'");
                                        $hasil = mysql_fetch_array($q);
                                        $info = $hasil['info'];
                                        $id = $hasil['id'];
                                        $status = $hasil['status'];
                                    }
                                    ?>

                                    <div class="form" id="editInfo">
                                        <form id="input" class="cmxform form-horizontal tasi-form" method="post" action="saveinfo.php?jenis=update">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Informasi</label>
                                                <div class="col-sm-5">
                                                    <input type="text" class="form-control" name="info" required  placeholder="Informasi" value="<?php echo $info; ?>">
                                                    <input type="hidden" class="form-control" name="idinfo"  placeholder="Informasi" value="<?php echo $idInfo; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Status</label>
                                                <div class="col-sm-3">
                                                    <select name="status" id="status" required="" class="form-control">
                                                        <option value="">Pilih:</option>
                                                        <option value="1">Aktif</option>
                                                        <option value="0">Non-Aktif</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-4 col-lg-6">
                                                    <button type="submit" id="tombol" class="btn btn-danger">Simpan</button>
                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                </div>
                                        </form>

                                    </div>
                            </section>

                        </div>
                    </div>
                    <div class="row state-overview">
                    </div>
                </section>

            </section>
        </section>
        <!--main content end-->
        <!--footer start-->
        <?php include '../../template/foot.php' ?>
        <!--footer end-->
    </section>
    <?php include '../../template/footer.php' ?>

    <script>
        $('#menuinfo').attr('class', 'active');
        $('#master').attr('class', 'active');
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

        function idConfirm(idcon) {
            $('#idconfirm').val(idcon);
            $('#pesan').text('Anda yakin akan menghapus Instansi dengan KODE ' + idcon);
            // alert(idcon);
        }
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
