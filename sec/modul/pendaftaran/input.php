<?php include'../../session/level3.php'; ?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <title>Pondok Pesantren Matholiul Anwar</title>

        <!-- Bootstrap core CSS -->
        <!-- Custom styles for this template -->
        <?php include '../../template/head.php'; ?>
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />
        <style>
            #harga{
                text-align: left;
            }
            .datepicker{
                z-index:1151;
            }
            .tt-hint{
                font-size: 14px;
            }
            .tt-dropdown-menu {
                width: 200px;
                margin-top: 5px;
                padding: 8px 12px;
                background-color: #fff;
                border: 1px solid #ccc;
                border: 1px solid rgba(0, 0, 0, 0.2);
                border-radius: 8px 8px 8px 8px;
                font-size: 15px;
                color: #111;
                background-color: #F1F1F1;
            }
        </style>
    </head>

    <body>

        <section id="container" >
            <!--header start-->

            <!--header end-->
            <!--sidebar start-->
            <?php include'../../template/header.php'; ?>

            <?php include'../../template/side.php'; 
            include './getalamat.php';?>
         
            <!--sidebar end-->
            <!--main content start-->

            <section id="main-content">
                <section class="wrapper">
                    <!--state overview start-->
                    <div class="row state-overview">
                        <div class="col-lg-8">
                            <section class="panel">
                                <header class="panel-heading">
                                    Input Pendaftaran
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    $operator = $_SESSION['username'];
                                    include '../../config/kon.php';
                                    $kodedaftar = $_POST['kodedaftar'];
                                    $cekpendaftaran = mysql_query("SELECT DATE_FORMAT(tanggal, '%d-%m-%Y') AS tanggal, kode, namawali, alamat, administrasi, pangkal, gedung, proses, status FROM reg_pembayaran WHERE STATUS=1  AND kode='$kodedaftar'");
                                    $cekdaftar = mysql_num_rows($cekpendaftaran);
                                    $cekprosesdaftar = mysql_fetch_array($cekpendaftaran);
                                    if (!empty($kodedaftar)) {
                                        if ($cekdaftar == '0') {
                                            echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutuperror' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon--sign'></i>
                                            Gagal
                                        </h4>
                                        <p>Kode Pendaftaran Tidak Dikenal</p>
                                        
                                    </div>";
                                        } elseif ($cekprosesdaftar['proses'] > '1') {
                                            echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutuperror' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon--sign'></i>
                                            Gagal
                                        </h4>
                                        <p>Maaf Kode Pendaftaran $kodedaftar sudah diinput.</p>
                                        
                                    </div>";
                                        }
                                    }
                                    if ($status == "sukses") {
                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        <p>Data telah disimpan...</p>
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
                                    } else if ($status == "hapus") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon--sign'></i>
                                            Hapus Sukses.
                                        </h4>
                                        <p>Data berhasil dihapus</p>
                                       </div>";
                                    }
                                    ?>
                                    <div class="form" id="addpenerimaan">
                                        <?php
                                        if ($cekdaftar == '1' && $cekprosesdaftar['proses'] == '1') {
                                            include './formpendaftaran.php';
                                        } else {

                                            echo "<form id='pendaft' name='formdaf' class='cmxform form-horizontal tasi-form' method='post'>    
                                                 
                                            <div class='form-group'>
                                                <label class='col-sm-3 control-label'>Kode Pendaftaran</label>
                                                <div class='col-sm-4'>
                                                    <input id='kodedaftar' autofocus name='kodedaftar' autofocus='' placeholder='Kode Pendaftaran' class='form-control' required='' type='text' value='' />
                                                </div>
                                              
                                            </div>
                                             
                                             <div class='form-group'>
                                                <div class='col-lg-offset-4 col-lg-6'>
                                                    <button type='submit' id='tombol' class='btn btn-success'>Proses</button>
                                                    <button type='reset' id='cancel' class='btn btn-default'>Cancel</button>
                                                </div>
                                            </div>
                                            </form>";
                                        }
                                        ?>
                                    </div>
                            </section>
                        </div>
                        <!--                             table kiri   -->

                    </div>

                </section>
            </section>
            <!--main content end-->
            <!--modal proses-->
            <div class="modal fade" id="modProses" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Proses?</h4>
                        </div>
                        <div id="" class="modal-body">
                            Anda yakin pembelian ini akan diproses? 
                        </div>
                        <div class="modal-footer">
                            <form name="confirmdelete" id="confirmproses" method="post" action="prospembelian.php?type=proses">
                                <input type="hidden" name="kodesp" value="<?php echo $tampil['supplier'] ?>">
                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                <button class="btn btn-warning" type="submit"> Confirm</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--end modal proses-->

            <!--            modal delete-->

            <!--footer start-->
            <?php include '../../template/foot.php' ?>
            <!--footer end-->
        </section>
        <script src="../../js/jquery.js"></script>
        <script src="../../js/jquery-1.8.3.min.js"></script>
        <script src="../../js/jquery.maskedinput.min.js"></script>
        <script src="../../js/bootstrap.min.js"></script>
        <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
        <script src="../../js/jquery.scrollTo.min.js"></script>
        <script src="../../js/jquery.nicescroll.js" type="text/javascript"></script>
        <script src="../../js/jquery.sparkline.js" type="text/javascript"></script>
        <script src="../../assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
        <script src="../../js/owl.carousel.js" ></script>
        <script src="../../js/jquery.customSelect.min.js" ></script>
        <script src="../../js/respond.min.js" ></script>
        <script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
        <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
        <script type="text/javascript" src="../../assets/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
        <script class="include" type="text/javascript" src="../../js/nominal.js"></script>

        <!--common script for all pages-->
        <script src="../../js/common-scripts.js"></script>

        <!--script for this page-->
        <script type="text/javascript" src="../../js/jquery.validate.min.js"></script>
        <script src="../../js/form-validation-script.js"></script>

        <script type="text/javascript">
            jQuery(function ($) {
                $("#tgldaftar").mask("99-99-9999");
                $("#tanggallahir").mask("99-99-9999");
            });
            function popupsup() {
                kodesup = document.formsup.kodesp;
                namasup = document.formsup.namasp;
                dataitem = window.open("supplier.php", "", "width=100", "height=50");
                dataitem.kodesupplier = kodesp;
                dataitem.namasupplier = namasp;
                //dataitem.hargaobat = hargaobat;
            }
            function delet(kode) {
                tanya = confirm("Yakin delete data?");
                if (tanya == 1) {
                    window.location.href = "prospembelian.php?type=delete&kode=" + kode;
                }
            }
