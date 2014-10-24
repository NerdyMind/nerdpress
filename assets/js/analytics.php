<?php
// Event tracking gleefully stolen from here:
// http://www.blastam.com/blog/index.php/2013/03/how-to-track-downloads-in-google-analytics-v2/
require_once( '../../../../../wp-load.php' );
header( 'Content-Type:text/javascript' );
$analytics_id = get_option( 'options_analytics_id' );
$analytics_site_url = get_option( 'options_analytics_site_url' );
$analytics_demographics = get_option( 'options_analytics_demographics' );
$analytics_universal = get_option( 'options_enable_universal_google_analytics');

if ( !$analytics_id ) return;

if ( $analytics_universal ) :
?>
 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
 (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
 m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

 ga('create', '<?= $analytics_id; ?>', 'auto');
 <?php if ( $analytics_demographics ) : ?>
 ga('require', 'displayfeatures');
 <?php endif; ?>
 ga('send', 'pageview');
<?php else : ?>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '<?= $analytics_id; ?>']);
<?php
	if ( $analytics_site_url ) {
	?>
_gaq.push(['_setDomainName', '<?= $analytics_site_url; ?>']);
_gaq.push(['_setAllowLinker', true]);
<?php
}
?>
_gaq.push(['_trackPageview']);

(function() {

var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;

<?php if ( $analytics_demographics ) : ?>
ga.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'stats.g.doubleclick.net/dc.js';
<?php else: ?>
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
<?php endif; ?>
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);

})();
<?php endif; ?>

if (typeof jQuery != 'undefined') {
  jQuery(document).ready(function($) {
    var filetypes = /\.(zip|exe|dmg|pdf|doc.*|xls.*|ppt.*|mp3|txt|rar|wma|mov|avi|wmv|flv|wav)$/i;
    var baseHref = '';
    if (jQuery('base').attr('href') != undefined) baseHref = jQuery('base').attr('href');
 
    jQuery('a').on('click', function(event) {
      var el = jQuery(this);
      var track = true;
      var href = (typeof(el.attr('href')) != 'undefined' ) ? el.attr('href') :"";
      var isThisDomain = href.match(document.domain.split('.').reverse()[1] + '.' + document.domain.split('.').reverse()[0]);
      if (!href.match(/^javascript:/i)) {
        var elEv = []; elEv.value=0, elEv.non_i=false;
        if (href.match(/^mailto\:/i)) {
          elEv.category = "email";
          elEv.action = "click";
          elEv.label = href.replace(/^mailto\:/i, '');
          elEv.loc = href;
        }
        else if (href.match(filetypes)) {
          var extension = (/[.]/.exec(href)) ? /[^.]+$/.exec(href) : undefined;
          elEv.category = "download";
          elEv.action = "click-" + extension[0];
          elEv.label = href.replace(/ /g,"-");
          elEv.loc = baseHref + href;
        }
        else if (href.match(/^https?\:/i) && !isThisDomain) {
          elEv.category = "external";
          elEv.action = "click";
          elEv.label = href.replace(/^https?\:\/\//i, '');
          elEv.non_i = true;
          elEv.loc = href;
        }
        else if (href.match(/^tel\:/i)) {
          elEv.category = "telephone";
          elEv.action = "click";
          elEv.label = href.replace(/^tel\:/i, '');
          elEv.loc = href;
        }
        else track = false;
 
        if (track) {
	      <?php if ( $analytics_universal ) : ?>
	      ga('send', 'event', elEv.category.toLowerCase(), elEv.action.toLowerCase(), elEv.label.toLowerCase(), elEv.value);
	      <?php else : ?>
          _gaq.push(['_trackEvent', elEv.category.toLowerCase(), elEv.action.toLowerCase(), elEv.label.toLowerCase(), elEv.value, elEv.non_i]);
          <?php endif; ?>
          if ( el.attr('target') == undefined || el.attr('target').toLowerCase() != '_blank') {
            setTimeout(function() { location.href = elEv.loc; }, 400);
            return false;
      }
    }
      }
    });
  });
}

jQuery(document).ready(function($) {
	$('a[data-toggle="tab"]').click(function() {
		var tabName = $(this).text();
		<?php if ( $analytics_universal ) : ?>
		ga( 'send', 'event', 'click', 'tab', tabName );
		<?php else : ?>
		_gaq.push(['_trackEvent', 'click', 'tab', tabName]);
		<?php endif; ?>
	});

	$('.nerdpress-social-share a').click(function(share) {
		<?php if ( $analytics_universal ) : ?>
		ga( 'send', 'event', 'share', 'social', $(this).attr('data-network') );
		<?php else : ?>
		_gaq.push(['_trackEvent', 'share', 'social', $(this).attr('data-network')]);
		<?php endif; ?>
	});
});