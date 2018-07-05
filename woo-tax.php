<?php
/**
 * Plugin Name: WooTax Subscriptions
 * 
 * Description: Frontload Tax on WooCommerce Subscription Items 
 *
 * @package WooCommerce
 */

add_action('woocommerce_calculate_totals', 'calculate_totals_not_wc', 10, 1);

function calculate_totals_not_wc($totals){	
	
	foreach($totals->cart_contents as $key => $item){
		if($item['product_id'] == '3179'){			
			$product_key = $key;
			$line_tax_data['subtotal']['29'] = $item['line_tax_data']['subtotal']['29'];
			$line_tax_data['subtotal']['29'] = $item['line_tax_data']['subtotal']['29'];	
				
			$totals->cart_contents[$key]['line_tax_data'] = $line_tax_data;
		}
	}
	
}

add_action( 'woocommerce_subscription_totals_table', 'subscription_totals_not_wc' );

function subscription_totals_not_wc($subscription){
//print_r($subscription);
}

add_filter('woocommerce_payment_complete_order_status', 'set_subscription_to_untaxable', 5, 2);

function set_subscription_to_untaxable($order_status, $order_id){


	global $wpdb;
	$order_id = intval($order_id) + 1;
	$table = $wpdb->prefix . 'postmeta';

	$sql = "SELECT `meta_value` FROM $table WHERE `post_id` = $order_id AND `meta_key` = '_order_tax'";
	
	$results = $wpdb->get_results($sql);

	
	foreach ($results as $result) {
		$tax_amount = $result->meta_value;
	}

	if($tax_amount && $tax_amount != 0){
		
		$sql = "UPDATE $table SET `meta_value` = 0 WHERE `post_id` = $order_id AND `meta_key` = '_order_tax'";
		$results = $wpdb->get_results($sql);	

		$sql = "SELECT `meta_value` FROM $table WHERE `post_id` = $order_id AND `meta_key` = '_order_total'";
		$results = $wpdb->get_results($sql);
		
		foreach ($results as $result) {
			$order_total = $result->meta_value;
		}

		$new_order_total = $order_total - $tax_amount;

		$sql = "UPDATE $table SET `meta_value` = $new_order_total WHERE `post_id` = $order_id AND `meta_key` = '_order_total'";
		$results = $wpdb->get_results($sql);	
	}
	return $order_status;		
	
}





