<div class="body-decor">
	<img src="<?php echo CBSB_BASE_URL . 'public/images/body-decor.png'; ?>">
</div>
<!-- main container of all the page elements -->
<div id="wrapper">
	<!-- contain main informative part of the site -->
	<main id="main">
		<div class="head-block">
			<div class="top-block">
				<!-- page logo -->
				<div class="startbooking-logo">
					<a href="#">
						<img src="<?php echo CBSB_BASE_URL . 'public/images/startbooking-logo.svg'; ?>" alt="Start booking">
					</a>
				</div>
				<h1><?php _e( 'Connect your account.', 'calendar-booking' ); ?></h1>
				<p><?php _e( 'Connect your Start Booking account with your WordPress website by entering your Start Booking email and password.', 'calendar-booking' ); ?></p>
			</div>
		</div>
		<!-- container holder -->
		<div class="container-holder">
			<!-- registration form -->
			<div class="form" style="margin: 0 auto;">
				<form method="POST" id="connect">
					<div class="row">
						<div class="col-sm">
							<label for="email"><?php _e( 'Email Address', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="email" type="email" id="email" placeholder="my.email@gmail.com" required />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm">
							<label for="password"><?php _e( 'Password', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="password" type="password" id="password" required />
							</div>
						</div>
					</div>
					<button class="button-override" type="submit"><?php _e( 'Connect with Start Booking', 'calendar-booking' ); ?></button>
				</form>

				<a target="_blank" style="color:#969ea4; margin: 25px auto 0 auto; text-align: center; display: block;" href="https://app.startbooking.com/password/reset"><?php _e( 'Forgot your password?', 'calendar-booking' ); ?></a>
			</div>

		</div>
	</main>
	<!-- bottom panel -->
	<div class="bottom-panel">
		<div class="frame">
			<p>
				<a href="<?php echo admin_url( 'admin.php?page=cbsb-signup' ); ?>" class="button button-light-gray">&larr; <?php _e( 'Back to Signup', 'calendar-booking' ); ?></a>

				<span style="display: inline-block;margin: 7px auto 7px 10px;font-weight: 500;"><?php _e( 'Don\'t have an account?', 'calendar-booking' ); ?></span>
			</p>
		</div>
	</div>
</div>