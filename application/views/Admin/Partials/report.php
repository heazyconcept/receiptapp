<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">
<?php $this->load->view('Templates/header'); ?>
<?php $date7daysfromnow = strtotime("-7 day"); ?>

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
                            <h3 class="panel-title">Report</h3>

                        </header>
                        <div class="panel-body">
                            <form class="form-veritcal" id="ReceiptForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Receipt Categories: </label>
                                            <div>
                                                <select id="CategoryId" class="form-control">
                                                    <option value="99">All</option>
                                                    <?php foreach ($receiptCategories as $category): ?>
                                                    <option value="<?php echo $category->Id ?>">
                                                        <?php echo $category->Category;  ?> </option>
                                                    <?php endforeach;?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Department: </label>
                                            <div>
                                                <select id="DepartmentId" class="form-control">
                                                    <option value="99">All</option>
                                                    <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo $dept->Id ?>">
                                                        <?php echo $dept->Department;  ?> </option>
                                                    <?php endforeach;?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-control-label">From </label>
                                            <div>
                                                <input type="date" class="form-control" id="dateFrom" placeholder="from"
                                                    value="<?php echo date("Y-m-d", $date7daysfromnow); ?>"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-control-label">To </label>
                                            <div>
                                                <input type="date" class="form-control" id="dateTo" placeholder="from"
                                                    value="<?php echo date("Y-m-d"); ?>" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-control-label" style="visibility:hidden;">Mode Of
                                                Payment:</label>
                                            <div>

                                                <button type="button" class="btn btn-primary btn-filter">Filter
                                                </button>

                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 ">
                    <div class="panel">
                        <header class="panel-heading">
                            <button type="button" class="btn btn-primary btn-export pull-right"> Export</button>
                            <h3 class="panel-title">All Payments</h3>
                        </header>
                        <div class="panel-body">
                            <table class="receipt-table table table-hover table-responsive table-striped w-full">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt Number</th>
                                        <th>Category</th>
                                        <th>Department</th>
                                        <th>Client Name</th>
                                        <th>Amount Paid</th>
                                        <th>Currency</th>
                                        <th>Issued By</th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt Number</th>
                                        <th>Category</th>
                                        <th>Department</th>
                                        <th>Client Name</th>
                                        <th>Amount Paid</th>
                                        <th>Currency</th>
                                        <th>Issued By</th>
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
        var receiptTable = $('.receipt-table').DataTable({
            "Destroy": true,
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo base_url("adminApi/FetchReport") ?>',
                "dataType": "json",
                "type": "GET",
                "data": function(d) {
                    d.CategoryId = $("#CategoryId").val();
                    d.DepartmentId = $("#DepartmentId").val();
                    d.From = $("#dateFrom").val();
                    d.To = $("#dateTo").val();
                }
            },
            "columns": [{
                    "data": "Date"
                },
                {
                    "data": "ReceiptNumber"
                },
                {
                    "data": "Category"
                },
                {
                    "data": "Department"
                },
                {
                    "data": "ClientName"
                },
                {
                    "data": "AmountPaid"
                },
                {
                    "data": "Currency"
                },

                {
                    "data": "IssuedBy"
                }

            ],

        });
        $(".btn-filter").click(function() {
            receiptTable.ajax.reload();
        })
        $(".btn-export").click(function() {
            var url = "<?php echo base_url('adminApi/GetReportCSV') ?>";
            $.post(url)
                .done(function(data) {
                    try {
                        response = JSON.parse(data);
                        if (response.StatusCode == "00") {
                            
                            var hiddenElement = document.createElement('a');
                            hiddenElement.href = 'data:text/csv;charset=utf-8,' + 
                            encodeURI(response.StatusMessage);
                            hiddenElement.target = '_blank';
                            hiddenElement.download = 'payment_report.csv';
                            hiddenElement.click();

                        } else {
                            errorMessage(response.StatusMessage);

                        }
                    } catch (error) {
                        console.log(error);
                        fatalMessage();
                    }
                })
                .fail(function(err) {
                    console.log(err);
                    fatalMessage();
                })

        })

    });
    </script>
</body>


</html>