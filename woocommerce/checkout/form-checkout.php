<?php
/**
 * Checkout Form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

wc_print_notices();

do_action( 'woocommerce_before_checkout_form', $checkout );

wp_enqueue_script( 'nerdpress-checkout', get_template_directory_uri() . '/assets/js/nerdpress-checkout.js', array( 'jquery' ), null, true );

// If checkout registration is disabled and not logged in, the user cannot checkout
if ( ! $checkout->enable_signup && ! $checkout->enable_guest_checkout && ! is_user_logged_in() ) {
	echo apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) );
	return;
}

// filter hook for include new pages inside the payment method
$get_checkout_url = apply_filters( 'woocommerce_get_checkout_url', WC()->cart->get_checkout_url() ); ?>

<form name="checkout" method="post" class="checkout" action="<?php echo esc_url( $get_checkout_url ); ?>">

	<?php if ( sizeof( $checkout->checkout_fields ) > 0 ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="row">
			<div class="col-xs-12">
				<ul class="nav nav-crumbs">
					<li class="crumb-cart">
						<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>">
							<i class="fa fa-fw fa-shopping-cart fa-g"></i> 
							<span class="step-label">Your Cart</span>
						</a>
					</li>
					<li class="crumb-billing active">
						<a href="#billing" data-toggle="tab">
							<i class="fa fa-fw fa-building fa-lg"></i> 
							<span class="step-label">Details</span>
						</a>
					</li>
					<li class="crumb-shipping">
						<a href="#shipping" data-toggle="tab">
							<i class="fa fa-fw fa-lg fa-<?php echo ( WC()->cart->needs_shipping_address() === true ) ? 'plane' : 'info-circle'; ?>"></i> 
							<span class="step-label"><?php echo ( WC()->cart->needs_shipping_address() === true ) ? 'Shipping' : 'Additional'; ?></span>
						</a>
					</li>
					<li class="crumb-review">
						<a href="#review" data-toggle="tab">
							<i class="fa fa-fw fa-credit-card fa-lg"></i> 
							<span class="step-label">Review</span>
						</a>
					</li>
					<li class="crumb-confirmation">
						<a href="#" class="disabled">
							<i class="fa fa-fw fa-check-circle-o fa-lg"></i> 
							<span class="step-label">Confirmation</span>
						</a>
					</li>
				</ul>
			</div><!-- /.col-xs-12 -->
		</div><!-- /.row -->
		
		<div class="tab-content">
			<div class="tab-pane fade in active" id="billing">
			
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
				
				<hr/>
				
				<div class="text-right space-top20">
					<a href="#shipping" class="btn btn-primary btn-lg crumb-shipping" data-toggle="tab">
						<?php echo ( WC()->cart->needs_shipping_address() === true ) ? 'Shipping' : 'Additional Information'; ?> 
						<i class="fa fa-chevron-right"></i>
					</a>
				</div>
				
			</div>
			
			<div class="tab-pane fade" id="shipping">
				
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				
				<hr/>
				
				<div class="row space-top20">
					<div class="col-xs-6">
						<a href="#billing" class="btn btn-primary btn-lg crumb-billing" data-toggle="tab" data-tab="1">
							<i class="fa fa-chevron-left"></i> Back to Billing
						</a>
					</div>
					<div class="col-xs-6 text-right">
						<a href="#review" class="btn btn-primary btn-lg crumb-review" data-toggle="tab" data-tab="3">
							Review &amp; Finish <i class="fa fa-chevron-right"></i>
						</a>
					</div>
				</div>
				
			</div>
			
			<div class="tab-pane fade" id="review">
			
				<h3>Review &amp; Finish</h3>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
				
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>

			</div>
		</div>

	<?php endif; ?>	

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>