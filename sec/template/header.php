<?php include '../../config/kon.php'; ?>
<script type="text/javascript">
    function confirmps() {
        var passpertama = $('#password').val();
        var passkedua = $('#passwordconfirm').val();
        if ($('#password').val() != $('#passwordconfirm').val()) {
            $('#passwordconfirm').css('background', '#e74c3c');
            $('#password').css('background', '#e74c3c');
            $("#tombolsubmit").attr('disabled', 'disabled');

        } else if (passpertama == passkedua) {
            $('#passwordconfirm').css('background', '#2ecc71');
            $('#password').css('background', '#2ecc71');
            $("#tombolsubmit").removeAttr('disabled');
        }
    }
// 1 detik = 1000 
    window.setTimeout("waktu()", 1000);
    function waktu() {
        var tanggal = new Date();
        setTimeout("waktu()", 1000);
        document.getElementById("output").innerHTML = ("0" + tanggal.getHours()).slice(-2) + ":" + ("0" + tanggal.getMinutes()).slice(-2) + ":" + ("0" + tanggal.getSeconds()).slice(-2);
    }
</script> 
<body onLoad="waktu()">
    <header class="header white-bg">
        <div class="sidebar-toggle-box">
            <div data-original-title="Toggle Navigation" data-placement="right" class="icon-reorder tooltips"></div>
        </div>
        <!--logo start-->
        <a href="index.html" class="logo">PONPES <span>MATHOLI'UL ANWAR</span></a>
        <!--logo end-->
        <div class="nav notify-row" id="top_menu">
            <!--  notification start -->


            <!--  notification end -->
        </div>
        <div class="top-nav ">
            <!--search & user info start-->
            <ul class="nav pull-right top-menu">

                <!-- user login dropdown start-->
                <li class="dropdown">
                    <br>
                    <div style="font-size: 15px;"><?php $blnindo = date('m');
$hariindo = date('w');
include '../../config/tanggal.php';
echo $hariindo . ", " . date('d') . " " . $blnindo . " " . date('Y') . " --"; ?></div>

                </li>
                <li class="dropdown">
                    <br>
                    <div id="output" style="font-size: 15px;"></div>

                </li>
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <img alt="" src="../../img/avatar-mini4.jpg">
                        <span class="username"><?php echo $_SESSION['nama'] ?></span>
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu extended logout">
                        <div class="log-arrow-up"></div>
                        <li><a href="#modEdit" data-toggle="modal"><i class="icon-edit"></i>Edit User</a></li>
                        <li><a href=""><i class="icon-picture"></i>Edit Photo</a></li>
                        <li><a href="#modInfo" data-toggle="modal"><i class="icon-info"></i>Help</a></li>
                        <li><a href="../login/logout.php"><i class="icon-key"></i>Log Out</a></li>
                    </ul>
                </li>
                <!-- user login dropdown end -->
            </ul>
            <!--search & user info end-->
        </div>
    </header>

    <div class="modal fade" id="modInfo" tabindex="-1" role="dialog" aria-labelledby="ModEditUser" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Information</h4>
                </div>
                <div id="pesan" class="modal-body">
                    Kalau ada error hubungi BPSTI. :)
                </div>

            </div>
        </div>
    </div>
    <div class="modal fade" id="modEdit" tabindex="-1" role="dialog" aria-labelledby="ModEditUser" aria-hidden="true">
        <div class="modal-dialog" style="width: 400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <button aria-hidden="true" data-dismiss="modal"  class="close" type="button">×</button>
                    <h4 class="modal-title">Edit Password</h4>
                </div>
                <div class="modal-body">
                    <form id="edituser" class="cmxform form-horizontal tasi-form" method="post" action="../user/saveuser.php?jenis=edit">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Username</label>
                            <div class="col-sm-5">
                                <input readonly type="username" class="form-control" name="username" required id="username" placeholder="Username" value="<?php echo $_SESSION['username'] ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Password</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control" name="password" required id="password" placeholder="Password">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Password Confirm</label>
                            <div class="col-sm-5">
                                <input type="password" class="form-control" onkeyup="confirmps()" name="passwordconfirm" required id="passwordconfirm" placeholder="Password Confirm">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-4 col-lg-6">
                                <button type="submit" id="tombol" class="btn btn-danger">Simpan</button>
                                <button aria-hidden="true" data-dismiss="modal"  class="btn btn-default" type="button">Cancel</button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal -->