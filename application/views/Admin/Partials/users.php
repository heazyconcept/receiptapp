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
            <div class="row">
                <div class="col-md-5">
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">New User</h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-horizontal" id="UserForm">
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Full Name: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="FullName" id="FullName"
                                            placeholder="Full Name" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Email Address: </label>
                                    <div class="col-md-8">
                                        <input type="email" class="form-control" name="EmailAddress" id="EmailAddress"
                                            placeholder="@email.com" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Phone Number: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="PhoneNumber" id="PhoneNumber"
                                            placeholder="Phone Number" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">User Role: </label>
                                    <div class="col-md-8">
                                        <div class="radio-custom radio-default radio-inline">
                                            <input type="radio" id="AgentRadio" name="Role" value="Agent" checked />
                                            <label for="inputHorizontalMale">Agent</label>
                                        </div>
                                        <div class="radio-custom radio-default radio-inline">
                                            <input type="radio" id="AdminRadio" name="Role" value="Admin" />
                                            <label for="inputHorizontalFemale">Admin</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <input id="UserId" type="hidden" name="UserId">
                                        <button type="submit" class="btn btn-primary">Submit </button>
                                        <button type="reset" class="btn btn-default btn-outline">Reset</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="panel">
                        <header class="panel-heading">


                            <h3 class="panel-title">All Users</h3>



                        </header>
                        <div class="panel-body">
                            <table class="user-table table table-hover table-responsive table-striped w-full">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>Role</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>Role</th>
                                        <th></th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Panel Table Tools -->

            <!-- End Panel Table Tools -->
        </div>
    </div>
    <!-- End Page -->

    <!-- Footer -->
    <?php $this->load->view('Admin/Templates/footer'); ?>
    <!-- Core  -->
    <!-- <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->

    <?php $this->load->view('Templates/footer'); ?>

    <script>
    $(document).ready(function() {
        var userTable = $('.user-table').DataTable({
            "Destroy": true,
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo base_url("adminApi/FetchUsers") ?>',
                "dataType": "json",
                "type": "GET",
            },
            "columns": [{
                    "data": "FullName"
                },
                {
                    "data": "EmailAddress"
                },
                {
                    "data": "PhoneNumber"
                },
                {
                    "data": "Role"
                },
                {
                    "data": "Action"
                },

            ],
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('#UserForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($('form#UserForm')[0]);
            var url = '<?php echo base_url("adminApi/AddUser"); ?>'
            AjaxInit(url, formData, false, true);
        });
        $(document).on("click", ".delete-user", function() {
            var userId = $(this).data("id");
            var url = '<?php echo base_url("adminApi/DeleteUser/' + userId + '"); ?>'
            AjaxInit(url, {}, false, true);
        })
        $(document).on("click", ".edit-user", function() {
            var userId = $(this).data("id");

            $.post('<?php echo base_url("adminApi/GetUser/' + userId + '") ?>')
                .done(function(data) {
                    if (data == "") {
                        fatalMessage();
                    } else {
                        try {
                            var response = JSON.parse(data);
                            $("#UserForm").my({
                                ui: {
                                    "#FullName": "FullName",
                                    "#EmailAddress": "EmailAddress",
                                    "#PhoneNumber": "PhoneNumber",
                                    "#UserId": "Id",
                                    "input[name='Role']": "Role"
                                }
                            }, response);
                            $("#UserForm").my("remove");
                        } catch (error) {
                            console.log(error);
                            fatalMessage();


                        }
                    }
                })
        })
    });
    </script>
</body>


</html>