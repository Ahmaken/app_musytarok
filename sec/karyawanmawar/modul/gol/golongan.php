<?php include'../../session/level1.php';?>
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
                                    Golongan
                                </header>
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
                                    <form name="fr" id="fr" class="form-inline" role="form" method="post" action="saveins.php?jenis=save">
                                        <div class="form-group">
                                            <label class="sr-only" for="">Kode</label>
                                            <input type="type" class="form-control" id="kode" name="kode" placeholder="Kode">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="">Golongan</label>
                                            <input type="text" class="form-control" id="instansi" name="golongan" placeholder="Golongan">
                                        </div>

                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <button type="reset" class="btn btn-default">Cancel</button>
                                    </form>

                                </div>
                            </section>

                        </div>
                    </div>
                    <div class="row state-overview">


                        <div class="col-lg-8">
                            <section class="panel">
                                <header class="panel-heading">GOLONGAN</header>
                                <div class="panel-body">
                                    <table class="table table-striped table-advance table-hover">
                                        <thead>
                                            <tr>
                                                <th><i class="icon-book"></i> NOMOR </th>
                                                <th><i class="icon-code"></i> KODE</th>
                                                <th><i class="icon-tasks"></i> GOLONGAN </th>
                                                <th><i class="icon-edit"></i> EDIT</th>
                                                <th></th>
                                            </tr>
                                        <tbody>
                                            <?php
                                            include '../../config/kon.php';
                                            $no = 1;
                                            $q = mysql_query("select id, UPPER(golongan) as golongan from inv_golongan");
                                            while ($show = mysql_fetch_array($q)) {
                                                echo "<tr><td>" . $no++ . "</td><td>" . $show['id'] . "</td><td>" . $show['golongan'] . "</td>"
                                                . "<td><a class='btn btn-primary btn-xs' href='updateins.php?id=" . $show['id'] . "'>"
                                                . "<i class='icon-pencil'></i></a> <a class='btn btn-default btn-xs' "
                                                . "data-toggle='modal' onclick='idConfirm(" . $show['id'] . ");' href='#modconfirm'><i class='icon-remove'></i></a></td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                        <!-- Modal -->
                                        <div class="modal fade" id="modconfirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title">Hapus data</h4>
                                                    </div>
                                                    <div id="pesan" class="modal-body">



                                                    </div>
                                                    <div class="modal-footer">
                                                        <form name="confirm" id="confirm" method="post" action="saveins.php?jenis=del">
                                                            <input type="hidden" name="kode" id="idconfirm"/>
                                                            <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                                            <button class="btn btn-warning" type="submit"> Confirm</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- modal -->
                                        </thead>
                                    </table> 
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
<?php include '../../template/footer.php' ?>

    <script>
        $('#menugedung').attr('class','active');
        $('#master').attr('class','active');
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
