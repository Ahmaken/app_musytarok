<?php include'../../session/level4.php'; ?>
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
        <!-- Custom styles for this template -->
        <?php include '../../template/head.php'; ?>
        <style>
            #harga{
                text-align: right;
            }
            .datepicker{
                z-index:1151;
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

                                <header class="panel-heading">
                                    Filter by 
                                </header>
                                <div class="panel-body">
                                    <form name="filter" id="filter" class="form-inline" role="form" method="get">
                                        <div class="form-group">
                                            <label class="sr-only" for="">Cabang</label>
                                            <select  class="form-control" id="cabang" name="cabang" required>
                                                <option selected value="all">Seluruh Cabang</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['cabang'])) {
                                                    $getcab = $_GET['cabang'];
                                                    if ($getcab != "all") {
                                                        $cab = mysql_query("select * from umum_cabang where kode='$getcab' ");
                                                        // echo $getcab;
                                                        while ($hasil = mysql_fetch_array($cab)) {
                                                            echo "<option selected value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                        }
                                                    }
                                                }
                                                // $getcab;
                                                $gol = mysql_query("select * from umum_cabang");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <!--                                        <div class="form-group">
                                                                                    <label class="sr-only" for="">Jabatan</label>
                                                                                    <select class="form-control" id="instansi" name="jabatan" required>
                                                                                        <option selected value="all">Seluruh Cabang</option>
                                        <?php
                                        if (isset($_GET['jabatan'])) {
                                            $getjab = $_GET['jabatan'];
                                            if ($getjab != "all") {
                                                $jabat = mysql_query("select * from karyawan_jabatan where kode='$getjab' ");
                                                while ($hasil = mysql_fetch_array($jabat)) {
                                                    echo "<option selected value=" . $hasil['kode'] . ">" . $hasil['jabatan'] . "</option>";
                                                }
                                            }
                                        }
                                        $jabat = mysql_query("select * from karyawan_jabatan");
                                        while ($tampil = mysql_fetch_array($jabat)) {
                                            echo "<option value=" . $tampil['kode'] . ">" . $tampil['jabatan'] . "</option>";
                                        }
                                        ?>
                                                                                    </select>
                                                                                </div>-->
                                        <div class="form-group">
                                            <label class="sr-only" for="">Koordinator</label>
                                            <select class="form-control" id="koordinator" name="koordinator" required>
                                                <option selected value="all">Seluruh Koordinator</option>
                                                <?php
                                                if (isset($_GET['koordinator'])) {
                                                    $getkoor = $_GET['koordinator'];
                                                    if ($getkoor != "all") {
                                                        $koor = mysql_query("select * from karyawan_koordinator where id='$getkoor' ");
                                                        while ($hasil = mysql_fetch_array($koor)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['koordinator'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $koor = mysql_query("select * from karyawan_koordinator");
                                                while ($tampil = mysql_fetch_array($koor)) {
                                                    echo "<option value=" . $tampil['id'] . ">" . $tampil['koordinator'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">Filter</button>

<!--                                        <a href='#' target='_blank' onclick="$(this).closest('form').submit();" id="topdf" class="btn btn-danger"><i class="icon-camera"></i> Cetak</a>-->

                                    
                                    <a href="#myModal-1" data-toggle="modal" id="topdf" class="btn btn-info"><i class="icon-building"></i> Print </a>
                                </form>
                                </div>



                        </div>
                </section>

                </div>
                </div>

                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-1" class="modal fade">
                    <div class="modal-dialog" style="width: 400px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" onclick="reset()" class="close" type="button">×</button>
                                <h4 class="modal-title">To PDF</h4>
                            </div>
                            <div class="modal-body">

                                    <form class="form-horizontal" role="form" id="frmctk" method="post" action="../reportpdf/karyawanpercabang.php?type=percabang" target="_blank">
                                    <div class="form-group">
                                        <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Cabang</label>
                                        <div class="col-lg-8">
                                            <select  class="form-control" id="cabang" name="cabang" required>
                                                <option selected value="all">Seluruh Cabang</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['cabang'])) {
                                                    $getcab = $_GET['cabang'];
                                                    if ($getcab != "all") {
                                                        $cab = mysql_query("select * from umum_cabang where kode='$getcab' ");
                                                        // echo $getcab;
                                                        while ($hasil = mysql_fetch_array($cab)) {
                                                            echo "<option selected value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                        }
                                                    }
                                                }
                                                // $getcab;
                                                $gol = mysql_query("select * from umum_cabang");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                         <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Koordinator</label>
                                            <div class="col-lg-8">
                                         <select class="form-control" id="koordinator" name="koordinator" required>
                                                <option selected value="all">Seluruh Koordinator</option>
                                                <?php
                                                if (isset($_GET['koordinator'])) {
                                                    $getkoor = $_GET['koordinator'];
                                                    if ($getkoor != "all") {
                                                        $koor = mysql_query("select * from karyawan_koordinator where id='$getkoor' ");
                                                        while ($hasil = mysql_fetch_array($koor)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['koordinator'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $koor = mysql_query("select * from karyawan_koordinator");
                                                while ($tampil = mysql_fetch_array($koor)) {
                                                    echo "<option value=" . $tampil['id'] . ">" . $tampil['koordinator'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                            </div>        
                                    </div>
                                    

                                    <div class="form-group">
                                        <div class="col-lg-offset-6 col-lg-8">
                                            <button type="submit" class="btn btn-success">Proses</button>
                                            <button type="reset" class="btn btn-info">Reset</button>
                                        </div>



                                    </div>
                                </form>

                            </div>

                        </div>
                    </div>
                </div>
                <!--end modal-->
                <!--modal 2-->


                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Data Bank Karyawan
                        </header>
                        <div class="panel-body">
                            <div class="adv-table">
                                <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>NIP</th>
                                            <th class="hidden-phone">No KTP</th>
                                            <th class="hidden-phone">Nama</th>
                                            <th class="hidden-phone">TTL</th>
                                            <th class="hidden-phone">Status Alumni</th>
                                            <th class="hidden-phone">Tinggi Badan</th>
                                            <th class="hidden-phone">Berat Badan</th>
                                            <th class="hidden-phone">Status Karyawan</th>
                                            <th class="hidden-phone">Cabang</th>
                                            <th class="hidden-phone">Jabatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $getkoor = isset($_GET['koordinator']) ? $_GET['koordinator'] : "";
                                        $getcabang = isset($_GET['cabang']) ? $_GET['cabang'] : "";

                                        $wherecab = "";
                                        $wherekoor = "";
                                        if ($getcabang != "all" and $getcabang != "") {
                                            $wherecab = " AND b.cabang = '$getcabang'";
                                        }
                                        if ($getkoor != "all" and $getkoor != "") {
                                            $wherekoor = " AND d.kodekoordinator = '$getkoor'";
                                        }
                                        $no = 1;
                                        $q = mysql_query("SELECT a.nik, a.noktp, UPPER(a.nama) AS nama, CONCAT(a.tempatlahir, ', ', DATE_FORMAT(a.tgllahir, '%d/%m/%Y')) AS ttl, a.statusalumni, a.tinggibadan, a.beratbadan,  a.status, b.cabang, d.jabatan FROM karyawan_master a JOIN max_penempatan b ON a.nik=b.nik JOIN umum_cabang c ON b.cabang=c.kode LEFT JOIN max_mutasijabatan d ON a.nik=d.nik WHERE a.status=1" . $wherecab . "" . $wherekoor . " ORDER BY a.id ASC");
                                        //echo "SELECT a.kode, a.nama, a.spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%l/%Y') AS tglperolehan, a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id AND STATUS=1" . $wheregol."".$whereins;
                                        while ($row = mysql_fetch_array($q)) {
                                            echo "<tr><td>" . $no++ . "</td><td>" . $row['nik'] . "</td><td>" . $row['noktp'] . "</td><td>" . $row['nama'] . "</td><td>" . $row['ttl'] . "</td><td>"
                                            . "" . $row['statusalumni'] . "</td><td>" . $row['tinggibadan'] . "</td><td>" . $row['beratbadan'] . "</td><td>" . $row['status'] . "</td><td>" . $row['cabang'] . "</td><td>" . $row['jabatan'] . "</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </section>
                </div>
            </section>
        </section>
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
    <script type="text/javascript" src="../../assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-daterangepicker/daterangepicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
    <script type="text/javascript" src="../../assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
    <!--common script for all pages-->
    <script src="../../js/common-scripts.js"></script>

    <script type="text/javascript">
      
                                                    $(document).keyup(function(e) {

                                                        if (e.keyCode == 27) {
                                                            reset()
                                                        }   // esc
                                                    });
                                                    function reset() {
                                                        $('#frmctk')[0].reset();
                                                        $('#frmctk2')[0].reset();
                                                    }
                                                   
                                                    

                                                    $('#sublaporankaryawan').attr('class', 'active');
                                                    $('#laporan').attr('class', 'active');
                                                    /* Formating function for row details */
                                                    function fnFormatDetails(oTable, nTr)
                                                    {
                                                        var aData = oTable.fnGetData(nTr);
                                                        var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                                                        sOut += '<tr><td>Spesifikasi:</td><td>' + aData[4] + '</td></tr>';
                                                        sOut += '<tr><td>Harga: </td><td>' + aData[6] + '</td></tr>';
                                                        sOut += '<tr><td>Gambar:</td><td></td></tr>';
                                                        sOut += '</table>';
                                                        return sOut;
                                                    }

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
                                                            "aaSorting": [[1, 'asc']]
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



</body>
</html>
