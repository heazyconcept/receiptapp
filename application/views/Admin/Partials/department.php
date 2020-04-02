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
                            <h3 class="panel-title">New Department</h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-horizontal" id="DepartmentForm">
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Department Name: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="Department" id="Department"
                                            placeholder="Department Name" autocomplete="off" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Department Address: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="EmailAddress" id="EmailAddress"
                                            placeholder="Department Address" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-md-8 offset-md-4">
                                        <input id="DepartmentId" type="hidden" name="DepartmentId">
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


                            <h3 class="panel-title">All Department</h3>



                        </header>
                        <div class="panel-body">
                            <table class="user-table table table-hover table-responsive table-striped w-full">
                                <thead>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Department Email</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <?php if (!empty($allDepartments)): ?>
                                <tbody>


                                    <?php foreach ($allDepartments as $department): ?>
                                    <tr>
                                        <td><?php echo $department->Department ?></td>
                                        <td><?php echo $department->EmailAddress ?>
                                        </td>
                                        <td><button data-id="<?php echo $department->Id; ?>" class="edit-department btn btn-dark"><i
                                                    class="icon wb-pencil" aria-hidden="true"></i> Edit</button></td>
                                    </tr>
                                    <?php endforeach;?>
                                </tbody>
                                <?php endif;?>

                                <tfoot>
                                    <tr>
                                        <th>Department Name</th>
                                        <th>Department Email</th>
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

    <?php $this->load->view('Templates/footer'); ?>

    <script>
    $(document).ready(function() {
        var userTable = $('.user-table').DataTable({
            "Destroy": true,
            "pageLength": 50,
            dom: 'Bfrtip',
            buttons: [
                'copy', 'csv', 'excel', 'pdf', 'print'
            ]
        });
        $('#DepartmentForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($('form#DepartmentForm')[0]);
            var url = '<?php echo base_url("adminApi/AddDepartment"); ?>'
            AjaxInit(url, formData, false, true);
        });
        $(document).on("click", ".delete-user", function() {
            var userId = $(this).data("id");
            var url = '<?php echo base_url("adminApi/DeleteUser/' + userId + '"); ?>'
            AjaxInit(url, {}, false, true);
        })
        $(document).on("click", ".edit-department", function() {
            var departmentId = $(this).data("id");
            $.post('<?php echo base_url("adminApi/GetDepartment/' + departmentId + '") ?>')
                .done(function(data) {
                    if (data == "") {
                        fatalMessage();
                    } else {
                        try {
                            var response = JSON.parse(data);
                            $("#DepartmentForm").my({
                                ui: {
                                    "#Department": "Department",
                                    "#EmailAddress": "EmailAddress",
                                    "#DepartmentId": "Id"
                                }
                            }, response);
                            $("#DepartmentForm").my("remove");
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