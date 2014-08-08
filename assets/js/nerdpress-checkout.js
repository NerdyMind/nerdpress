jQuery(document).ready(function($) {
	$('.crumb-billing').click(function() {
		$('ul.nav-crumbs > li').removeClass('current');
		$('ul.nav-crumbs > li.crumb-billing').addClass('current');
	});
	
	$('.crumb-shipping').click(function() {
		$('ul.nav-crumbs > li').removeClass('current');
		$('ul.nav-crumbs > li.crumb-billing').addClass('current');
		$('ul.nav-crumbs > li.crumb-shipping').addClass('current');
	});
	
	$('.crumb-review').click(function() {
		$('ul.nav-crumbs > li').removeClass('current');
		$('ul.nav-crumbs > li.crumb-billing').addClass('current');
		$('ul.nav-crumbs > li.crumb-shipping').addClass('current');
		$('ul.nav-crumbs > li.crumb-review').addClass('current');
	});
	
	$('#order_review').on( 'click', '.payment_methods input.input-radio', function() {
		$('.payment_methods li').removeClass('text-primary');
		$(this).parent().addClass('text-primary');
	});
});