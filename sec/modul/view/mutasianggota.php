<?php include'../../session/level4.php'; ?>
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
                                    Mutasi Simpanan Anggota
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    $kode = isset($_GET['kode']) ? $_GET['kode'] : '';
                                    $kodeanggota = isset($_POST['kodeanggota']) ? $_POST['kodeanggota'] : '';
                                    $namaanggota = isset($_POST['nama']) ? $_POST['nama'] : '';
                                    $operator = $_SESSION['username'];
                                    include '../../config/security.php';
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
                                        
                                        <h3>No Transaksi: <a rel='tooltip-top' target='_blank' title='Cetak' href='../reportpdf/slipsetoran.php?$kodex=$kodeencript'>$kode</h3>
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
                                        <form id="jurnalpenerimaan" class="cmxform form-horizontal tasi-form" method="post" action="">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Anggota</label>
                                                <div class="col-sm-4">
                                                    <input type="text" autofocus required="" class="form-control" name="kodeanggota"  placeholder="Kode" id="kodeanggota">
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" required="" readonly="" class="form-control" name="nama"  placeholder="Nama" id="namaanggota">
                                                </div>
                                            </div> 
                                            <div class="form-group">
                                                <div class="col-lg-offset-6 col-lg-6">
                                                    <button type="submit" id="tombol" class="btn btn-success">Proses</button>
                                                    <button type="reset" id="cancel" class="btn btn-default">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                            </section>
                        </div>
                        <!--                             table kiri   -->
                        <?php if ($kodeanggota!="") {
                            include './detailmutasi.php';
                        }
                        ?>
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
            $(document).ready(function() {

            });
            $('#tanggal').datepicker({
                format: "dd/mm/yyyy"
            });

            $(document).ready(function() {
                $("#kodeanggota").autocomplete("../../config/getanggota.php?type=kode", {
                    width: 250
                });

                $("#kodeanggota").result(function(event, data, formatted) {
                    var kode = formatted;
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function(data) {
                            $("#kodeanggota").val(data.kode);
                            $("#namaanggota").val(data.nama);
                        }
                    });
                });
                $("#kodeanggota").keyup(function() {
                    var kode = $('#kodeanggota').val();
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getanggota.php?type=data",
                        dataType: "json",
                        success: function(data) {
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
            $(document).keyup(function(e) {

                if (e.keyCode == 27) {
                    reset();
                }   // esc
            });
            function reset() {
                $('#frmctk')[0].reset();
                $('#frmctk2')[0].reset();
            }
            $('#menuview').attr('class', 'active');
            $('#submenumutasianggota').attr('class', 'active');
            /* Formating function for row details */


            setInterval(function() {
                $("#tutup").click();
            }, 5000);
        </script>



    </body>
</html>
