<?php
/**
 * View Order
 *
 * Shows the details of a particular order on the account page 
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version   2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<?php wc_print_notices(); ?>

<p class="order-info alert alert-info hidden"><?php printf( __( 'Order <mark class="order-number">%s</mark> was placed on <mark class="order-date">%s</mark> and is currently <mark class="order-status">%s</mark>.', 'woocommerce' ), $order->get_order_number(), date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ), __( $status->name, 'woocommerce' ) ); ?></p>

<?php if ( $notes = $order->get_customer_order_notes() ) : $the_note = 0;
	?>
	
	<div class="panel panel-success">
		<div class="panel-heading">
			<h3 class="panel-title"><?php _e( 'Order Updates', 'woocommerce' ); ?></h3>
		</div>
		
		<ul class="list-group">
			<?php foreach ( $notes as $note ) : $the_note++; ?>
			<li class="list-group-item">
				<div class="row">
					<div class="col-sm-1 text-center">
						<span class="text-primary top label label-default"><?= $the_note; ?></span>
					</div>
					<div class="col-sm-11">
						<?php echo wpautop( wptexturize( $note->comment_content ) ); ?>
						
						<div class="small">
							<em><?php echo date_i18n( __( 'l F jS, Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) ); ?></em> 
						</div>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
endif;

do_action( 'woocommerce_view_order', $order_id );
?>

<div class="text-right">
	<a href="<?php echo get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ); ?>" class="btn btn-primary">
		<i class="fa fa-chevron-left"></i> Back to My Account
	</a>
</div>