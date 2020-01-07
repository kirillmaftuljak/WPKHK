<div class="body-decor">
	<img src="<?php echo CBSB_BASE_URL . 'public/images/body-decor.png'; ?>">
</div>

	<!-- main container of all the page elements -->
<div id="wrapper">
	<!-- contain main informative part of the site -->
	<main id="main">
		<div class="head-block">
			<div class="head-block-panel">
				<!-- <form method="POST" id="navigate">
					<input type="hidden" name="page" value="signup" />
					<input type="hidden" name="action" value="cbsb_navigate" />
					<button type="submit" class="button button-light-gray">&larr; <?php //_e( 'Go back', 'calendar-booking' ); ?></button>
				</form> -->
<!-- 				<form method="POST" id="set_plan">
					<input id="plan_selected" type="hidden" name="plan" value="" />
					<input id="term_selected" type="hidden" name="term" value="month" />
					<input id="onboard_id" type="hidden" name="onboard_id" value="<?php //echo get_option( 'cbsb_onboard' ); ?>" />
					<button style="display:none!important;" type="submit" class="button button-continue"><?php //_e( 'Continue to next step', 'calendar-booking' ); ?> &rarr;</button>
				</form> -->
			</div>
			<div class="top-block">
				<!-- page logo -->
				<div class="startbooking-logo">
					<a href="#">
						<img src="<?php echo CBSB_BASE_URL . 'public/images/startbooking-logo.svg'; ?>" alt="Start Booking">
					</a>
				</div>
				<h1><?php _e( 'Select the plan that fits your needs', 'calendar-booking' ); ?></h1>
				<p><?php _e( 'Finally Easy Booking Without the High Costs', 'calendar-booking' ); ?></p>
			</div>
		</div>
		<!-- tabs switcher -->
		<div class="tabset-holder">
			<ul class="tabset">
				<li>
					<a id="month" href="#"><?php _e( 'Monthly', 'calendar-booking' ); ?></a>
				</li>
				<li>
					<a id="annual" href="#"><?php _e( 'Annually', 'calendar-booking' ); ?></a>
				</li>
			</ul>
		</div>
		<!-- tabs content holder -->
		<div class="tab-content">

				<!-- plan list -->
			<div class="plan-list">
				<div class="plan plan-free">
					<div class="frame">
						<div class="head">
							<h2><?php _e( 'Free', 'calendar-booking' ); ?></h2>
							<strong class="sub-head">$<span id="price_free">0</span>	</strong>
						</div>
						<ul>
						</ul>
						<a id="free" href="#" class="button button-light select-plan"><?php _e( 'Select this plan', 'calendar-booking' ); ?></a>
					</div>
				</div>
				<div class="plan plan-individual">
					<div class="frame">
						<div class="head">
							<h2><?php _e( 'Individual', 'calendar-booking' ); ?></h2>
							<strong class="sub-head"><span id="price_individual">15</span><span id="price_term">/<?php _e( 'month', 'calendar-booking' ); ?></span></strong>
						</div>
						<ul>
						</ul>
						<a id="individual" href="#" class="button button-light select-plan"><?php _e( 'Select this plan', 'calendar-booking' ); ?></a>
					</div>
				</div>
				<div class="plan plan-business">
					<div class="frame">
						<div class="head">
							<h2><?php _e( 'Business', 'calendar-booking' ); ?></h2>
							<strong class="sub-head"><span id="price_business">35</span><span id="price_term">/<?php _e( 'month', 'calendar-booking' ); ?></span></strong>
						</div>
						<ul>
						</ul>
						<a id="business" href="#" class="button button-light select-plan"><?php _e( 'Select this plan', 'calendar-booking' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</main>
	<!-- bottom panel -->
	<div class="bottom-panel">
		<div class="frame">
		</div>
		<form method="POST" id="set_plan">
			<input id="plan_selected" type="hidden" name="plan" value="" />
			<input id="term_selected" type="hidden" name="term" value="month" />
			<input id="onboard_id" type="hidden" name="onboard_id" value="<?php echo get_option( 'cbsb_onboard' ); ?>" />
			<button style="display:none!important;" type="submit" class="button button-continue"><?php _e( 'Continue', 'calendar-booking' ); ?> &rarr;</button>
		</form>
		<!-- <a href="<?php //echo admin_url( 'admin.php?page=cbsb-connect' ); ?>" class="button button-gray"><?php// _e( 'Continue', 'calendar-booking' ); ?></a> -->
	</div>
</div>