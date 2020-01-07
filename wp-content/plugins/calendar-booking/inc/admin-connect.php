<?php

add_action( 'wp_ajax_cbsb_navigate', 'cbsb_navigate' );
add_action( 'wp_ajax_cbsb_app_signup', 'cbsb_app_signup' );
add_action( 'wp_ajax_cbsb_app_connect_account', 'cbsb_app_connect_account' );
add_action( 'wp_ajax_cbsb_app_set_plan_free', 'cbsb_app_set_plan_free' );
add_action( 'wp_ajax_cbsb_get_pricing', 'cbsb_get_pricing' );
add_action( 'wp_ajax_cbsb_app_create_account', 'cbsb_app_create_account' );

function cbsb_navigate() {
	$page = $_REQUEST['page'];
	update_option( 'cbsb_connect_step', $page );

	wp_send_json( array(
		'status'  => 'success',
		'reload' => true,
		'code' => 200
	));
}

function cbsb_app_signup() {

	global $cbsb;
	global $wp_version;

	$onboarding_details = get_transient( 'cbsb_onboard_details', false );

	$create_user = array(
		'first_name' => sanitize_text_field( $_REQUEST['first_name'] ),
		'last_name' => sanitize_text_field( $_REQUEST['last_name'] ),
		'email' => sanitize_email( $_REQUEST['email'] ),
		'password' => sanitize_text_field( $_REQUEST['password'] ),
		'single_use_token' => wp_generate_password(),
		'notification_url' => admin_url( 'admin-ajax.php' )
	);

	if ( isset( $onboarding_details['time'] ) && isset( $onboarding_details['type'] ) ) {
		$create_user['time'] = time() - $onboarding_details['time'];
		$create_user['type'] = $onboarding_details['type'];
	}

	$args = array(
		'user-agent'  => 'WP:BK:API/' . $wp_version . ':' . CBSB_VERSION . ':CBSB_Api; ' . home_url(),
		'blocking'    => true,
		'headers'     => array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'X-Requested-With' => 'XMLHttpRequest'
		),
		'timeout'     => 20,
		'body'        => json_encode( $create_user )
	);

	$http_response = wp_remote_post( CBSB_APP_URL . 'api/v1/onboard/user', $args );
	$http_body = wp_remote_retrieve_body( $http_response );

	$json = json_decode( $http_body );
 
	if ( property_exists( $json, 'errors' ) ) {

		wp_send_json( array(
			'status'  => 'error',
			'reload' => false,
			'code' => 422,
			'errors' => $json->errors
		));

	} else {

		update_option( 'cbsb_connect_step', 'pricing' );
		update_option( 'cbsb_onboard', $json->onboard );

		wp_send_json( array(
			'status'  => 'success',
			'reload'  => 'true',
			'page'    => admin_url( 'admin.php?page=cbsb-pricing' ),
			'code'    => 200
		));
	}
}

function cbsb_app_connect_account() {
	
	global $wp_version;
		
	$email = sanitize_email( $_POST['email'] );
	$password = sanitize_post( $_POST['password'] );
	$account_id = ( isset( $_POST['account_id'] ) ) ? sanitize_text_field( $_POST['account_id'] ) : false;
	
	$body = array_filter( array(
		'email'       => $email,
		'password'    => $password,
		'website'     => get_site_url(),
		'account_id'  => $account_id
	) );

	$args = array(
		'user-agent'  => 'WP:BK:API/' . $wp_version . ':' . CBSB_VERSION . ':CBSB_Api; ' . home_url(),
		'blocking'    => true,
		'headers'     => array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'X-Requested-With' => 'XMLHttpRequest'
		),
		'timeout'     => 20,
		'body' => json_encode( $body )
	);

	$http_response = wp_remote_post( CBSB_APP_URL . 'api/v1/initialize', $args );
	$http_body = wp_remote_retrieve_body( $http_response );
	$json = json_decode( $http_body );

	if ( property_exists( $json, 'error' ) && 'Invalid API Credentials' == $json->error ) {
		$json->errors = array( 'password' => 'Invalid Password' );
	}

	if ( property_exists( $json, 'errors' ) ) {
		
		$response = array( 
			'status' => 'error', 
			'message' => 'Invalid Authentication.', 
			'reload' => false,
			'code' => 422,
			'errors' => $json->errors
		);
	
	} else {

		if ( isset( $json->token ) ) {
			
			update_option( 'cbsb_connection', array( 'token' => $json->token, 'account' => $json->account ) );
			
			// If they don't have a booking page, create one
			if ( false == get_option( 'cbsb_booking_page' ) && !has_book_now_page()) {
				cbsb_create_booking_page( 'Book Now' );
			}
			
			update_option( 'cbsb_connect_step', 'complete' );
			update_option( 'cbsb_overview_step', 'overview' );
			
			$response = array( 'status' => 'success', 'message' => 'Connection Established.', 'reload' => true );
		
		} elseif ( isset( $json->select_account ) ) {
			
			$response = array( 'status' => 'info', 'message' => 'Select an Account.', 'accounts' => $json->select_account, 'reload' => false );

		} else {
			
			$response = array( 'status' => 'error', 'message' => 'Invalid response from StartBooking.com.', 'reload' => false );
		}
	}
	
	wp_send_json( $response );
}

