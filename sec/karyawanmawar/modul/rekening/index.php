<?php include'../../session/level2.php'; ?>
<!DOCTYPE html>
<html lang="id">
    <head>
        <title>COA | Keuangan Pondok Pesantren Matholi'ul Anwar</title>

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
                        <div class="col-lg-4">
                            <section class="panel">
                                <header class="panel-heading">
                                    Input Rekening Akun
                                </header>
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
                                    <div class="form" id="addakun">
                                        <form id="tambahrek" class="cmxform form-horizontal tasi-form" method="post" action="prosakun.php?type=save">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Kode Parent</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="kodeparent"  placeholder="Kode Parent" id="kode">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Uraian</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="uraian" class="form-control" name="uraian"  readonly="" placeholder="Uraian">
                                                    <input type="hidden" id="grup" class="form-control" name="grup">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Kode Akun</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="kodeakun" required  placeholder="Kode Akun">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Nama Akun</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="namaakun" required  placeholder="Nama Akun">
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
                    
                        <div class="col-lg-8">
                            <section class="panel">
                                <header class="panel-heading">
                                    Rekening Akun
                                </header>
                                <div class="panel-body">
                                    <div class="adv-table">
                                        <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                                            <thead>
                                                <tr>
                                                    <th style="width: 50px;">No</th>
                                                    <th style="width: 120px;">Kode Rekening</th>
                                                    <th>Nama Rekening</th>
                                                    <th style="width: 50px;">Grup</th>
                                                    <th style="width: 100px;">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                include '../../config/security.php';
                                                $kodex = md5('kode');
                                                $group = md5('grup');
                                                $no = 1;
                                                $q = mysql_query("SELECT * FROM ak_akun WHERE STATUS=1 order by kode");
                                                while ($row = mysql_fetch_array($q)) {
                                                    $kode = urlencode(encryptIt($row['id']));
                                                    $grup = $row['grup'];
                                                    $getgrup = $row['grup'];
                                                    //untuk membuat padding
                                                    switch ($grup) {
                                                        case '1':
                                                            $grup='10';
                                                            break;
                                                        case '2':
                                                            $grup='30';
                                                            break;
                                                        case '3':
                                                            $grup= '50';
                                                            break;
                                                        case '4':
                                                            $grup= '70';
                                                            break;
                                                        case '5':
                                                            $grup = '90';
                                                            break;
                                                    }
                                                    echo "<tr><td>" . $no++ . "</td><td>" . $row['kode'] . "</td><td style='padding-left:".$grup."px;'>" . $row['nama'] . "</td><td>" . $row['grup'] . "</td><td>"
                                                    . "<a class='btn btn-primary btn-xs' href='updakun.php?$kodex=$kode&$group=$getgrup'><i class='icon-pencil'></i></a> "
                                                    . "<a data-toggle='modal' onclick='hapus(" . $row['kode'] . ")' class='btn btn-default btn-xs' href='#modDelete'><i class='icon-remove'></i></a></td></tr>";
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
            <!--            modal-->
            <div class="modal fade" id="modDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Hapus data</h4>
                        </div>
                        <div id="pesanx" class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <form name="confirm" id="confirm" method="post" action="prosakun.php?type=delete">
                                <input type="hidden" name="kode" id="kodex"/>
                                <button data-dismiss="modal" class="btn btn-default" type="button">Close</button>
                                <button class="btn btn-warning" type="submit"> Confirm</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
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
        <script src="../../js/nominal.js" ></script>
        <script type="text/javascript" language="javascript" src="../../assets/advanced-datatable/media/js/jquery.dataTables.js"></script>
        <!--common script for all pages-->
        <script src="../../js/common-scripts.js"></script>
        <script src="../../js/jquery.autocomplete.js"></script>
        <script type="text/javascript">
            $('#kode').focus();
            $(document).ready(function() {
                $("#kode").autocomplete("../../config/getRekening.php?type=kode", {
                    width: 150
                });

                $("#kode").result(function(event, data, formatted) {
                    var kode = formatted;
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getRekening.php?type=data",
                        dataType: "json",
                        success: function(data) {
                           $("#uraian").val(data.nama);
                            $("#grup").val(data.grup);
                        }
                    });
                });
                $("#kode").keyup(function() {
                    var kode = $('#kode').val();
                    $.ajax({
                        type: "POST",
                        data: "kode=" + kode,
                        url: "../../config/getRekening.php?type=data",
                        dataType: "json",
                        success: function(data) {
                            $("#uraian").val(data.nama);
                            $("#grup").val(data.grup);
                        }
                    });
                });

            });
            function hapus($id) {
                $('#pesanx').text('Anda yakin akan menghapus data sub-program dengan kode: ' + $id);
                $('#kodex').val($id);
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
            $('#menurekening').attr('class', 'active');
            $('#master').attr('class', 'active');
            /* Formating function for row details */
            function fnFormatDetails(oTable, nTr)
            {
                var aData = oTable.fnGetData(nTr);
                var sOut = '<table cellpadding="5" cellspacing="0" border="0" style="padding-left:50px;">';
                sOut += '<tr><td>Nama:</td><td>' + aData[4] + '</td></tr>';
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
            setInterval(function() {
                $("#tutup").click();
            }, 3000);
        </script>



    </body>
</html>
