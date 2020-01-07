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
				<h1><?php _e( 'Last step, finish creating your account.', 'calendar-booking' ); ?></h1>
				<p><?php _e( 'The last step is to create your account name and location.', 'calendar-booking' ); ?></p>
			</div>
		</div>
		<!-- container holder -->
		<div class="container-holder">
			<!-- registration form -->
			<div class="form" style="margin: 0 auto;">
				<form method="POST" id="account">
					<div class="row">
						<div class="col-sm">
							<label for="account_name"><?php _e( 'Account Name', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="account_name" type="text" id="account_name" placeholder="" />
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm">
							<label for="street"><?php _e( 'Address', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="street" type="text" id="street" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm">
							<label for="city"><?php _e( 'City', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="city" type="text" id="city" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm">
							<label for="state"><?php _e( 'State', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="state" type="text" id="state" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm">
							<label for="zip"><?php _e( 'Zip', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="zip" type="text" id="zip" />
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-sm">
							<label for="country"><?php _e( 'Country', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" name="country" type="text" id="country" />
							</div>
						</div>
					</div>

					<button class="button-override" type="submit"><?php _e( 'Complete and get started', 'calendar-booking' ); ?> &rarr;</button>
				</form>
			</div>

		</div>
	</main>
</div>