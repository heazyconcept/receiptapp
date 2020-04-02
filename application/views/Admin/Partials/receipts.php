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
                            <h3 class="panel-title">New Receipt</h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-veritcal" id="ReceiptForm">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Receipt Categories: </label>
                                            <div>
                                                <select name="CategoryId" id="CategoryId" class="form-control">
                                                    <?php foreach ($receiptCategories as $category): ?>
                                                    <option value="<?php echo $category->Id ?>">
                                                        <?php echo $category->Category;  ?> </option>
                                                    <?php endforeach;?>
                                                </select>

                                            </div>
                                        </div>
                                        <div class="form-group tag-input" style="position:relative;display:none;">
                                            <label class="form-control-label">Receipt Tag (For Others Only)</label>
                                            <div>
                                            <select name="ReceiptTag" class="form-control" id="ReceiptTag">
                                            <?php foreach ($receiptTags as $tag): ?>
                                                    <option value="<?php echo $tag->Tags ?>">
                                                        <?php echo $tag->Tags;  ?> </option>
                                                    <?php endforeach;?>
                                            </select>
                                          
                                                
                                            </div>
                                           
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Department: </label>
                                            <div>
                                                <select name="DepartmentId" id="DepartmentId" class="form-control">
                                                    <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo $dept->Id ?>">
                                                        <?php echo $dept->Department;  ?> </option>
                                                    <?php endforeach;?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group" style="position:relative;">
                                            <label class="form-control-label">Client Name: </label>
                                            <div>
                                                <input type="text" class="form-control" name="FullName" id="FullName"
                                                    placeholder="search or add new client" autocomplete="off" />
                                            </div>
                                            <div class="search-suggestion" style="display:none;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Email Address: </label>
                                            <div>
                                                <input type="email" class="form-control" name="EmailAddress"
                                                    id="EmailAddress" placeholder="@email.com" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Phone Number: </label>
                                            <div>
                                                <input type="text" class="form-control" name="PhoneNumber"
                                                    id="PhoneNumber" placeholder="Phone Number" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Contact Address: </label>
                                            <div>
                                                <input type="text" class="form-control" name="ResidentialAddress"
                                                    id="ResidentialAddress" placeholder="Contact Address"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Amount: </label>
                                            <div class="row amount-wrapper">
                                                <div class="col-md-7">
                                                    <input type="text" class="form-control" name="Amount" id="Amount"
                                                        placeholder="Amount" autocomplete="off" />
                                                </div>
                                                <div class="col-md-5">
                                                    <select name="Currency" id="Currency" class="form-control">
                                                        <option value="USD">Dollars</option>
                                                        <option value="NGN">Naira</option>
                                                    </select>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Payment Details: </label>
                                            <div>
                                                <input type="text" class="form-control" name="PaymentDetails"
                                                    id="PaymentDetails" placeholder="Payment Details"
                                                    autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Mode Of Payment:</label>
                                            <div>
                                                <select id="ModeOfPayment" name="ModeOfPayment" class="form-control">
                                                    <option value="POS">POS</option>
                                                    <option value="CASH">CASH</option>
                                                    <option value="FUNDS TRANSFER">FUNDS TRANSFER</option>
                                                    <option value="CHEQUE/DRAFT">CHEQUE/DRAFT</option>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label">Description: </label>
                                            <div>
                                                <textarea name="Description" id="Description" autocomplete="off"
                                                    class="form-control" placeholder="Description"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-control-label" style="visibility:hidden;">Mode Of
                                                Payment:</label>
                                            <div>
                                                <input id="ClientId" type="hidden" name="ClientId">
                                                <input id="ReceiptId" type="hidden" name="ReceiptId">
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
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">All Receipts</h3>
                        </header>
                        <div class="panel-body">
                            <table class="receipt-table table table-hover table-responsive table-striped w-full">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt ID</th>
                                        <th>Receipt Number</th>
                                        <th>Category</th>
                                        <th>Full Name</th>
                                        <th>Email Address</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Issued By</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th>Receipt ID</th>
                                        <th>Receipt Number</th>
                                        <th>Category</th>
                                        <th>Full Name</th>
                                        <th>Email Address</th>
                                        <th>Amount</th>
                                        <th>Payment Method</th>
                                        <th>Issued By</th>
                                        <th>Status</th>
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
        var receiptTable = $('.receipt-table').DataTable({
            "Destroy": true,
            "pageLength": 50,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '<?php echo base_url("adminApi/FetchReceipts") ?>',
                "dataType": "json",
                "type": "GET",
            },
            "columns": [{
                    "data": "Date"
                },
                {
                    "data": "ReceiptId"
                },
                {
                    "data": "ReceiptNumber"
                },
                {
                    "data": "ReceiptCategory"
                },
                {
                    "data": "FullName"
                },
                {
                    "data": "EmailAddress"
                },

                {
                    "data": "Amount"
                },
                {
                    "data": "PaymentMethod"
                },
                {
                    "data": "IssuedBy"
                },
                {
                    "data": "Status"
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
        $('#ReceiptForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData($('form#ReceiptForm')[0]);
            var url = '<?php echo base_url("adminApi/InitiateReceipt"); ?>'
            AjaxInit(url, formData, false, true);
        });
        $('#FullName').on("keypress", function() {
            var searchParam = $(this).val();
            $.post("<?php echo base_url('adminApi/SearchClient/" + searchParam + "') ?>")
                .done(function(data) {
                    ;

                    if (data == "") {
                        $('.search-suggestion').hide("slow");

                    } else {
                        var response = JSON.parse(data);
                        var html = "";
                        $.each(response, function(indexInArray, valueOfElement) {
                            html += `<div data-id="${valueOfElement.ClientId}" class="search-item">
                            <div class="search-client-name">
                            <h4>${valueOfElement.ClientName}</h4>
                            </div>
                            <div class="search-client-details">
                            <p>${valueOfElement.ClientDetails}</p>
                            </div>
                            <div class="divider"></div>
                         </div>`;

                        });
                        $('.search-suggestion').html(html);
                        $('.search-suggestion').show("slow");
                    }
                })
        })
        $(document).on("click", ".search-item", function() {
            var clientId = $(this).data("id");
            $('.search-suggestion').hide("slow");
            $.post("<?php echo base_url('adminApi/GetClient/" + clientId + "') ?>")
                .done(function(data) {
                    try {
                        var response = JSON.parse(data);

                        $("#ReceiptForm").my({
                            ui: {
                                "#FullName": "FullName",
                                "#EmailAddress": "EmailAddress",
                                "#PhoneNumber": "PhoneNumber",
                                "#ResidentialAddress": "ResidentialAddress",
                                "#ClientId": "Id",


                            }
                        }, response);
                        $("#ReceiptForm").my("remove");
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
        $(document).on("click", ".edit-receipt", function() {
            var receiptId = $(this).data("id");
            $.post('<?php echo base_url("adminApi/GetReceipt/' + receiptId + '") ?>')
                .done(function(data) {
                    ;
                    if (data == "") {
                        fatalMessage();
                    } else {
                        try {
                            var response = JSON.parse(data);
                            $("#ReceiptForm").my({
                                ui: {
                                    "#CategoryId": "CategoryId",
                                    "#DepartmentId": "DepartmentId",
                                    "#FullName": "FullName",
                                    "#EmailAddress": "EmailAddress",
                                    "#PhoneNumber": "PhoneNumber",
                                    "#ResidentialAddress": "ResidentialAddress",
                                    "#Amount": "Amount",
                                    "#Currency": "Currency",
                                    "#PaymentDetails": "PaymentDetails",
                                    "#ModeOfPayment": "ModeOfPayment",
                                    "#Description": "Description",
                                    "#ClientId": "ClientId",
                                    "#ReceiptId": "ReceiptId",
                                    "#ReceiptTag": "ReceiptTag",
                                }
                            }, response);
                            $("#ReceiptForm").my("remove");
                            if (response.CategoryId == 4) {
                                $('.tag-input').fadeIn("slow");
                            } else {
                                $('.tag-input').fadeOut("slow");
                            }
                        } catch (error) {
                            console.log(error);
                            fatalMessage();


                        }
                    }
                })
        })
        $("#CategoryId").change(function() {
            var categoryId = $(this).val();
            if (categoryId == 4) {
                $('.tag-input').fadeIn("slow");
            } else {
                $('.tag-input').fadeOut("slow");
            }
        })
       
    });
    </script>
</body>


</html>