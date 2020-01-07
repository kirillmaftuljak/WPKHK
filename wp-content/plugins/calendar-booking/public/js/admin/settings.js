jQuery( document ).ready( function( $ ) {

	var data = {
		'action' : 'cbsb_check_connection'
	}

	$.post( ajaxurl, data, function( response ) {

		if (response.data.hasOwnProperty("data")) {
			$('#connection_indicator').addClass('led_green');
		} else {
			$('#connection_indicator').addClass('led_red');
		}
	
	});

	function cbsb_toast( message, type = 'info', duration = 4 ) {
		
		$( 'body' ).append(
			'<div class="notification-message">' +
				message +
				'<a href="#" class="close"><i class="icon-close"></i></a>' +
			'</div>'
		);
		
		$( '.notification-message' ).fadeIn();
		
		window.setTimeout( function() {
			$( '.notification-message' ).fadeOut();
			$( '.notification-message' ).remove();
		}, duration * 1000 );
	}

	$('#booking-page-submit').on('click', function (e) {
		e.preventDefault();

		var booking_page_id = $('#booking-page option:selected').val();

		var data = {
			'action'         : 'cbsb_update_booking_page',
			'booking_page'   : booking_page_id
		}

		if ( 'Select a page' != booking_page_id ) {

			$.post( ajaxurl, data, function( response ) {
			
				cbsb_toast( response.message, response.status, 5 );

				if ( response.reload ) {
					window.setTimeout( function() {
						window.location.reload();
					}, 1000 );
			
				}
			
			});
		}
	});

	function cbsb_update_setting(name, value) {
		var data = {
			'action'    : 'cbsb_settings_update',
			'settings'   : [{ 'name' : name, 'value' : value }]
		}
		cbsb_send_settings( data );
	}

	function cbsb_update_settings( settings ) {
		var data = {
			'action'    : 'cbsb_settings_update',
			'settings'   : settings
		}
		cbsb_send_settings( data );
	}

	function cbsb_send_settings( data ) {
		
		$.post( ajaxurl, data, function( response ) {
			
			cbsb_toast( response.message, response.status, 5 );
			
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}
		
		});
	}

	if ( $('#booking-window-start-type').val() == 'none' ) {
		$('#booking-window-start-qty').hide();
	}

	if ( $('#booking-window-end-type').val() == 'none' ) {
		$('#booking-window-end-qty').hide();
	}

	$('#booking-window-start-type').change(function() {
		if ( $('#booking-window-start-type').val() !== 'none' ) {
			$('#booking-window-start-qty').show();
		} else {
			$('#booking-window-start-qty').hide();
		}
		cbsb_update_setting( 'booking_window_start_type', $( '#booking-window-start-type' ).val() );
	} );

	$('#booking-window-end-type').change(function() {
		if ( $('#booking-window-end-type').val() !== 'none' ) {
			$('#booking-window-end-qty').show();
		} else {
			$('#booking-window-end-qty').hide();
		}
		cbsb_update_setting( 'booking_window_end_type', $( '#booking-window-end-type' ).val() );
	} );

	$( '#booking-window-start-qty' ).focus(function () {
		window.cbsb_booking_window_start_type = $( '#booking-window-start-qty' ).val();
	});

	$( '#booking-window-end-qty' ).focus(function () {
		window.cbsb_booking_window_end_type = $( '#booking-window-end-qty' ).val();
	});

	$( '#booking-window-start-qty' ).blur(function () {
		if ( $( '#booking-window-start-qty' ).val() != window.cbsb_booking_window_start_type ) {
			cbsb_update_setting( 'booking_window_start_qty', $( '#booking-window-start-qty' ).val() );
		}
	});

	$( '#booking-window-end-qty' ).blur(function () {
		if ( $( '#booking-window-end-qty' ).val() != window.cbsb_booking_window_end_type ) {
			cbsb_update_setting( 'booking_window_end_qty', $( '#booking-window-end-qty' ).val() );
		}
	});

	$('#api-communication').change(function() {
		cbsb_update_setting( 'api_communication', $( '#api-communication' ).val() );
	} );

	$('#endorse').click(function() {
		cbsb_update_setting( 'endorse_us', $(this).is(':checked') );
	});

	$('#additional_data').click(function() {
		cbsb_update_setting( 'allow_data_collection', $(this).is(':checked') );
	});

	// Timezone & Locale

	$('#calendar-locale').change(function () {
		cbsb_update_setting( 'calendar_locale', $( '#calendar-locale' ).val() );
	});

	$('#appointment-use-visitor-timezone').change(function () {
		cbsb_update_setting( 'appointment_use_visitor_timezone', $( '#appointment-use-visitor-timezone' ).val() );
	});

	// Connection

	$('#disable').click(function() {
		cbsb_update_setting( 'disable_booking', $(this).is(':checked') );
	});

	$('button#reconnect').click( function( e ) {
		e.preventDefault();

		$(this).addClass('loading');
		$(this).html('...');
		$(this).prop('disabled', true);

		var email = $('#reconnect_row input[name=reconnect_email]').val();
		var password = $('#reconnect_row input[name=reconnect_password]').val();

		var data = {
			'action':'cbsb_app_reconnect_account',
			'email':email,
			'password':password
		};

		$.post( ajaxurl, data, function( response ) {

			cbsb_toast( response.message, response.status, 5 );

			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.reload();
				}, 1000 );
			}

		});
	});

	// $('#disconnect_check').click( function() {
	// 	if ( confirm( 'If you disconnect, all booking functionality will stop working until you reconnect with Start Booking.' ) == true ) {

	// 		var data = {
	// 			'action'               : 'cbsb_disconnect',
	// 			'confirm_disconnect'   : 'true'
	// 		}

	// 		$.post( ajaxurl, data, function( response ) {
				
	// 			// cbsb_toast( response.message, response.status, 5 );
				
	// 			// if ( response.reload ) {
	// 			// 	window.setTimeout( function() {
	// 			// 		window.location.reload();
	// 			// 	}, 1000 );
	// 			// }
	// 		} );
	// 	}
	// });

});