<?php
// Define theme support for WooCommerce
add_theme_support( 'woocommerce' );

// Disable Woo's CSS
add_filter( 'woocommerce_enqueue_styles', '__return_false' );

// Get column count from site config
global $woocommerce_loop;
$woocommerce_loop['columns'] = NerdPress::variable( 'woocommerce_columns' );

// Remove WC generator tag from <head>
function nerdpress_remove_woo_head() {
	global $woocommerce;
	remove_action( 'wp_head', array( $woocommerce, 'generator' ) );
}
 
// called only after WooCommerce has finished loading
add_action( 'woocommerce_init', 'nerdpress_remove_woo_head' );

// Remove coupon from from top of checkout (it is loaded later inside Payment tab)
remove_action( 'woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10 );

// Remove Woo breadcrumbs since we have our own
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
?>