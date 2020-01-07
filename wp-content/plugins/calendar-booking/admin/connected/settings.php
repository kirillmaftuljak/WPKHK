<!-- main container of all the page elements -->
<div id="wrapper settings">

	<!-- header of the page --> 
	<?php include( CBSB_BASE_DIR . 'admin/connected/header.php' ); ?>

	<main id="main">
		<!-- twocolumns -->
		<div class="twocolumns">
			<!-- content -->

			<?php

				$nav = isset( $_GET['nav'] ) ? $_GET['nav'] : 'cbsb-embed';
				switch( $nav ) {
					case 'cbsb-embed' :
						include( CBSB_BASE_DIR . 'admin/connected/settings/embed.php' );
						break;

					case 'cbsb-general' :
						include( CBSB_BASE_DIR . 'admin/connected/settings/general.php' );
						break;

					case 'cbsb-timezone' :
						include( CBSB_BASE_DIR . 'admin/connected/settings/timezone.php' );
						break;

					case 'cbsb-integrations' :
						include( CBSB_BASE_DIR . 'admin/connected/settings/integrations.php' );
						break;

					case 'cbsb-connection' :
						include( CBSB_BASE_DIR . 'admin/connected/settings/connection.php' );
						break;

					default :
						include( CBSB_BASE_DIR . 'admin/connected/settings/embed.php' );
				
				}
			?>
			
			<!-- sidebar -->
			<aside id="sidebar">
				<h2 class="sub-heading"><?php _e( 'NAVIGATION', 'calendar-booking' ); ?></h2>
				<!-- sub navigation -->
				<ul class="sub-nav">
					<!-- To make the li active, class .active should be added to the li element. -->
					<li <?php if ( isset( $_GET['nav'] ) && $_GET['nav'] === 'cbsb-embed' || !isset( $_GET['nav'] ) ) { ?>class="active"<?php } ?>><a href="?page=cbsb-settings&nav=cbsb-embed"><?php _e( 'Embed Options', 'calendar-booking' ); ?></a></li>
					<li <?php if ( isset( $_GET['nav'] ) && $_GET['nav'] === 'cbsb-general' ) { ?>class="active"<?php } ?>><a href="?page=cbsb-settings&nav=cbsb-general"><?php _e( 'General Settings', 'calendar-booking' ); ?></a></li>
					<li <?php if ( isset( $_GET['nav'] ) && $_GET['nav'] === 'cbsb-timezone' ) { ?>class="active"<?php } ?>><a href="?page=cbsb-settings&nav=cbsb-timezone"><?php _e( 'Timezone & Locale', 'calendar-booking' ); ?></a></li>
					<li <?php if ( isset( $_GET['nav'] ) && $_GET['nav'] === 'cbsb-integrations' ) { ?>class="active"<?php } ?>><a href="?page=cbsb-settings&nav=cbsb-integrations"><?php _e( 'Integrations', 'calendar-booking' ); ?></a></li>
					<li <?php if ( isset( $_GET['nav'] ) && $_GET['nav'] === 'cbsb-connection' ) { ?>class="active"<?php } ?>><a href="?page=cbsb-settings&nav=cbsb-connection"><?php _e( 'Connection', 'calendar-booking' ); ?> <div id="connection_indicator" class=""></div></a></li>
				</ul>
			</aside>
		</div>

	</main>

</div>