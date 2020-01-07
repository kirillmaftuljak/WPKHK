<?php

if ( ! defined( 'WPINC' ) ) { die; }

function cbsb_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug' => 'startbooking',
				'title' => __( 'Start Booking', 'calendar-booking' ),
			),
		)
	);
}
add_filter( 'block_categories', 'cbsb_block_category', 10, 2 );

require_once( CBSB_BASE_DIR . 'blocks/default-appointment.php' );
require_once( CBSB_BASE_DIR . 'blocks/single-service.php' );
require_once( CBSB_BASE_DIR . 'blocks/legacy-class.php' );