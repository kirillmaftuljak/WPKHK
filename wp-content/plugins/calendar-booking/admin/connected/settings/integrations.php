<?php
	$editor = add_query_arg( 'page', 'cbsb-editor', admin_url('admin.php') );
?>
<section id="content">
	<div class="block">

		<h3><?php _e( 'Integrations', 'calendar-booking' ); ?></h3>
		<br />

		<div class="text-sm">
			<img src="<?php echo CBSB_BASE_URL . 'public/images/integrations/stripe/logo.svg'; ?>" width="100" height="41" style="margin-bottom: 10px;" />
			<p><?php _e('To accept payments on your booking flow, connect your Start Booking account with Stripe. The Stripe integration will automatically add the payment form to your booking flow and you can control the display settings and requirements from the details tab of the <a href="' . $editor . '">form editor.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>account/integrations?utm_campaign=calendar_booking_plugin&utm_source=integration_page&utm_medium=stripe">
				<?php _e('Connect on Start Booking &rarr;', 'calendar-booking') ?>	
			</a>
		</div>

		<hr />

		<div class="text-sm">
			<img src="<?php echo CBSB_BASE_URL . 'public/images/integrations/google-calendar/logo.png'; ?>" width="160" style="margin-bottom: 10px;" />
			<p><?php _e('Easily view and manage your appointments and availability straight from your Google Calendar. This integrations comes with <strong>two-way syncing</strong> between Google and the Start Booking platform and even allows you to monitor other calendars when we display your availability on your booking flow.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>account/integrations?utm_campaign=calendar_booking_plugin&utm_source=integration_page&utm_medium=google_calendar">
				<?php _e('Connect on Start Booking &rarr;', 'calendar-booking') ?>	
			</a>
		</div>

		<hr />

		<div class="text-sm">
			<img src="<?php echo CBSB_BASE_URL . 'public/images/integrations/google-analytics/logo.png'; ?>" width="160" style="margin-bottom: 10px;" />
			<p><?php _e('Gain traffic insights and booking performance metrics in your existing Google Analytics account for various booking channels.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>account/integrations?utm_campaign=calendar_booking_plugin&utm_source=integration_page&utm_medium=google_analytics">
				<?php _e('Connect on Start Booking &rarr;', 'calendar-booking') ?>	
			</a>
		</div>

		<hr />

		<div class="text-sm">
			<img src="<?php echo CBSB_BASE_URL . 'public/images/integrations/mailchimp/logo.png'; ?>" width="140" style="margin-bottom: 10px;" />
			<p><?php _e('With Mailchimp, you can promote your business across email, social, landing pages, postcards, and more. Connect your account to push your Start Booking customers into your Mailchimp account.', 'calendar-booking') ?></p>
			<a href="<?php echo CBSB_APP_URL; ?>account/integrations?utm_campaign=calendar_booking_plugin&utm_source=integration_page&utm_medium=mailchimp">
				<?php _e('Connect on Start Booking &rarr;', 'calendar-booking') ?>	
			</a>
		</div>

	</div>
</section>