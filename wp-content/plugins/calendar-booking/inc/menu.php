<?php
/*
*  Menu
*/

function cbsb_main_menu() {
	$menu_titles = array(
		'booking'       => __( 'Booking', 'calendar-booking' ),
		'dashboard'     => __( 'Dashboard', 'calendar-booking' ),
		'services'      => __( 'Services', 'calendar-booking' ),
		'form_editor'   => __( 'Form Editor', 'calendar-booking' ),
		'settings'      => __( 'Settings', 'calendar-booking' ),
		'connection'    => __( 'Connection', 'calendar-booking' ),
		'signup'        => __( 'Signup', 'calendar-booking' ),
		'pricing'       => __( 'Pricing', 'calendar-booking' ),
		'account_setup' => __( 'Account Setup', 'calendar-booking' ),
	);

	$icon = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIiB2aWV3Qm94PSIwIDAgODkuMSA4OC43MiI+IAogIDxkZWZzPgogICAgPHN0eWxlPgogICAgICBzdmd7CiAgICAgIAlwYWRkaW5nOiAxMCU7CiAgICAgIH0KICAgICAgLmEgewogICAgICAgIGZpbGw6IHVybCgjYSk7CiAgICAgIH0KCiAgICAgIC5iIHsKICAgICAgICBmaWxsOiB1cmwoI2IpOwogICAgICB9CiAgICA8L3N0eWxlPgogICAgPGxpbmVhckdyYWRpZW50IGlkPSJhIiB4MT0iNDAuMiIgeTE9IjQ4LjAyIiB4Mj0iNDAuMiIgeTI9Ijg2LjQ2IiBncmFkaWVudFVuaXRzPSJ1c2VyU3BhY2VPblVzZSI+CiAgICAgIDxzdG9wIG9mZnNldD0iMCIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPgogICAgICA8c3RvcCBvZmZzZXQ9IjEiIHN0b3AtY29sb3I9IiNmZmZmZmYiLz4KICAgIDwvbGluZWFyR3JhZGllbnQ+CiAgICA8bGluZWFyR3JhZGllbnQgaWQ9ImIiIHgxPSI0MC4yIiB5MT0iMzQuMjUiIHgyPSI0MC4yIiB5Mj0iLTEuMDQiIGdyYWRpZW50VW5pdHM9InVzZXJTcGFjZU9uVXNlIj4KICAgICAgPHN0b3Agb2Zmc2V0PSIwIiBzdG9wLWNvbG9yPSIjZmZmZmZmIi8+CiAgICAgIDxzdG9wIG9mZnNldD0iMSIgc3RvcC1jb2xvcj0iI2ZmZmZmZiIvPgogICAgPC9saW5lYXJHcmFkaWVudD4KICA8L2RlZnM+CiAgPHRpdGxlPnN0YXJ0LWJvb2tpbmctaWNvbjwvdGl0bGU+CiAgPGc+CiAgICA8cGF0aCBjbGFzcz0iYSIgZD0iTTU4LjYxLDc5LjkzaDBsLTQyLjg1LS4wOGExNS4xNCwxNS4xNCwwLDAsMSwwLTMwLjI4SDU4LjY3YTkuMSw5LjEsMCwxLDEsMCwxOC4xOWgtNDZWNjEuNzFoNDZhMywzLDAsMCwwLDMtMy4wNywzLDMsMCwwLDAtMy0zSDE1Ljc1YTkuMDksOS4wOSwwLDAsMCwwLDE4LjE4bDQyLjg1LjA4aDBhMTUuMTQsMTUuMTQsMCwwLDAsLjA2LTMwLjI4bC0xOC41NC0uMDcsMC02LjA1LDE4LjU0LjA3QTIxLjI0LDIxLjI0LDAsMCwxLDc5LjgxLDU4Ljc0YTIxLjIsMjEuMiwwLDAsMS0yMS4yLDIxLjE5WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuNiAtMS4wNykiLz4KICAgIDxwYXRoIGNsYXNzPSJiIiBkPSJNNDAuMjYsNDMuNTNsLTE4LjU0LS4wN2EyMS4yLDIxLjIsMCwwLDEsLjA3LTQyLjM5aC4wNWw0Mi44NS4wOWExNS4xNCwxNS4xNCwwLDAsMSwwLDMwLjI4SDIxLjc0YTkuMTMsOS4xMywwLDAsMS05LjEtOSw5LjEsOS4xLDAsMCwxLDkuMS05LjE2aDQ2VjE5LjNoLTQ2YTMsMywwLDAsMC0yLjE2LjksMywzLDAsMCwwLDIuMTYsNS4xOUg2NC42NmE5LjA5LDkuMDksMCwwLDAsMC0xOC4xOEwyMS44Miw3LjEyaDBhMTUuMTUsMTUuMTUsMCwwLDAtLjA1LDMwLjI5bDE4LjU0LjA3WiIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTAuNiAtMS4wNykiLz4KICA8L2c+Cjwvc3ZnPgo=';
	add_menu_page( $menu_titles['booking'], $menu_titles['booking'], 'manage_options', 'cbsb-dashboard', 'cbsb_dashboard_page', $icon, 61 );
	add_submenu_page( 'cbsb-dashboard', $menu_titles['dashboard'], $menu_titles['dashboard'], 'manage_options', 'cbsb-dashboard', 'cbsb_dashboard_page' );
	add_submenu_page( 'cbsb-dashboard', $menu_titles['services'], $menu_titles['services'], 'manage_options', 'cbsb-services', 'cbsb_services_page' );
	add_submenu_page( 'cbsb-dashboard', $menu_titles['form_editor'], $menu_titles['form_editor'], 'manage_options', 'cbsb-editor', 'cbsb_editor_page' );
	add_submenu_page( 'cbsb-dashboard', $menu_titles['settings'], $menu_titles['settings'], 'manage_options', 'cbsb-settings', 'cbsb_settings_page' );

	add_submenu_page( null, $menu_titles['connection'], $menu_titles['connection'], 'manage_options', 'cbsb-connect', 'cbsb_connect_page' );
	add_submenu_page( null, $menu_titles['signup'], $menu_titles['signup'], 'manage_options', 'cbsb-signup', 'cbsb_signup_page' );
	add_submenu_page( null, $menu_titles['pricing'], $menu_titles['pricing'], 'manage_options', 'cbsb-pricing', 'cbsb_pricing_page' );
	add_submenu_page( null, $menu_titles['account_setup'], $menu_titles['account_setup'], 'manage_options', 'cbsb-account', 'cbsb_account_page' );
}
add_action( 'admin_menu', 'cbsb_main_menu' );

