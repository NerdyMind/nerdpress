<?php
$nerdpress_widget_id++;

$fields_to_get = array(
	'accordion',
);

foreach ( $fields_to_get as $field ) {
	${ $field } = get_field( $field );
}

$a = 0;
?>
<div class="panel-group" id="nerdpress-widget-<?= $nerdpress_widget_id; ?>">
	<?php
	while ( has_sub_field( 'accordion' ) ) :
		$subfields_to_get = array(
			'acc_title',
			'acc_content',
		);
		
		foreach ( $subfields_to_get as $subfield ) {
			${ $subfield } = get_sub_field( $subfield );
		}
	?>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a class="accordion-toggle block" data-toggle="collapse" data-parent="#nerdpress-widget-<?= $nerdpress_widget_id; ?>" href="#collapse<?= $a; ?>">
					<?= $acc_title; ?>
				</a>
			</h4>
		</div>
		<div id="collapse<?= $a; ?>" class="panel-collapse collapse<?= ( $a == 0 ? ' in' : '' ); ?>">
			<div class="panel-body">
				<?= $acc_content; ?>
			</div>
		</div>
	</div>
	<?php $a++; endwhile; ?>
</div>