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
                <div class="col-md-12">
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">New Client</h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-veritcal" id="ClientForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Client Name: </label>
                                            <div>
                                                <input type="text" class="form-control" name="FullName" id="FullName"
                                                    placeholder="Full Name" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Email Address: </label>
                                            <div>
                                                <input type="email" class="form-control" name="EmailAddress"
                                                    id="EmailAddress" placeholder="@email.com" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Phone Number: </label>
                                            <div>
                                                <input type="text" class="form-control" name="PhoneNumber"
                                                    id="PhoneNumber" placeholder="Phone Number" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Company Name: </label>
                                            <div>
                                                <input type="text" class="form-control" name="Company" id="Company"
                                                    placeholder="Company Name" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label">Contact Address: </label>
                                            <div>
                                                <input type="text" class="form-control" name="ResidentialAddress"
                                                    id="ResidentialAddress" placeholder="Contact Address"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-control-label" style="visibility:hidden">Company Name:
                                            </label>
                                            <div>
                                                <input id="ClientId" type="hidden" name="ClientId">
                                                <button type="submit" class="btn btn-primary">Submit </button>
                                                <button type="reset" class="btn btn-default btn-outline">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <!-- Panel Table Tools -->
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">All Clients</h3>
                        </header>
                        <div class="panel-body">
                            <table class="client-table table table-hover table-responsive  table-striped w-full">
                                <thead>
                                    <tr>
                                        <th>Client Number</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>Contact Address</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Client Number</th>
                                        <th>Name</th>
                                        <th>Company</th>
                                        <th>Email Address</th>
                                        <th>Phone Number</th>
                                        <th>Contact Address</th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!-- End Panel Table Tools -->
                </div>
            </div>

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
        var clientTable = $('.client-table').DataTable({
            "Destroy": true,
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo base_url("adminApi/FetchClients") ?>',
                "dataType": "json",
                "type": "GET",
            },
            "columns": [{
                    "data": "ClientNumber"
                },
                {
                    "data": "FullName"
                },
                {
                    "data": "Company"
                },
                {
                    "data": "EmailAddress"
                },
                {
                    "data": "PhoneNumber"
                },
                {
                    "data": "Address"
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
        $('#ClientForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($('form#ClientForm')[0]);
            var url = '<?php echo base_url("adminApi/AddClient"); ?>'
            AjaxInit(url, formData, false, true);
        });
        $(document).on("click", ".edit-client", function() {
            var clientId = $(this).data("id");

            $.post('<?php echo base_url("adminApi/GetClient/' + clientId + '") ?>')
                .done(function(data) {
                    if (data == "") {
                        fatalMessage();
                    } else {
                        try {
                            var response = JSON.parse(data);
                            $("#ClientForm").my({
                                ui: {
                                    "#FullName": "FullName",
                                    "#EmailAddress": "EmailAddress",
                                    "#PhoneNumber": "PhoneNumber",
                                    "#Company": "Company",
                                    "#ResidentialAddress": "ResidentialAddress",
                                    "#ClientId": "Id",
                                }
                            }, response);
                            $("#ClientForm").my("remove");
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