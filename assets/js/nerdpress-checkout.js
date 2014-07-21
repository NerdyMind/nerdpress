jQuery(document).ready(function($) {
	$('ul.setup-panel li').addClass('disabled');
	$('ul.setup-panel li:first-child').removeClass('disabled');
	
	$('.checkout-wizard').click(function() {
		$('ul.setup-panel li').removeClass('active');
		$('ul.setup-panel li:nth-child(' + $(this).attr('data-tab') + ')').addClass('active').removeClass('disabled');
	});
	
	$('#order_review').on( 'click', '.payment_methods input.input-radio', function() {
		$('.payment_methods li').removeClass('text-primary');
		$(this).parent().addClass('text-primary');
	});
});