<?php
/**
 * Cross-sells
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce, $woocommerce_loop;

$crosssells = WC()->cart->get_cross_sells();

if ( sizeof( $crosssells ) == 0 ) return;

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => apply_filters( 'woocommerce_cross_sells_total', $posts_per_page ),
	'orderby'             => $orderby,
	'post__in'            => $crosssells,
	'meta_query'          => $meta_query
);

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = apply_filters( 'woocommerce_cross_sells_columns', $columns );

if ( $products->have_posts() ) : ?>

<div class="cross-sells">
	<div class="panel panel-info">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e( 'You may be interested in&hellip;', 'woocommerce' ) ?></h3>
		</div>
		
		<ul class="list-group">
			<?php while ( $products->have_posts() ) : $products->the_post(); ?>
			<li class="list-group-item clearfix">
				<div class="pull-right">
					<?php echo do_shortcode( '[add_to_cart id="' . get_the_ID() . '" style=""]' ); ?>
				</div>
				<a href="<?php the_permalink(); ?>">
					<i class="fa fa-cube text-primary"></i> 
					<?php the_title(); ?>
				</a>
			</li>
			<?php endwhile; // end of the loop. ?>		
		</ul>
	</div>	
</div>

<?php endif;

wp_reset_query();