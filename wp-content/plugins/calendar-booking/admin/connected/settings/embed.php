<section id="content">
				
	<div class="block">

		<h3><?php _e( 'Embed Options', 'calendar-booking' ); ?></h3>
		<div class="text-sm">
			<p><?php _e( 'Embedding the Start Booking form on a page or post is really simple. If you are using the Gutenberg editor (<a target="_blank" href="https://wordpress.org/gutenberg/">Learn More</a>) you have the option to use our block embed which is listed below. If you are not using Gutenberg, we still support implementing shortcodes on any page or post as well.', 'calendar-booking' ); ?></p>
		</div>

		<hr />

		<h3><?php _e( 'Blocks', 'calendar-booking' ); ?></h3>
		<div class="text-sm">
			<p><?php _e( 'To use the Gutenberg blocks, navigate to any page or post that you want to display your booking form. The services block will display a booking form for your services and the classes block will display a booking form to join one of your classes.', 'calendar-booking' ); ?></p>
			<img src="<?php echo CBSB_BASE_URL . 'public/images/start-booking-blocks.png'; ?>" width="605" style="margin:0 auto; display: block;" />
		</div>

		<hr />

		<h3><?php _e( 'Legacy Options - Shortcodes', 'calendar-booking' ); ?></h3>
		<br />

		<h4><?php _e( 'Services', 'calendar-booking' ); ?></h4>
		<div class="badge">[startbooking flow="services"]</div>
		<div class="text-sm">
			<p><?php _e( 'This shortcode is best used on a page that you want to display the full booking experience. This will provide users with a list of your available services for booking.', 'calendar-booking' ); ?></p>
		</div>

		<hr />

		<h4><?php _e( 'Single Service', 'calendar-booking' ); ?></h4>
		<div class="badge">[startbooking flow="single-service" service="4K59oyjEP" details="true"]</div>
		<div class="text-sm">
			<p><?php _e( 'Use this shortcode if you want to predefine a specific service to be booked. Use the details option to either show the service details or skip directly to the calendar.', 'calendar-booking' ); ?></p>
		</div>

		<hr />

		<h4><?php _e( 'Classes', 'calendar-booking' ); ?></h4>
		<div class="badge">[startbooking flow="classes"]</div>
		<div class="text-sm">
			<p><?php _e( 'This shortcode allows your customers to book classes from a calendar or list view with filtering and date range selection.', 'calendar-booking' ); ?></p>
			<p><?php _e( '<strong>Note</strong>: This shortcode requires a paid plan.', 'calendar-booking' ); ?></p>
		</div>

	</div>
</section>