function cbsb_check_connection_redirect() {
	$no_connection_pages = array( 'cbsb-connect', 'cbsb-signup', 'cbsb-pricing', 'cbsb-account' );
	if ( cbsb_is_connected() ) {
		if ( isset( $_GET['page'] ) && in_array( $_GET['page'], $no_connection_pages ) ) {
			$dashboard = menu_page_url( 'cbsb-dashboard', false );
			wp_redirect( $dashboard );
		}
	} else {
		if ( isset( $_GET['page'] ) && false !== strpos( $_GET['page'], 'cbsb-' ) && ! in_array( $_GET['page'], $no_connection_pages ) ) {
			$last_step = get_option( 'cbsb_connect_step', 'signup' );
			$signup = menu_page_url( 'cbsb-' . $last_step, false );
			wp_redirect( $signup );
		}
	}
}
add_action( 'admin_init', 'cbsb_check_connection_redirect', 9 );

function cbsb_dashboard_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/connected/dashboard.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_services_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/connected/services.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_editor_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/connected/editor.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_settings_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/connected/settings.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_connect_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/connect.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_signup_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/signup.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_pricing_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/pricing.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}

function cbsb_account_page() {
	echo "<div id='start-booking'>";
	include( CBSB_BASE_DIR .  '/admin/account.php' );
	echo "</div>";
	add_action( 'admin_footer', 'cbsb_suppress_notices' );
}
