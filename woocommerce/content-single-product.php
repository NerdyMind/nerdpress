<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>

<div itemscope itemtype="<?php echo woocommerce_get_product_schema(); ?>" id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

<?php get_template_part( 'templates/page', 'header' ); ?>

	<div class="row">
		<div class="col-sm-3">
			<?php NerdPress::widget_area( 'Shop Sidebar' ); ?>
		</div><!-- /.col-sm-3 -->
		
		<div class="col-sm-9">
			
			<div class="prod-details">
			
				<div class="row">
					<div class="col-sm-6 prod-images">
						<?php woocommerce_show_product_sale_flash(); ?>
				
						<?php woocommerce_show_product_images(); ?>
					</div><!-- /.col-sm-6 -->
					
					<div class="col-sm-6 prod-desc">
						<?php do_action( 'woocommerce_before_single_product_summary' ); ?>
					
						<h4 class="text-primary text-right space-bottom20">
							<?php woocommerce_template_single_price(); ?>
						</h4>
						
						<?php woocommerce_template_single_add_to_cart(); ?>
						
						<div class="clearfix"></div>						
						
						<div class="summary entry-summary">
							<?php woocommerce_template_single_excerpt(); ?>
						</div>
						
						<div class="prod-meta">
							<?php woocommerce_template_single_meta(); ?>
						</div>
						
					</div><!-- /.col-sm-6 -->
				</div><!-- /.row -->
				
				<div class="row space-top20">
					<div class="col-sm-4">
						<h4><?php woocommerce_template_single_rating(); ?></h4>
					</div><!-- /.col-sm-6 -->
					
					<div class="col-sm-8 text-right">
						<?php woocommerce_template_single_sharing(); ?>
					</div><!-- /.col-sm-6 -->
				</div><!-- /.row -->				
				
			</div><!-- /.thumbnail -->
			
			<?php do_action( 'woocommerce_after_single_product_summary' ); ?>
			
		</div><!-- /.col-sm-9 -->
	</div><!-- /.row -->

	<meta itemprop="url" content="<?php the_permalink(); ?>" />

</div><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>