<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wc_print_notices(); ?>

<p class="myaccount_user">
	<?php
	printf(
		__( 'Hello <strong>%1$s</strong> (not %1$s? <a href="%2$s">Sign out</a>).', 'woocommerce' ) . ' ',
		$current_user->display_name,
		wp_logout_url( get_permalink( wc_get_page_id( 'myaccount' ) ) )
	);

	printf( __( 'From your account dashboard you can view your recent orders, manage your shipping and billing addresses and <a href="%s">edit your password and account details</a>.', 'woocommerce' ),
		wc_customer_edit_account_url()
	);
	?>
</p>

<?php do_action( 'woocommerce_before_my_account' ); ?>

<?php do_action( 'woocommerce_after_my_account' ); ?>

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-justified" role="tablist">
	<li class="active">
		<a href="#my-orders" role="tab" data-toggle="tab">
			<i class="fa fa-cubes text-primary"></i> Orders
		</a>
	</li>
	<li>
		<a href="#my-addresses" role="tab" data-toggle="tab">
			<i class="fa fa-plane text-primary"></i> Addresses
		</a>
	</li>
	<li>
		<a href="#my-downloads" role="tab" data-toggle="tab">
			<i class="fa fa-cloud-download text-primary"></i> Downloads
		</a>
	</li>
	<li>
		<a href="<?php echo wc_get_endpoint_url( 'edit-account' ); ?>">
			<i class="fa fa-edit text-primary"></i> Change Password
		</a>
	</li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
	<div class="tab-pane active fade in" id="my-orders">
		<?php wc_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); ?>
	</div>
	
	<div class="tab-pane fade" id="my-addresses">
		<?php wc_get_template( 'myaccount/my-address.php' ); ?>
	</div>
	
	<div class="tab-pane fade" id="my-downloads">
		<?php wc_get_template( 'myaccount/my-downloads.php' ); ?>
	</div>
</div>