<div class="nerdpress-social-networks">
	<ul class="list-inline">
	<?php
	$social_networks = NerdPress::variable( 'social_networks' );
	
	if ( $social_networks ) :
	
		foreach ( $social_networks as $network => $data ) :
	?>
		<li>
			<a href="<?= $data['link']; ?>" title="<? bloginfo( 'name' ); ?> on <?= $network; ?>" target="_blank">
				<i class="fa <?= $data['icon']; ?>"></i>
			</a>
		</li>
<?php
	endforeach;
	
endif;
?>
	</ul>
</div>