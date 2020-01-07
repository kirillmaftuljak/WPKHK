<?php
/*
Manage/Load CSS/JS
*/

function cbsb_admin_main_js() {
	// Global
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) ) {
		// Need to merge this with the other stylesheet but will take time and will follow up
		wp_enqueue_style( 'cbsb-admin-main-css', CBSB_BASE_URL . 'public/css/admin/main.css' );
		wp_enqueue_script( 'cbsb-admin-navigation', CBSB_BASE_URL . 'public/js/admin/navigation.js', 'jquery' );
	}

	$connection_pages = array( 'cbsb-connect', 'cbsb-signup', 'cbsb-pricing', 'cbsb-account' );
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && in_array( $_GET['page'], $connection_pages ) ) {
		wp_enqueue_style( 'cbsb-admin-phone-countries-css', CBSB_BASE_URL . 'public/css/admin/phone-countries.css' );
		wp_enqueue_script( 'cbsb-admin-phone-countries-js', CBSB_BASE_URL . 'public/js/admin/connection/intlTelInput.js', 'jquery' );
		wp_enqueue_script( 'cbsb-admin-validator-js', CBSB_BASE_URL . 'public/js/admin/connection/validator.js', 'jquery' );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'cbsb-admin-connect-js', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		}
		wp_enqueue_script( 'cbsb-admin-connect-js', CBSB_BASE_URL . 'public/js/admin/connection/connect.js', array('jquery', 'cbsb-admin-validator-js', 'cbsb-admin-phone-countries-js') );
	}

	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-pricing' ) {
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'cbsb-admin-pricing-js', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		}
		wp_enqueue_script( 'cbsb-admin-pricing-js', CBSB_BASE_URL . 'public/js/admin/connection/pricing.js', 'jquery' );
	}

	// Dashboard
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-dashboard' ) {
		wp_enqueue_script( 'cbsb-admin-dashboard-js', CBSB_BASE_URL . 'public/js/admin/graph/graph.js', array('wp-i18n', 'jquery', 'cbsb-graph-js') );
		wp_enqueue_script( 'cbsb-graph-js', CBSB_BASE_URL . 'public/js/admin/graph/jquery.canvasjs.min.js', array('jquery'), CBSB_VERSION );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'cbsb-admin-react-dashboard', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		}
		wp_enqueue_script( 'cbsb-admin-react-dashboard', CBSB_BASE_URL . 'public/js/admin/dashboard.js', array('wp-i18n'), CBSB_VERSION, true );
	}

	// Services
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-services' ) {
		wp_enqueue_style( 'cbsb-admin-slide-panel-css', CBSB_BASE_URL . 'public/css/admin/slidePanel.css' );
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'cbsb-admin-react-services', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		}
		wp_enqueue_script( 'cbsb-admin-react-services', CBSB_BASE_URL . 'public/js/admin/services.js', array('wp-i18n'), CBSB_VERSION, true );
	}

	// Booking Form
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-editor' ) {
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations( 'cbsb-admin-react-editor', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		}
		wp_enqueue_script( 'cbsb-admin-react-editor', CBSB_BASE_URL . 'public/js/admin/editor.js', array('wp-i18n'), CBSB_VERSION, true );
		wp_enqueue_style( 'cbsb-admin-custom-fields', CBSB_BASE_URL . 'public/css/admin/custom-fields.css' );
	}

	// Settings
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-settings' ) {
		wp_enqueue_script( 'cbsb-admin-settings-js', CBSB_BASE_URL . 'public/js/admin/settings.js', 'jquery' );
	}
}
add_action( 'admin_init', 'cbsb_admin_main_js' );

