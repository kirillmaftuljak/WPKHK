<div class="body-decor">
	<img src="<?php echo CBSB_BASE_URL . 'images/V2/body-decor.png'; ?>">
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
						<img src="<?php echo CBSB_BASE_URL . 'public/images/startbooking-logo.svg'; ?>" alt="Start Booking">
					</a>
				</div>
				<h1><?php _e( 'Signup to Get Started', 'calendar-booking' ); ?></h1>
				<p><?php _e( 'Start Booking powers thousands of service based individuals and businesses. Get started booking today!', 'calendar-booking' ); ?></p>
			</div>
		</div>
		<!-- container holder -->
		<div class="container-holder">
			<!-- registration form -->
			<div class="form">
				<form method="POST" id="signup">
					<div class="row">
						<div class="col-sm">
							<label for="first_name"><?php _e( 'First Name', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							
							<div class="form-field">
								<input class="text-input" tabindex="1" name="first_name" type="text" id="first_name" placeholder="First Name" required data-validation="length" data-validation-length="min1">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm">
							<label for="last_name"><?php _e( 'Last Name', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" tabindex="2" name="last_name" type="text" id="last_name" placeholder="Last Name" required data-validation="length" data-validation-length="min1">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm">
							<label for="email"><?php _e( 'Email Address', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" tabindex="3" name="email" type="email" id="email" placeholder="my.email@gmail.com" required data-validation="email">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm">
							<label for="password"><?php _e( 'Password', 'calendar-booking' ); ?> *</label>
						</div>
						<div class="col-md">
							<div class="form-field">
								<input class="text-input" tabindex="5" name="password" type="password" id="password" required data-validation="strength" data-validation-strength="2">
							</div>
						</div>
					</div>
					<button tabindex="6" class="button-override" type="submit"><?php _e( 'Continue to next step', 'calendar-booking' ); ?> &rarr;</button>
				</form>
			</div>
			<!-- testimonials -->
			<div class="testimonials">
				<h2><?php _e( 'Testimonials', 'calendar-booking' ); ?></h2>
				<blockquote>
					<q>“<?php _e( 'Start Booking was game changing in helping us schedule and manage our customers. Won\'t open another location without it.', 'calendar-booking' ); ?>”</q>
					<ul class="avarars-list">
						<li>
							<picture>
								<source srcset="<?php echo CBSB_BASE_URL; ?>public/images/avatar-1.jpg, <?php echo CBSB_BASE_URL; ?>public/images/avatar-1@2x.jpg 2x">
								<img src="<?php echo CBSB_BASE_URL; ?>public/images/avatar-1.jpg" alt="">
							</picture>
						</li>
						<li>
							<picture>
								<source srcset="<?php echo CBSB_BASE_URL; ?>public/images/avatar-2.jpg, <?php echo CBSB_BASE_URL; ?>public/images/avatar-2@2x.jpg 2x">
								<img src="<?php echo CBSB_BASE_URL; ?>public/images/avatar-2.jpg" alt="">
							</picture>
						</li>
						<li>
							<picture>
								<source srcset="<?php echo CBSB_BASE_URL; ?>public/images/avatar-3.jpg, <?php echo CBSB_BASE_URL; ?>public/images/avatar-3@2x.jpg 2x">
								<img src="<?php echo CBSB_BASE_URL; ?>public/images/avatar-3.jpg" alt="">
							</picture>
						</li>
					</ul>
					<cite>Tyler Devere <span><?php _e( 'Barbershop Owner', 'calendar-booking' ); ?></span></cite>
				</blockquote>
			</div>
		</div>
	</main>
	<!-- bottom panel -->
	<div class="bottom-panel">
		<div class="frame">
			<p>
				<?php _e( 'Already have an account with us?', 'calendar-booking' ); ?> <a href="#"><?php _e( 'Let\'s get your site connected', 'calendar-booking' ); ?></a>
			</p>
		</div>
		<!-- <form method="POST" id="navigate">
			<input type="hidden" name="page" value="connect" />
			<input type="hidden" name="action" value="cbsb_navigate" />
			<button type="submit" class="button button-gray"><?php _e( 'Connect Account', 'calendar-booking' ); ?></button>
		</form> -->
		<a href="<?php echo admin_url( 'admin.php?page=cbsb-connect' ); ?>" class="button button-gray"><?php _e( 'Connect Account', 'calendar-booking' ); ?></a>
	</div>
</div>