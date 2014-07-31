<?php
/**
 * Cart Page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( WC()->cart->get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<div class="row">
	<div class="col-sm-7">
	
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-fw fa-cubes text-primary"></i> Your Item(s)
				</h3>
			</div>
			
			<div class="panel-body">
			
<table class="shop_table cart table table-hover table-striped table-bordered" cellspacing="0">
	<thead>
		<tr>
			<th class="product-remove">&nbsp;</th>
			<th class="product-thumbnail hidden">&nbsp;</th>
			<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
			<th class="product-price"><?php _e( 'Price', 'woocommerce' ); ?></th>
			<th class="product-quantity"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal"><?php _e( 'Total', 'woocommerce' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

					<td class="product-remove text-center">
					
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" title="%s"><span class="fa-stack fa-lg"><i class="fa fa-fw fa-circle fa-stack-2x text-primary"></i><i class="fa fa-fw fa-trash-o fa-stack-1x fa-inverse"></i></span></a>', esc_url( WC()->cart->get_remove_url( $cart_item_key ) ), __( 'Remove this item', 'woocommerce' ) ), $cart_item_key );
						?>
					</td>

					<td class="product-thumbnail hidden">
						<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							if ( ! $_product->is_visible() )
								echo $thumbnail;
							else
								printf( '<a href="%s">%s</a>', $_product->get_permalink(), $thumbnail );
						?>
					</td>

					<td class="product-name">
						<?php
							if ( ! $_product->is_visible() )
								echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
							else
								echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_title() ), $cart_item, $cart_item_key );

							// Meta data
							echo WC()->cart->get_item_data( $cart_item );

               				// Backorder notification
               				if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
               					echo '<p class="backorder_notification">' . __( 'Available on backorder', 'woocommerce' ) . '</p>';
						?>
					</td>

					<td class="product-price">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
						?>
					</td>

					<td class="product-subtotal">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
		<tr>
			<td colspan="6" class="actions">
			
				<button type="submit" class="btn btn-default" name="update_cart" value="<?php _e( 'Update Cart', 'woocommerce' ); ?>">
					<i class="fa fa-refresh"></i> <?php _e( 'Update Cart', 'woocommerce' ); ?>
				</button>
				
			</td>
		</tr>

		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>

			
			</div><!-- /.panel-body -->
		</div><!-- /.panel -->
		
		<?php woocommerce_shipping_calculator(); ?>
		
		<?php if ( WC()->cart->coupons_enabled() ) : ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-fw fa-ticket text-primary"></i> Use A Coupon
				</h3>
			</div>
			
			<div class="panel-body">
			
					<div class="coupon">

						<label for="coupon_code" class="sr-only"><?php _e( 'Coupon', 'woocommerce' ); ?>:</label>
							
						<div class="input-group">
							<input type="text" name="coupon_code" id="coupon_code" class="form-control" value="" placeholder="<?php _e( 'Coupon code', 'woocommerce' ); ?>" autocomplete="off" />
							<span class="input-group-btn">
								<input type="submit" class="btn btn-primary" name="apply_coupon" value="<?php _e( 'Apply Coupon', 'woocommerce' ); ?>" />
							</span>
						</div><!-- /input-group -->

						<?php do_action('woocommerce_cart_coupon'); ?>

					</div><!-- /.coupon -->
			
			</div><!-- /.panel-body -->			
		</div><!-- /.panel -->
		<?php endif; ?>
	
	</div><!-- /.col-sm-7 -->
	
	<div class="col-sm-5">
	
		<?php woocommerce_cross_sell_display(); ?>

		<div class="panel panel-primary">
			<div class="panel-heading">
				<h3 class="panel-title">
					<i class="fa fa-fw fa-shopping-cart"></i> Cart Totals
				</h3>
			</div>
			
			<div class="panel-body">

				<div class="cart-collaterals">
				
					<?php do_action( 'woocommerce_cart_collaterals' ); ?>
				
					<?php woocommerce_cart_totals(); ?>
					
						<button type="submit" class="checkout-button btn btn-lg btn-block btn-success" name="proceed" value="<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?>">
							<?php _e( 'Proceed to Checkout', 'woocommerce' ); ?> 
							<i class="fa fa-chevron-right"></i>
						</button>

						<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
		
						<?php wp_nonce_field( 'woocommerce-cart' ); ?>
				
				</div>
			
			</div><!-- /.panel-body -->
		</div><!-- /.panel -->

	</div><!-- /.col-sm-5 -->
</div><!-- /.row -->

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<?php do_action( 'woocommerce_after_cart' ); ?>