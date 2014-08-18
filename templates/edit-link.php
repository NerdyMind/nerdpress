<?php
if ( is_singular() ) {
	$edit = true;
	$link = admin_url( 'post.php?post=' . get_the_ID() . '&action=edit' );
	$post_type = get_post_type_object( get_post_type() );
	$title = $post_type->labels->singular_name . ' (' . get_the_title() . ')';
}
if ( is_page() ) $icon = 'file-text-o';

if ( get_post_type() == 'nrd_element' ) $icon = 'th';

if ( get_post_type() == 'product' ) $icon = 'shopping-cart';

if ( get_post_type() == 'nrd_event' ) $icon = 'calendar';

if ( is_category() || is_tax() ) {
	$edit = true;
	$object = get_queried_object();
	$tax = get_taxonomy( $object->taxonomy );
	$link = admin_url( 'edit-tags.php?action=edit&taxonomy=' . $object->taxonomy . '&tag_ID=' . $object->term_id );
	$title = $tax->labels->singular_name . ' (' . $object->name . ')';
}
if ( isset( $widget_area_id ) ) {
	$edit = true;
	$link = admin_url( 'widgets.php' );
	$title = 'Widgets (' . $widget_area_id . ')';
	$icon = 'cog';
}
?>
<?php if ( current_user_can( 'edit_posts' ) && isset( $edit ) ) : ?>
<div class="text-right nerdpress-edit-link">
	<a href="<?= $link; ?>" target="_blank" class="btn btn-default btn-sm" title="Edit <?= $title; ?>" data-toggle="tooltip"><i class="fa fa-<?= ( $icon ) ? $icon : 'edit'; ?> text-primary"></i> Edit</a>
</div>
<?php endif; ?>