<?php
if (!defined('IS_ADMIN_FLAG')) {
    die('Illegal Access');
} 
if (function_exists('zen_register_admin_page')) {
	
	if(!zen_page_key_exists('moceansms')) {
		$conn = new mysqli(DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);

		$autoloader_file = __DIR__.'/../../../../includes/auto_loaders/config.core.php';
		if(is_file($autoloader_file) && is_readable($autoloader_file)) {
			$append_pluggin = PHP_EOL."\$autoLoadConfig[210][] = array('autoType'=>'init_script',
                                 'loadFile'=> 'init_moceansms.php');".PHP_EOL;
			file_put_contents($autoloader_file, $append_pluggin, FILE_APPEND);
		}
		// Check connection
		if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
		}

		// sql to create table
		$sql = "CREATE TABLE `".DB_PREFIX."mocean_config` (
		  `key` varchar(255) NOT NULL,
		  `value` text NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
		
		$conn->query($sql);
		
		$conn->close();
		zen_register_admin_page('moceansms', 'BOX_MOCEANSMS', 'FILENAME_MOCEANSMS','' , 'tools', 'Y', 20);
	
	}    
}
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
$mocean_key_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_key' LIMIT 1");
$mocean_key = $mocean_key_request->fields['value'];

$mocean_secret_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_secret' LIMIT 1");
$mocean_secret = $mocean_secret_request->fields['value'];
include __DIR__.DIRECTORY_SEPARATOR.'moceansms'.DIRECTORY_SEPARATOR.'order_status.php';

