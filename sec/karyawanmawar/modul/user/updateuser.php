<?php include '../../session/level2.php'; ?>
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
                                    Edit User
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';

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
                                    ?>
                                    <div class="form">
                                        <form id="inputinv" class="cmxform form-horizontal tasi-form" method="post" action="saveuser.php?jenis=update">
                                            <?php
                                            include '../../config/kon.php';
                                            $username = isset($_GET['id']) ? $_GET['id'] : '';
                                            $q = mysql_query("SELECT a.nik, b.nama, a.otorisasi as kodeotorisasi, c.otorisasi FROM umum_user a, karyawan_master b, user_otorisasi c WHERE a.nik=b.nik and a.otorisasi=c.id and a.nik='$username';");
                                            $row = mysql_fetch_array($q);
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">NIK</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nik" required id="kode" readonly autofocus="" placeholder="NIK" value="<?php echo $row['nik'] ?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Nama</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="nama" required id="nama" readonly="" placeholder="Nama" value="<?php echo $row['nama'] ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Otorisasi</label>
                                                <div class="col-sm-5">
                                                    <select class="form-control" name="otorisasi" id="otorisasi" required>
                                                        <?php
                                                        $qotorisasi = mysql_query("select * from user_otorisasi");
                                                        while ($row1 = mysql_fetch_array($qotorisasi)) {
                                                            if ($row1['id'] == $row['kodeotorisasi']) {
                                                                echo "<option selected value='" . $row1['id'] . "'>" . $row1['otorisasi'] . "</option>";
                                                            } else {
                                                                echo "<option value='" . $row1['id'] . "'>" . $row1['otorisasi'] . "</option>";
                                                            }
                                                        }
                                                        ?>
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
        $('#menuuser').attr('class', 'active');
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
