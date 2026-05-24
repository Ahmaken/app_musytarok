<?php
include'../../session/level2.php';
include'../../config/kon.php';
$kode = $_GET['id'];
$q = mysql_query("SELECT * from umum_cabang where id='$kode'");
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

                        <div class="col-lg-8">
                            <section class="panel">
                                <header class="panel-heading"><b>Cabang</b></header>
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
                                        <form id="kabupatenupdate" class="cmxform form-horizontal tasi-form" method="post" action="proscab.php?type=update">
                                               <div class="form-group">
                                                <label class="col-sm-4 control-label">Kode</label>
                                                <div class="col-sm-5">
                                                    <input id="kode" name="kode" autofocus="" placeholder="Kode" class="form-control" required="" readonly type="text" value="<?php echo $tampil['kode']?>"/>
                                                    <input id="kode" name="id"  type='hidden'value="<?php echo $kode ?>"/>
                                                </div>
                                            </div>    
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Nama Cabang</label>
                                                <div class="col-sm-8">
                                                    <input id="kode" name="nama"  placeholder="Nama Cabang" class="form-control" required="" readonly type="text" value="<?php echo $tampil['nama']?>" />
                                                </div>
                                            </div>   
                                <!--            <div class="form-group">
                                                <label class="col-sm-4 control-label">Kode Kabupaten</label>
                                                <div class="col-sm-5">
                                                    <select name="kodekab" class="form-control" required>
                                                        <option value="<?php echo $tampil['kodekab']?>" selected><?php echo $tampil['kodekab']?></option>
                                                        <?php
                                                        include '../../config/kon.php';
                                                        $q= mysql_query("SELECT kode, namakabupaten from umum_kabupaten");
                                                        while ($row = mysql_fetch_array($q)) {
                                                            $kode= $row['kode'];
                                                            $namakab= $row['namakabupaten'];
                                                            echo "<option value='$kode'> $namakab</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div> 
                                             <div class="form-group">
                                                <label class="col-sm-4 control-label">Alamat</label>
                                                <div class="col-sm-8">
                                                    <textarea id="kode" name="alamat"  placeholder="Alamat" class="form-control" required="" type="text"><?php echo $tampil['alamat']?></textarea>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-4 control-label">Telepon</label>
                                                <div class="col-sm-8">
                                                    <input id="kode" name="telepon"  placeholder="Telepon" class="form-control"  type="text" value="<?php echo $tampil['telepon']?>"/>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-4 control-label">Email</label>
                                                <div class="col-sm-8">
                                                    <input id="kode" name="email"  placeholder="Email" class="form-control" type="email" value="<?php echo $tampil['email']?>"/>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="col-sm-4 control-label">Usaha</label>
                                                <div class="col-sm-8">
                                                    <select id="kode" name="usaha"  placeholder="Usaha" class="form-control" required>
                                                        <option selected value="">Usaha:</option>
                                                            <?php
                                                            $kodeusaha= $tampil['usaha'];
                                                            $q1 = mysql_fetch_array(mysql_query("select * from umum_usaha where id='$kodeusaha'"));
                                                            $usaha=$q1['usaha'];
                                                            echo "<option selected value='$kodeusaha'>$usaha</option>";
                                                            $q2= mysql_query("SELECT * from umum_usaha");
                                                        while ($row = mysql_fetch_array($q2)) {
                                                            $kode= $row['id'];
                                                            $usaha= $row['usaha'];
                                                            echo "<option value='$kode'> $usaha</option>";
                                                        }
                                                            ?>
                                                    </select>
                                                </div>
                                            </div>
                                              <div class="form-group">
                                                <label class="col-sm-4 control-label">Berdiri Tanggal:</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" placeholder="hh-bb-tttt" data-mask="99-99-9999" name="berdiri" id="tanggallahir" value="<?php $tgllhr= explode("-", $tampil['tanggalberdiri']);  $tglbdr = $tgllhr[2] . "-" . $tgllhr[1] . "-" . $tgllhr[0]; echo $tglbdr;?>">
                                                </div>
                                            </div> -->
											<div class="form-group">
                                                <label class="col-sm-4 control-label">Nomor NPP</label>
                                                <div class="col-sm-8">
                                                    <input id="kode" name="npp"  placeholder="npp" class="form-control" required="" type="text" value="<?php echo $tampil['npp']?>" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-4 control-label">Keterangan</label>
                                                <div class="col-sm-8">
                                                    <textarea id="kode" name="keterangan"  placeholder="Keterangan" class="form-control" type="text"><?php echo $tampil['kode']?></textarea>
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

        $('#master').attr('class', 'active');
        $('#menucab').attr('class', 'active');

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
