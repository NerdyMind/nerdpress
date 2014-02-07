jQuery(document).ready(function($) {
	$('.checkout-actions a').click(function() {
		var step = $(this).attr('data-step');
		$('#checkout-nav li').removeClass('active');
		$('#checkout-nav #'+step).addClass('active');
	});
});