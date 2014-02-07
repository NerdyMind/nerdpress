<?php
$nerdpress_widget_id++;

$fields_to_get = array(
	'gal_columns',
	'gal_images',
);

foreach ( $fields_to_get as $field ) {
	${ $field } = get_field( $field );
}

if ( $gal_columns == 2 ) $grid_col = '6';
if ( $gal_columns == 3 ) $grid_col = '4';
if ( $gal_columns == 4 ) $grid_col = '3';
if ( $gal_columns == 6 ) $grid_col = '2';

$i = 0;

if ( $gal_images ) :
?>
<div id="nerdpress-widget-<?= $nerdpress_widget_id; ?>">
	<div class="row">
		<?php foreach ( $gal_images as $image ) : $i++; ?>
		<div class="col-sm-<?= $grid_col; ?> text-center">
			<a href="<?= $image['sizes']['large']; ?>" data-toggle="lightbox"><img src="<?= $image['sizes']['thumbnail']; ?>" class="thumbnail" /></a>
		</div>
		<? if ( $i % $gal_columns == 0 ) echo '</div><div class="row">'; ?>
		<? endforeach; ?>
	</div><!-- /.row -->
	<?php else : ?>
	<div class="alert alert-danger">
		There are no images in this gallery. <a href="<?= admin_url( 'post.php?action=edit&post=' . get_the_ID() ); ?>" class="alert-link" target="_blank">Add some?</a>
	</div>
</div>
<?php endif; ?>