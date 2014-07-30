<?php
// Define theme support for WooCommerce
add_theme_support( 'woocommerce' );

// Disable Woo's CSS
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

// Get column count from site config
global $woocommerce_loop;
$woocommerce_loop['columns'] = NerdPress::variable( 'woocommerce_columns' );

// Remove coupon from from top of checkout (it is loaded later inside Payment tab)
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// Remove Woo breadcrumbs since we have our own
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

// Remove WC generator tag from <head>
remove_action( 'wp_head', 'wc_generator_tag' );

// Modifying the default product tabs
add_filter( 'woocommerce_product_tabs', 'nrd_change_woo_tabs' );

function nrd_change_woo_tabs( $tabs ) {
	
	// Add badge markup to "Reviews" tab
	if ( get_comments_number( $post->ID ) > 0 ) 
		$tabs['reviews']['title'] = sprintf( __( 'Reviews <span class="badge">%d</span>', 'woocommerce' ), get_comments_number( $post->ID ) );
	else 
		$tabs['reviews']['title'] = __( 'Write a Review', 'woocommerce' );
	
	return $tabs;
}

// Clean up single product template
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );

//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
//remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

// Add NerdPress social share to single product template
add_action( 'woocommerce_share', 'nrd_woo_share' );

function nrd_woo_share() {
	echo do_shortcode( '[nerdpress_social_share]' );
}

// Remove subscriptions from above My Account
remove_action( 'woocommerce_before_my_account', 'WC_Subscriptions::get_my_subscriptions_template' );

// Remove payment methods from My Account
remove_action( 'woocommerce_after_my_account', 'woocommerce_stripe_saved_cards' ); // Stripe
remove_action( 'woocommerce_after_my_account', array( 'WC_Authorize_Net_CIM', 'add_my_payment_methods' ) ); // Authorize.net
?>