function cbsb_admin_data() {
	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) ) {
		$settings = cbsb_current_settings();
		$rest_token = get_transient( 'cbsb_rest_token', false );
		if ( false === $rest_token ) {
			$rest_token = cbsb_generate_rest_token();
		}
	}

	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-pricing' ) {
		global $cbsb;
		$pricing = $cbsb->get( 'onboard/pricing' );
	?>
	<script type="text/javascript">
		var startbooking = {};
		startbooking.app_url = '<?php echo CBSB_APP_URL; ?>';
		startbooking.rest_api = '<?php echo cbsb_get_rest_api(); ?>';
		startbooking.wp_admin_url = '<?php echo admin_url(); ?>';
		startbooking.pricing = <?php echo json_encode( $pricing['data'] ); ?>;
		startbooking.token = '<?php echo $rest_token; ?>';
	</script>
	
	<?php
		} else if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) ) {
	?>
		<script type="text/javascript">
			var startbooking = {};
			startbooking.app_url = '<?php echo CBSB_APP_URL; ?>';
			startbooking.base_url = '<?php echo CBSB_BASE_URL; ?>';
			startbooking.rest_api = '<?php echo cbsb_get_rest_api(); ?>';
			startbooking.wp_admin_url = '<?php echo admin_url(); ?>';
			startbooking.pricing = '';
			startbooking.token = '<?php echo $rest_token; ?>';
			startbooking.settings = <?php echo json_encode( cbsb_current_settings() ); ?>;
			<?php

			global $wp_version;
			$connection = get_option( 'cbsb_connection' );
			$token = isset( $connection['token'] ) ? $connection['token'] : null;
			$account = isset( $connection['account'] ) ? $connection['account'] : null;
			$direct = array(
				'user-agent'  => 'WP:BK:Direct/' . $wp_version . ':' . CBSB_VERSION . ':StartBookingComAPI; ' . home_url(),
				'token'       => $token,
				'account'     => $account
			);

			if ( $settings['api_communication'] == 'direct' ) { ?>
				startbooking.direct = <?php echo json_encode( $direct ); ?>;
			<?php } ?>
		</script>
	<?php }

	if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && $_GET['page'] === 'cbsb-editor' ) {
		global $cbsb;

		$account = $cbsb->get( 'account/details' );
		$general = $cbsb->get( 'editors/general',  array(), false );

		$general_values = array(
			'loading' => false
		);

		if ( isset( $general['data']->general->default_button_text ) ) {
			$general_values['default_button_text'] = $general['data']->general->default_button_text;
		} else { 
			$general_values['default_button_text'] = __( 'Select', 'calendar-booking' );
		}

		if ( isset( $general['data']->general->default_button_text_color ) ) {
			$general_values['default_button_text_color'] = $general['data']->general->default_button_text_color;
		} else { 
			$general_values['default_button_text_color'] = '#ffffff';
		}

		if ( isset( $general['data']->general->default_button_background_color ) ) {
			$general_values['default_button_background_color'] = $general['data']->general->default_button_background_color;
		} else { 
			$general_values['default_button_background_color'] = '#1480e6';
		}

		if ( isset( $general['data']->general->selected_button_text ) ) {
			$general_values['selected_button_text'] = $general['data']->general->selected_button_text;
		} else { 
			$general_values['selected_button_text'] =  __( 'Selected', 'calendar-booking' );
		}

		if ( isset( $general['data']->general->selected_button_text_color ) ) {
			$general_values['selected_button_text_color'] = $general['data']->general->selected_button_text_color;
		} else { 
			$general_values['selected_button_text_color'] =  '#1480e6';
		}

		if ( isset( $general['data']->general->selected_button_background_color ) ) {
			$general_values['selected_button_background_color'] = $general['data']->general->selected_button_background_color;
		} else { 
			$general_values['selected_button_background_color'] =  '#ffffff';
		}	

		if ( isset( $general['data']->general->continue_button_text ) ) {
			$general_values['continue_button_text'] = $general['data']->general->continue_button_text;
		} else { 
			$general_values['continue_button_text'] =  __( 'Continue', 'calendar-booking' );
		}

		if ( isset( $general['data']->general->continue_button_text_color ) ) {
			$general_values['continue_button_text_color'] = $general['data']->general->continue_button_text_color;
		} else { 
			$general_values['continue_button_text_color'] =  '#ffffff';
		}

		if ( isset( $general['data']->general->continue_button_background_color ) ) {
			$general_values['continue_button_background_color'] = $general['data']->general->continue_button_background_color;
		} else { 
			$general_values['continue_button_background_color'] =  '#1480e6';
		}

		if ( isset( $general['data']->general->back_button_text ) ) {
			$general_values['back_button_text'] = $general['data']->general->back_button_text;
		} else { 
			$general_values['back_button_text'] = __( 'Back', 'calendar-booking' );
		}

		if ( isset( $general['data']->general->back_button_text_color ) ) {
			$general_values['back_button_text_color'] = $general['data']->general->back_button_text_color;
		} else { 
			$general_values['back_button_text_color'] = '#5b636a';
		}

		if ( isset( $general['data']->general->back_button_background_color ) ) {
			$general_values['back_button_background_color'] = $general['data']->general->back_button_background_color;
		} else { 
			$general_values['back_button_background_color'] = '#e6ebf0';
		}

		if ( isset( $general['data']->general->navigation ) ) {
			$general_values['navigation'] = $general['data']->general->navigation;
		} else { 
			$general_values['navigation'] =  'contextual';
		}

		if ( isset( $general['data']->general->skip_service_types ) ) {
			$general_values['skip_service_types'] = $general['data']->general->skip_service_types;
		} else { 
			$general_values['skip_service_types'] =  false;
		}

		if ( isset( $general['data']->general->skip_single_service_type ) ) {
			$general_values['skip_single_service_type'] = $general['data']->general->skip_single_service_type;
		} else { 
			$general_values['skip_single_service_type'] =  true;
		}

		if ( isset( $general['data']->general->skip_low_qty ) ) {
			$general_values['skip_low_qty'] = $general['data']->general->skip_low_qty;
		} else { 
			$general_values['skip_low_qty'] =  true;
		}

		if ( isset( $general['data']->general->skip_single_service ) ) {
			$general_values['skip_single_service'] = $general['data']->general->skip_single_service;
		} else { 
			$general_values['skip_single_service'] =  false;
		}

		if ( isset( $general['data']->general->skip_providers ) ) {
			$general_values['skip_providers'] = $general['data']->general->skip_providers;
		} else { 
			$general_values['skip_providers'] =  true;
		}

		$general['data']->general = $general_values;
		$default_store = array( 'editor' => $general['data'], 'account' => $account['data'] );
	?>
		<script type="text/javascript">
			startbooking.default_store = <?php echo json_encode( $default_store ); ?>;
		</script>
	<?php
	}
}
add_action( 'admin_head', 'cbsb_admin_data' );

