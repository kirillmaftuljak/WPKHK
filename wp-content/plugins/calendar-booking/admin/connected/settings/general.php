<?php
global $cbsb;
$current_settings = cbsb_current_settings();

$booking_window_start_qty = $current_settings['booking_window_start_qty'];
$booking_window_end_qty = $current_settings['booking_window_end_qty'];

?>
<section id="content">
				
	<div class="block">

		<h3><?php _e( 'General Settings', 'calendar-booking' ); ?></h3>
		<br />

		<form class="booking-form">
			
			<h4><?php _e( 'Booking Limits', 'calendar-booking' ); ?></h4>

			<div class="text-sm">
				<p><?php _e( 'Define the earliest time from now that a customer can book an appointment. For example, change to 1 day if you don\'t accept same day appointments.', 'calendar-booking' ); ?></p>
			</div>

			<div class="row">
				<label for="booking-window-start-type"><?php _e( 'Earliest time', 'calendar-booking' ); ?></label>
				<div class="form-field">

					<div class="fake-select">
						<select name="booking-window-start-type" id="booking-window-start-type">
								<option value="none" <?php selected( $current_settings['booking_window_start_type'], 'none' ); ?>><?php _e( 'Immediately', 'calendar-booking' ); ?></option>
								<option value="days" <?php selected( $current_settings['booking_window_start_type'], 'days' ); ?>><?php _e( 'Day(s)', 'calendar-booking' ); ?></option>
								<option value="weeks" <?php selected( $current_settings['booking_window_start_type'], 'weeks' ); ?>><?php _e( 'Week(s)', 'calendar-booking' ); ?></option>
								<option value="weeks" <?php selected( $current_settings['booking_window_start_type'], 'months' ); ?>><?php _e( 'Week(s)', 'calendar-booking' ); ?></option>
						</select>
					</div>

					<input 
						type="number"
						id="booking-window-start-qty"
						name="booking-window-start-qty"
						value="<?php echo (int) $booking_window_start_qty; ?>"
					/>

				</div>
			</div>

			<br />
			<div class="text-sm">
				<p><?php _e( 'Define the furthest time from now that a customer can book an appointment. For example, change to 90 days if you want customers to be limited to booking within the next 90 days.', 'calendar-booking' ); ?></p>
			</div>

			<div class="row">
				<label for="booking-window-end-type"><?php _e( 'Furthest time', 'calendar-booking' ); ?></label>
				<div class="form-field">

					<div class="fake-select">
						<select name="booking-window-end-type" id="booking-window-end-type">
							<option value="none" <?php selected( $current_settings['booking_window_end_type'], 'none' ); ?>><?php _e( 'No Limit', 'calendar-booking' ); ?></option>
							<option value="days" <?php selected( $current_settings['booking_window_end_type'], 'days' ); ?>><?php _e( 'Day(s)', 'calendar-booking' ); ?></option>
							<option value="weeks" <?php selected( $current_settings['booking_window_end_type'], 'weeks' ); ?>><?php _e( 'Week(s)', 'calendar-booking' ); ?></option>
							<option value="months" <?php selected( $current_settings['booking_window_end_type'], 'months' ); ?>><?php _e( 'Month(s)', 'calendar-booking' ); ?></option>
						</select>
					</div>

					<input 
						type="number"
						id="booking-window-end-qty"
						name="booking-window-end-qty"
						value="<?php echo (int) $booking_window_end_qty; ?>"
					/>

				</div>
			</div>
		</form>

		<hr />

		<h4><?php _e( 'Openings Interval', 'calendar-booking' ); ?></h4>
		<div class="text-sm">
			<p><?php _e('Set the interval of time for available openings. Default is every 30 minutes.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>/account/settings"><?php _e('Adjust on Start Booking &rarr;', 'calendar-booking') ?></a>
		</div>

		<hr />

		<h4><?php _e( 'Type of Location', 'calendar-booking' ); ?></h4>
		<div class="text-sm">
			<p><?php _e('This setting will change whether we display your address to customers. i.e. physical or digital.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>/account/settings"><?php _e('Adjust on Start Booking &rarr;', 'calendar-booking') ?></a>
		</div>

		<hr />

		<h4><?php _e( 'Currency', 'calendar-booking' ); ?></h4>
		<div class="text-sm">
			<p><?php _e('Change the currency that is displayed.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>/account/settings"><?php _e('Adjust on Start Booking &rarr;', 'calendar-booking') ?></a>
		</div>

		<hr />

		<h4><?php _e( 'Cancellations', 'calendar-booking' ); ?></h4>
		<div class="text-sm">
			<p><?php _e('This setting will add a link to the appointment confirmation email allowing the customer to cancel their own appointments.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>/account/settings"><?php _e('Adjust on Start Booking &rarr;', 'calendar-booking') ?></a>
		</div>

		<hr />

		<form class="booking-form">
			<div class="row">
				<label for="endorse"><?php _e( 'Endorse Us', 'calendar-booking' ); ?></label>
				<div class="form-field">
					<div class="checkbox">
						<input id="endorse" type="checkbox" name="endorse-us" <?php checked( $current_settings['endorse_us'], 'true', true ); ?>/>
						<label for="endorse"><?php _e( 'Yes', 'calendar-booking' ); ?></label>
					</div>
				</div>
			</div>
			<div class="text-sm">
				<p><?php _e( 'Display a small powered by Start Booking at the end of checkout to help support Start Booking.', 'calendar-booking' ); ?></p>
			</div>

			<hr />

			<div class="row">
				<label for="additional_data"><?php _e( 'Additional Data', 'calendar-booking' ); ?></label>
				<div class="form-field">
					<div class="checkbox">
						<input id="additional_data" type="checkbox" name="additional-data" <?php checked( $current_settings['allow_data_collection'], 'true', true ); ?>/>
						<label for="additional_data"><?php _e( 'Yes', 'calendar-booking' ); ?></label>
					</div>
				</div>
			</div>
			<div class="text-sm">
				<p><?php _e( 'Empower the Start Booking team to make data driven decisions by sharing basic information with us. This does not contain any confidential or sensitive information.', 'calendar-booking' ); ?></p>
			</div>

		</form>

	</div>
</section>