<!-- main container of all the page elements -->
<div id="wrapper dashboard">

	<!-- header of the page --> 
	<?php include( CBSB_BASE_DIR . 'admin/connected/header.php' ); ?>
	<!-- contain main informative part of the site -->
	<main id="main">
		<div class="holder">

			<h2><?php _e( 'Bookings', 'calendar-booking' ); ?></h2>
			
			<div id="loading-bookings">
                <div class="block-empty">
                    <div class="sb-loading"></div>
                </div>
			</div>

			<!-- booking section -->
			<div style="display:none;" id="has-bookings" class="booking-section">
				<div class="dates">
					<div class="today"></div>
					<div class="yesterday"></div>
				</div>
				<div class="graph" id="chartContainer" style="width:100%; height:150px;"></div>
			</div>

			<img id="no-bookings" style="display:none;" src="<?php echo CBSB_BASE_URL . 'public/images/graph-empty.svg'; ?>" />

			<hr />

			<!-- columns section -->
			<div id="react-dashboard-container"></div>
		</div>
	</main>
</div>