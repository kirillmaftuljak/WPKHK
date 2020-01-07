<?php

class StartBookingComAPI {

	function __construct() {
		global $wp_version;
		$connection = get_option( 'cbsb_connection' );
		$token = isset( $connection['token'] ) ? $connection['token'] : null;
		$account = isset( $connection['account'] ) ? $connection['account'] : null;
		$this->args = array(
			'user-agent'  => 'WP:BK:API/' . $wp_version . ':' . CBSB_VERSION . ':StartBookingComAPI; ' . home_url(),
			'blocking'    => true,
			'headers'     => array(
				'Accept'              => 'application/json',
				'Authorization'       => 'Bearer ' . $token,
				'Account'             => $account,
				'Content-Type'        => 'application/json',
				'X-Requested-With'    => 'XMLHttpRequest',
				'X-WP-Version'        => $wp_version,
				'X-WP-Plugin-Version' => CBSB_VERSION,
			),
			'timeout' => 30,
		);
	}

	public function proxy( $request ) {
		$offered_token = $request->get_header( 'x-startbooking-token' ) || null;
		$token_status = cbsb_check_token( $offered_token );
		if ( 'invalid' !== $token_status ) {
			$method = $request->get_method();
			$route = str_replace( '/startbooking/v1/', '', $request->get_route() );
			$route = CBSB_APP_URL . 'api/v1/' . $route;

			if ( 'GET' == $method ) {
				$route = add_query_arg( $request->get_params(), $route );
			} else {
				$this->args['body'] = json_encode( $request->get_params() );
			}

			$this->args['method']  = $method;
			$this->args['headers']['X-WP-Token-Status'] = $token_status;
			if ( $request->get_header( 'x-startbooking-timezone' ) ) {
				$this->args['headers']['X-Visitor-Timezone'] = $request->get_header( 'x-startbooking-timezone' );
			}
			do_action( 'cbsb_request', $request->get_route(), $request->get_params(), $this->args );
			do_action( 'cbsb_request_' . $request->get_route(), $request->get_params(), $this->args );
			$response = wp_remote_request( $route, $this->args );
			do_action( 'cbsb_response', $request->get_route(), $request->get_params(), $this->args, $response );
			do_action( 'cbsb_response_' . $request->get_route(), $request->get_params(), $this->args, $response );
			$status_code = wp_remote_retrieve_response_code( $response );
			wp_send_json( json_decode( wp_remote_retrieve_body( $response ) ), $status_code );
		} else {
			wp_send_json( array(), 403 );
		}
	}

	public function init() {
		$uri = explode( 'startbooking', $_SERVER['REQUEST_URI'] );
		if ( count( $uri ) > 1 ) {
			$route = $uri[1];

			if ( strpos( $_SERVER['REQUEST_URI'], 'index.php?rest_route' ) ) {
				$match = explode( '&', $route );
			} else {
				$match = explode( '?', $route );
			}

			register_rest_route(
				'startbooking', 
				$match[0],
				array(
					'methods'  => WP_REST_Server::ALLMETHODS,
					'callback' => array( $this, 'proxy' ),
				) 
			);
		}
	}

	public function internal_proxy( $route, $params, $method ) {
		$route = CBSB_APP_URL . 'api/v1/' . $route;
		if ( 'GET' == $method ) {
			$route = add_query_arg( $params, $route );
		} else {
			$this->args['body'] = json_encode( $params );
		}
		global $wp_version;
		$this->args['user-agent'] = 'WP:BK:API/' . $wp_version . ':' . CBSB_VERSION . ':StartBookingComAPI; ' . home_url();

		$response = wp_remote_request( $route, $this->args );
		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}
add_action( 'rest_api_init', array( new StartBookingComAPI, 'init' ) );

function cbsb_api_request( $route, $params = array(), $method = 'GET' ) {
	$sbc = new StartBookingComAPI;
	return $sbc->internal_proxy( $route, $params, $method );
}
