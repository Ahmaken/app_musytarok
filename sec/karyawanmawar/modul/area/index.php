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
                                <header class="panel-heading"><b>Area</b></header>
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
                                        <form id="inputinv" class="cmxform form-horizontal tasi-form" method="post" action="prosarea.php?type=save">
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Kode Area</label>
                                                <div class="col-sm-5">
                                                    <input id="kode" name="kode" autofocus="" placeholder="Kode Area" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Nama Area</label>
                                                <div class="col-sm-5">
                                                    <input id="kode" name="nama"  placeholder="Nama Area" class="form-control" required="" type="text" value="" />
                                                </div>
                                            </div>   
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea id="kode" name="keterangan"  placeholder="Keterangan" class="form-control" required="" type="text"></textarea>
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <div class="col-lg-offset-4 col-lg-6">
                                                    <button type="submit" class="btn btn-danger">Simpan</button>
                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                </div>
                                        </form>
                                    </div>
                                </div>

                            </section>

                        </div>
                        <!-- Table -->
                        <div class="col-lg-6">
                            <section class="panel">
                                <header class="panel-heading">
                                    Data Area
                                </header>
                                <div class="panel-body">
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Area</th>
                                                    <th>Keterangan</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $q = mysql_query("SELECT id, kode,namaarea, keterangan FROM umum_area ORDER BY id desc;");
                                                while ($hasil = mysql_fetch_array($q)) {
                                                    echo "<tr class='gradeX'>"
                                                    . "<td>" . $i++ . "</td>"
                                                    . "<td>" . $hasil['kode'] . "</td>"
                                                    . "<td>" . $hasil['namaarea'] . "</td>"
                                                    . "<td>" . $hasil['keterangan'] . "</td>"
                                                    . "<td><a rel='tooltip-top' title='Edit' href='uparea.php?id=" . $hasil['id'] . "'>"
                                                    . "<i class='icon-edit'></i></a> <a rel='tooltip-top' title='Hapus' href='javascript: delet(" . $hasil['id'] . ")'><i class='icon-remove'></i></a> </td>";
                                                }
                                                ?>
                                            </tbody>
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
        function delet(nip) {
            tanya = confirm("Yakin delete data?");
            if (tanya == 1) {
                window.location.href = "prosarea.php?type=delete&kode=" + nip;
            }
        }
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

        $('#master').attr('class', 'active');
        $('#menukamar').attr('class', 'active');

        $(document).ready(function() {
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
            $("#nomorrm").autocomplete("getpasien.php?type=rm", {
                width: 150
            });
            $("#nomorrm").result(function(event, data, formatted) {
                var kode = formatted;
                $.ajax({
                    type: "POST",
                    data: "rm=" + kode,
                    url: "getpasien.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#nama").val(data.nama);
                        $("#umur").val(data.umur);
                    }
                });
            });
            $("#nomorrm").keyup(function() {
                var kode = $('#nomorrm').val();
                $.ajax({
                    type: "POST",
                    data: "rm=" + kode,
                    url: "getpasien.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#nama").val(data.nama);
                        $("#umur").val(data.umur);
                    }
                });
            });
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
