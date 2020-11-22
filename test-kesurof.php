<?php
require_once "php/classes/service.php";
?>
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
    <!-- Toastr -->
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">


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

        <!-- Modal: modalPoll -->
        <div class="modal fade right" id="modalPoll-1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
          aria-hidden="true" data-backdrop="false">
          <div class="modal-dialog modal-full-height modal-right modal-notify modal-info" role="document">
            <div class="modal-content">
              <!--Header-->
              <div class="modal-header">
                <p class="heading lead">SSD
                </p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true" class="white-text">×</span>
                </button>
              </div>
              <!--Body-->
              <div class="modal-body">
                <!-- Radio -->
                <div class="form-check mb-4">
                  <form action="rclone/token.php" method="post">
                    <input class="form-check-input" name="drive" type="radio" id="radio-179" value="gdrive" checked>
                    <label class="form-check-label" for="radio-179">Gdrive</label>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" name="drive" type="radio" id="radio-279" value="sharedrive">
                    <label class="form-check-label" for="radio-279">Share Drive</label>
                </div>
                <!-- Radio -->

                <!--Basic textarea-->
                <div class="md-form">
                  <label for="form79textarea">Nom du Gdrive - Share Drive</label>
                  <textarea type="text" name="nom" class="md-textarea form-control" rows="3"></textarea>
                </div>

                <div class="md-form">
                  <label for="form79textarea">Coller le token</label>
                  <textarea type="text" name="token" class="md-textarea form-control" rows="3"></textarea>
                </div>
              </div>
              <!--Footer-->
              <div class="modal-footer justify-content-center">
                 <button type="submit" class="btn btn-info">Valider</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        <!-- Modal: modalPoll -->

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
        <div class="modal fade" id="modalLibre" style="display: none;" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="titre-modal-libre">Logs du serveur</h4>
                    </div>
                    <div class="modal-body">
                        <div class="embed-responsive embed-responsive-16by9 z-depth-1-half" id="data-modal-libre">

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
                            <h1>
                                Installation Rclone
                            </h1>
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


                        <!-- Start 1er etape -->
                        <div class="col-md-6">
                            <div class="card card-info">
                                <div class="card-header">
                                    <h3 class="card-title">1er Étape : Identifiants API</h3>
                                </div>
                                <form action="rclone/identifiant.php" method="post" id="idOfForm">
                                <form role="form">
                                    <div class="card-body">

                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Coller ID clients OAuth 2.0 :</label>
                                                    <textarea class="form-control" rows="3" name="client_id" placeholder="Enter ..."></textarea>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label>Coller ID secret OAuth 2.0 :</label>
                                                    <textarea class="form-control" rows="3" name="client_secret" placeholder="Enter ..."></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                               </form>
                               <button class="btn btn-info float-right" onclick="doPreview();">Valider</button>
                               <script type="text/javascript">
                               function doPreview()
                               {
                               form=document.getElementById('idOfForm');
                               form.target='_blank';
                               form.submit();
                               form.action='rclone/identifiant.php';
                               }
                               </script>
                                    </div>
                            </div>
                        </div> <!-- End 1er etape -->

                        <!-- Start 2eme etape -->
                        <div class="col-md-6">
                            <div class="card card-secondary">
                                <div class="card-header">
                                    <h3 class="card-title">2ème Étape : Validation du Token</h3>
                                </div>
                                <form role="form" >
                                    <div class="card-body" >
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-info" style="float: right;" data-toggle="modal" data-target="#modalPoll-1">Poursuivre avec la validation du token</button>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- End 2eme etape -->


                    </div>
                    <!-- /.card -->

                    <div class="row">

                        <!-- Start Explications-->
                        <div class="col-md-6">
                            <div class="card card-warning card-outline">
                                <div class="card-header">
                                    <h3 class="card-title">
                                        <i class="fas fa-edit"></i>
                                        Guide installation
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-5 col-sm-3">
                                            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                                <a class="nav-link active" id="vert-tabs-home-tab" data-toggle="pill" href="#vert-tabs-home" role="tab" aria-controls="vert-tabs-home" aria-selected="true">rclone.conf</a>
                                                <a class="nav-link" id="vert-tabs-profile-tab" data-toggle="pill" href="#vert-tabs-profile" role="tab" aria-controls="vert-tabs-profile" aria-selected="false">2ème Étape</a>
                                                <a class="nav-link" id="vert-tabs-messages-tab" data-toggle="pill" href="#vert-tabs-messages" role="tab" aria-controls="vert-tabs-messages" aria-selected="false">3ème Étape</a>
                                            </div>
                                        </div>
                                        <div class="col-7 col-sm-9">
                                            <div class="tab-content" id="vert-tabs-tabContent">
                                                <div class="tab-pane text-left fade show active" id="vert-tabs-home" role="tabpanel" aria-labelledby="vert-tabs-home-tab">
                                                    Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                                                </div>
                                                <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                                                    Mauris tincidunt mi at erat gravida, eget tristique urna bibendum. Mauris pharetra purus ut ligula tempor, et vulputate metus facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Maecenas sollicitudin, nisi a luctus interdum, nisl ligula placerat mi, quis posuere purus ligula eu lectus. Donec nunc tellus, elementum sit amet ultricies at, posuere nec nunc. Nunc euismod pellentesque diam.
                                                </div>
                                                <div class="tab-pane fade" id="vert-tabs-messages" role="tabpanel" aria-labelledby="vert-tabs-messages-tab">
                                                    Morbi turpis dolor, vulputate vitae felis non, tincidunt congue mauris. Phasellus volutpat augue id mi placerat mollis. Vivamus faucibus eu massa eget condimentum. Fusce nec hendrerit sem, ac tristique nulla. Integer vestibulum orci odio. Cras nec augue ipsum. Suspendisse ut velit condimentum, mattis urna a, malesuada nunc. Curabitur eleifend facilisis velit finibus tristique. Nam vulputate, eros non luctus efficitur, ipsum odio volutpat massa, sit amet sollicitudin est libero sed ipsum. Nulla lacinia.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- End Explications-->

                        <!-- Start 3eme etape -->
                        <div class="col-md-6">
                            <div class="card card-success">
                                <div class="card-header">
                                    <h3 class="card-title"> Création Share drive effectuée  -  Affichage rclone.conf</h3>
                                </div>
                                <form role="form">
                                    <div class="card-body">
                                    <?php
                                    $file='rclone/files/rclone.conf';
                                    $contenu=file_get_contents($file);
                                    echo "<pre>$contenu</pre>"; 
                                    ?>
                                    </div>
                                </form>
                            </div>
                        </div> <!-- End 3eme etape -->


                    </div>

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
    <script type="text/javascript" src="dist/js/ssd_specific.js"></script>
    <!-- Toastr -->
    <script src="plugins/toastr/toastr.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="plugins/sweetalert2/sweetalert2.min.js"></script>


</body>

</html>