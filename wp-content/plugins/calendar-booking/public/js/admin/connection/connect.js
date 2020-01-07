jQuery( document ).ready( function( $ ) {

	// Phone validation
    $.formUtils.addValidator({
        name : 'valid_phone',
    
        validatorFunction : function(value, $el, config, language, $form) {
          return $($el).intlTelInput("isValidNumber");
        },
    
        errorMessage : 'Please enter a valid phone number.',
        errorMessageKey: 'badPhone'
    
    });

    $('#phone_selector').intlTelInput({
        nationalMode: true,
    });

    $('#phone_selector').on("keyup change", function() {
        var intlNumber = $('#phone_selector').intlTelInput("getNumber");
        if (intlNumber) {
            $('#phone').val(intlNumber);
        }
    });

    // Email validation
    $('#signup input[name="email"]').bind('blur', function () {
        
        $(this).parent().addClass('validating-server-side');	
        $(this).addClass('validating-server-side');
        
        var serverURL = window.startbooking.app_url + 'api/v1/users/check';
        var input = $(this);
        var val = $(this).val();

        requestServer(serverURL, input, val, function (response) {
            input.removeClass('validating-server-side');
            input.parent().removeClass('validating-server-side');

            if (response.message === 'error') {
                input.parent().removeClass('has-success');
                input.removeClass('valid');
                input.parent().addClass('error');

                displayContainer = $('<span></span>');
                displayContainer
                    .addClass('help-block form-error')
                    .appendTo(input.parent());
                    displayContainer.html(response.data);
            }

            if (response.message === 'success') {
                input.parent().addClass('has-success');
                input.addClass('valid');
                input.parent().removeClass('error');
                $('.help-block').hide();
            }
        });
    });

    var requestServer = function (serverURL, $element, val, callback) {
        var reqParams = $element.valAttr('req-params') || $element.data('validation-req-params') || {},
            inputName = $element.valAttr('param-name') || $element.attr('name'),
            handleResponse = function (response, callback) {
            callback(response);
        };

        if (!inputName) {
            throw new Error('Missing input name used for http requests made by server validator');
        }
        if (!reqParams) {
            reqParams = {};
        }
        if (typeof reqParams === 'string') {
            reqParams = $.parseJSON(reqParams);
        }
        reqParams[inputName] = val;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('#register_user > input[type="hidden"]').val()
            }
        });

        $.ajax({
            url: serverURL,
            type: 'POST',
            cache: false,
            data: reqParams,
            dataType: 'json',
            error: function (error) {
                handleResponse({valid: false, message: 'Connection failed with status: ' + error.statusText}, callback);
                return false;
            },
            success: function (response) {
                handleResponse(response, callback);
            }
        });
    };

	$('#signup').submit( function( e ) {
		e.preventDefault();

		$('#signup button[type="submit"]').addClass('loading');
		$('#signup button[type="submit"]').html('Loading...');
		$('#signup button[type="submit"]').prop('disabled', true);

		var first_name = $('#signup input[name=first_name]').val();
		var last_name = $('#signup input[name=last_name]').val();
		var email = $('#signup input[name=email]').val();
		var phone = $('#signup input[name=phone]').val();
		var password = $('#signup input[name=password]').val();

		var data = {
			'action':'cbsb_app_signup',
			'first_name':first_name,
			'last_name':last_name,
			'email':email,
			'phone':phone,
			'password':password
		};

		$.post( ajaxurl, data, function( response ) {
			if ( response.reload ) {
				window.setTimeout( function() {
					window.location = response.page;
				}, 1000 );
			} else {
				$.each( response.errors, function( error, message ) {
		            var input = $('#signup input[name=' + error + ']');
		            var inputParent = input.parent();

		            inputParent.addClass('error');
		            displayContainer = $('<span></span>');
		            displayContainer
		                .addClass('help-block form-error')
		                .appendTo(inputParent);
		                displayContainer.html(message);
				});
		
				$('#signup button[type="submit"]').removeClass('loading');
				$('#signup button[type="submit"]').html('Continue to next step &rarr;');
				$('#signup button[type="submit"]').prop('disabled', false);
			}
		});
	} );

	$('#connect').submit( function( e ) {
		e.preventDefault();

		$('#connect button[type="submit"]').addClass('loading');
		$('#connect button[type="submit"]').html('Loading...');
		$('#connect button[type="submit"]').prop('disabled', true);

		var email = $('#connect input[name=email]').val();
		var password = $('#connect input[name=password]').val();

		var data = {
			'action':'cbsb_app_connect_account',
			'email':email,
			'password':password
		};

		$.post( ajaxurl, data, function( response ) {

			if ( response.reload ) {
				window.setTimeout( function() {
					window.location.href = window.startbooking.wp_admin_url  + 'admin.php?page=cbsb-dashboard';
				}, 1000 );
			} else {
				$.each( response.errors, function( error, message ) {
		            var input = $('#connect input[name=' + error + ']');
		            var inputParent = input.parent();

		            inputParent.addClass('error');
		            displayContainer = $('<span></span>');
		            displayContainer
		                .addClass('help-block form-error')
		                .appendTo(inputParent);
		                displayContainer.html(message);
				});
				$('#connect button[type="submit"]').removeClass('loading');
				$('#connect button[type="submit"]').html('Connect with Start Booking');
				$('#connect button[type="submit"]').prop('disabled', false);
			}

		});
	});

	$('#set_plan').submit( function( e ) {
		e.preventDefault();

		$('#set_plan button[type="submit"]').addClass('loading');
		$('#set_plan button[type="submit"]').html('Loading...');
		$('#set_plan button[type="submit"]').prop('disabled', true);

		var plan = $('#set_plan input[name=plan]').val();
		var onboard_id = $('#set_plan input[name=onboard_id]').val();

		if (plan == 'free') {

			var data = {
				'action':'cbsb_app_set_plan_free'
			};

			$.post( ajaxurl, data, function( response ) {
				if ( response.page ) {
					window.setTimeout( function() {
						window.location = response.page;
					}, 1000 );
				}
			});

		} else {

			var term_value = $('#set_plan input[name=term]').val()

			// WE do this so we store annual in the app since thats
			// the naming we use
			if (term_value == 'annual') {
				var term = 'annual';
			}

			if (term_value == 'month') {
				var term = 'month';
			}

			var data = {
				'action':'cbsb_app_plan',
				'plan':plan,
				'term':term,
			};

			var url = window.startbooking.app_url + 'payment';

			var form = $('<form action="' + url + '" method="POST">' +
			  '<input type="text" name="plan" value="' + plan + '" />' +
			  '<input type="text" name="term" value="' + term + '" />' +
			  '<input type="text" name="onboard_id" value="' + onboard_id + '" />' +
			  '<input type="text" name="redirect_url" value="' + window.startbooking.wp_admin_url + '" />' +
			  '</form>');
			
			$('body').append(form);
			form.submit();

		}

	});

	$('#account').submit( function( e ) {
		e.preventDefault();

		$('#account button[type="submit"]').addClass('loading');
		$('#account button[type="submit"]').html('Loading...');
		$('#account button[type="submit"]').prop('disabled', true);

		var account_name = $('#account input[name=account_name]').val();
		var street = $('#account input[name=street]').val();
		var city = $('#account input[name=city]').val();
		var state = $('#account input[name=state]').val();
		var zip = $('#account input[name=zip]').val();
		var country = $('#account input[name=country]').val();
		var timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

		var data = {
			'action':'cbsb_app_create_account',
			'account_name':account_name,
			'street':street,
			'city':city,
			'state':state,
			'zip':zip,
			'country':country,
			'timezone':timezone
		};

		$.post( ajaxurl, data, function( response ) {
			
			cbsb_toast( response.message, response.status, 5 );

			if ( response.reload ) {
				window.setTimeout( function() {
					window.location = response.page;
				}, 1000 );
			} else {
				$.each( response.errors, function( error, message ) {
		            var input = $('#account input[name=' + error + ']');
		            var inputParent = input.parent();

		            inputParent.addClass('error');
		            displayContainer = $('<span></span>');
		            displayContainer
		                .addClass('help-block form-error')
		                .appendTo(inputParent);
		                displayContainer.html(message);
				});
				$('#account button[type="submit"]').removeClass('loading');
				$('#account button[type="submit"]').html('Complete and get started &rarr;');
				$('#account button[type="submit"]').prop('disabled', false);
			}

		});
	} );

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

});