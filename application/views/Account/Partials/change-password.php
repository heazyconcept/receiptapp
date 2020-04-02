<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">


<?php $this->load->view('Templates/header'); ?>

<body class="animsition site-navbar-small page-forgot-password layout-full">
  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->


  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">
    <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
      <h2>Change Your Password </h2>
      <p>Input your new password </p>

      <form id="passwordForm">
        <div class="form-group">
          <input type="password" class="form-control" id="OldPassword" name="OldPassword" placeholder="Your old password">
        </div>
         <div class="form-group">
          <input type="password" class="form-control" id="Password" name="Password" placeholder="New password">
        </div>
         <div class="form-group">
          <input type="password" class="form-control" id="PasswordConfirm" name="PasswordConfirm" placeholder="Confirm new password">
        </div>
        <div class="form-group">
          <button type="submit" class="btn btn-primary btn-block">Change Your Password</button>
        </div>
      </form>

     
    </div>
  </div>
  <!-- End Page -->
 <?php $this->load->view('Templates/footer');?>
<script>
$(document).ready(function () {
   $('#passwordForm').submit(function (e) { 
      e.preventDefault();
      var formData = new FormData($('form#passwordForm')[0]);
      var url = '<?php echo base_url("accountApi/PasswordChange"); ?>'
      AjaxInit(url, formData, true,false,true);
    });
});
</script>
  <!-- Core  -->
 
</body>


</html>