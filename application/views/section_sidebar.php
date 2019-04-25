  <body class="nav-md full-width">
    <div class="container body">
      <div class="main_container">
	  
        <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
			<?php
				$app_name = '';
				$app_icon = '';
				if(!empty($app_data)){
					foreach($app_data as $value) {
						$app_name = $value->NAME;
						$app_icon = $value->ICON;
					}
				}
			?>
              <a href="<?php echo base_url(); ?>" class="site_title">
				<?php if ($app_icon != ''): ?>
					<?php echo '<i class="fa fa-'.$app_icon.'"></i>'; ?>
				<?php else: ?> 
					<i class="fa fa-paw"></i> 
				<?php endif; ?>
				<?php if ($app_name != ''): ?>
					<span><?php echo $app_name; ?></span>  
				<?php else: ?> 
					<span>Utakata 2.0</span>
				<?php endif; ?>			  
			  </a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
				<?php if($this->session->PHOTO != null or $this->session->PHOTO != ''): ?>
					<img src="<?php echo $this->session->PHOTO; ?>" alt="Photo profile" class="img-circle profile_img">
				<?php else: ?>
					<img src="<?php echo base_url(); ?>assets/images/avatar.png" alt="Photo profile" class="img-circle profile_img">
				<?php endif; ?>			  
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
				<?php if(isset($this->session->userdata['is_logged_in'])): ?>
					<h2><?php echo $this->session->USERNAME; ?></h2>
				<?php else: ?>
					<h2><?php echo 'Guest'; ?></h2>
				<?php endif; ?>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3><?php echo $this->session->ROLE_NAME; ?></h3>
                <ul class="nav side-menu">
				<?php foreach($menu as $value): ?>
                  <li> 
				  <?php if($value->PERMALINK == '#' OR $value->PERMALINK == '' OR $value->PERMALINK == NULL): ?>
					<a><i class="fa fa-<?php echo $value->MENU_ICON; ?>"></i> <?php echo $value->MENU_NAME; ?> <span class="fa fa-chevron-down"></span></a>
				  <?php else: ?>
				    <?php echo '<a href="'.base_url().$value->PERMALINK.'">'; ?>
				    <i class="fa fa-<?php echo $value->MENU_ICON; ?>"></i> <?php echo $value->MENU_NAME; ?></a>
				  <?php endif; ?>
                    <ul class="nav child_menu">
					  <?php foreach($sub_menu as $val): ?>
						  <?php if($value->ID == $val->MENU_ID): ?>
						  <li>
							  <?php if($val->PERMALINK == '#' OR $val->PERMALINK == '' OR $val->PERMALINK == NULL): ?>
							  <a>
							  <?php else: ?>
							  <?php echo '<a href="'.base_url().$val->PERMALINK.'">'; ?>
							  <?php endif; ?>
							  <?php echo $val->MENU_NAME; ?>
							  </a>
						  </li>
						  <?php endif; ?>
					  <?php endforeach; ?>
                    </ul>
                  </li>
				  <?php endforeach; ?>
                </ul>
              </div>
			  
              <!-- <div class="menu_section">
                <h3>Live On</h3>
                <ul class="nav side-menu">
                  <li><a><i class="fa fa-bug"></i> Additional Pages <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="e_commerce.html">E-commerce</a></li>
                      <li><a href="projects.html">Projects</a></li>
                      <li><a href="project_detail.html">Project Detail</a></li>
                      <li><a href="contacts.html">Contacts</a></li>
                      <li><a href="profile.html">Profile</a></li>
                    </ul>
                  </li>
                  <li><a><i class="fa fa-windows"></i> Extras <span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="page_403.html">403 Error</a></li>
                      <li><a href="page_404.html">404 Error</a></li>
                      <li><a href="page_500.html">500 Error</a></li>
                      <li><a href="plain_page.html">Plain Page</a></li>
                      <li><a href="login.html">Login Page</a></li>
                      <li><a href="pricing_tables.html">Pricing Tables</a></li>
                    </ul>
                  </li>                  
                  <li><a href="index2.html"><i class="fa fa-laptop"></i> Landing Page <span class="label label-success pull-right">Coming Soon</span></a></li>
                </ul>
              </div> -->

            </div>
            <!-- /sidebar menu -->

            <!-- /menu footer buttons -->
            <div class="sidebar-footer hidden-small">
              <a data-toggle="tooltip" data-placement="top" title="Settings">
                <i class="fa fa-cog"></i>
              </a>
              <a href="#!" onclick="javascript:toggleFullScreen()" data-toggle="tooltip" data-placement="top" title="FullScreen">
                <i class="fa fa-arrows-alt"></i>
              </a>                            
              <a data-toggle="tooltip" data-placement="top" title="Lock">
                <i class="fa fa-lock"></i>
              </a>
			  <?php if(isset($this->session->userdata['is_logged_in'])): ?>
				  <a data-toggle="tooltip" data-placement="top" title="Logout" href="<?php echo base_url().'authentication/logout/'; ?>">
					<i class="fas fa-sign-out-alt"></i>
				  </a>
			  <?php else: ?>
				  <a data-toggle="tooltip" data-placement="top" title="Login" href="<?php echo base_url().'authentication/login/'; ?>">
					<i class="fas fa-sign-in-alt"></i>
				  </a>			  
			  <?php endif; ?>
            </div>
            <!-- /menu footer buttons -->
          </div>
        </div>