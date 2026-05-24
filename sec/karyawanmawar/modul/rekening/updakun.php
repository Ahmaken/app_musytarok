<?php include'../../session/level2.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="keyword" content="">
        <link rel="shortcut icon" href="img/favicon.png">

        <title>COA | Keuangan Pondok Pesantren Matholi'ul Anwar</title>

        <!-- Bootstrap core CSS -->

        <?php include '../../template/head.php'; ?>
         <link href="../../css/jquery.autocomplete.css" rel="stylesheet" />
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
                        <div class="col-lg-4">
                            <section class="panel">

                                <header class="panel-heading">
                                    Edit Akun Rekening
                                </header>
                                <div class="panel-body">
                                    <?php
                                    include '../../config/security.php';
                                    $id = md5('kode');
                                    $group = md5('grup');
                                    $group = isset($_GET[$group]) ? $_GET[$group] : '';
                                    $status = isset($_GET['status']) ? $_GET['status'] : '';
                                    $idAkun = isset($_GET[$id]) ? $_GET[$id] : '';
                                    $idAkun = decryptIt($idAkun);
                                    //echo $kodeProg;
                                    //$un = isset($_GET['un']) ? $_GET['un'] : '';
                                    if ($status == "sukses") {

                                        echo "<div class='alert alert-success alert-block fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-ok-sign'></i>
                                            Success!
                                        </h4>
                                        <p>Data berhasil diubah...<br>
                                    </div>";
                                    } else if ($status == "gagal") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutuperror' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-remove'></i>
                                            Gagal
                                        </h4>
                                        <p>Proses gagal</p>
                                        <p>Error:" . $ket . "</p>
                                    </div>";
                                    } else if ($status == "hapus") {
                                        echo "<div class='alert alert-block alert-danger fade in'>
                                        <button data-dismiss='alert' id='tutup' class='close close-sm' type='button'>
                                            <i class='icon-remove'></i>
                                        </button>
                                        <h4>
                                            <i class='icon-signout'></i>
                                            Sukses
                                        </h4>
                                        <p>Data berhasil dihapus</p>
                                        </div>";
                                    }
                                    //isi form
                                    if (!empty($group)) {
                                        if ($group==1){
                                            $parent= "";
                                            $uraian = "";
                                            $q= mysql_query("SELECT kode, nama from ak_akun where id='$idAkun'");
                                            $hasil =  mysql_fetch_array($q);
                                            $kode= $hasil['kode'];
                                            $namaakun= $hasil['nama'];                                                    
                                            $grup= "";                                                    
                                        } else{
                                            $q= mysql_query("SELECT a.kode, a.grup, a.nama, (SELECT b.kode FROM ak_akun b WHERE b.kode=a.parent) AS kodeparent, "
                                                    . "(SELECT b.nama FROM ak_akun b WHERE b.kode=a.parent) AS namaparent  FROM ak_akun a WHERE a.id='$idAkun'");
                                            $hasil = mysql_fetch_array($q);
                                            $kode= $hasil['kode'];
                                            $namaakun= $hasil['nama'];
                                            $grup= $hasil['grup']-1;
                                            $parent= $hasil['kodeparent'];
                                            $uraian = $hasil['namaparent'];
                                        }
                                     }
                                    ?>

                                    <div class="form" id="addakun">
                                        <form id="tambahrek" class="cmxform form-horizontal tasi-form" method="post" action="prosakun.php?type=update">
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Kode Parent</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="kodeparent"  placeholder="Kode Parent" id="kode" value="<?php echo $parent; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Uraian</label>
                                                <div class="col-sm-8">
                                                    <input type="text" id="uraian" class="form-control" name="uraian"  readonly="" placeholder="Uraian" value="<?php echo $uraian; ?>">
                                                    <input type="hidden" id="grup" class="form-control" name="grup" value ="<?php echo $grup; ?>">
                                                    <input type="hidden" id="id" class="form-control" name="id" value ="<?php echo $idAkun; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Kode Akun</label>
                                                <div class="col-sm-8">
                                                    <input type="number" class="form-control" name="kodeakun" required  placeholder="Kode Akun" value="<?php echo $kode; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-sm-3 control-label">Nama Akun</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control" name="namaakun" required  placeholder="Nama Akun" value="<?php echo $namaakun; ?>">
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-lg-offset-4 col-lg-6">
                                                    <button type="submit" id="tombol" class="btn btn-success">Update</button>
                                                    <a href="../rekening/" class="btn btn-default">Cancel</a>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                            </section>

                        </div>
                    </div>
                    <div class="row state-overview">
                    </div>
                </section>

            </section>
        </section>
        <!--main content end-->
        <!--footer start-->
        <?php include '../../template/foot.php' ?>
        <!--footer end-->
    </section>
    <?php include '../../template/footer.php' ?>
     <script src="../../js/nominal.js" ></script>
       <script src="../../js/jquery.autocomplete.js"></script>
    <script>
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
        $('#menusubprogram').attr('class', 'active');
        $('#master').attr('class', 'active');
        setInterval(function() {
            $("#tutup").click();
        }, 3000);

        function idConfirm(idcon) {
            $('#idconfirm').val(idcon);
            $('#pesan').text('Anda yakin akan menghapus Instansi dengan KODE ' + idcon);
            // alert(idcon);
        }
    </script>
    <script>

        //owl carousel

        $(document).ready(function() {
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
