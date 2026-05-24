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

            <?php
            include'../../template/side.php';
            include '../anggota/getalamat.php';
            ?>

            <!--sidebar end-->
            <!--main content start-->

            <section id="main-content">
                <section class="wrapper">
                    <!--state overview start-->
                    <div class="row state-overview">
                        <div class="col-lg-10">
                            <section class="panel">

                                <header class="panel-heading">
                                    Pilih Salah Satu Filter:
                                </header>
                                <div class="panel-body">
                                    <form name="filter" id="filter" class="form-inline" role="form" method="get">
                                        <div class="form-group">
                                            <label class="sr-only" for="">Provinsi</label>
                                            <select id="provinsi"  name="provinsi" class="form-control">
                                                <option value="">Provinsi:</option>
                                                <?php
                                                foreach ($arrpropinsi as $kodeprov => $namaprov) {
                                                    echo "<option value='$kodeprov'>$namaprov</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="kabupaten" name="kota" class="form-control">
                                                <option value="">Kota/Kabupaten:</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="kecamatan" name="kecamatan" class="form-control">
                                                <option value="">Kecamatan</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="filtertahun" name="filtertahun" class="form-control">
                                                <option value="all">Tahun:</option>
                                                <?php
                                                for ($thnawal = 2009; $thnawal < 2020; $thnawal++) {
                                                    echo "<option value='$thnawal'>$thnawal</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <select id="filtertahun" name="ver" class="form-control">
                                                <option value="all">Verifikasi:</option>
                                                <option value="1">Sudah</option>
                                                <option value="2">Belum</option>

                                                ?>
                                            </select>
                                        </div>   

                                        <button type="submit" class="btn btn-success">Filter</button>

<!--                                        <a href='#' target='_blank' onclick="$(this).closest('form').submit();" id="topdf" class="btn btn-danger"><i class="icon-camera"></i> Cetak</a>-->


                                        <a href="#myModal-1" data-toggle="modal" id="topdf" class="btn btn-info"><i class="icon-building"></i> Print </a>
                                    </form>
                                </div>



                        </div>
                </section>

                </div>
                </div>

                <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="myModal-5" class="modal fade">
                    <div class="modal-dialog" style="width: 400px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button aria-hidden="true" data-dismiss="modal" onclick="reset()" class="close" type="button">×</button>
                                <h4 class="modal-title">To PDF</h4>
                            </div>
                            <div class="modal-body">

                                <form class="form-horizontal" role="form" id="frmctk" method="post" action="../reportpdf/karyawanpercabang.php?type=percabang" target="_blank">
                                    <div class="form-group">
                                        <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Cabang</label>
                                        <div class="col-lg-8">
                                            <select  class="form-control" id="cabang" name="cabang">
                                                <option selected value="all">Provinsi</option>
                                                <?php
                                                include '../../config/kon.php';
                                                if (isset($_GET['cabang'])) {
                                                    $getcab = $_GET['cabang'];
                                                    if ($getcab != "all") {
                                                        $cab = mysql_query("select * from umum_cabang where kode='$getcab' ");
                                                        // echo $getcab;
                                                        while ($hasil = mysql_fetch_array($cab)) {
                                                            echo "<option selected value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                        }
                                                    }
                                                }
                                                // $getcab;
                                                $gol = mysql_query("select * from umum_cabang");
                                                while ($hasil = mysql_fetch_array($gol)) {
                                                    echo "<option value=" . $hasil['kode'] . ">" . $hasil['kode'] . ' ' . $hasil['nama'] . "</option>";
                                                }
                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="inputEmail1" class="col-lg-4 col-sm-4 control-label">Koordinator</label>
                                        <div class="col-lg-8">
                                            <select class="form-control" id="koordinator" name="koordinator">
                                                <option selected value="all">Seluruh Koordinator</option>
                                                <?php
                                                if (isset($_GET['koordinator'])) {
                                                    $getkoor = $_GET['koordinator'];
                                                    if ($getkoor != "all") {
                                                        $koor = mysql_query("select * from karyawan_koordinator where id='$getkoor' ");
                                                        while ($hasil = mysql_fetch_array($koor)) {
                                                            echo "<option selected value=" . $hasil['id'] . ">" . $hasil['koordinator'] . "</option>";
                                                        }
                                                    }
                                                }
                                                $koor = mysql_query("select * from karyawan_koordinator");
                                                while ($tampil = mysql_fetch_array($koor)) {
                                                    echo "<option value=" . $tampil['id'] . ">" . $tampil['koordinator'] . "</option>";
                                                }
                                                ?>
                                            </select>
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


                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">
                            Data Anggota
                        </header>
                        <div class="panel-body">
                            <div class="adv-table">
                                <table cellpadding="0" cellspacing="0" border="0" class="display table table-bordered" id="hidden-table-info">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kode</th>
                                            <th>ID Lama</th>
                                            <th>Nama</th>
                                            <th>Verifikasi</th>
                                            <th>Debet</th>
                                            <th>Kredit</th>
                                            <th>Saldo</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        require_once '../../config/security.php';
                                        $i = 1;
                                        $idcryp = md5('id');
                                        $provinsi = $_GET['provinsi'];
                                        if ($_GET['filtertahun'] != 'all') {
                                            $tahundftr = $_GET['filtertahun'];
                                            $wheretahun = " and b.tanggaldaftar like '$tahundftr%'";
                                        }
                                        if (!empty($_GET['ver'])) {
                                            if ($_GET['ver'] != 'all') {
                                                $verifikasi = $_GET['ver'];
                                                if ($verifikasi == 2) {
                                                    $verifikasi = 0;
                                                }
                                                $wherever = " and b.verifikasi='$verifikasi'";
                                            }
                                        }
                                        $where = "";
                                        if ($_GET['provinsi'] != "" && $_GET['kota'] == "" && $_GET['kecamatan'] == "") {
                                            $where = " and provinsi='$provinsi'";
                                        } else if ($_GET['kota'] != "" && $_GET['kecamatan'] == "") {
                                            $kabupaten = $_GET['kota'];
                                            $where = " and kabupaten = '$kabupaten'";
                                        } else if ($_GET['kecamatan'] != "") {
                                            $kecamatan = $_GET['kecamatan'];
                                            $where = " and kecamatan = '$kecamatan'";
                                        }
                                        $q = mysql_query("Select a.kode_anggota, b.idlama, b.nama, sum(a.debet) as debet, sum(a.kredit) as kredit, "
                                                . "sum(a.debet)-sum(a.kredit) as saldo, b.verifikasi from anggota_simpanan a, anggota_master b where a.kode_anggota=b.kode "
                                                . "and b.status=1 " . $where . "" . $wheretahun . "" . $wherever . " group by a.kode_anggota order by kode_anggota;");
                                        while ($hasil = mysql_fetch_array($q)) {
                                            $totaldebet[] = $hasil['debet'];
                                            $totalkredit[] = $hasil['kredit'];
                                            $idbaris = urlencode(encryptIt($hasil['id']));
                                            if ($hasil['verifikasi'] == 1) {
                                                $verShow = 'Sudah';
                                            } elseif ($hasil['verifikasi'] == 0) {
                                                $verShow = "<font color='red'>Belum</font>";
                                            }


                                            echo "<tr class='gradeA'>"
                                            . "<td>" . $i++ . "</td>"
                                            . "<td>" . $hasil['kode_anggota'] . "</td>"
                                            . "<td>" . $hasil['idlama'] . "</td>"
                                            . "<td>" . $hasil['nama'] . "</td>"
                                            . "<td>" . $verShow . "</td>"
                                            . "<td id='harga'>" . number_format($hasil['debet'], 0, '.', '.') . "</td>"
                                            . "<td id='harga'>" . number_format($hasil['kredit'], 0, '.', '.') . "</td>"
                                            . "<td id='harga'>" . number_format($hasil['saldo'], 0, '.', '.') . "</td></tr>";
//                                                    . "<td><a rel='tooltip-top' title='Edit' href='upanggota.php?$idcryp=$idbaris'>"
//                                                    . "<i class='icon-edit'></i></a> <a rel='tooltip-top' title='Hapus' href='javascript: delet(" . $hasil['id'] . ")'><i class='icon-remove'></i></a> </td>";
                                        }
                                        ?>
                                        <tr>
                                            <td colspan="5" id="harga"><b>Jumlah</b></td>
                                            <td id="harga"><b><?php echo number_format(array_sum($totaldebet), 0, '.', '.'); ?></b></td>
                                            <td id="harga"><b><?php echo number_format(array_sum($totalkredit), 0, '.', '.'); ?></b></td>
                                            <td id="harga"><b><?php echo number_format(array_sum($totaldebet) - array_sum($totalkredit), 0, '.', '.'); ?></b></td>
                                        </tr>


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

                                    $(document).keyup(function (e) {

                                        if (e.keyCode == 27) {
                                            reset()
                                        }   // esc
                                    });
                                    function reset() {
                                        $('#frmctk')[0].reset();
                                        $('#frmctk2')[0].reset();
                                    }



                                    $('#subfiltersaldo').attr('class', 'active');
                                    $('#laporan').attr('class', 'active');
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

                                    $(document).ready(function () {
                                        //retrive data json
                                        $('#provinsi').change(function () {
                                            $.getJSON('../anggota/getalamat.php', {action: 'getKab', kode_prop: $(this).val()}, function (json) {
                                                $('#kabupaten').html('');
                                                $('#kabupaten').append("<option value=''>Kabupaten</option>");
                                                $.each(json, function (index, row) {
                                                    $('#kabupaten').append('<option value=' + row.kodekab + '>' + row.namakab + '</option>');
                                                });
                                            });
                                        });

                                        $('#kabupaten').change(function () {
                                            $.getJSON('../anggota/getalamat.php', {action: 'getKec', kode_kab: $(this).val()}, function (json) {
                                                $('#kecamatan').html('');
                                                $('#kecamatan').append("<option value=''>Kecamatan</option>");
                                                $.each(json, function (index, row) {

                                                    $('#kecamatan').append('<option value=' + row.kodeKec + '>' + row.namaKec + '</option>');

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
                                        /*
                                         * Initialse DataTables, with no sorting on the 'details' column
                                         */

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
    </script>



</body>
</html>
