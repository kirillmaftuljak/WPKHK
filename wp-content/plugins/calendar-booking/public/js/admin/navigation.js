jQuery( document ).ready( function( $ ) {

	$('.nav-opener').on('click', function(e) {
		var header = $('#header');
		if (header.hasClass('nav-active')) {
			$('#header').removeClass('nav-active');	
		} else {
			$('#header').addClass('nav-active');	
		}
		
	});
});