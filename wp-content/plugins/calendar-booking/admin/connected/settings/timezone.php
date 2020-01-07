<?php
	global $cbsb;
	$current_settings = cbsb_current_settings();
?>
<section id="content">
				
	<div class="block">

		<h3><?php _e( 'Timezone & Locale', 'calendar-booking' ); ?></h3>

		<form class="booking-form">

			<div class="text-sm">
				<p><?php _e( 'Define what timezone the available appointments should display to your customers.', 'calendar-booking' ); ?></p>
				<br />
			</div>

			<div class="row">
				<label for="booking-window-start-type"><?php _e( 'Timezone', 'calendar-booking' ); ?></label>
				<div class="form-field">

					<div class="fake-select">
						<select name="appointment-use-visitor-timezone" id="appointment-use-visitor-timezone">
								<option value="true" <?php selected( $current_settings['appointment_use_visitor_timezone'], 'true' ); ?>><?php _e( 'Visitor', 'calendar-booking' ); ?></option>
								<option value="false" <?php selected( $current_settings['appointment_use_visitor_timezone'], 'false' ); ?>><?php _e( 'Account', 'calendar-booking' ); ?></option>
						</select>
					</div>

				</div>
			</div>

			<hr />

			<div class="row">
				<label for="booking-window-start-type"><?php _e( 'Calendar Locale', 'calendar-booking' ); ?></label>
				<div class="form-field">

					<div class="fake-select">

						<select name="calendar-locale" id="calendar-locale">
							<option value="inherit" <?php selected( $current_settings['calendar_locale'], 'true' ); ?>>Inherit (from WordPress)</option>
							<?php
								$locales = cbsb_get_locale_map();
								foreach ( $locales as $locale => $details ) {
									?><option value="<?php _e( $details['code'] ); ?>" <?php selected( $current_settings['calendar_locale'], $details['code'] ); ?>><?php _e( $details['name'] ); ?></option>
									<?php
								}
							?>
						</select>

					</div>

				</div>
			</div>

		</form>

	</div>
</section>