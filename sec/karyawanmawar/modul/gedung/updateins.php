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
                                    Update
                                </header>
                                <div class="panel-body">
                                    <?php
                                    include '../../config/kon.php';
                                    $id = isset($_GET['id']) ? $_GET['id'] : '';
                                    $q = mysql_query("select * from umum_gedung where kode='$id'");
                                    while ($hasil = mysql_fetch_array($q)) {
                                        $kode = $hasil['kode'];
                                        $instansi = $hasil['gedung'];
                                    }
                                    ?>
                                    <form name="fr" id="fr" class="form-inline" role="form" method="post" action="saveins.php?jenis=update">
                                        <div class="form-group">
                                            <label class="sr-only" for="">Kode</label>
                                            <input type="type" class="form-control" value="<?php echo "$kode"; ?>" id="kode" name="kode">
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="">Gedung</label>
                                            <input type="text" class="form-control" value="<?php echo "$instansi"; ?>" id="instansi" name="gedung">
                                        </div>

                                        <button type="submit" class="btn btn-success">Simpan</button>
                                        <button type="reset" class="btn btn-default">Cancel</button>
                                    </form>

                                </div>
                            </section>

                        </div>
                    </div>
                    <div class="row state-overview">


                       
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
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

    </script>
    <script>

        //owl carousel
        $('#menuinstansi').attr('class','active');
        $('#master').attr('class','active');
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
