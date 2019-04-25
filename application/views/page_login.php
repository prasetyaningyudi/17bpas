  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>
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
      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="" class="login-form" method="post">
              <h1>Login Form</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" name="username" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" name="password" required="" />
              </div>
              <div>
                <input class="btn btn-default submit" type="submit" id="login-submit" name="login-submit" value="Log in"/>
                <a class="reset_pass" href="#">Lost your password?</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="#signup" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
				
				<div class="error_info">
				<?php 
				if($error != null){
					echo '<div class="alert alert-success" role="alert">';
					foreach($error->info as $value){
						echo ''.$value.'<br>';
					}
					echo '</div>';
				}
				?>
				</div>
				
                <br />
                <div>
                  <h1>
					<?php if ($app_icon != ''): ?>
						<?php echo '<i class="fa fa-'.$app_icon.'"></i>'; ?>
					<?php else: ?> 
						<i class="fa fa-paw"></i> 
					<?php endif; ?>
					<?php if ($app_name != ''): ?>
						<?php echo $app_name; ?>  
					<?php else: ?> 
						Utakata 2.0
					<?php endif; ?>				  
				  </h1>
                  <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> All Rights Reserved.
				  <br>Utakata 2.0 Created with <i class="fa fa-heart" aria-hidden="true" style="color:red"></i> by <a href="https://yudiprasetya.com/" title="yudiprasetya.com" target="_blank">Yudi Prasetya</a>.
				  <br>Template based on Gentelella by <a target="_blank" href="https://colorlib.com">Colorlib</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>

        <div id="register" class="animate form registration_form">
          <section class="login_content">
            <form>
              <h1>Create Account</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" required="" />
              </div>
              <div>
                <input type="email" class="form-control" placeholder="Email" required="" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" />
              </div>
              <div>
                <a class="btn btn-default submit" href="#">Submit</a>
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">Already a member ?
                  <a href="#signin" class="to_register"> Log in </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>			
                  <h1>
					<?php if ($app_icon != ''): ?>
						<?php echo '<i class="fa fa-'.$app_icon.'"></i>'; ?>
					<?php else: ?> 
						<i class="fa fa-paw"></i> 
					<?php endif; ?>
					<?php if ($app_name != ''): ?>
						<?php echo $app_name; ?>  
					<?php else: ?> 
						Utakata 2.0
					<?php endif; ?>				  
				  </h1>
                  <p>Copyright &copy; <script>document.write(new Date().getFullYear());</script> All Rights Reserved.
				  <br>Utakata 2.0 Created with <i class="fa fa-heart" aria-hidden="true" style="color:red"></i> by <a href="https://yudiprasetya.com/" title="yudiprasetya.com" target="_blank">Yudi Prasetya</a>.
				  <br>Template based on Gentelella by <a target="_blank" href="https://colorlib.com">Colorlib</a></p>
                </div>
              </div>
            </form>
          </section>
        </div>
      </div>
    </div>
  </body>
</html>