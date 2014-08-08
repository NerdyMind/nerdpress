<?php
/**
 * Order details
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$order = new WC_Order( $order_id );
?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<div class="pull-right">
				<span class="italic">Order <?php echo $order->get_order_number(); ?>, placed on <?php echo date_i18n( get_option( 'date_format' ), strtotime( $order->order_date ) ); ?></span> 
				<span class="label label-<?= ( $order->status == 'completed' ) ? 'success' : 'default'; ?>"><?= ucfirst( $order->status ); ?></span>
			</div>
			
			<i class="fa fa-fw fa-cubes text-primary"></i> Your Order
		</h3>
	</div>
	
	<div class="panel-body">

		<table class="shop_table order_details table table-hover table-striped table-bordered">
			<thead>
				<tr>
					<th class="product-name"><?php _e( 'Product', 'woocommerce' ); ?></th>
					<th class="product-total"><?php _e( 'Total', 'woocommerce' ); ?></th>
				</tr>
			</thead>
			<tfoot>
			<?php
				if ( $totals = $order->get_order_item_totals() ) foreach ( $totals as $total ) :
					?>
					<tr>
						<th scope="row"><?php echo $total['label']; ?></th>
						<td><?php echo $total['value']; ?></td>
					</tr>
					<?php
				endforeach;
			?>
					<tr>
						<th scope="row">Payment Method:</th>
						<td><?php echo $order->payment_method_title; ?></td>
					</tr>
			</tfoot>
			<tbody>
				<?php
				if ( sizeof( $order->get_items() ) > 0 ) {
		
					foreach( $order->get_items() as $item ) {
						$_product     = apply_filters( 'woocommerce_order_item_product', $order->get_product_from_item( $item ), $item );
						$item_meta    = new WC_Order_Item_Meta( $item['item_meta'], $_product );
		
						?>
						<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'order_item', $item, $order ) ); ?>">
							<td class="product-name">
								<?php
									if ( $_product && ! $_product->is_visible() )
										echo apply_filters( 'woocommerce_order_item_name', $item['name'], $item );
									else
										echo apply_filters( 'woocommerce_order_item_name', sprintf( '<a href="%s">%s</a>', get_permalink( $item['product_id'] ), $item['name'] ), $item );
		
									echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>', $item );
		
									$item_meta->display();
		
									if ( $_product && $_product->exists() && $_product->is_downloadable() && $order->is_download_permitted() ) {
		
										$download_files = $order->get_item_downloads( $item );
										$i              = 0;
										$links          = array();
		
										foreach ( $download_files as $download_id => $file ) {
											$i++;
		
											$links[] = '<small><a href="' . esc_url( $file['download_url'] ) . '">' . sprintf( __( 'Download file%s', 'woocommerce' ), ( count( $download_files ) > 1 ? ' ' . $i . ': ' : ': ' ) ) . esc_html( $file['name'] ) . '</a></small>';
										}
		
										echo '<br/>' . implode( '<br/>', $links );
									}
								?>
							</td>
							<td class="product-total">
								<?php echo $order->get_formatted_line_subtotal( $item ); ?>
							</td>
						</tr>
						<?php
		
						if ( in_array( $order->status, array( 'processing', 'completed' ) ) && ( $purchase_note = get_post_meta( $_product->id, '_purchase_note', true ) ) ) {
							?>
							<tr class="product-purchase-note">
								<td colspan="3"><?php echo apply_filters( 'the_content', $purchase_note ); ?></td>
							</tr>
							<?php
						}
					}
				}
		
				do_action( 'woocommerce_order_items_table', $order );
				?>
			</tbody>
		</table>

	</div><!-- /.panel-body -->
</div><!-- /.panel -->

<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>

<?php if ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

<div class="row addresses">

	<div class="col-sm-6">

<?php endif; ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-fw fa-building text-primary"></i> <?php _e( 'Billing Address', 'woocommerce' ); ?></h3>
	</div>
	
	<div class="panel-body">
		<address><p>
			<?php
				if ( ! $order->get_formatted_billing_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_billing_address();
			?>
		</p></address>

		<dl class="customer_details">
		<?php
			if ( $order->billing_email ) echo '<div><strong>' . __( 'Email:', 'woocommerce' ) . '</strong> ' . $order->billing_email . '</div>';
			if ( $order->billing_phone ) echo '<div><strong>' . __( 'Telephone:', 'woocommerce' ) . '</strong> ' . $order->billing_phone . '</div>';
		
			// Additional customer details hook
			do_action( 'woocommerce_order_details_after_customer_details', $order );
		?>
		</dl>
	</div><!-- /.panel-body -->
</div><!-- /.panel -->

<?php if ( get_option( 'woocommerce_ship_to_billing_address_only' ) === 'no' && get_option( 'woocommerce_calc_shipping' ) !== 'no' ) : ?>

	</div><!-- /.col-sm-6 -->

	<div class="col-sm-6">
	
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title"><i class="fa fa-fw fa-plane text-primary"></i> <?php _e( 'Shipping Address', 'woocommerce' ); ?></h3>
	</div>
	
	<div class="panel-body">
		<address><p>
			<?php
				if ( ! $order->get_formatted_shipping_address() ) _e( 'N/A', 'woocommerce' ); else echo $order->get_formatted_shipping_address();
			?>
		</p></address>		
	</div><!-- /.panel-body -->
</div><!-- /.panel -->

	</div><!-- /.col-sm-6 -->

</div><!-- /.row -->

<?php endif; ?>

<div class="clearfix"></div>
