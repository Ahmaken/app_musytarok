<?php
include'../../session/level3.php';
include'../../config/kon.php';
$kode = $_GET['id'];
$q = mysql_query("SELECT a.id, a.nik, d.nama, a.jabatan AS kodejabatan, b.jabatan AS jabatan, DATE_FORMAT(a.tglawal, '%d-%m-%Y') AS tglawal, DATE_FORMAT(a.tglakhir, '%d-%m-%Y') AS tglakhir, a.landasan AS idlandasan, c.landasan, a.keterangan FROM karyawan_mutasijabatan a, karyawan_jabatan b, karyawan_landasanmutasi c, karyawan_master d WHERE a.jabatan=b.kode AND a.landasan=c.id AND a.nik=d.nik AND a.id='$kode'");
$tampil = mysql_fetch_array($q);
//$oto = $huser['otorisasi'];
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
        <title>Pondok Pesantern Matholi'ul Anwar</title>

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
                                <header class="panel-heading"><b>Jabatan</b></header>
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
                                        <form id="kabupatenupdate" class="cmxform form-horizontal tasi-form" method="post" action="prosposisi.php?type=update">
                                                <div class="form-group">
                                                <label class="col-sm-2 control-label">NIK</label>
                                                <div class="col-sm-4">
                                                    <input id="kode" name="kode" autofocus="" placeholder="NIK" class="form-control" required="" readonly type="text" value="<?php echo $tampil['nik']?>" />
                                                    <input id="kode" name="id"   type="hidden" value="<?php echo $tampil['id']?>" />
                                                </div>
                                                <div class="col-sm-5">
                                                    <input id="nama" name="nama"  placeholder="Nama" class="form-control"   type="text" value="<?php echo $tampil['nama']?>"  />
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Jabatan</label>
                                                <div class="col-sm-4">
                                                    <input id="kodejabatan" name="kodejabatan" placeholder="Kode Jabatan" class="form-control" required="" value="<?php echo $tampil['kodejabatan']?>" type="text"/>
                                                </div>
                                                <div class="col-sm-5">
                                                    <input id="jabatan" name="jabatan"  readonly placeholder="Jabatan" class="form-control"   type="text" value="<?php echo $tampil['jabatan']?>" />
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Periode</label>
                                                <div class="col-sm-4">
                                                   <input type="text" class="form-control" required placeholder="hh-bb-tttt" data-mask="99-99-9999" name="tglawal" value="<?php echo $tampil['tglawal']?>">
                                                </div>
                                                <div class="col-sm-4">
                                                   <input type="text" class="form-control" required placeholder="hh-bb-tttt" data-mask="99-99-9999" name="tglakhir" value="<?php echo $tampil['tglakhir']?>">
                                                </div>
                                            </div>  
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Landasan</label>
                                                <div class="col-sm-5">
                                                    <select id="landasan" name="landasan" class="form-control" required="" type="text">
                                                     <option value="">Landasan:</option>
                                                     <option value="<?php echo $tampil['idlandasan']?>" selected><?php echo $tampil['landasan']?></option>
                                                        <?php
                                                        include '../../config/kon.php';
                                                        $qs = mysql_query("SELECT * from karyawan_landasanmutasi");
                                                        while ($row = mysql_fetch_array($qs)) {
                                                            $kode = $row['id'];
                                                            $landasan = $row['landasan'];
                                                            echo "<option value='$kode'> $landasan</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>  
                                            <div class="form-group">
                                                <label class="col-sm-2 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea id="kode" name="keterangan"  placeholder="Keterangan" class="form-control"  type="text"><?php echo $tampil['keterangan']?></textarea>
                                                </div>
                                            </div> 

                                            <div class="form-group">
                                                <div class="col-lg-offset-6 col-lg-6">
                                                    <button type="submit" class="btn btn-danger">Simpan</button>
                                                    <button type="reset" class="btn btn-default">Cancel</button>
                                                </div>
                                        </form>
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
                window.location.href = "proskamar.php?type=delete&kode=" + nip;
            }
        }
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

         $('#menutransaksi').attr('class', 'active');
        $('#menupenetapanjabatan').attr('class', 'active');

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

        //getkaryawan
        $(document).ready(function() {
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
            //getcab
            $("#kodejabatan").autocomplete("../jabatan/getjabatan.php?type=kode", {
                width: 150
            });
            $("#kodejabatan").result(function(event, data, formatted) {
                var kode = formatted;
                $.ajax({
                    type: "POST",
                    data: "kode=" + kode,
                    url: "../jabatan/getjabatan.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#jabatan").val(data.jabatan);
                        //$("#umur").val(data.umur);
                    }
                });
            });
            $("#kodejabatan").keyup(function() {
                var kode = $('#kodejabatan').val();
                $.ajax({
                    type: "POST",
                    data: "kode=" + kode,
                    url: "../jabatan/getjabatan.php?type=data",
                    dataType: "json",
                    success: function(data) {
                        $("#jabatan").val(data.jabatan);
                        // $("#umur").val(data.umur);
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
