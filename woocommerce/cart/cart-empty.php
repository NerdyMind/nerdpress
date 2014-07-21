<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

wc_print_notices();

?>

<div class="alert alert-info">
	<?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?>
</div>

<?php do_action( 'woocommerce_cart_is_empty' ); ?>

<p class="return-to-shop">
	<a class="btn btn-lg btn-primary wc-backward" href="<?php echo apply_filters( 'woocommerce_return_to_shop_redirect', get_permalink( wc_get_page_id( 'shop' ) ) ); ?>">
		<?php _e( 'Start Shopping', 'woocommerce' ) ?> <i class="fa fa-chevron-right"></i>
	</a>
</p>