//         
            //get pegawai

            $(document).keyup(function (e) {

                if (e.keyCode == 27) {
                    reset();
                }   // esc
            });
            function reset() {
                $('#frmctk')[0].reset();
                $('#frmctk2')[0].reset();
            }
            $('#menutransaksi').attr('class', 'active');
            $('#submenuinput').attr('class', 'active');
            /* Formating function for row details */
            function fnFormatDetails(oTable, nTr)
            {
                var aData = oTable.fnGetData(nTr);
                var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                sOut += '<tr><td>Nama:</td><td>' + aData[4] + '</td></tr>';
                sOut += '</table>';

                return sOut;
            }

            $(document).ready(function () {
                $('#provinsi').change(function () {
                    $.getJSON('getalamat.php', {action: 'getKab', kode_prop: $(this).val()}, function (json) {
                        $('#kabupaten').html('');
                        $('#kabupaten').append("<option value=''>Kabupaten/Kota:</option>");
                        $.each(json, function (index, row) {
                            $('#kabupaten').append('<option value=' + row.kodekab + '>' + row.namakab + '</option>');
                        });
                    });
                });

                $('#kabupaten').change(function () {
                    $.getJSON('getalamat.php', {action: 'getKec', kode_kab: $(this).val()}, function (json) {
                        $('#kecamatan').html('');
                        $('#kecamatan').append("<option value=''>Kecamatan:</option>");
                        $.each(json, function (index, row) {
                            $('#kecamatan').append('<option value=' + row.kodeKec + '>' + row.namaKec + '</option>');

                        });
                    });
                });
                $('#kecamatan').change(function () {
                    $.getJSON('getalamat.php', {action: 'getPos', kode_kec: $(this).val()}, function (json) {
                        //$('#kodepos').html('');
                        $.each(json, function (index, row) {
                            //$('#kecamatan').append('<option value=' + row.kodeKec + '>' + row.namaKec + '</option>');
                            $('#kodepos').val(row.kodepos);
                        });
                    });
                });
                /*
                 * Insert a 'details' column to the table
                 */
                var nCloneTh = document.createElement('th');
                var nCloneTd = document.createElement('td');
                nCloneTd.innerHTML = '<img src="../../assets/advanced-datatable/examples/examples_support/details_open.png">';
                nCloneTd.className = "center";

                $('#hidden-table-info thead tr').each(function () {
                    this.insertBefore(nCloneTh, this.childNodes[0]);
                });

                $('#hidden-table-info tbody tr').each(function () {
                    this.insertBefore(nCloneTd.cloneNode(true), this.childNodes[0]);
                });

                /*
                 * Initialse DataTables, with no sorting on the 'details' column
                 */
//                var oTable = $('#hidden-table-info').dataTable({
//                    "aoColumnDefs": [
//                        {"bSortable": false, "aTargets": [0]}
//                    ],
//                    "aaSorting": [[1, 'asc']]
//                });

                /* Add event listener for opening and closing details
                 * Note that the indicator for showing which row is open is not controlled by DataTables,
                 * rather it is done here
                 */
                $('#hidden-table-info tbody td img').live('click', function () {
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
            setInterval(function () {
                $("#tutup").click();
            }, 3000);

        </script>



    </body>
</html>
