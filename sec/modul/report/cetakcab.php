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

        <title>Pondok Pesantern Matholi'ul Anwar</title>

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
						<div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading">
                                    Data Cabang
                                </header>
                                <div class="panel-body">
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Cabang</th>
                                                    <th>Alamat</th>
                                                    <th>Email</th>
                                                    <th>NPP</th>
                                                    <th>Usaha</th>
                                                    <th>Tgl. Berdiri</th>
													
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i = 1;
												$q = mysql_query("SELECT * FROM umum_cabang ORDER BY id desc;");
                                                while ($hasil = mysql_fetch_array($q)) {
													
                                                    echo "<tr class='gradeX'>"
                                                    . "<td>" . $i++ . "</td>"
                                                    . "<td>" . $hasil['kode'] . "</td>"
                                                    . "<td>" . $hasil['nama'] . "</td>"
                                                    . "<td>" . $hasil['alamat'] . "</td>"
                                                    . "<td>" . $hasil['email'] . "</td>"
                                                    . "<td>" . $hasil['npp'] . "</td>"
                                                    . "<td>" . $hasil['usaha'] . "</td>"
                                                    . "<td>" . $hasil['tanggalberdiri'] . "</td>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </section>
                        </div>
 <!--                       <div class="col-lg-12">
                            <section class="panel">
                                <header class="panel-heading">
                                    Pegawai
                                </header>
                                <div class="panel-body">
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>NIK</th>
                                                    <th>Nama</th>
                                                    <th>TTL</th>
                                                    <th>Alamat</th>
                                                    <th>Kota</th>
                                                    <th>Provinsi</th>
                                                    <th>Telepon</th>
                                                    
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $i = 1;
                                                $q = mysql_query("SELECT a.id, a.nik, a.nama, CONCAT(a.tempatlahir,', ', DATE_FORMAT(a.tgllahir, '%d-%m-%Y')) AS ttl, "
                                                        . "CONCAT(a.alamat, ' ', a.desa, ' ', a.kecamatan) AS alamat, a.kota, a.provinsi, CONCAT(a.hp1,', ', a.hp2) AS telp "
                                                        . " FROM karyawan_master a WHERE a.status=1 ORDER BY nik ASC;");
                                                while ($hasil = mysql_fetch_array($q)) {
                                                    echo "<tr class='gradeA'>"
                                                    . "<td>" . $i++ . "</td>"
                                                    . "<td>" . $hasil['nik'] . "</td>"
                                                    . "<td>" . $hasil['nama'] . "</td>"
                                                    . "<td>" . $hasil['ttl'] . "</td>"
                                                    . "<td>" . $hasil['alamat'] . "</td>"
                                                    . "<td>" . $hasil['kota'] . "</td>"
                                                    . "<td>" . $hasil['provinsi'] . "</td>"
                                                    . "<td>" . $hasil['telp'] . "</td>"
                                                    
                                                    . "<td><a rel='tooltip-top' title='Edit' href='upkar.php?id=" . $hasil['id'] . "'>"
                                                    . "<i class='icon-edit'></i></a> <a rel='tooltip-top' title='Hapus' href='javascript: delet(" . $hasil['id'] . ")'><i class='icon-remove'></i></a> </td>";
                                                }
                                                ?>


                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                            </section>
                        </div> -->
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
            function delet(nik) {
                tanya = confirm("Yakin delete data?");
                if (tanya == 1) {
                    window.location.href = "proskaryawan.php?type=delete&id=" + nik;
                }
            }
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

            $('#menupegawai').attr('class', 'active');
            $('#submenudatapegawai').attr('class', 'active');
            /* Formating function for row details */

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
                    ]

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
