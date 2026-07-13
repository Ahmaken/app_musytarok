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
                                    Edit Setoran Simpanan
                                </header>
                                <div class="panel-body">
                                    <?php
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $ket = isset($_GET['ket']) ? $_GET['ket'] : '';
                                    $kode = isset($_GET['kode']) ? $_GET['kode'] : '';
                                    $operator = $_SESSION['username'];
                                    include '../../config/security.php';
                                    $kodex = md5('kode');
                                    $kodeencript= decryptIt($_GET[$kodex]);
                                    $query = mysql_query("SELECT DATE_FORMAT(a.tanggal, '%d/%m/%Y') AS tanggal, a.kode, a.kode_anggota, b.nama, a.debet, a.keterangan, b.verifikasi FROM anggota_simpanan a, anggota_master b WHERE a.kode_anggota=b.kode AND a.kode='$kodeencript'");
                                    $hasil = mysql_fetch_array($query);
                                    if ($hasil['verifikasi']==1){
                                        $cekver_ok='checked';
                                    }else{
                                        $cekver_no='checked';
                                    }
                                    //kas masuk =1

                                    ?>
                                    <div class="form" id="addpenerimaan">
                                        <form id="jurnalpenerimaan" class="cmxform form-horizontal tasi-form" method="post" action="prossimpanan.php?type=editpenambahan">
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Kode Transaksi</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="kode" name="kode" placeholder="Kode Transaksi" readonly="" value="<?php echo $hasil['kode'] ?>" >
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Tanggal</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal" name="tanggal" placeholder="Tanggal" value="<?php echo $hasil['tanggal'] ?>" ?>                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Anggota</label>
                                                <div class="col-sm-4">
                                                    <input type="text"  required="" class="form-control" name="kodeanggota"  placeholder="Kode" id="kodeanggota" value="<?php echo $hasil['kode_anggota'] ?>">
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" required="" readonly="" class="form-control" name="nama"  placeholder="Nama" id="namaanggota" value="<?php echo $hasil['nama'] ?>">
                                                    
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Nominal</label>
                                                <div class="col-sm-5">
                                                    <input type ="text" autofocus="" required="" class="form-control" name="nominal" onkeyup="javascript: tandaPemisahTitik(this)" onkeydown="return numbersonly(this, event)" id="nominal" placeholder="Nominal" value="<?php echo number_format($hasil['debet'], 0, '.','.')  ?>"/>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-2 control-label">Verifikasi</label>

                                                <div class="col-sm-2">
                                                    <div class="radio">
                                                        <label> <input  name="verifikasi" type="radio" value="1" <?php echo $cekver_ok?>/>Sudah</label>
                                                    </div>
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="radio">
                                                        <label><input  name="verifikasi" type="radio" value="0" <?php echo $cekver_no?>/>Belum</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan"><?php echo $hasil['keterangan'] ?></textarea>
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
                                                        $('#menutransaksi').attr('class', 'active');
                                                        $('#submenusetoran').attr('class', 'active');
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
                                                        setInterval(function() {
                                                            $("#tutup").click();
                                                        }, 5000);
        </script>



    </body>
</html>
