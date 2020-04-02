<!DOCTYPE html>
<html class="no-js css-menubar" lang="en">


<?php $this->load->view('Templates/header'); ?>

<body class="animsition site-navbar-small page-login layout-full page-dark">
  <!--[if lt IE 8]>
        <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

 
  <!-- Page -->
  <div class="page vertical-align text-center" data-animsition-in="fade-in" data-animsition-out="fade-out">>
    <div class="page-content vertical-align-middle animation-slide-top animation-duration-1">
      <div class="brand">
        <img class="brand-img" src="<?php echo asset_url('images/logo.png') ?>" alt="...">
        <h2 class="brand-text">E-Receipt</h2>
      </div>
      <p>Sign into your pages account</p>
      <form id="LoginForm">
        
        <div class="form-group">
          <label class="sr-only" for="inputEmail">Email</label>
          <input type="email" class="form-control" id="inputEmail" name="EmailAddress" placeholder="Email">
        </div>
        <div class="form-group">
          <label class="sr-only" for="inputPassword">Password</label>
          <input type="password" class="form-control" id="inputPassword" name="Password"
            placeholder="Password">
        </div>
        <!-- <div class="form-group clearfix">
          <div class="checkbox-custom checkbox-inline checkbox-primary float-left">
            <input type="checkbox" id="inputCheckbox" name="remember">
            <label for="inputCheckbox">Remember me</label>
          </div>
          <a class="float-right" href="forgot-password.html">Forgot password?</a>
        </div> -->
        <button type="submit" class="btn btn-primary btn-block">Sign in</button>
      </form>

     
    </div>
  </div>
  <!-- End Page -->


  <!-- Core  -->
  <?php $this->load->view('Templates/footer'); ?>
<script>
  $(document).ready(function () {
    $('#LoginForm').submit(function (e) { 
      e.preventDefault();
      var formData = new FormData($('form#LoginForm')[0]);
      var url = '<?php echo base_url("accountApi/login"); ?>'
      AjaxInit(url, formData, true,false,true);
    });
  });
</script>
 
</body>


</html>