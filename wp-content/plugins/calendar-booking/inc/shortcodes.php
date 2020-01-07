<?php 

function cbsb_legacy_service_flow() {
	add_action( 'wp_footer', 'cbsb_fe_styles' );
	add_action( 'wp_footer', 'cbsb_fe_script' );
	$markup = '
	<div id="startbooking-flow">
		<div id="startbooking-appointment-flow">
			' . cbsb_get_application_loader() . '
		</div>
	</div>';
	return $markup;
}

function cbsb_legacy_classes_flow() {
	add_action( 'wp_footer', 'cbsb_fe_styles' );
	add_action( 'wp_footer', 'cbsb_fe_script' );
	$markup = '
	<div id="startbooking-flow">
		<div id="startbooking-class-flow">
			' . cbsb_get_application_loader() . '
		</div>
	</div>';
	return $markup;
}

function cbsb_service_block_to_shortcode() {
	cbsb_load_service_block_scripts_styles();
	$markup = '
		<div class="wp-block-calendar-booking-default-booking-flow">
			<div id="startbooking-block-default">' . cbsb_get_application_loader() . '</div>
		</div>
	';
	return $markup;
}

function cbsb_single_service_block_to_shortcode( $atts ) {
	if ( is_array( $atts ) ) {
		$atts = array_map( 'trim', $atts );
	}
	cbsb_load_single_service_block_scripts_styles();
	if ( ! isset( $atts['service'] ) ) {
		return '<div><p style="color: red; font-weight:bold;">Service is a required parameter with single service shortcode.</p></div>';
	}

	if ( ! isset( $atts['details'] ) || ! in_array( $atts['details'], array( 'true', 'false' ) ) ) {
		$atts['details'] = 'false';
	}

	$markup = '<div id="startbooking-block-single-service" class="wp-block-calendar-booking-single-service-flow"
				data-block-service="' . trim( $atts['service'] ) . '"
				data-block-display-service="' . trim( $atts['details'] ) . '"
			></div>';
	return $markup;
}

function cbsb_sc_flow( $atts ) {
	if ( is_array( $atts ) ) {
		$atts = array_map( 'trim', $atts );
	}
	$atts = wp_parse_args( $atts, array( 'flow' => null, 'service' => null ) );
	switch ( $atts['flow'] ) {
		case 'services':
			$markup = cbsb_service_block_to_shortcode();
			break;
		case 'classes':
			$markup = cbsb_legacy_classes_flow();
			break;
		case 'class':
			$markup = cbsb_legacy_classes_flow();
			break;
		case 'single-service':
			$markup = cbsb_single_service_block_to_shortcode( $atts );
			break;
		default:
			$markup = cbsb_legacy_service_flow();
			break;
	}
	return $markup;
}
add_shortcode( 'startbooking', 'cbsb_sc_flow' );

function cbsb_sc_book_items( $atts, $content = 'Book Now' ) {
	if ( is_array( $atts ) ) {
		$atts = array_map( 'trim', $atts );
	}
	$booking_page_id = get_option( 'cbsb_booking_page' );

	if ( false === get_option( 'cbsb_connection' ) ) {
		return '<p>Unable to display quick book link because StartBooking is not connected.';
	}

	if ( isset( $_GET['in_page_book'] ) && $_GET['in_page_book'] ) {
		return cbsb_sc_flow();
	}

	$default_atts = array(
		'services' => array(),
	);
	if ( isset( $atts['services'] ) ) {
		$atts['services'] = explode( ',', $atts['services'] );
	}

	$atts = wp_parse_args( $atts, $default_atts );

	if ( is_numeric( $booking_page_id ) ) {
		$args = array(
			'add_service' => $atts['services']
		);
		$booking_url = get_permalink( $booking_page_id );
		$booking_url = add_query_arg( $args, $booking_url );
		$href = $booking_url . '#appointment-page';
	} else {
		$args = array(
			'in_page_book' => true,
			'add_service' => $atts['services']
		);
		$booking_url = get_permalink( get_the_ID() );
		$booking_url = add_query_arg( $args, $booking_url );
		$href = $booking_url . '#appointment-page';
	}
	return "<a href='" . $href . "'>" . $content . "</a>";
}
add_shortcode( 'startbooking_cta', 'cbsb_sc_book_items' );

function cbsb_sc_class_flow( $atts = array(), $content = null ) {
	add_action( 'wp_footer', 'cbsb_fe_styles' );
	add_action( 'wp_footer', 'cbsb_fe_script' );
	$markup = '
	<div id="startbooking-flow">
		<div id="startbooking-class-flow">
			' . cbsb_get_application_loader() . '
		</div>
	</div>';
	return $markup;
}
add_shortcode( 'startbooking_classes', 'cbsb_sc_class_flow' );