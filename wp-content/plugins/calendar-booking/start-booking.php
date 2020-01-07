<?php
/*
Plugin Name: Start Booking
Plugin URI: https://www.startbooking.com/
Description: Integrate online booking service StartBooking.com. Including an appointment form for customers to book directly on your website and a widget to display open hours.
Version: 2.4.4
Author: Start Booking
Author URI: https://www.startbooking.com
License: GPLv2 or later
Text Domain: calendar-booking
Domain Path: /languages
*/

if ( ! defined( 'WPINC' ) ) { die; }

define( 'CBSB_BASE_DIR', plugin_dir_path( __FILE__ ) );
define( 'CBSB_BASE_URL', plugin_dir_url( __FILE__ ) );
define( 'CBSB_VERSION', '2.4.4' );
if ( ! defined( 'CBSB_APP_URL' ) ) {
	define( 'CBSB_APP_URL', 'https://app.startbooking.com/' );
}
require_once( CBSB_BASE_DIR . 'inc/base.php' );
require_once( CBSB_BASE_DIR . 'inc/copy.php' );
require_once( CBSB_BASE_DIR . 'inc/service-api.php' );
require_once( CBSB_BASE_DIR . 'inc/expedited-booking.php' );
require_once( CBSB_BASE_DIR . 'inc/menu.php' );
require_once( CBSB_BASE_DIR . 'inc/enqueue.php' );
require_once( CBSB_BASE_DIR . 'inc/blocks.php' );
require_once( CBSB_BASE_DIR . 'inc/admin-connect.php' );
require_once( CBSB_BASE_DIR . 'inc/admin-ajax-endpoints.php' );
require_once( CBSB_BASE_DIR . 'inc/response-cleaning.php' );
require_once( CBSB_BASE_DIR . 'inc/shortcodes.php' );
require_once( CBSB_BASE_DIR . 'inc/widgets.php' );
require_once( CBSB_BASE_DIR . 'inc/startbooking-api.php' );
