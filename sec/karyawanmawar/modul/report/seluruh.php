<?php include'../../session/level4.php';?>
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
                                            <label class="sr-only" for="">Golongan</label>
                                            <select  class="form-control" id="golongan" name="golongan" placeholder="Golongan" required>
                                                <option selected value="all">Golongan</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['golongan'])) {
                                                    $getgol = $_GET['golongan'];
                                                    if ($getgol != "all") {
                                                        $gol = mysql_query("select * from inv_golongan where id='$getgol' ");
                                                        while ($hasil = mysql_fetch_array($gol)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $gol = mysql_query("select * from inv_golongan");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="">Ruang</label>
                                            <select class="form-control" id="instansi" name="instansi" placeholder="instansi" required>
                                                <option selected value="all">Instansi</option>
                                                <?php
                                                 if (isset($_GET['instansi'])) {
                                                    $getins = $_GET['instansi'];
                                                    if ($getins != "all") {
                                                        $ins = mysql_query("select * from umum_instansi where id='$getins' ");
                                                        while ($hasil = mysql_fetch_array($ins)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['instansi'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $ins = mysql_query("select * from umum_instansi");
                                                while ($tampil = mysql_fetch_array($ins)) {
                                                    echo "<option value=" . $tampil['id'] . ">" . $tampil['instansi'] . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">Filter</button>
                                        <a href="#myModal-2" data-toggle="modal" id="topdf" class="btn btn-danger"><i class="icon-camera"></i> Print Golongan</a>
                                        <a href="#myModal-1" data-toggle="modal" id="topdf" class="btn btn-info"><i class="icon-building"></i> Print Ruang</a>
                                </div>

                                </form>

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

                                              <form class="form-horizontal" role="form" id="frmctk" method="post" action="../reportpdf/asetinstopdf.php" target="_blank">
                                                  <div class="form-group">
                                                      <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Ruang</label>
                                                      <div class="col-lg-6">
                                                          <select class="form-control" id="instansi" name="filterby" placeholder="filterby" required>
                                                                <option selected value="">Pilih :</option>
                                                                <option value="all">SEMUA INSTANSI </option>
                                                                <?php 
                                                                 $ins = mysql_query("select * from umum_instansi");
                                                                 while ($tampil = mysql_fetch_array($ins)) {
                                                                  echo "<option value=" . $tampil['id'] . ">" . $tampil['instansi'] . "</option>";
                                                }
                                                                ?>
                                                          </select>
                                                      </div>
                                                  </div>
                                                  <div class="form-group">
                                                      <label for="tanggal1" class="col-lg-4 col-sm-4 control-label">Tanggal</label>
                                                      <div class="col-lg-6">
                                                          <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="tanggal1" name="tanggal1" placeholder="Tanggal" required="">
                                                      </div>
                                                     
                                                          <div style="margin-left: 10px;">
                                                              <label>
                                                                  <input id="checkbox" type="checkbox" onclick="tampilTanggal()"> s/d
                                                              </label>
                                                          </div>
                                                     
                                                  </div>
                                                  <div id="tgl2" class="form-group" style="visibility: hidden;">
                                                      <label for="tanggal2" class="col-lg-4 col-sm-4 control-label">S.d Tanggal</label>
                                                      <div class="col-lg-6">
                                                          <input type="text" class="form-control form-control-inline input-medium default-date-picker" name="tanggal2" id="tanggal2" placeholder="Sampai Tanggal">
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
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-2" class="modal fade">
    <div class="modal-dialog" style="width: 400px;">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                              <button aria-hidden="true" data-dismiss="modal" onclick="reset()"class="close" type="button">×</button>
                                              <h4 class="modal-title">To PDF</h4>
                                          </div>
                                          <div class="modal-body">

                                              <form class="form-horizontal" role="form" id="frmctk2" method="post" action="../reportpdf/asetgoltopdf.php" target="_blank">
                                                  <div class="form-group">
                                                      <label for="" class="col-lg-4 col-sm-4 control-label">Golongan</label>
                                                      <div class="col-lg-6">
                                                          <select  class="form-control" id="golongan" name="filterby" placeholder="Golongan" required>
                                                <option selected value="">Pilih:</option>
                                                <option value="all">SEMUA GOLONGAN</option>
                                                <?php
                                                include '../../config/kon.php';
                                                $gol = mysql_query("select id, upper(golongan)as golongan from inv_golongan");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                                      </div>
                                                  </div>
                                                  <div class="form-group">
                                                      <label for="tanggal1" class="col-lg-4 col-sm-4 control-label">Tanggal</label>
                                                      <div class="col-lg-6">
                                                          <input type="text" class="form-control form-control-inline input-medium default-date-picker" id="goltanggal1" name="tanggal1" placeholder="Tanggal" required="">
                                                      </div>
                                                     
                                                          <div style="margin-left: 10px;">
                                                              <label>
                                                                  <input id="golcheckbox" type="checkbox" onclick="golTampilTanggal()"> s/d
                                                              </label>
                                                          </div>
                                                     
                                                  </div>
                                                  <div id="goltgl2" class="form-group" style="visibility: hidden;">
                                                      <label for="tanggal2" class="col-lg-4 col-sm-4 control-label">S.d Tanggal</label>
                                                      <div class="col-lg-6">
                                                          <input type="text" class="form-control form-control-inline input-medium default-date-picker" name="tanggal2" id="goltanggal2" placeholder="Sampai Tanggal">
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
                                                        
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Data Seluruh Inventaris
                        </header>
                        <div class="panel-body">
                            <div class="adv-table">
                                <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th class="hidden-phone">Nama</th>
                                            <th class="hidden-phone">Spesifikasi</th>
                                            <th class="hidden-phone">Tgl Perolehan</th>
                                            <th class="hidden-phone">Harga</th>
                                            <th class="hidden-phone">Umur</th>

                                            <th class="hidden-phone">Instansi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ins = isset($_GET['instansi']) ? $_GET['instansi'] : "";
                                        $gol = isset($_GET['golongan']) ? $_GET['golongan'] : "";

                                        $wheregol = "";
                                        $whereins = "";
                                        if ($ins != "all" and $ins !="") {
                                            $whereins = " AND a.instansi = '$ins'";
                                        }
                                        if ($gol != "all" and $gol !="") {
                                            $wheregol = " AND a.kode LIKE '$gol%'";
                                        }
                                        $no = 1;
                                        $q = mysql_query("SELECT a.kode, a.nama, a.spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%m/%Y') AS tglperolehan, a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id AND STATUS=1" . $wheregol . "" . $whereins." ORDER BY a.instansi, a.id ASC");
                                        //echo "SELECT a.kode, a.nama, a.spesifikasi, DATE_FORMAT(a.tglperolehan, '%d/%l/%Y') AS tglperolehan, a.harga, a.umur, b.instansi, a.keterangan FROM inv_inventaris a, umum_instansi b WHERE a.instansi=b.id AND STATUS=1" . $wheregol."".$whereins;
                                        while ($row = mysql_fetch_array($q)) {
                                            $harga = number_format($row['harga'], 0, '', '.');
                                            echo "<tr><td>" . $no++ . "</td><td>" . $row['kode'] . "</td><td>" . $row['nama'] . "</td><td>" . $row['spesifikasi'] . "</td><td>"
                                            . "" . $row['tglperolehan'] . "</td><td id='harga'>" . $harga . "</td><td>" . $row['umur'] . "</td><td>" . $row['instansi'] . "</td></tr>";
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

        if (e.keyCode == 27) { reset() }   // esc
      });
        function reset(){
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
        function tampilTanggal(){
            if($('#checkbox').attr('checked')) {
                $('#tanggal2').val('');
                $('#tanggal2').attr('required', '');
                $('#tgl2').css('visibility', 'visible');
            } else {
                $('#tanggal2').removeAttr('required'); 
                $('#tanggal2').val(''); 
                $('#tgl2').css('visibility', 'hidden'); 
            }
      }
      function golTampilTanggal(){
            if($('#golcheckbox').attr('checked')) {
                $('#goltanggal2').val('');
                $('#goltanggal2').attr('required', '');
                $('#goltgl2').css('visibility', 'visible');
            } else {
                $('#goltanggal2').val(''); 
                $('#goltanggal2').removeAttr('required'); 
                $('#goltgl2').css('visibility', 'hidden'); 
                $('#goltgl2').css('visibility', 'hidden'); 
            }
      }
                                                            
        $('#aset').attr('class','active');
        $('#laporan').attr('class','active');
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
