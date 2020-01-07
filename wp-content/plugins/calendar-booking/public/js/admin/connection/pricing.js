jQuery( document ).ready( function( $ ) {

	function formatMoney(money) {
		return new Intl.NumberFormat(undefined, { style: 'currency', currency: 'usd', minimumFractionDigits: 2 }).format(money)
	}

	var pricing = window.startbooking.pricing.data;

	// This check is to just make sure we didn't fat finger it on accident
	if (pricing.default.term === 'month' || pricing.default.term === 'annual') {
		var default_term = pricing.default.term;
	} else {
		var default_term = 'month';
	}

	if (default_term === 'month') {

		var individual_price = pricing.month.individual.price;
		var business_price = pricing.month.business.price;
		var per_term = 'month';

		var free_features = pricing.month.free.features;
		var individual_features = pricing.month.individual.features;
		var business_features = pricing.month.business.features;

		$('.tabset-holder > ul > li:nth-child(1)').addClass('active');

	} else {

		var individual_price = pricing.annual.individual.price;
		var business_price = pricing.annual.business.price;
		var per_term = 'year';

		var free_features = pricing.annual.free.features;
		var individual_features = pricing.annual.individual.features;
		var business_features = pricing.annual.business.features;

		$('.tabset-holder > ul > li:nth-child(2)').addClass('active');
	}

	// Set the hidden input for the default
	$('input#term_selected').val(per_term);

	// Set Price
	$('.plan-individual span#price_individual').html(formatMoney(individual_price));
	$('.plan-business span#price_business').html(formatMoney(business_price));
	$('.plan span#price_term').html('/' + per_term);

	// Set Features
	$.each(free_features, function (index, value) {
		$( '.plan-free ul' ).append('<li><span>' + value + '</span></li>');
	});

	$.each(individual_features, function (index, value) {
		$( '.plan-individual ul' ).append('<li><span>' + value + '</span></li>');
	});

	$.each(business_features, function (index, value) {
		$( '.plan-business ul' ).append('<li><span>' + value + '</span></li>');
	});

	$('.tabset-holder .tabset li a').on('click', function(e) {
		e.preventDefault();

		var term = $(this).attr('id');
		$('input#term_selected').val(term);

		$(".tabset-holder .tabset li").removeClass("active");
		$(this).closest('li').addClass('active');

		var term = $(this).attr('id');

		if (term === 'month') {

			var individual_price = pricing.month.individual.price;
			var business_price = pricing.month.business.price;
			var per_term = 'month';

			var free_features = pricing.month.free.features;
			var individual_features = pricing.month.individual.features;
			var business_features = pricing.month.business.features;

			$('.tabset-holder > ul > li:nth-child(1)').addClass('active');

		} else {

			var individual_price = pricing.annual.individual.price;
			var business_price = pricing.annual.business.price;
			var per_term = 'year';

			var free_features = pricing.annual.free.features;
			var individual_features = pricing.annual.individual.features;
			var business_features = pricing.annual.business.features;

			$('.tabset-holder > ul > li:nth-child(2)').addClass('active');
		}

		$('.plan-individual span#price_individual').html(formatMoney(individual_price));
		$('.plan-business span#price_business').html(formatMoney(business_price));

		$('.plan span#price_term').html('/' + per_term);
	});

	$('.plan a.select-plan').on('click', function(e) {
		e.preventDefault();
		var plan = $(this).attr('id');

		$('.plan-list .selected a.select-plan').html('Select this plan');
		$('.plan-list .plan').removeClass('selected');
		
		$(this).closest('.plan').addClass('selected');
		$(this).html('<i class="icon-check1"></i>Selected');

		$('input#plan_selected').val(plan);

		$('form#set_plan button').show();
	});

} );