function cbsb_fe_data() {
	global $wp_version;
	global $cbsb;
	$settings = cbsb_current_settings();
	if ( $settings['booking_window_start'] ) {
		$start_time = time() + $settings['booking_window_start'] + DAY_IN_SECONDS;
	} else {
		$start_time = time();
	}

	$initial_state =  new stdClass();
	$initial_state->step = 'services';
	$initial_state->default_cart = array();
	$initial_state->dateTime = $start_time;

	$initial_state->day = (int) date( 'j', $start_time );
	$initial_state->month = (int) date( 'n', $start_time );

	$initial_classes_state = new stdClass();
	$initial_classes_state->groupFilter = new stdClass();
	$initial_classes_state->cart = new stdClass();
	$initial_classes_state->customers = array();
	$initial_classes_state->step = ( $settings['default_class_view'] == 'list' ) ? 1 : 2;

	if ( is_user_logged_in() && current_user_can( 'administrator' ) ) {
		$isAdmin = 'true';
	} else {
		$isAdmin = null;
	}

	$all_services = $cbsb->get( 'services', null, 60 );

	if ( isset( $all_services['data'] ) && property_exists( $all_services['data'], 'all_services' ) ) {
		$all_services = $all_services['data'];
		$initial_state->all_services = $all_services->all_services;
	}

	$wp_data = array(
		'baseUrl'      => admin_url( 'admin-ajax.php' ),
		'endPoints'    => array(
			'processAppointment'    => 'cbsb_proccess_appointment',
			'getServices'           => 'cbsb_get_services',
			'getServiceDates'       => 'cbsb_get_service_dates',
			'getAvailabilityByDate' => 'cbsb_get_availablility_by_date',
		),
		'initialState'        => $initial_state,
		'initialClassesState' => $initial_classes_state,
		'settings'     => $settings,
		'mixpanelKey'  => 'eb7a78544eed4e6a20e481834df14d18',
		'appointmentStepOrder' => array(
			50  => 'services',
			100 => 'provider',
			150 => 'time',
			200 => 'details',
			250 => 'confirmation'
		),
		'skipSteps'    => array(),
		'isAdmin'      => $isAdmin
	);

	if ( $settings['automatic_provider'] == 'true' ) {
		$wp_data['skipSteps'][] = 'provider';
	}

	$wp_data = apply_filters( 'cbsb_react_fe', $wp_data );

	$wp_data['appointmentStepOrder'] = array_values( $wp_data['appointmentStepOrder'] );
	$wp_data['skipSteps'] = array_unique( $wp_data['skipSteps'] );
	$wp_data['progressSteps'] = array_values( array_diff( $wp_data['appointmentStepOrder'], $wp_data['skipSteps'] ) );
	return $wp_data;
}

	function cbsb_fe_script() {
		global $cbsb;

		wp_register_script( 'cbsb-react-js', CBSB_BASE_URL . 'public/js/legacy.js' );

		$wp_data = cbsb_fe_data();

		wp_localize_script( 'cbsb-react-js', 'cbsbData', $wp_data );

		$startbooking_js_global = cbsb_get_startbooking_global();

		wp_localize_script( 'cbsb-react-js', 'startbooking', $startbooking_js_global );

		$details = $cbsb->get( 'account/details' );

		if ( 'success' == $details['status'] ) {
			$details = $details['data'];
		}

		if ( property_exists( $details, 'payments' ) && property_exists( $details->payments, 'payment_key' ) ) {
			$allow_payment = true;
		} else {
			$allow_payment = false;
		}

		if ( $allow_payment ) {
			wp_enqueue_script( 'cbsb-stripe-v3', 'https://js.stripe.com/v3/' );
		}
		wp_enqueue_script( 'cbsb-react-js' );
	}

