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
                                    Pendaftar
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    if ($status == "update") {
                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        <p>Data berhasil diperbarui...</p>
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
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Kode</th>
                                                    <th>Nama</th>
                                                    <th>TTL</th>
                                                    <th>Alamat</th>
                                                    <th>Kecamatan</th>
                                                    <th>Kota/Kab</th>
                                                    <th>Telepon</th>
                                                    <th>Tgl Daftar</th>
                                                    <th>Status</th>
                                                  <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                require_once '../../config/security.php';
                                                $i = 1;
                                                $idcryp = md5('id');
                                                $q = mysql_query("SELECT a.id, a.idmawar, a.NomorDaftar, a.nama,  CONCAT(a.tempatlahir, ', ', DATE_FORMAT(a.tanggallahir, '%d/%m/%Y')) AS ttl, 
                                                        CONCAT(a.dusun, ' ', a.desa) AS alamat, d.kecamatanNama, c.kabupatenNama, CONCAT(a.WKontakHP1,' ', a.WKontakHP2) AS hp, 
                                                        DATE_FORMAT(a.TanggalDaftar, '%d/%m/%Y') AS tanggaldaftar, a.operator, b.proses FROM sekretariat_datasantri a, reg_pembayaran b, kabupaten c, kecamatan d WHERE a.status=1 AND a.kecamatan=d.kecamatanId AND a.kabupaten=c.kabupatenId
                                                        AND a.NomorDaftar=b.kode ORDER BY a.tanggaldaftar;");
                                                while ($hasil = mysql_fetch_array($q)) {
                                                    $idbaris = urlencode(encryptIt($hasil['id']));
                                                    $nomordaftar=$hasil['NomorDaftar'];
                                                    if($hasil['proses']==2){
                                                        $statusdaftar="Belum Aktif";
                                                    } elseif ($hasil['proses']>2){
                                                        $statusdaftar=="Aktif";
                                                    }
                                                    echo "<tr class='gradeA'>"
                                                    . "<td>" . $i++ . "</td>"
                                                    . "<td>" . $hasil['idmawar'] . "</td>"
                                                    . "<td>" . $hasil['nama'] . "</td>"
                                                    . "<td>" . $hasil['ttl'] . "</td>"
                                                    . "<td>" . $hasil['alamat'] . "</td>"
                                                    . "<td>" . $hasil['kecamatanNama'] . "</td>"
                                                    . "<td>" . $hasil['kabupatenNama'] . "</td>"
                                                    . "<td>" . $hasil['hp'] . "</td>"
                                                    . "<td>" . $hasil['tanggaldaftar'] . "</td>"
                                                    . "<td>" . $statusdaftar. "</td>"
                                                    . "<td><a rel='tooltip-top' target='_blank' title='Edit' href='../reportpdf/salinanform.php?type=bykode&kodereg=$nomordaftar'>"
                                                    . "<i class='icon-print'></i></a> <a rel='tooltip-top' title='Edit' href='uppendaftar.php?$idcryp=$idbaris'>"
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
                tanya = confirm("Yakin non aktifkan data?");
                if (tanya == 1) {
                    window.location.href = "prosanggota.php?type=delete&id=" + nik;
                }
            }
            $(document).keyup(function(e) {

                if (e.keyCode == 27) {
                    reset()
                }   // esc
            });


            $('#menutransaksi').attr('class', 'active');
            $('#submenupendaftar').attr('class', 'active');
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
                        {"bSortable": true, "aTargets": [0]}
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
            setInterval(function() {
                $("#tutup").click();
            }, 3000);
        </script>



    </body>
</html>
