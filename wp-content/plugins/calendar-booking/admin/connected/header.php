<?php
	$dashboard = add_query_arg( 'page', 'cbsb-dashboard', admin_url( 'admin.php' ) );
	$services = add_query_arg( 'page', 'cbsb-services', admin_url( 'admin.php' ) );
	$editor = add_query_arg( 'page', 'cbsb-editor', admin_url( 'admin.php' ) );
	$settings = add_query_arg( array( 'page' => 'cbsb-settings', 'nav' => 'cbsb-embed' ), admin_url( 'admin.php' ) );
?>

<header id="header">
	<!-- page logo -->
	<div class="logo">
		<a href="<?php echo $dashboard; ?>">
			<img src="<?php echo CBSB_BASE_URL . 'public/images/startbooking-logo.svg'; ?>" width="139" height="22" alt="Start Booking">
		</a>
	</div>
	<!-- To make the navigation active, class .nav-active should be added to the #header element. -->
	<a href="#" class="nav-opener"><span></span></a>
	<div class="nav-right">
		<!-- main navigation of the page -->
		<nav id="nav">
			<ul>
				<!-- To make the li active, class .active should be added to the li element. -->
				<li class="<?php if ( $_GET['page'] === 'cbsb-dashboard') { echo 'active'; } ?>">
					<a href="<?php echo $dashboard; ?>"><?php _e( 'Dashboard', 'calendar-booking' ); ?></a>
				</li>
				<li class="<?php if ( $_GET['page'] === 'cbsb-services' ) { echo 'active'; } ?>">
					<a href="<?php echo $services; ?>"><?php _e( 'Services', 'calendar-booking' ); ?></a>
				</li>
				<li class="<?php if ( $_GET['page'] === 'cbsb-editor' ) { echo 'active'; } ?>">
					<a href="<?php echo $editor; ?>"><?php _e( 'Form Editor', 'calendar-booking' ); ?></a>
				</li>
				<li class="<?php if ( $_GET['page'] === 'cbsb-settings' ) { echo 'active'; } ?>">
					<a href="<?php echo $settings; ?>"><?php _e( 'Settings', 'calendar-booking' ); ?></a>
				</li>
			</ul>
		</nav>
		<a href="<?php echo CBSB_APP_URL; ?>" class="link"><?php _e( 'Go to Start Booking &rarr;', 'calendar-booking' ); ?></a>
	</div>
</header>