<?php include'../../session/level3.php';?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">

        <title>Penyusutan | Pondok Pesantren Matholi'ul Anwar</title>

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
                                            <label class="sr-only" for="">Golongan</label>
                                            <select  class="form-control" id="golongan" name="golongan" placeholder="Golongan" required>
                                                <option selected value="all">GOLONGAN</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['golongan'])) {
                                                    $getgol = $_GET['golongan'];
                                                    if ($getgol != "all") {
                                                        $gol = mysql_query("select id, upper(golongan) as golongan from inv_golongan where id='$getgol' ");
                                                        while ($hasil = mysql_fetch_array($gol)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $gol = mysql_query("select id, upper(golongan)as golongan from inv_golongan");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['id'] . ">" . $hasil['golongan'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label class="sr-only" for="">INSTANSI</label>
                                            <select class="form-control" id="instansi" name="instansi" placeholder="instansi" required>
                                               <option selected value="all">INSTANSI</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['instansi'])) {
                                                    $getins = $_GET['instansi'];
                                                    if ($getins != "all") {
                                                        $instan = mysql_query("select id, upper(instansi) as instansi from umum_instansi where id='$getins' ");
                                                        while ($hasil = mysql_fetch_array($instan)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['instansi'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $instan = mysql_query("select id, upper(instansi)as instansi from umum_instansi");
                                                while ($hasil = mysql_fetch_array($instan)) {
                                                    echo "<option value=" . $hasil['id'] . ">" . $hasil['instansi'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                        <div class="form-group">
                                        </div>
                                        <button type="submit" class="btn btn-success" style="margin-left: 20px;">Filter</button>
                                        <a href="#myModal-2" data-toggle="modal" id="topdf" class="btn btn-danger"><i class="icon-camera"></i> Print Golongan</a>
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
                            Penyusutan Inventaris
                        </header>
                        <div class="panel-body">
                            <section id="unseen">
                                <table class="table table-bordered table-striped table-condensed">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>Nama</th>
                                            <th>Instansi</th>
                                            <th class="numeric">Harga</th>
                                            <th class="numeric">Residu</th>
                                            <th class="numeric">Umur</th>
                                            <th class="numeric">Sisa Umur</th>
                                            <th class="numeric">Awal</th>
                                            <th class="numeric">Bulan ini</th>
                                            <th class="numeric">Akhir</th>
                                            <th class="numeric">Nilai Buku</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $ins = isset($_GET['instansi']) ? $_GET['instansi'] : "";
                                        $gol = isset($_GET['golongan']) ? $_GET['golongan'] : "";

                                        $wheregol = "";
                                        $whereins = "";
                                        if ($ins != "all" and $ins != "") {
                                            $whereins = " AND a.instansi = '$ins'";
                                        }
                                        if ($gol != "all" and $gol != "") {
                                            $wheregol = " AND a.kode LIKE '$gol%'";
                                        }
                                        $where = " AND status=1 and umur>umur- (12*(YEAR(CURDATE())-YEAR(tglperolehan))+(MONTH(CURDATE())-MONTH(tglperolehan)))";
                                        $q = mysql_query("SELECT a.kode
                                                                                            , a.nama
                                                                                            , a.spesifikasi
                                                                                            , b.instansi
                                                                                            , a.harga
                                                                                            , a.penyusutan
                                                                                            , a.nilaisisa AS residu
                                                                                            , a.umur
                                                                                            , @sisaumur:= a.umur- (12*(YEAR(CURDATE())-YEAR(a.tglperolehan))+(MONTH(CURDATE())-MONTH(a.tglperolehan))) AS sisaumur
                                                                                            , @akhir:=(a.umur-@sisaumur)*penyusutan AS akhir
                                                                                            , @akhir-penyusutan AS awal
                                                                                            , harga-@akhir AS nilaibuku

                                                                                        FROM
                                                                                            inv_inventaris a, umum_instansi b
                                                                                            WHERE a.instansi=b.id" . $where . "" . $wheregol . "" . $whereins." ORDER BY a.instansi, a.id ASC");
                                        $no = 1;

                                        while ($r = mysql_fetch_array($q)) {
                                            $harga = number_format($r['harga'], 2, ",", ".");
                                            $residu = number_format($r['residu'], 2, ",", ".");
                                            $awal = number_format($r['awal'], 2, ",", ".");
                                            $akhir = number_format($r['akhir'], 2, ",", ".");
                                            $sisaumur= $r['sisaumur'];
                                            if ($sisaumur<0){
                                                $sisaumur=0;
                                            }
                                            $penyusutan = number_format($r['penyusutan'], 2, ",", ".");
                                            $nilaibuku = number_format($r['nilaibuku'], 2, ",", ".");
                                            //$arraypenyu = number_format($r['penyusutan'], 2);
                                            $totpenyu[] = $r['penyusutan'];
                                            $totakhir[] = $r['nilaibuku'];
                                            echo "<tr><td>" . $no++ . "</td><td>" . $r['kode'] . "</td><td>" . $r['nama'] . "</td><td>" . $r['instansi'] . ""
                                            . "</td><td class='numeric' id='harga'>" . $harga . "</td><td class='numeric' id='harga'>" . $residu . "</td><td class='numeric'>" . $r['umur'] . "</td><td class='numeric'>"
                                            . $sisaumur . "</td><td class='numeric' id='harga'>" . $awal . "</td><td class='numeric' id='harga'>" . $penyusutan . "</td><td class='numeric' id='harga'>"
                                            . $akhir . "</td><td class='numeric' id='harga'>" . $nilaibuku . "</td></tr>";
                                        }
                                        if (empty($totpenyu)){
                                            $totpenyu= "0";
                                            $totakhir= "0";
                                        } else{
                                            $totpenyu= array_sum($totpenyu);
                                            $totakhir= array_sum($totakhir);
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <div class="form-group" style="padding-left: 20px; ">
                                    <table border="0">
                                        <tr>
                                            <td style="width: 150px;">Penyusutan bulan ini:</td>
                                            <td id="harga"><b><?php
                                                    $sumpenyu = number_format($totpenyu, 2);
                                                    echo "$sumpenyu";
                                                    ?><b></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Total Nilai Buku </td>
                                                            <td id="harga"><b><?php
                                                                    $sumakhir = number_format($totakhir, 2);
                                                                    echo "$sumakhir";
                                                                    ?></b>
                                                            </td>
                                                        </tr>
                                                        </table>
                                                        </div>
                                                        </section>
                                                        </div>

                                                        </section>
                                                        </div>
                                                        </section>
                                                        </div>
                                                        </div>

                                                        </section>
                                                        </section>
                                                        
<!--Modal modal-->
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-1" class="modal fade">
    <div class="modal-dialog" style="width: 400px;">
                                      <div class="modal-content">
                                          <div class="modal-header">
                                              <button aria-hidden="true" data-dismiss="modal" onclick="reset()" class="close" type="button">×</button>
                                              <h4 class="modal-title">To PDF</h4>
                                          </div>
                                          <div class="modal-body">

                                              <form class="form-horizontal" role="form" id="frmctk" method="post" action="../reportpdf/penyusutanpdf.php?type=koordinator" target="_blank">
                                                  <div class="form-group">
                                                      <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Koordinator</label>
                                                      <div class="col-lg-6">
                                                          <select class="form-control" id="instansi" name="filterby" placeholder="filterby" required>
                                                                <option selected value="">Pilih :</option>
                                                                <option value="1">KETUA I</option>
                                                                <option value="2">KETUA II</option>
                                                                <option value="3">KETUA III</option>
                                                                <option value="4">KETUA IV</option>
                                                                <option value="5">BENDAHARA UMUM</option>
                                                                <option value="6">SEKRETARIS UMUM</option>
                                                                <option value="7">WAKETUM</option>
                                                                <option value="8">KETUA UMUM</option>
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

                                              <form class="form-horizontal" role="form" id="frmctk2" method="post" action="../reportpdf/penyusutanpdf.php?type=golongan" target="_blank">
                                                  <div class="form-group">
                                                      <label for="" class="col-lg-4 col-sm-4 control-label">Golongan</label>
                                                      <div class="col-lg-6">
                                                          <select  class="form-control" id="golongan" name="filterby" placeholder="Golongan" required>
                                                <option selected value="">Pilih:</option>
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
                                                            
                                        
                                            function toPdf() {
//                                              
                                                var instansi = $('#instansi').val();
                                                var golongan = $('#golongan').val();
                                                
                                                window.open('../reportpdf/penyusutanpdf.php?golongan='+golongan+'&instansi='+instansi);

                                            }
                                            $('#penyusutan').attr('class', 'active');
                                            $('#laporan').attr('class', 'active');
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
