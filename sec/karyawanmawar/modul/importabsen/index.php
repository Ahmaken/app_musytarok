<?php
include'../../session/level2.php';
include '../../config/kon.php';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />
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
                        <div class="col-lg-5">
                            <section class="panel">

                                <header class="panel-heading">
                                    Import Absen
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    $row = isset($_GET['row']) ? $_GET['row'] : '';
                                    $gagal = isset($_GET['gagal']) ? $_GET['gagal'] : '';
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
                                        Sukses import sebanyak: " . $row . "<br>
                                        Gagal import : " . $gagal . "<br>
                                      
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
                                        <form id="formimport"  enctype="multipart/form-data" class="cmxform form-horizontal tasi-form" method="post" action="prosabsen.php?jenis=import">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Bulan</label>
                                                <div class="col-sm-5">
                                                    <select class="form-control" name="bulan" id="bulan" required>
                                                        <option selected value="">Pilih:</option>
                                                        <?php
                                                        $date = "d/m/Y";
                                                        $lastmonth = date(('m/Y'), strtotime('last month'));
                                                        $thismonth = date('m/Y');
                                                        $nextmonth = date(('m/Y'), strtotime('next month'));
                                                        echo "<option value='" . $lastmonth . "'>" . $lastmonth . "</option>";
                                                        echo "<option value='" . $thismonth . "'>" . $thismonth . "</option>";
                                                        echo "<option value = '" . $nextmonth . "'>" . $nextmonth . "</option>";
                                                        //  echo "<option>" . $bulan . "</option>";
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Import Absen</label>
                                                <div class="col-sm-8">
                                                    <input name="userfile" class="form-control" type="file">
                                                                                                    <p class="help-block">File Excel harus format *.xls (workbook 95-2003)</p>
                                                    <p class="help-block"><a href="format.xls">Download Format Data</a></p>
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


                        <div class="col-lg-7">
                            <section class="panel">
                                <header class="panel-heading">Realita Absen</header>
                                <div class="panel-body">
                                   <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIK</th>
                                                    <th>Nama</th>
                                                    <th>Bulan</th>
                                                    <th>Jam</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $qdate = mysql_query("select bulan from karyawan_rekapabsen order by id desc limit 1;");
                                                $rowbulan = mysql_fetch_array($qdate);
                                                $bulan = $rowbulan['bulan'];
                                                $i = 1;
                                                $q = mysql_query("select a.nik, b.nama, a.bulan, a.jamabsen from karyawan_rekapabsen a, karyawan_master b where a.nik=b.nik and b.status=1 and a.bulan= '$bulan';");
                                                while ($hasil = mysql_fetch_array($q)) {
                                                   echo "<tr class='gradeX'>"
                                                    . "<td>" . $i++ . "</td>"
                                                    . "<td>" . $hasil['nik'] . "</td>"
                                                    . "<td>" . $hasil['nama'] . "</td>"
                                                    . "<td>" . $hasil['bulan'] . "</td>"
                                                    . "<td>" . $hasil['jamabsen'] . "</td>"
                                                    . "<td><a rel='tooltip-top' title='Edit' href='updateuser.php?id=" . $hasil['nik'] . "'>"
                                                    . "<i class='icon-edit'></i></a> ";
                                                    if ($hasil['status'] == 1) {
                                                        echo "<a rel='tooltip-top' title='Hapus' href='javascript: delet(" . $hasil['nik'] . ")'><i class='icon-remove'></i></a> </td>";
                                                    } else {
                                                        echo "<a rel='tooltip-top' title='Aktifkan' href='javascript: aktif(" . $hasil['nik'] . ")'><i class=' icon-rotate-left '></i></a> </td>";
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                            </thead>
                                        </table> 
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
        function delet(kode) {
            tanya = confirm("Yakin Akun ini dinonaktifkan?");
            if (tanya == 1) {
                window.location.href = "saveuser.php?jenis=del&kode=" + kode;
            }
        }
        function aktif(kode) {
            tanya = confirm("Yakin mengaktifkan akun?");
            if (tanya == 1) {
                window.location.href = "saveuser.php?jenis=aktif&kode=" + kode;
            }
        }
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

        $('#menubisyarah').attr('class', 'active');
        $('#menuimportabsen').attr('class', 'active');

        $(document).ready(function() {

            //ajax
            $("#kode").autocomplete("../karyawan/getkaryawan.php?type=nik", {
                width: 150
            });
            $("#kode").result(function(event, data, formatted) {
                var kode = formatted;
                $.ajax({
                    type: "POST",
                    data: "nik=" + kode,
                    url: "../karyawan/getkaryawan.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#nama").val(data.nama);
                        //$("#umur").val(data.umur);
                    }
                });
            });
            $("#kode").keyup(function() {
                var kode = $('#kode').val();
                $.ajax({
                    type: "POST",
                    data: "nik=" + kode,
                    url: "../karyawan/getkaryawan.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#nama").val(data.nama);
                        // $("#umur").val(data.umur);
                    }
                });
            });
            /*
             * Insert a 'details' column to the table
             */
            var nCloneTh = document.createElement('th');
            var nCloneTd = document.createElement('td');
            nCloneTd.innerHTML = '<img src="../../assets/advanced-datatable/examples/examples_support/details_open.png">';
            nCloneTd.className = "center";



            /*
             * Initialse DataTables, with no sorting on the 'details' column
             */
            var oTable = $('#hidden-table-info').dataTable({
                "aoColumnDefs": [
                    {"bSortable": false, "aTargets": [0]}
                ],
            });

            /* Add event listener for opening and closing details
             * Note that the indicator for showing which row is open is not controlled by DataTables,
             * rather it is done here
             */
            $('#hidden-table-info tbody td img').live('click', function() {
                var nTr = $(this).parents('tr')[0];
                if (oTable.fnIsOpen(nTr))
                {
                    /* This row is already open - close it */
                    this.src = "../../assets/advanced-datatable/examples/examples_support/details_open.png";
                    oTable.fnClose(nTr);
                }
                else
                {
                    /* Open this row */
                    this.src = "../../assets/advanced-datatable/examples/examples_support/details_close.png";
                    oTable.fnOpen(nTr, fnFormatDetails(oTable, nTr), 'details');
                }
            });
        });
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
