<!DOCTYPE html>
<html lang="en" dir="ltr">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>Mono - Responsive Admin & Dashboard Template</title>

        <!-- GOOGLE FONTS -->
        <link href="https://fonts.googleapis.com/css?family=Karla:400,700|Roboto" rel="stylesheet">
        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/material/css/materialdesignicons.min.css'); ?>" rel="stylesheet" />
        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/simplebar/simplebar.css" rel="stylesheet'); ?>" />

        <!-- PLUGINS CSS STYLE -->
        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/nprogress/nprogress.css'); ?>" rel="stylesheet" />
        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/jvectormap/jquery-jvectormap-2.0.3.css'); ?>" rel="stylesheet" />
        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/daterangepicker/daterangepicker.css'); ?>" rel="stylesheet" />
        <!-- MONO CSS -->
        <link id="main-css-href" rel="stylesheet" href="<?php echo $this->helper->url->baseurl('public/admin/css/style.css'); ?>" />

        <link href="<?php echo $this->helper->url->baseurl('public/admin/plugins/DataTables/DataTables-1.10.18/css/jquery.dataTables.min.css'); ?>" rel="stylesheet" />

        <!-- FAVICON -->
        <link href="<?php echo $this->helper->url->baseurl('public/admin/images/favicon.png'); ?>" rel="shortcut icon" />

        <!--
          HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries
        -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/plugins/nprogress/nprogress.js');?>"></script>
        
    </head>

    <body class="navbar-fixed sidebar-fixed" id="body">
        <script>
            NProgress.configure({showSpinner: false});
            NProgress.start();
        </script>
        <!-- ==============WRAPPER========================= -->
        <div class="wrapper">
            <!-- ============== LEFT SIDEBAR WITH OUT FOOTER ============ -->
            <?php echo $_side_menu; ?>
            <!-- ============= PAGE WRAPPER ============= -->
            <div class="page-wrapper">
                <!-- Header -->
                <?php echo $_header; ?>
                <!-- Body -->
                <?php echo $_body_; ?>
                <!-- Footer -->
                <?php echo $_footer; ?>
            </div>
        </div>
        <!-- Card Offcanvas -->
        <div class="card card-offcanvas" id="contact-off" >
            <div class="card-header">
                <h2>Contacts</h2>
                <a href="#" class="btn btn-primary btn-pill px-4">Add New</a>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <input type="text" class="form-control form-control-lg form-control-secondary rounded-0" placeholder="Search contacts...">
                </div>
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-01.jpg" alt="User Image">
                            <span class="active bg-primary"></span>
                        </a>
                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Selena Wagner</span>
                            <span class="discribe">Designer</span>
                        </a>
                    </div>
                </div>
                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-02.jpg" alt="User Image">
                            <span class="active bg-primary"></span>
                        </a>
                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Walter Reuter</span>
                            <span>Developer</span>
                        </a>
                    </div>
                </div>

                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-03.jpg" alt="User Image">
                        </a>
                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Larissa Gebhardt</span>
                            <span>Cyber Punk</span>
                        </a>
                    </div>
                </div>

                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-04.jpg" alt="User Image">
                        </a>

                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Albrecht Straub</span>
                            <span>Photographer</span>
                        </a>
                    </div>
                </div>

                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-05.jpg" alt="User Image">
                            <span class="active bg-danger"></span>
                        </a>
                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Leopold Ebert</span>
                            <span>Fashion Designer</span>
                        </a>
                    </div>
                </div>

                <div class="media media-sm">
                    <div class="media-sm-wrapper">
                        <a href="user-profile.html">
                            <img src="images/user/user-sm-06.jpg" alt="User Image">
                            <span class="active bg-primary"></span>
                        </a>
                    </div>
                    <div class="media-body">
                        <a href="user-profile.html">
                            <span class="title">Selena Wagner</span>
                            <span>Photographer</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>


        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/jquery/jquery.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/simplebar/simplebar.min.js'); ?>"></script>
        <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>

        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/apexcharts/apexcharts.js'); ?>"></script>

        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/jvectormap/jquery-jvectormap-world-mill.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/jvectormap/jquery-jvectormap-us-aea.js'); ?>"></script>

        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/daterangepicker/moment.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            jQuery(document).ready(function () {
                jQuery('input[name="dateRange"]').daterangepicker({
                    autoUpdateInput: false,
                    singleDatePicker: true,
                    locale: {
                        cancelLabel: 'Clear'
                    }
                });
                jQuery('input[name="dateRange"]').on('apply.daterangepicker', function (ev, picker) {
                    jQuery(this).val(picker.startDate.format('MM/DD/YYYY'));
                });
                jQuery('input[name="dateRange"]').on('cancel.daterangepicker', function (ev, picker) {
                    jQuery(this).val('');
                });
            });
        </script>
        <script src="https://unpkg.com/hotkeys-js/dist/hotkeys.min.js"></script>
        
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/simplebar/simplebar.min.js'); ?>"></script>

        <script src="<?php echo $this->helper->url->baseurl('public/admin/js/mono.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/js/chart.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/js/map.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/js/custom.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/jquery/jquery.min.js'); ?>"></script>
        <script src="<?php echo $this->helper->url->baseurl('public/admin/plugins/DataTables/DataTables-1.10.18/js/jquery.dataTables.min.js'); ?>"></script>
        
        <!--  -->
    </body>
</html>