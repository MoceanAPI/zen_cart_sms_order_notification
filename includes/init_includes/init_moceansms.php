<?php
if($_GET['main_page'] == 'checkout_success' && isset($_SESSION['order_summary'])) {
	function send_moceansms($mocean_key, $mocean_secret, $mocean_from = "New Order", $mocean_message, $mocean_phone) {
		//echo $admin_notify_message;
		$postdata = http_build_query(
			array(
				'mocean-api-key' => $mocean_key,
				'mocean-api-secret' => $mocean_secret,
				'mocean-from' => $mocean_from,
				'mocean-text' => $mocean_message,
				'mocean-to' => $mocean_phone,
				'mocean-resp-format' => 'json',
			)
		);

		$opts = array('http' =>
			array(
				'method'  => 'POST',
				'header'  => 'Content-Type: application/x-www-form-urlencoded',
				'content' => $postdata
			)
		);

		$context  = stream_context_create($opts);

		$result = file_get_contents('https://rest.moceanapi.com/rest/2/sms', false, $context);
		return $result;
	}
	
	$admin_notify_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify' LIMIT 1");
	$admin_notify = $admin_notify_request->fields['value'];
	
		$mocean_key_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_key' LIMIT 1");
		$mocean_key = $mocean_key_request->fields['value'];

		$mocean_secret_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_secret' LIMIT 1");
		$mocean_secret = $mocean_secret_request->fields['value'];
		if(isset($mocean_secret) && isset($mocean_key)){
			
		
			//SELECT * FROM `zen_orders_products` ORDER BY `zen_orders_products`.`products_quantity` ASC
			$orders_products = $db->Execute("SELECT * FROM ".DB_PREFIX."orders_products WHERE orders_id=".$_SESSION['order_summary']['order_number']." LIMIT 1");
			$orders_products = $orders_products->fields;
			
			$order_status = $db->Execute("SELECT * FROM ".DB_PREFIX."orders_status WHERE orders_status_id=".$_SESSION['order_summary']['orders_status']." and language_id = " . (int)$_SESSION['languages_id']."
			LIMIT 1");
			$order_status = $order_status->fields;
			
			$orders = $db->Execute("SELECT * FROM ".DB_PREFIX."orders WHERE orders_id=".$_SESSION['order_summary']['order_number']."
			LIMIT 1");
			$orders = $orders->fields;
			
			$customers = $db->Execute("SELECT * FROM ".DB_PREFIX."customers WHERE customers_id=".$orders['customers_id']."
			LIMIT 1");
			$customers = $customers->fields;
			
			$message_vars = [
				'[shop_name]' => STORE_NAME,
				'[shop_email]' => EMAIL_FROM,
				'[shop_url]' => HTTP_SERVER,
				'[order_id]' => $_SESSION['order_summary']['order_number'],
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
		$mocean_message_from_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='message_from' LIMIT 1");
		$mocean_message_from = $mocean_message_from_request->fields['value'];
		foreach($message_vars as $key=>$value) {
				$mocean_message_from = str_replace($key, $value, $mocean_message_from);
		}
		if($admin_notify == 'true') {
			$admin_notify_message_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify_message' LIMIT 1");
			$admin_notify_message = $admin_notify_message_request->fields['value'];
			$admin_phone_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_phone' LIMIT 1");
			$admin_phone = $admin_phone_request->fields['value'];

			foreach($message_vars as $key=>$value) {
					$admin_notify_message = str_replace($key, $value, $admin_notify_message);
			}
			send_moceansms($mocean_key, $mocean_secret, $mocean_message_from, $admin_notify_message, $admin_phone);
		}

		$orders_status_query_raw = "select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = " . (int)$_SESSION['languages_id'];
		$orders_status = $db->Execute($orders_status_query_raw);

		foreach ($orders_status as $status) {
			$customer_status_notify_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_status_notify_".$status['orders_status_name']."' LIMIT 1");
			$customer_status_notify_result = $customer_status_notify_request->fields['value'];
			//echo $order_status['orders_status_name'];
			if($customer_status_notify_result == "true" && $order_status['orders_status_name'] == $status['orders_status_name']) {
				$message = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_".lcfirst($status['orders_status_name'])."_notify_message' LIMIT 1");
			$message = $message->fields['value'];
			foreach($message_vars as $key=>$value) {
				$message = str_replace($key, $value, $message);
			}
				send_moceansms($mocean_key, $mocean_secret, $mocean_message_from, $message, $customers['customers_telephone']);
			}
			
		}
	}
}