<?php
$enable_statcounter = NerdPress::variable( 'enable_statcounter' );
$statcounter_id = NerdPress::variable( 'statcounter_id' );
$statcounter_security_code = NerdPress::variable( 'statcounter_security_code' );

if ( $enable_statcounter && $statcounter_id && $statcounter_security_code ) :

	if ( !empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== 'off'
	    || $_SERVER['SERVER_PORT'] == 443 ) {
	    $secure = true;
	}
?>
<!-- Start of StatCounter Code -->
<script type="text/javascript">
var sc_project=<?= $statcounter_id; ?>;
var sc_invisible=1;
var sc_security="<?= $statcounter_security_code; ?>";
var sc_https=1;
var scJsHost = (("https:" == document.location.protocol) ? "https://secure." : "http://www.");
document.write("<sc"+"ript type='text/javascript' src='" + scJsHost + "statcounter.com/counter/counter.js'></"+"script>");
</script>
<noscript><div class="statcounter">
<img class="statcounter" src="http<?= ( $secure ) ? 's' : ''; ?>://c.statcounter.com/<?= $statcounter_id; ?>/0/<?= $statcounter_security_code; ?>/1/" alt="">
</div></noscript>
<!-- End of StatCounter Code -->
<?php endif; ?>