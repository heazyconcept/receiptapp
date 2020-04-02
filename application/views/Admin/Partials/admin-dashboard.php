<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<?php $this->load->view('Templates/header'); ?>


<body class="animsition site-navbar-small dashboard">
    <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <?php $this->load->view('Admin/Templates/navigation'); ?>

    <!-- Page -->
    <div class="page">
        <div class="page-content container-fluid">
            <div class="row" data-plugin="matchHeight" data-by-row="true">
                <!-- First Row -->
                <!-- Completed Options Pie Widgets -->
                <div class="col-xxl-3">
                    <div class="row h-full" data-plugin="matchHeight">
                        <div class="col-xxl-12 col-lg-4 col-sm-4">
                            <div class="card card-shadow card-completed-options">
                                <div class="card-block p-30">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="counter text-left blue-grey-700">
                                                <div class="counter-label mt-10">All Clients
                                                </div>
                                                <div class="counter-number font-size-40 mt-10">
                                                    <?php echo number_format($clientCount) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="pie-progress pie-progress-sm" data-plugin="pieProgress"
                                                data-valuemax="100" data-barcolor="#57c7d4" data-size="100"
                                                data-barsize="10" data-goal="86" aria-valuenow="86" role="progressbar">
                                                <span class="pie-progress-number blue-grey-700 font-size-20">
                                                    86%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12 col-lg-4 col-sm-4">
                            <div class="card card-shadow card-completed-options">
                                <div class="card-block p-30">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="counter text-left blue-grey-700">
                                                <div class="counter-label mt-10">Total Receipts
                                                </div>
                                                <div class="counter-number font-size-40 mt-10">
                                                    <?php echo number_format($receiptCount) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="pie-progress pie-progress-sm" data-plugin="pieProgress"
                                                data-valuemax="100" data-barcolor="#62a8ea" data-size="100"
                                                data-barsize="10" data-goal="62" aria-valuenow="62" role="progressbar">
                                                <span class="pie-progress-number blue-grey-700 font-size-20">
                                                    62%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-12 col-lg-4 col-sm-4">
                            <div class="card card-shadow card-completed-options">
                                <div class="card-block p-30">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="counter text-left blue-grey-700">
                                                <div class="counter-label mt-10">Pending Receipts
                                                </div>
                                                <div class="counter-number font-size-40 mt-10">
                                                   <?php echo number_format($pendingReceiptCount) ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="pie-progress pie-progress-sm" data-plugin="pieProgress"
                                                data-valuemax="100" data-barcolor="#926dde" data-size="100"
                                                data-barsize="10" data-goal="56" aria-valuenow="56" role="progressbar">
                                                <span class="pie-progress-number blue-grey-700 font-size-20">
                                                    56%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Completed Options Pie Widgets -->
            </div>
        </div>
    </div>
    <!-- End Page -->

    <!-- Footer -->
    <?php $this->load->view('Admin/Templates/footer'); ?>
    <!-- Core  -->
    <!-- <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->

    <?php $this->load->view('Templates/footer'); ?>


</body>


</html>