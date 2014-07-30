<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( class_exists( 'WC_Subscriptions' ) ) 
	$has_subscriptions = true;
	
if ( class_exists( 'WC_Authorize_Net_CIM' ) || function_exists( 'woocommerce_stripe_init' ) ) 
	$has_payments = true;
?>

<?php wc_print_notices(); ?>

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-justified space-bottom20" role="tablist">
	<li>
		<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>#my-orders">
			<i class="fa fa-cubes text-primary"></i> Orders
		</a>
	</li>
	
	<?php if ( $has_payments ) : ?>
	<li>
		<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>#my-payment-methods">
			<i class="fa fa-credit-card text-primary"></i> Payment
		</a>		
	</li>
	<?php endif; ?>
	
	<li>
		<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>#my-addresses">
			<i class="fa fa-plane text-primary"></i> Addresses
		</a>
	</li>
	<li>
		<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>#my-downloads">
			<i class="fa fa-cloud-download text-primary"></i> Downloads
		</a>
	</li>
	<li class="active">
		<a href="<?php echo wc_get_endpoint_url( 'edit-account' ); ?>">
			<i class="fa fa-edit text-primary"></i> Password
		</a>
	</li>
</ul>

<form action="" method="post">

	<div class="row space-bottom20">
		<div class="col-sm-6">
			<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="text" class="form-control" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
		</div>
		<div class="col-sm-6">
			<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="text" class="form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
		</div>
	</div><!-- /.row -->
	
	<div class="row space-bottom20">
		<div class="col-sm-12">
			<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
			<input type="email" class="form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
		</div>
	</div><!-- /.row -->

	<div class="row space-bottom20">
		<div class="col-sm-6">
			<label for="password_1"><?php _e( 'Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="form-control" name="password_1" id="password_1" />			
		</div>
		<div class="col-sm-6">
			<label for="password_2"><?php _e( 'Confirm new password', 'woocommerce' ); ?></label>
			<input type="password" class="form-control" name="password_2" id="password_2" />			
		</div>
	</div><!-- /.row -->

	<p><input type="submit" class="btn btn-lg btn-primary" name="save_account_details" value="<?php _e( 'Save changes', 'woocommerce' ); ?>" /></p>

	<?php wp_nonce_field( 'save_account_details' ); ?>
	<input type="hidden" name="action" value="save_account_details" />
</form>