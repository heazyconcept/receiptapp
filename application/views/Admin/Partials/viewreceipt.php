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
                <div class="col-md-6">
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">Receipt #<?php echo $receiptData->ReceiptId; ?></h3>
                        </header>
                        <div class="panel-body">
                            <form class="form-horizontal" id="ReceiptForm">
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Client Name: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="FullName" placeholder="Full Name"
                                            autocomplete="off" disabled value="<?php echo $clientData->FullName; ?>" />
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Email Address: </label>
                                    <div class="col-md-8">
                                        <input type="email" class="form-control" name="EmailAddress"
                                            placeholder="@email.com" autocomplete="off" disabled
                                            value="<?php echo $clientData->EmailAddress; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Phone Number: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="PhoneNumber"
                                            placeholder="Phone Number" autocomplete="off" disabled
                                            value="<?php echo $clientData->PhoneNumber; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Company Name: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="Company"
                                            placeholder="Company Name" autocomplete="off" disabled
                                            value="<?php echo $clientData->Company; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Contact Address: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="ResidentialAddress"
                                            placeholder="Contact Address" autocomplete="off" disabled
                                            value="<?php echo $clientData->ResidentialAddress; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Amount: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="Amount" placeholder="Amount"
                                            autocomplete="off" disabled value="<?php echo $receiptData->Amount; ?>" />
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Description: </label>
                                    <div class="col-md-8">
                                        <textarea name="Description" id="" autocomplete="off" class="form-control"
                                            placeholder="Description"
                                            disabled><?php echo $receiptData->Description; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-4 form-control-label">Status: </label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" autocomplete="off" disabled
                                            value="<?php echo $receiptData->TransactionState; ?>" />
                                    </div>
                                </div>
                                <?php if ($receiptData->TransactionState == "Pending" && $this->utilities->GetSessionRole() == "Admin") { ?>
                                <div class="form-group row initial-action">
                                    <div class="col-md-8 offset-md-4">
                                        <button type="button" data-id="<?php echo $receiptData->Id; ?>"
                                            class="btn btn-success btn-approve">Approve </button>
                                        <button type="button" data-id="<?php echo $receiptData->Id; ?>"
                                            class="btn btn-danger btn-decline">Decline</button>
                                    </div>

                                </div>
                                <div class="reject-reason" style="display:none;">
                                    <div class="form-group row">
                                        <label class="col-md-4 form-control-label">Reason: </label>
                                        <div class="col-md-8">
                                            <textarea id="RejectReason" autocomplete="off" class="form-control"
                                                placeholder="RejectReason"></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-md-8 offset-md-4">
                                            <button type="button" data-id="<?php echo $receiptData->Id; ?>"
                                                class="btn btn-danger btn-decline-fin">Submit</button>
                                        </div>

                                    </div>
                                </div>
                                <?php } ?>


                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel">
                        <header class="panel-heading">
                            <h3 class="panel-title">Receipt Preview</h3>
                        </header>
                        <div class="panel-body">
                            <iframe id="previewReceipt"></iframe>
                        </div>
                    </div>
                </div>
                <div class="go-back" style="width: 100%;">
                    <a href="<?php echo base_url('admin/receipts') ?>" 
                        class="btn btn-primary float-right">Back to Receipt List </a>
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
        var iframe = document.getElementById('previewReceipt'),
            iframedoc = iframe.contentDocument || iframe.contentWindow.document;

        iframedoc.body.innerHTML =
            `<?php echo $this->emailservices->GetEmailTemplate($receiptData->Id) ?>`;
        $('.btn-approve').click(function(e) {
            e.preventDefault();
            var data = {}
            var receiptId = $(this).data('id');
            url = '<?php echo base_url("adminApi/ApproveReceipt/' + receiptId + '") ?>';
            SimpleAjaxInit(url, data, false, true, true);
        });
        $('.btn-decline').click(function (e) { 
            e.preventDefault();
            $('.initial-action').fadeOut();
            $('.reject-reason').fadeIn();
        });
        $('.btn-decline-fin').click(function (e) {
            e.preventDefault();
            var receiptId = $(this).data('id');
            var data = {RejectReason: $('#RejectReason').val(), ReceiptId: receiptId};
            url = '<?php echo base_url("adminApi/DeclineReceipt/") ?>';
            SimpleAjaxInit(url, data, false, true, true);
        });
        
    });
    </script>
</body>


</html>