<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <html lang="fr">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>SSD | Blank Page</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="dist/css/adminlte.min.css">
    <!-- Google Font: Source Sans Pro -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">


<body class="hold-transition sidebar-mini sidebar-collapse">


    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- START top navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="index.php" class="nav-link">Accueil</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="blankpage.html" class="nav-link">Blankpage</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="installation.html" class="nav-link">Installation</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="#" class="nav-link">Discord</a>
                </li>
            </ul>
            <!-- END top navbar left links -->
            <!-- START top navbar right links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <button type="button" class="btn btn-info float-right" data-toggle="modal" data-target="#modalYT1">Logs
                        SSD</button>
                </li>
                <!-- END top navbar right links -->
            </ul>
        </nav>
        <!--Start content modal 2-->
        <div class="modal fade" id="modalYT1" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Logs du serveur</h4>
                    </div>
                    <div class="modal-body">
                        <div class="embed-responsive embed-responsive-16by9 z-depth-1-half">
                            <iframe class="embed-responsive-item" src="http://178.170.54.173/logtail/" allowfullscreen></iframe>
                        </div>
                    </div>
                    <div class="modal-footer float-right">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
        <!--End content modal 2-->
        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="index.php" class="brand-link">
                <img src="dist/img/AdminLTELogo.png" alt="SSD Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Script Seedbox Docker</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                        <li class="nav-item">
                            <a href="index.php" class="nav-link">
                                <i class="nav-icon fas fa-th"></i>
                                <p>
                                    Accueil
                                </p>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active"><a href="index.php">Accueil</a></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">

                            <!-- Profile Image -->
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">

                                    <h3 class="profile-username text-center">Nom du serveur</h3>

                                    <p class="text-muted text-center"><?php include 'php/system.php'; ?></p>

                                    <ul class="list-group list-group-unbordered mb-3">
                                        <li class="list-group-item">
                                            <b>En marche depuis</b> <a class="float-right"><?php include 'php/reboot.php'; ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Espace disque libre</b> <a class="float-right"><?php include 'php/disque.php'; ?></a>
                                        </li>
                                        <li class="list-group-item">
                                            <b>Adresse IP</b> <a class="float-right"><?php include 'php/ip.php'; ?></a>
                                        </li>
                                    </ul>

                                    <form action="/php/index.php" method="post">
                                        <button type="submit" name="submit" class="btn btn-warning btn-block">Relancer Docker</button>
                                    </form>
                            
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->

                            <!-- About Me Box -->
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Dernières activités</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <strong>Restart Plex</strong>
                                    <p class="text-muted">
                                        Le 02/11/2020 à 13h21, status OK
                                    </p>
                                    <hr>
                                    <strong>Installation Sonarr</strong>
                                    <p class="text-muted">Le 02/11/2020 à 13h56, status OK</p>
                                    <hr>
                                    <strong>Backup auto</strong>
                                    <p class="text-muted"> Le 02/11/2020 à 13h21, status OK</p>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                        <!-- /.col -->
                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li>Applications</li>
                                    </ul>
                                </div><!-- /.card-header -->
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="apps_seedbox">
                                       </div>

                                                <!-- app -->
                                                <div class=col-md-4>
                                                    <div class="post">
                                                        <div class="card card-info card-outline">
                                                            <div class="card-body user-block">
                                                                <img class="img-circle img-bordered-sm" src="https://www.scriptseedboxdocker.com/wp-content/uploads/2020/05/radarr.png" alt="user image">
                                                                <span class="username">
                                                                    <a href="#">Radarr</a>
                                                                </span>
                                                                <span class="description">Version 3.0.4.991</span>
                                                            </div>

                                                            <div class="card-footer" id="toto">
                                                                  <form action="/php/index.php" method="post">
                                                                   <a href="php/index.php?reset=true" class="link-black start-stop-button text-sm mr-2" id="reset" name="reset"><i class="fas fa-share mr-1"></i>Restart</a>
                                                                   <a href="php/index.php?stop=true" class="link-black start-stop-button text-sm mr-2" id="stop" name="stop"><i class="fas fa-stop mr-1"></i>Stop</a>

                                                                    <span class="float-right">
                                                                       <button type="submit" name="radarr" id="status" class="btn btn-block btn-success btn-sm text-with" onclick="myFunction()">Installer</button>
                                                                    </span>
                                                                  </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.app -->


                                    </div>
                                    <!-- /.tab-content -->
                                </div><!-- /.card-body -->
                            </div>
                            <!-- /.nav-tabs-custom -->
                        </div>
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                </div>
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <div class="float-right d-none d-sm-block">
                <b>Version</b> 1.0.0
            </div>
            <strong>Copyright &copy; 2020 <a href="http://scriptseedboxdocker.com">SSD</a>.</strong> All rights
            reserved.
        </footer>

        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <!-- jQuery -->
    <script src="plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE App -->
    <script src="dist/js/adminlte.min.js"></script>
    <script type="text/javascript" src="dist/js/scripts.js"></script> 

</body>

</html>