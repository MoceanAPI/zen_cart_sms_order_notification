<?php
$request_file = basename($_SERVER['REQUEST_URI'], '?' . $_SERVER['QUERY_STRING']);
if(($request_file == 'orders.php' || $_GET['cmd'] =='orders') && $_GET['page'] == 1 && $_GET['action'] == 'edit') {
?>
	<script type="text/javascript">
		document.addEventListener("DOMContentLoaded", function(){
			$('[name=\'notify\']').parent().parent().parent().append('<div class="radio"><label><?php echo zen_draw_radio_field('notify', '2', FALSE); ?>MoceanSMS</label></div><div class="radio"><label><?php echo zen_draw_radio_field('notify', '3', FALSE); ?>MoceanSMS and Email</label></div>');
		});
	</script>
<?php
} 
if(($request_file == 'orders.php' || $_GET['cmd'] =='orders')  && $_GET['page'] == 1 && $_GET['action'] == 'update_order') {
	$status = $_POST['status'];
	$notify = $_POST['notify'];
	if($notify == 2 || $notify == 3) {
		
		if($_POST['notify'] == 2) {
			$_POST['notify'] = 0;
		} elseif($_POST['notify'] == 3) {
			$_POST['notify'] = 2;
		}
		
		$mocean_message_from_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='message_from' LIMIT 1");
		$mocean_message_from = $mocean_message_from_request->fields['value'];
		foreach($message_vars as $key=>$value) {
				$mocean_message_from = str_replace($key, $value, $mocean_message_from);
		}
		
		$order_status = $db->Execute("SELECT * FROM ".DB_PREFIX."orders_status WHERE orders_status_id=".$status." LIMIT 1");
		$order_status = lcfirst($order_status->fields['orders_status_name']);
		$orders = $db->Execute("SELECT * FROM ".DB_PREFIX."orders WHERE orders_id=".$_GET['oID']."
			LIMIT 1");
		$orders = $orders->fields;

		$customer_status_message = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_".$order_status."_notify_message' LIMIT 1");
		$customer_status_message = $customer_status_message->fields['value'];
		$customer_status_notify_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_status_notify_".$order_status."' LIMIT 1");
		$customer_status_notify_result = $customer_status_notify_request->fields['value'];
		
		
		if($customer_status_notify_result == "true"){
			$orders_products = $db->Execute("SELECT * FROM ".DB_PREFIX."orders_products WHERE orders_id=".$_GET['oID']." LIMIT 1");
			
			$orders_products = $orders_products->fields;
			$customers = $db->Execute("SELECT * FROM ".DB_PREFIX."customers WHERE customers_id=".$orders['customers_id']."
			LIMIT 1");
			$customers = $customers->fields;
			
			$message_vars = [
				'[shop_name]' => STORE_NAME,
				'[shop_email]' => EMAIL_FROM,
				'[shop_url]' => HTTP_SERVER,
				'[order_id]' => $_GET['oID'],
				'[order_amount]' => $orders_products['products_quantity'],
				'[order_status]' => $order_status['orders_status_name'],
				'[order_product]' => $orders_products['products_name'],
				'[payment_method]' => $orders['payment_method'],
				'[billing_last_name]' => $customers['customers_lastname'],
				'[billing_phone]' => $customers['customers_telephone'],
				'[billing_email]' => $customers['customers_email_address'],
				'[billing_company]' => $orders['customers_company'],
				'[billing_address]' => $orders['customers_street_address'],
				'[billing_country]' => $orders['customers_country'],
				'[billing_city]' => $orders['customers_city'],
				'[billing_state]' => $orders['customers_state'],
				'[billing_postcode]' => $orders['customers_postcode'],
				'[billing_first_name]' => $customers['customers_firstname'],
			];
			
			foreach($message_vars as $key=>$value) {
				$customer_status_message = str_replace($key, $value, $customer_status_message);
			}
			
			send_moceansms($mocean_key, $mocean_secret, $mocean_message_from, $customer_status_message, $customers['customers_telephone']);
		};
	}
}
