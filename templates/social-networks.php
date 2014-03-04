<div class="nerdpress-social-networks">
	<ul class="list-inline">
	<?php
	$social_networks = NerdPress::variable( 'social_networks' );
	
	if ( $social_networks ) :
	
		foreach ( $social_networks as $network ) :
	?>
		<li>
			<a href="<?= $network['net_link']; ?>" title="<? bloginfo( 'name' ); ?> on <?= $network['net_name']; ?>" target="_blank">
				<i class="fa <?= $network['net_icon']; ?>"></i>
			</a>
		</li>
<?php
	endforeach;
	
	else :
		if ( current_user_can( 'administrator' ) ) 
				echo '<div class="alert alert-danger"><strong>Problem!</strong> You are using the <code>[nerdpress_social_networks]</code> shortcode but 
				haven\'t entered any social networks. 
				<a href="' . admin_url( 'options-general.php?page=nerdpress-settings' ) . '" class="btn btn-sm btn-default" target="_blank">
					<i class="fa fa-cog text-primary"></i> Fix This</a></div>';	
endif;
?>
	</ul>
</div>