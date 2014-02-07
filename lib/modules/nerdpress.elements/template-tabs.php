<?php
$nerdpress_widget_id++;

$fields_to_get = array(
	'tabs',
);

foreach ( $fields_to_get as $field ) {
	${ $field } = get_field( $field );
}
?>
<ul class="nav nav-tabs nerdpress-widget" id="nerdpress-widget-<?= $nerdpress_widget_id; ?>">
<?
while ( has_sub_field( 'tabs' ) ) :
	
	$subfields_to_get = array(
		'tab_title',
		'tab_content',
	);
	
	foreach ( $subfields_to_get as $subfield ) {
		${ $subfield } = get_sub_field( $subfield );
	}
?>
	<li<?= ( $t == 0 ? ' class="active"' : '' ); ?>><a href="#<?= str_replace( ' ', '', $tab_title ); ?>" data-toggle="tab"><?= $tab_title; ?></a></li>
<? $t++; endwhile; ?>
</ul>

<div class="tab-content">
	<?
	$t = 0;
	
	while ( has_sub_field( 'tabs' ) ) :
		
		$subfields_to_get = array(
			'tab_title',
			'tab_content',
		);
		
		foreach ( $subfields_to_get as $subfield ) {
			${ $subfield } = get_sub_field( $subfield );
		}
	?>	
	<div class="tab-pane fade in<?= ( $t == 0 ? ' active' : '' ); ?>" id="<?= str_replace( ' ', '', $tab_title ); ?>">
		<?= $tab_content; ?>
	</div>
	<? $t++; endwhile; ?>		
</div>