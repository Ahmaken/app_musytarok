<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Login Sistem Informasi Manajemen Aset</title>

        <?php
        require_once '../../template/head.php';

        if (!empty($_GET['error'])) {
            echo "<script>
	  window.alert('Username atau password tidak cocok.');
        </script>";
        }
        ?>
        <style>
            .pemisah{
                padding-bottom: 230px;
            }
            </style>
    </head>

    <body class="login-body">
        <div class="container">
            <form class="form-signin" id='frmlogin' method="post" action="ceklogin.php">
                <h2 class="form-signin-heading">Pondok Pesantren Matholi'ul Anwar</h2>
                <div class="login-wrap">
                    <input type="text" required="" class="form-control" placeholder="Username" name="username" id="username" autofocus>
                    <input type="password" required="" class="form-control" name="password" id="password" placeholder="Password">
                    <!--                    <label class="checkbox">
                                            <input type="checkbox" value="remember-me"> Remember me
                                        </label>-->
                    <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>
<a href='../../../index.php'class="btn btn-large btn-block">Menu Utama</a>
                </div>
            </form>

        </div>
        <div class="pemisah">
            </div>
       <div class="site-footer">
            <div class="text-center">
                Enterprise Management Sistem  2015 &copy; Pondok Pesantren Matholi'ul Anwar
                
            </div>
        </div>
        

    </body>
     <script src="../../js/jquery.js"></script>
    <script src="../../js/jquery-1.8.3.min.js"></script>
    <script src="../../js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="../../js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="../../js/jquery.scrollTo.min.js"></script>
</html>
