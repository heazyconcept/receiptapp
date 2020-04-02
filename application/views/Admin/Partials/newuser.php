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
            <!-- Panel Table Tools -->
            <div class="row">
                <div class="col-md-5">
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">New User</h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-horizontal">
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">Your Name: </label>
                                    <div class="col-md-9">
                                        <input type="text" class="form-control" name="name" placeholder="Full Name"
                                            autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">Your Gender: </label>
                                    <div class="col-md-9">
                                        <div class="radio-custom radio-default radio-inline">
                                            <input type="radio" id="inputHorizontalMale" name="inputRadiosMale2" />
                                            <label for="inputHorizontalMale">Male</label>
                                        </div>
                                        <div class="radio-custom radio-default radio-inline">
                                            <input type="radio" id="inputHorizontalFemale" name="inputRadiosMale2"
                                                checked />
                                            <label for="inputHorizontalFemale">Female</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">Your Email: </label>
                                    <div class="col-md-9">
                                        <input type="email" class="form-control" name="email" placeholder="@email.com"
                                            autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 form-control-label">Description: </label>
                                    <div class="col-md-9">
                                        <textarea class="form-control"
                                            placeholder="Briefly Describe Yourself"></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-9 offset-md-3">
                                        <button type="button" class="btn btn-primary">Submit </button>
                                        <button type="reset" class="btn btn-default btn-outline">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- End Panel Table Tools -->
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