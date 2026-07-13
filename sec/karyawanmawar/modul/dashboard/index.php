<?php
include '../../session/level4.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Pondok Pesantren Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->

        <link href="../../css/bootstrap.min.css" rel="stylesheet">
        <link href="../../css/bootstrap-reset.css" rel="stylesheet">
        <!--external css-->
        <link href="../../assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
        <link href="../../assets/advanced-datatable/media/css/demo_page.css" rel="stylesheet" />
        <link href="../../assets/advanced-datatable/media/css/demo_table.css" rel="stylesheet" />
        <!-- Custom styles for this template -->
        <link href="../../css/style.css" rel="stylesheet">
        <link href="../../css/style-responsive.css" rel="stylesheet" />
        <style>
            #harga{
                text-align: right;
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
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <?php
                                include '../../config/kon.php';
                                $q = mysql_query("SELECT DATE_FORMAT(lastlogin, '%d-%m-%Y') AS tgllogin, 
                                                    DATE_FORMAT(lastlogin,'%H:%i:%s') AS jamlogin,
                                                    DATE_FORMAT(lastlogout, '%d-%m-%Y') AS tgllogout, 
                                                    DATE_FORMAT(lastlogout,'%H:%i:%s') jamlogout 
                                                    FROM umum_user WHERE nik='".$_SESSION['username']."'");
                                $hasil=  mysql_fetch_array($q);
                                ?>
                                Assalamualaikum.<br> 
                                Ahlan wasahlan <b><?php echo $_SESSION['nama']; ?></b> di halaman Sistem Informasi Pondok Pesantren Matholi'ul Anwar. <br>
                                Silahkan pilih menu yang tersedia di sebelah kiri.
                                <br>
                                <br>
                                Anda berhasil LOGIN pada jam <?php echo $hasil['jamlogin']; ?> tanggal <?php echo $hasil['tgllogin']; ?>.
                                <br>
                                <br>
                                Terakhir Anda LOGOUT dari aplikasi pada jam <?php echo $hasil['jamlogout']; ?> tanggal <?php echo $hasil['tgllogout']; ?>.
                                <br>
                                <br>
                                Terima kasih telah menggunakan sistem ini.
                                <br>
                                <br>
                                <br>
                               
                                <br>
                                <br>
                            </header>

                        </section>
                    </div>
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">
                                <marquee>
                                    <?php
//                                    include '../../config/kon.php';
//                                    $q = mysql_query("select upper(info) as info from umum_info where status=1");
//                                    while ($row = mysql_fetch_array($q)) {
//                                        echo $row['info'] . " -- ";
//                                    }
                                    ?>
                                </marquee>
                            </header>

                        </section>
                    </div>

                </section>
            </section>
            <br>
            <br>
            <br>
            <br>
            <br>
            <!--main content end-->
            <!--footer start-->
            <?php include '../../template/foot.php' ?>
            <!--footer end-->
        </section>
        <script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.js"></script>
        <script src="../../js/bootstrap.min.js"></script>
        <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="../../js/jquery.scrollTo.min.js"></script>
        <script src="../../js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="../../js/respond.min.js" ></script>
        <script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.dataTables.js"></script>


        <!--common script for all pages-->
        <script src="../../js/common-scripts.js"></script>

        <script type="text/javascript">
            $('#menudashboard').attr('class', 'active');
        </script>



    </body>
</html>
