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

        <title>ANGGOTA Pondok Pesantern Matholi'ul Anwar - Mutasi Harian</title>

        <!-- Bootstrap core CSS -->
        <?php include '../../template/head.php'; ?>



        <!-- Custom styles for this template -->

        <style>
            .datepicker{
                z-index:1151;
            }
            #harga{
                text-align: right;
            }
            th{
                text-align: center;
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
                    <div class="row">
                        <div class="col-lg-8">
                            <section class="panel">

                                <header class="panel-heading">
                                    Filter by
                                </header>
                                <div class="panel-body">
                                    <form name="filter" id="filter" class="form-inline" role="form" method="get">
                                        <div class="form-group">
                                            <label class="sr-only" for="">Tanggal</label>
                                            <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal1" name="tanggal" placeholder="Tanggal" required="" value="<?php echo $_GET['tanggal'] ?>">
                                        </div>
                                        s.d 
                                        <div class="form-group">
                                            <label class="sr-only" for="">Tanggal</label>
                                            <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal2" name="tanggal2" placeholder="Tanggal" required="" value="<?php echo $_GET['tanggal2'] ?>">
                                        </div>
                                        <div class="form-group">
                                        </div>
                                        <button type="submit" class="btn btn-success" style="margin-left: 20px;">Filter</button>
                                        <a href="#myModal-2" data-toggle="modal" id="topdf" class="btn btn-danger"><i class="icon-camera"></i> Print </a>
<!--                                        <a href="#myModal-1" data-toggle="modal" id="topdf" class="btn btn-info"><i class="icon-building"></i> Print Koordinator</a>-->
                                </div>
                            </section>
                            </form>

                        </div>
                </section>

                </div>

                </div>

                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Laporan Mutasi
                        </header>
                        <div class="panel-body">
                            <h4>Tanggal <?php echo $_GET['tanggal'] . " s.d " . $_GET['tanggal2'] ?></h4>
                            <section id="unseen">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Sandi</th>
                                            <th>No Transaksi</th>
                                            <th>Kode Anggota</th>
                                            <th>Nama Anggota</th>
                                            <th class="numeric">Debet</th>
                                            <th class="numeric">Kredit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $operator = $_SESSION['username'];
                                        $tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : "";
                                        $tanggal = explode('/', $tanggal);
                                        $tanggal = $tanggal[2] . "-" . $tanggal[1] . "-" . $tanggal[0];
                                        $tanggal2 = isset($_GET['tanggal2']) ? $_GET['tanggal2'] : "";
                                        $tanggal2 = explode('/', $tanggal2);
                                        $tanggal2 = $tanggal2[2] . "-" . $tanggal2[1] . "-" . $tanggal2[0];
                                        if (isset($tanggal)) {
                                            $iftanggal = " and a.tanggal between '$tanggal' and '$tanggal2' ";
                                        } else {
                                            $iftanggal = " and a.tanggal = '$tanggal'";
                                        }

                                        $operator = $_SESSION['username'];
                                        $no = 1;
                                        $q = mysql_query("SELECT CONCAT(DATE_FORMAT(a.tanggal, '%d-%m-%Y'),' ' ,a.jam) AS tanggal, a.jenis, a.kode, a.kode_anggota, b.nama, a.debet, a.kredit  FROM anggota_simpanan a, anggota_master b WHERE a.kode_anggota=b.kode " . $iftanggal . " and a.operator='$operator' ORDER BY a.tanggal, a.jam ASC");
                                        while ($row = mysql_fetch_array($q)) {
                                            $totaldebet[] = $row['debet'];
                                            $totalkredit[] = $row['kredit'];
                                            echo "<tr><td>" . $no++ . "</td>"
                                            . "<td>" . $row['tanggal'] . "</td>"
                                            . "<td>" . $row['jenis'] . "</td>"
                                            . "<td>" . $row['kode'] . "</td>"
                                            . "<td>" . $row['kode_anggota'] . "</td>"
                                            . "<td>" . $row['nama'] . "</td>"
                                            . "<td id='harga'>" . number_format($row['debet'], 0, '.', '.') . "</td>"
                                            . "<td id='harga'>" . number_format($row['kredit'], 0, '.', '.') . "</td></tr>";
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="6" id="harga"><b>Jumlah</b></td>
                                            <td id="harga"><b><?php echo number_format(array_sum($totaldebet), 0, '.', '.'); ?></b></td>
                                            <td id="harga"><b><?php echo number_format(array_sum($totalkredit), 0, '.', '.'); ?></b></td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" id="harga"><b>SALDO</b></td>
                                            <td colspan ='2' id="harga"><b><?php echo number_format(array_sum($totaldebet)-array_sum($totalkredit), 0, '.', '.'); ?></b></td>
                                            
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="form-group" style="padding-left: 20px; ">

                                </div>
                            </section>
                        </div>

                    </section>
                </div>
            </section>
        </div>
    </div>

</section>


<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-2" class="modal fade">
    <div class="modal-dialog" style="width: 400px;">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" onclick="reset()"class="close" type="button">×</button>
                <h4 class="modal-title">To PDF</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" role="form" id="frmctk2" method="post" action="../reportpdf/lapmutasi.php" target="_blank">
                    <div class="form-group">
                        <label for="" class="col-lg-4 col-sm-4 control-label">Dari Tgl</label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal1" name="tanggal" placeholder="Tanggal" required="" value="<?php echo $_GET['tanggal'] ?>">

                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tanggal1" class="col-lg-4 col-sm-4 control-label">S.d Tgl</label>
                        <div class="col-lg-6">
                            <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal2" name="tanggal2" placeholder="Tanggal" required="" value="<?php echo $_GET['tanggal2'] ?>">
                            <input type="hidden" name="filter" value="1">
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
<script src="../../js/common-scripts.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="../../assets/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script type="text/javascript" src="../../assets/jquery-multi-select/js/jquery.multi-select.js"></script>
<script src="../../js/advanced-form-components.js"></script>
<script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
<script src="../../js/jquery.customSelect.min.js" ></script>
<script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.dataTables.js"></script>    

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
                    $('#tanggal1').datepicker({
                        format: "dd/mm/yyyy"
                    });
                    $('#goltanggal1').datepicker({
                        format: "dd/mm/yyyy"
                    });
                    $('#goltanggal2').datepicker({
                        format: "dd/mm/yyyy"
                    });
                    $('#tanggal2').datepicker({
                        format: "dd/mm/yyyy"
                    });
                    function toPdf() {
//                                              
                        var instansi = $('#instansi').val();
                        var golongan = $('#golongan').val();

                        window.open('../reportpdf/penyusutanpdf.php?golongan=' + golongan + '&instansi=' + instansi);

                    }
                    $('#menuview').attr('class', 'active');
                    $('#submenumutasiuser').attr('class', 'active');
                    /* Formating function for row details */
                    function fnFormatDetails(oTable, nTr)
                    {
                        var aData = oTable.fnGetData(nTr);
                        var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                        sOut += '<tr><td>Rendering engine:</td><td>' + aData[3] + ' ' + aData[4] + '</td></tr>';
                        sOut += '<tr><td>Link to source:</td><td>Could provide a link here</td></tr>';
                        sOut += '<tr><td>Extra info:</td><td>And any further details here (images etc)</td></tr>';
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

                        $('#hidden-table-info thead tr').each(function() {
                            this.insertBefore(nCloneTh, this.childNodes[0]);
                        });

                        $('#hidden-table-info tbody tr').each(function() {
                            this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
                        });

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
