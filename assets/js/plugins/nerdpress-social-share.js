jQuery(document).ready(function($) {
	$('.nerdpress-social-share a').click(function(share) {
		window.open( $(this).attr('data-share'), 'sharer', 'toolbar=0,status=0,width=550,height=450');
		share.preventDefault();
	});
});