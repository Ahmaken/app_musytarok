<?php
include'../../session/level3.php';
require_once '../../config/kon.php';
require_once '../../config/security.php';
$iddescryp = md5('kode');
$iddescryp = $_GET[$iddescryp];
?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <title>Pondok Pesantern Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->
        <!-- Custom styles for this template -->
        <?php include '../../template/head.php'; ?>
        <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />
        <style>
            #harga{
                text-align: right;
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
                                    Pembayaran Pendaftaran
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    $kode = isset($_GET['kode']) ? $_GET['kode'] : '';
                                    $operator = $_SESSION['username'];
                                    $kodex = md5('kode');
                                    $kodeencript = urlencode(encryptIt($kode));
                                    //kas masuk =1

                                    if ($status == "sukses") {
                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        
                                        <h3>Kode Pendaftaran: <a rel='tooltip-top' target='_blank' title='Cetak' href='../reportpdf/slippendaftaran.php?$kodex=$kodeencript'>$kode</h3>
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
                                        <form id="bayardaftar" class="cmxform form-horizontal tasi-form" method="post" action="prospendaftaran.php?type=pembayaran">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Tanggal</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal" name="tanggal" placeholder="Tanggal" value="<?php echo date('d/m/Y') ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Nama Wali</label>
                                                <div class="col-sm-6">
                                                    <input type="text" autofocus="" required="" class="form-control" name="nama"  placeholder="Nama" id="namawali"/>
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Alamat</label>
                                                <div class="col-sm-8">
                                                    <textarea type="text" required="" class="form-control" name="alamat"  placeholder="Alamat" id="alamatwali"></textarea>
                                                </div>
                                            </div>
                                            <?php
                                            $qadministrasi = mysql_query("select * from keuangan_biaya where id='2'");
                                            $showadmin = mysql_fetch_array($qadministrasi);
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Administrasi dan Pangkal</label>
                                                <div class="col-sm-4">
                                                    <input type ="text" required="" class="form-control" name="administrasi" id="nominal" placeholder="Uang Administrasi" value="<?php echo number_format($showadmin['nominal'], 0, ".", "."); ?>"/>
                                                </div>
                                                <?php
                                                $qadministrasi = mysql_query("select * from keuangan_biaya where id='1'");
                                                $showadmin = mysql_fetch_array($qadministrasi);
                                                ?>
                                                <div class="col-sm-4">
                                                    <input type ="text" required="" class="form-control" name="uangpangkal" id="uangpangkal" placeholder="Uang Pankal" value="<?php echo number_format($showadmin['nominal'], 0, ".", "."); ?>"/>
                                                </div>
                                            </div>
                                            <?php
                                            $qadministrasi = mysql_query("select * from keuangan_biaya where id='3'");
                                            $showadmin = mysql_fetch_array($qadministrasi);
                                            ?>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Gedung</label>
                                                <div class="col-sm-5">
                                                    <input type ="text" required="" class="form-control" name="uanggedung" id="uanggedung" placeholder="Uang Gedung" value="<?php echo number_format($showadmin['nominal'], 0, ".", "."); ?>"/>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-4 col-lg-6">
                                                    <button type="submit" id="tombol" class="btn btn-success">Simpan</button>
                                                    <button type="reset" id="cancel" class="btn btn-default">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                            </section>
                        </div>
                        <!--                             table kiri   -->
                        <div class="col-lg-7">
                            <section class="panel">
                                <header class="panel-heading">
                                    Detail Pembayaran

                                </header>
                                <div class="panel-body">
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered table-striped" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Tanggal</th>
                                                    <th>Kode Reg</th>
                                                    <th>Nama Wali</th>
                                                    <th class="numeric">Pembayaran</th>
                                                    <th>Alamat</th>
                                                    <th class="numeric">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //include '../../config/security.php';
                                                $q = mysql_query("SELECT DATE_FORMAT(tanggal, '%d/%m/%Y') AS tanggal, kode, namawali, alamat, administrasi, pangkal, gedung from reg_pembayaran ORDER BY id desc;");
                                                $no = 1;
                                                $kodex = md5('kode');
                                                while ($r = mysql_fetch_array($q)) {
                                                    $kodetrans = urlencode(encryptIt($r['kode']));
                                                    echo "<tr><td>" . $no++ . "</td><td>" . $r['tanggal'] . "</td><td>" . $r['kode'] . "</td><td>" . $r['namawali'] . "</td><td>" . $r['alamat'] . "</td>"
                                                    . "<td class='numeric' id='harga'>" . number_format($r['administrasi']+$r['pangkal']+$r['gedung'], 0, '.', '.') . "</td>"
                                                    . "<td><a rel='tooltip-top' target='_blank' title='Cetak' href='../reportpdf/slippendaftaran.php?$kodex=$kodetrans'>"
                                                    . "<i class='icon-print'></i></a> "
                                                    . " <a rel='tooltip-top' title='Edit' href='index.php?modul=editpenambahan&$kodex=$kodetrans'>"
                                                    . "<i class='icon-edit'></i></a></td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>

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
        <script type="text/javascript" src="../../assets/fuelux/js/spinner.min.js"></script>
        <script src="../../js/jquery.customSelect.min.js" ></script>
        <script src="../../js/nominal.js" ></script>
        <script src="../../js/jquery.autocomplete.js"></script>
        <script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.dataTables.js"></script>   
        <script type="text/javascript">
            $(document).ready(function () {

            });
            $('#tanggal').datepicker({
                format: "dd/mm/yyyy"
            });

            $(document).ready(function () {
                $("#kodeanggota").autocomplete("../../config/getanggota.php?type=kode", {
                    width: 250
                });

                $("#kodeanggota").result(function (event, data, formatted) {
                    var kode = formatted;
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function (data) {
                            $("#kodeanggota").val(data.kode);
                            $("#namaanggota").val(data.nama);
                        }
                    });
                });
                $("#kodeanggota").keyup(function () {
                    var kode = $('#kodeanggota').val();
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function (data) {
                            //  $("#kodeanggota").val(data.kode);
                            $("#namaanggota").val(data.nama);
                        }
                    });
                });

            });

            function hapus(id, akun) {
                $('#pesanx').text('Anda yakin akan menghapus data dengan kode akun: ' + akun);
                $('#kodex').val(id);
            }
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
            $('#submenusetoran').attr('class', 'active');
            /* Formating function for row details */


            $(document).ready(function () {
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
            }, 10000);
        </script>



    </body>
</html>