function cbsb_load_single_service_block_scripts_styles() {
	wp_register_style(
		'cbsb-single-service-flow-editor-style',
		CBSB_BASE_URL . 'public/css/admin/block-editor.css',
		array(),
		CBSB_VERSION
	);

	wp_register_style(
		'cbsb-single-service-flow-style',
		CBSB_BASE_URL . 'public/css/flows/single-service.css',
		array(),
		CBSB_VERSION
	);

	wp_register_script(
		'cbsb-stripe-v3',
		'https://js.stripe.com/v3/',
		array(),
		CBSB_VERSION
	);

	wp_register_script(
		'cbsb-single-service-flow-editor',
		CBSB_BASE_URL . 'public/js/blocks/single-service.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-components' ),
		CBSB_VERSION
	);

	wp_register_script(
		'cbsb-single-service-flow',
		CBSB_BASE_URL . 'public/js/flows/single-service.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'cbsb-stripe-v3' ),
		CBSB_VERSION,
		true
	);

	wp_enqueue_script( 'cbsb-single-service-flow' );
	wp_enqueue_style( 'cbsb-single-service-flow-style' );
	add_filter( 'print_late_styles', '__return_true', 30 );

	$startbooking_js_global = cbsb_get_startbooking_global();
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'cbsb-single-service-flow-editor', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		wp_set_script_translations( 'cbsb-single-service-flow', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
	}
	wp_localize_script( 'cbsb-single-service-flow', 'startbooking', $startbooking_js_global );
}

function cbsb_load_service_block_scripts_styles() {
	wp_register_style(
		'cbsb-default-booking-flow-style',
		CBSB_BASE_URL . 'public/css/flows/default.css',
		array(),
		CBSB_VERSION
	);

	wp_register_script(
		'cbsb-stripe-v3',
		'https://js.stripe.com/v3/',
		array(),
		CBSB_VERSION
	);

	wp_register_script(
		'cbsb-default-booking-flow',
		CBSB_BASE_URL . 'public/js/flows/default-appointment.js',
		array( 'wp-blocks', 'wp-i18n', 'wp-element', 'cbsb-stripe-v3' ),
		CBSB_VERSION,
		true
	);

	wp_enqueue_script( 'cbsb-default-booking-flow' );
	wp_enqueue_style( 'cbsb-default-booking-flow-style' );
	add_filter( 'print_late_styles', '__return_true', 30 );

	$startbooking_js_global = cbsb_get_startbooking_global();
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'cbsb-default-booking-flow-editor', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
		wp_set_script_translations( 'cbsb-default-booking-flow', 'calendar-booking', CBSB_BASE_DIR . 'languages/json' );
	}
	wp_localize_script( 'cbsb-default-booking-flow', 'startbooking', $startbooking_js_global );
}

function cbsb_fe_styles() {
	wp_enqueue_style( 'startbooking-flow', CBSB_BASE_URL . 'public/css/flows/legacy-booking-flow-layout.css' );
	$settings = cbsb_current_settings();
	$background_brightness = cbsb_get_brightness( $settings['btn_bg_color'] );
	if ( $background_brightness < 130 ) {
		$default_text = '#ffffff';
	} else {
		$default_text = '#5e5e5e';
	}
	echo '<style type="text/css">
		#startbooking-flow .DayPicker-Day--today{
			color: #' . $settings['btn_bg_color'] . ';
			background-color:#fcf8e3;
		}
		#startbooking-flow .DayPicker-Day--selected{
			color: ' . $default_text . ';
		}
		$startbooking-flow .DayPicker-Day--disabled {
			background-color: rgba(128,128,128,0.5);
			opacity: 0.5;
		}
		#startbooking-flow .DayPicker-Day--selected,
		#startbooking-flow .sb-button-wrap .sb-primary-action button,
		#startbooking-flow button.sb-styled-button,
		.sb-primary-action button {
			background-color: #' . $settings['btn_bg_color'] . ';
			color: #' . $settings['btn_txt_color'] . ';
			border: 0px;
		}
		#startbooking-flow .sb-button-wrap .sb-primary-action button:hover,
		#startbooking-flow button.sb-styled-button:hover,
		.sb-primary-action button:hover{
			opacity: .75;
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon {
			border-color: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon>.rc-steps-icon {
			color: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-icon>.rc-steps-icon .rc-steps-icon-dot {
			background: #' . $settings['btn_bg_color'] . ';
		}
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-title:after,
		#startbooking-flow .rc-steps-item-process .rc-steps-item-icon > .rc-steps-icon .rc-steps-icon-dot,
		#startbooking-flow .rc-steps-item-finish .rc-steps-item-tail:after {
			background-color: #' . $settings['btn_bg_color'] . ';
		}
	</style>' ."\r\n";
}
