<?php
$current_settings = cbsb_current_settings();
?>
<section id="content">
				
	<div class="block">
		<h3><?php _e( 'Connection', 'calendar-booking' ); ?></h3>
		
		<form class="booking-form">

			<div class="row">
				<label for="disable"><?php _e( 'Disable Booking', 'calendar-booking' ); ?></label>
				<div class="form-field">
					<div class="checkbox">
						<input id="disable" type="checkbox" name="disable" <?php checked( $current_settings['disable_booking'], 'true', true ); ?>/>
						<label for="disable"><?php _e( 'Yes', 'calendar-booking' ); ?></label>
					</div>
				</div>
			</div>

			<div class="text-sm">
				<p><?php _e( 'Disabling Start Booking will not remove your connection but will stop your customers from being able to book new appointments with you.', 'calendar-booking' ); ?></p>
			</div>

			<hr />

			<h4><?php _e( 'API Settings', 'calendar-booking' ); ?></h4><br />

			<div class="row">
				<label for="api-communication"><?php _e( 'Method', 'calendar-booking' ); ?></label>
				<div class="form-field">

					<div class="fake-select">
						<select name="api-communication" id="api-communication">
								<option <?php selected( $current_settings['api_communication'], null ); ?>><?php _e( 'Default', 'calendar-booking' ); ?></option>
								<option value="proxy" <?php selected( $current_settings['api_communication'], 'proxy' ); ?>><?php _e( 'Proxy', 'calendar-booking' ); ?></option>
								<option value="direct" <?php selected( $current_settings['api_communication'], 'direct' ); ?>><?php _e( 'Direct', 'calendar-booking' ); ?></option>
						</select>
					</div>

				</div>
			</div>

			<div class="text-sm">
				<p><?php _e( 'If you are experiencing problems with your server cache causing duplicate appointments or settings not saving, try updating to the Direct method. Direct allows your plugin to communicate directly with the Start Booking service. Proxy, which is the default setting, runs all the communication with Start Booking through your WordPress site. When communication is handled via the proxy option, those communications can be delayed due to your server configuration.', 'calendar-booking' ); ?></p>
			</div>

			<hr />

			<h4><?php _e( 'Reconnect', 'calendar-booking' ); ?></h4><br />

			<div class="text-sm">
				<p><?php _e( 'If you are having some issues with your connection, reconnect to establish a new connection. This process will not delete any of your other settings.', 'calendar-booking' ); ?></p>
			</div>

			<div class="row" id="reconnect_row">

				<div class="form-field">
					<input 
						class="text-input"
						type="email"
						id="reconnect_email"
						name="reconnect_email"
						placeholder="my.email@gmail.com"
					/>
				</div>
				
				<div class="form-field">
					<input 
						type="password"
						id="reconnect_password"
						name="reconnect_password"
					/>
				</div>

				<button class="button-light-gray" id="reconnect" type="submit">Save</button>

			</div>

		</form>

		<hr />

		<h4><?php _e( 'Disconnect', 'calendar-booking' ); ?></h4><br />

		<div class="text-sm">
			<p><?php _e( 'Disconnecting Start Booking will delete all settings from your WordPress website. This does not cancel your account or delete anything on the Start Booking application.', 'calendar-booking' ); ?></p>
		</div>

		<div class="row">
			<br />
			<?php $disconnect_link = add_query_arg( array( 'page' => 'cbsb-connect', 'cbsb-disconnect' => 'true' ), admin_url( 'admin.php' ) ); ?>
			<a href="<?php echo $disconnect_link; ?>" id="disconnect_check" style="padding:10px;" class="button-light-gray"><?php _e( 'Disconnect', 'calendar-booking' ); ?></a>
		</div><br />

	</div>
</section>