function has_book_now_page() {

     $page = get_page_by_path( 'book-now' , OBJECT );

     if ( isset($page) )
        return true;
     else
        return false;
}

function cbsb_app_set_plan_free() {
	
	global $cbsb;

	$data = array(
		'id' => get_option( 'cbsb_onboard' ),
		'plan' => 'free',
		'term' => null,
	);

	$http_response = wp_remote_post( CBSB_APP_URL . 'api/v1/onboard/plan', array( 'timeout' => 20, 'body' => $data ) );
	$http_body = wp_remote_retrieve_body( $http_response );
	$json = json_decode( $http_body );

	if ( property_exists( $json, 'error' ) ) {

		wp_send_json( array(
			'status'  => 'error',
			'reload' => false,
			'code' => 404
		));

	} else {

		update_option( 'cbsb_plan', 'free' );
		update_option( 'cbsb_connect_step', 'account' );

		wp_send_json( array(
			'status'  => 'success',
			'page' => admin_url( 'admin.php?page=cbsb-account' ),
			'code' => 200
		));
	}
}

function cbsb_app_create_account() {

	global $cbsb;

	$data = array(
		'id' => get_option( 'cbsb_onboard' ),
		'account_name' => sanitize_text_field( $_REQUEST['account_name'] ),
		'street' => sanitize_text_field( $_REQUEST['street'] ),
		'city' => sanitize_text_field( $_REQUEST['city'] ),
		'state' => sanitize_text_field( $_REQUEST['state'] ),
		'zip' => sanitize_text_field( $_REQUEST['zip'] ),
		'country' => sanitize_text_field( $_REQUEST['country'] ),
		'timezone' => sanitize_text_field( $_REQUEST['timezone'] )
	);

	$args = array(
		'user-agent'  => 'WP:BK:API/' . $wp_version . ':' . CBSB_VERSION . ':CBSB_Api; ' . home_url(),
		'blocking'    => true,
		'headers'     => array(
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
			'X-Requested-With' => 'XMLHttpRequest'
		),
		'timeout'     => 20,
		'body' => json_encode($data)
	);

	$http_response = wp_remote_post( CBSB_APP_URL . 'api/v1/onboard/account', $args );
	$http_body = wp_remote_retrieve_body( $http_response );
	$json = json_decode( $http_body );

	if ( property_exists( $json, 'errors' ) ) {

		wp_send_json( array(
			'status'  => 'error',
			'reload' => false,
			'code' => 422,
			'errors' => $json->errors
		));

	} else {

		update_option( 'cbsb_connection', array( 'token' => $json->token, 'account' => $json->account ) );
		update_option( 'cbsb_connect_step', 'complete' );

		// If they don't have a booking page, create one
		if ( false == get_option( 'cbsb_booking_page' ) && !has_book_now_page() ) {
			cbsb_create_booking_page( 'Book Now' );
		}

		wp_send_json( array(
			'status' => 'success',
			'message' => 'Success! Welcome to Start Booking',
			'reload' => true,
			'page' => admin_url( 'admin.php?page=cbsb-dashboard&welcome=true' ),
			'code' => 200
		));
	}
}
function cbsb_disconnect_settings() {
	if ( isset( $_GET['cbsb-disconnect'] ) &&  $_GET['cbsb-disconnect'] ) {
		global $cbsb;
		delete_option( 'cbsb_connection' );
		delete_option( 'cbsb_service_type_map' );
		delete_option( 'widget_startbooking_hours_widget' );
		delete_option( 'widget_startbooking_address_widget' );
		delete_option( 'cbsb_connect_step' );
		delete_option( 'cbsb_onboard' );
		delete_option( 'cbsb_plan' );
		delete_option( 'cbsb_overview_step' );
		delete_option( 'cbsb_service_map' );
		$cbsb->clear_transients();
	}
}
add_action( 'admin_init', 'cbsb_disconnect_settings' );
