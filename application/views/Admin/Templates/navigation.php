 <nav class="site-navbar navbar navbar-default navbar-fixed-top navbar-mega navbar-inverse"
    role="navigation">

    <div class="navbar-header">
      <button type="button" class="navbar-toggler hamburger hamburger-close navbar-toggler-left hided"
        data-toggle="menubar">
        <span class="sr-only">Toggle navigation</span>
        <span class="hamburger-bar"></span>
      </button>
      <button type="button" class="navbar-toggler collapsed" data-target="#site-navbar-collapse"
        data-toggle="collapse">
        <i class="icon wb-more-horizontal" aria-hidden="true"></i>
      </button>
      <a class="navbar-brand navbar-brand-center" href="index.html">
        <img class="navbar-brand-logo navbar-brand-logo-normal" src="<?php echo asset_url('images/logo.png') ?>"
          title="E-Receipt">
        <img class="navbar-brand-logo navbar-brand-logo-special" src="<?php echo asset_url('images/logo-colored.png') ?>"
          title="E-Receipt">
        <span class="navbar-brand-text hidden-xs-down"> E-Receipt</span>
      </a>
     
    </div>

    <div class="navbar-container container-fluid">
      <!-- Navbar Collapse -->
      <div class="collapse navbar-collapse navbar-collapse-toolbar" id="site-navbar-collapse">
        <!-- Navbar Toolbar -->
        <ul class="nav navbar-toolbar">
          <li class="nav-item hidden-float" id="toggleMenubar">
            <a class="nav-link" data-toggle="menubar" href="#" role="button">
                <i class="icon hamburger hamburger-arrow-left">
                  <span class="sr-only">Toggle menubar</span>
                  <span class="hamburger-bar"></span>
                </i>
              </a>
          </li>
        </ul>
        <!-- End Navbar Toolbar -->

        <!-- Navbar Toolbar Right -->
        <ul class="nav navbar-toolbar navbar-right navbar-toolbar-right">
        
          <li class="nav-item dropdown">
            <a class="nav-link navbar-avatar" data-toggle="dropdown" href="#" aria-expanded="false"
              data-animation="scale-up" role="button">
              <span class="avatar avatar-online">
                <img src="<?php echo asset_url('portraits/5.jpg') ?>" alt="...">
                <i></i>
              </span>
            </a>
            <div class="dropdown-menu" role="menu">
              <a class="dropdown-item" href="<?php echo base_url('account/changemypassword'); ?>" role="menuitem"><i class="icon wb-user" aria-hidden="true"></i> Change Password</a>
              <div class="dropdown-divider" role="presentation"></div>
              <a class="dropdown-item" href="<?php echo base_url('account/logout'); ?>" role="menuitem"><i class="icon wb-power" aria-hidden="true"></i> Logout</a>
            </div>
          </li>
         
        </ul>
        <!-- End Navbar Toolbar Right -->
      </div>
      <!-- End Navbar Collapse -->

      
    </div>
  </nav>
  <div class="site-menubar site-menubar-light">
    <div class="site-menubar-body">
      <div>
        <div>
          <ul class="site-menu" data-plugin="menu">
            <li class="site-menu-category">General</li>
            <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Dashboard</span>
                    </a>
              
            </li>
            <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin/clients'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Clients</span>
                    </a>
              
            </li>
            <?php if ($this->utilities->GetSessionRole() == "Admin") { ?>
               <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin/users'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Users</span>
                    </a>
              
            </li>
             <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin/departments'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Departments</span>
                    </a>
              
            </li>
            
          <?php  } ?>
            
             <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin/receipts'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Receipts</span>
                    </a>
              
            </li>
            <?php if ($this->utilities->GetSessionRole() == "Admin") { ?>
            <li class="site-menu-item ">
              <a  href="<?php echo base_url('Admin/report'); ?>" >
                        <i class="site-menu-icon wb-layout" aria-hidden="true"></i>
                        <span class="site-menu-title">Report</span>
                    </a>
              
            </li>
            <?php  } ?>
          
          </ul>
        </div>
      </div>
    </div>
  </div>
