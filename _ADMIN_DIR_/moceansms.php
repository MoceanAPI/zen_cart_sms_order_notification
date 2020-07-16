<?php
/**
 * @package admin
 * @copyright Copyright 2003-2018 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Zen4All Fri Nov 16 10:31:29 2018 +0100 Modified in v1.5.6 $
 */
	$version_check_index=true;
	require('includes/application_top.php');

	$languages = zen_get_languages();
	$languages_array = array();
	$languages_selected = DEFAULT_LANGUAGE;
	for ($i = 0, $n = sizeof($languages); $i < $n; $i++) {
		$languages_array[] = array('id' => $languages[$i]['code'], 'text' => $languages[$i]['name']);
		if ($languages[$i]['directory'] == $_SESSION['language']) {
			$languages_selected = $languages[$i]['code'];
		}
	}

if($_GET['action'] == 'settings' && isset($_POST['submit_settings']) && $_POST['submit_settings'] == '1') {
	$input_mocean_key = $_POST['key'];
	$input_mocean_secret = $_POST['secret'];
	$input_message_from = $_POST['message_from'];
	if(!empty($input_mocean_key) && !empty($input_mocean_secret) && !empty($input_message_from)) {
		$mocean_key_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_key' LIMIT 1");
		$mocean_secret_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_secret' LIMIT 1");
		$message_from_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='message_from' LIMIT 1");
		
		if($mocean_key_request->RecordCount() > 0) {
			$sql_update_key = "UPDATE  ".DB_PREFIX."mocean_config SET  value = :key: WHERE `key` = 'config_key'";
			$sql_update_key = $db->bindVars($sql_update_key, ':key:', $input_mocean_key, 'string');
			$db->Execute($sql_update_key);
		} else {
			$sql_insert_key = "INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('config_key', :key:)";
			$sql_insert_key = $db->bindVars($sql_insert_key, ':key:', $input_mocean_key, 'string');
			$db->Execute($sql_insert_key);
		}
		
		if($mocean_secret_request->RecordCount() > 0) {
			$sql_update_secret = "UPDATE  ".DB_PREFIX."mocean_config SET  value = :secret: WHERE `key` = 'config_secret'";
			$sql_update_secret = $db->bindVars($sql_update_key, ':secret:', $input_mocean_secret, 'string');
			$db->Execute($sql_update_secret);
		} else {
			$sql_insert_secret ="INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('config_secret', :secret:)";
			$sql_insert_secret = $db->bindVars($sql_insert_secret, ':secret:', $input_mocean_secret, 'string');
			$db->Execute($sql_insert_secret);
		}
		
		if($message_from_request->RecordCount() > 0) {
			$sql_update_message_from = "UPDATE  ".DB_PREFIX."mocean_config SET  value = :message_from: WHERE `key` = 'message_from'";
			$sql_update_message_from = $db->bindVars($sql_update_message_from, ':message_from:', $input_message_from, 'string');
			$db->Execute($sql_update_message_from);	
		} else {
			$sql_insert_message_from ="INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('message_from', :message_from:)";
			$sql_insert_message_from = $db->bindVars($sql_insert_message_from, ':message_from:', $input_message_from, 'string');
			$db->Execute($sql_insert_message_from);
		}
		$update_result_settings['successfully_message'] = MOCEANSMS_SUCCESSFULLY_CHANGES;
	} else {
		$update_result_settings['error_message'] = MOCEANSMS_INVALID_INPUTS;
	}
}

if($_GET['action'] == 'admin_settings' && isset($_POST['admin_settings']) && $_POST['admin_settings'] == '1') {
	$admin_notify_checkbox = $_POST['admin_notify'];
	if($admin_notify_checkbox == '1') {
		$admin_notify_checkbox = 'true';
	} else {
		$admin_notify_checkbox = 'false';
	}
	$admin_phone_input =  $_POST['admin_phone'];
	
	$admin_notify_input_message =  $_POST['admin_notify_message'];
if(!empty($admin_notify_checkbox) && !empty($admin_notify_checkbox)) {
	
		$admin_notify = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify' LIMIT 1");
		$admin_phone = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_phone' LIMIT 1");
		$admin_notify_message = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify_message' LIMIT 1");
		
		if($admin_notify->RecordCount() > 0 && $admin_notify_message->RecordCount() > 0 && $admin_phone->RecordCount() > 0) {
	
			$admin_notify_update = "UPDATE  ".DB_PREFIX."mocean_config SET  value = '$admin_notify_checkbox' WHERE `key` = 'admin_notify'";
			$db->Execute($admin_notify_update);
			
			$admin_phone_update = "UPDATE  ".DB_PREFIX."mocean_config SET  value = :admin_phone: WHERE `key` = 'admin_phone'";
			$admin_phone_update = $db->bindVars($admin_phone_update, ':admin_phone:', $admin_phone_input, 'string');

			$db->Execute($admin_phone_update);
			
			$admin_notify_message_update = "UPDATE  ".DB_PREFIX."mocean_config SET  value = :message: WHERE `key` = 'admin_notify_message'";
			$admin_notify_message_update = $db->bindVars($admin_notify_message_update, ':message:', $admin_notify_input_message, 'string');
			$db->Execute($admin_notify_message_update);
			
		} else {
			$admin_notify_insert = "INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('admin_notify', '$admin_notify_checkbox')";
			$db->Execute($admin_notify_insert);
			
			$admin_phone_insert ="INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('admin_phone', :admin_phone:)";
			$admin_phone_insert = $db->bindVars($admin_phone_insert, ':admin_phone:', $admin_phone_input, 'string');
			$db->Execute($admin_phone_insert);
			
			$admin_notify_message_insert ="INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES ('admin_notify_message', :message:)";
			$admin_notify_message_insert = $db->bindVars($admin_notify_message_insert, ':message:', $admin_notify_input_message, 'string');
			$db->Execute($admin_notify_message_insert);
		}
		$update_admin_settings_result['successfully_message'] = MOCEANSMS_SUCCESSFULLY_CHANGES;
	} else {
		$update_admin_settings_result['error_message'] = MOCEANSMS_INVALID_INPUTS;
	}

	
}


if($_GET['action'] == 'customer_settings' && isset($_POST['customer_settings']) && $_POST['customer_settings'] == '1') {
	unset($_POST['securityToken']);
	unset($_POST['customer_settings']);
	$orders_status_query_raw = "select orders_status_id, orders_status_name from " . TABLE_ORDERS_STATUS . " where language_id = " . (int)$_SESSION['languages_id'];

	$orders_status = $db->Execute($orders_status_query_raw);

	foreach ($orders_status as $status) {
		if(empty($_POST['customer_status_notify_'.$status['orders_status_name']])) {
			$value = "false";
		}
		$update_sql = "UPDATE ".DB_PREFIX."mocean_config SET value=:value: WHERE  `key`=:key:";
		$update_sql = $db->bindVars($update_sql, ':key:', 'customer_status_notify_'.$status['orders_status_name'], 'string');
		$update_sql = $db->bindVars($update_sql, ':value:', $value, 'string');
		$db->Execute($update_sql);
	}
	foreach($_POST as $key=>$value) {
		if($value == '1') {
			$value = 'true';
		}
		
		$check_notify = "SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`=:key: LIMIT 1";
		$check_notify = $db->bindVars($check_notify, ':key:', $key, 'string');
		$check_values = $db->Execute($check_notify);
		if($check_values->RecordCount() > 0) {
			$update_sql = "UPDATE ".DB_PREFIX."mocean_config SET value=:value: WHERE  `key`=:key:";
			$update_sql = $db->bindVars($update_sql, ':key:', $key, 'string');
			$update_sql = $db->bindVars($update_sql, ':value:', $value, 'string');
			$db->Execute($update_sql);
		} else {
			$update_sql = "INSERT INTO ".DB_PREFIX."mocean_config (`key`,`value`) VALUES (:key:, :value:)";
			$update_sql = $db->bindVars($update_sql, ':key:', $key, 'string');
			$update_sql = $db->bindVars($update_sql, ':value:', $value, 'string');
			$db->Execute($update_sql);
		}
	}
	$update_customer_settings_result['successfully_message'] = MOCEANSMS_SUCCESSFULLY_CHANGES;
}

//Settings
$mocean_key_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_key' LIMIT 1");
$mocean_key = $mocean_key_request->fields['value'];

$mocean_secret_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='config_secret' LIMIT 1");
$mocean_secret = $mocean_secret_request->fields['value'];

$mocean_message_from_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='message_from' LIMIT 1");
$mocean_message_from = $mocean_message_from_request->fields['value'];

//Admin Settings
$admin_notify_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify' LIMIT 1");
$admin_notify = $admin_notify_request->fields['value'];

$admin_phone_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_phone' LIMIT 1");
$admin_phone = $admin_phone_request->fields['value'];

$admin_message_request = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='admin_notify_message' LIMIT 1");
$admin_message = $admin_message_request->fields['value'];

//Customer Settings
$orders_status_query_raw = "select orders_status_id, orders_status_name
					from " . TABLE_ORDERS_STATUS . "
					where language_id = " . (int)$_SESSION['languages_id'];
$orders_status = $db->Execute($orders_status_query_raw);
?>
<!doctype html>
<html <?php echo HTML_PARAMS; ?>>
  <head>
    <meta charset="<?php echo CHARSET; ?>">
    <title><?php echo TITLE; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <link href="includes/stylesheet.css" rel="stylesheet">
    <link rel="stylesheet" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
    <script src="includes/menu.js"></script>
    <!--Load the AJAX API FOR GOOGLE GRAPHS -->
    <script src="https://www.google.com/jsapi"></script>

  </head>
  <body class="indexDashboard">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <?php require_once(DIR_WS_MODULES . 'notificationsDisplay.php'); ?>

    <div id="colone" class="col-md-12">
		<ul class="nav nav-tabs">
		  <li <?php if(empty($_GET['action']) || $_GET['action'] == 'settings'){ ?> class="active" <?php }?> ><a data-toggle="tab" href="#settings"><?=BOX_TITLE_MOCEANSMS_CONFIGURATION?></a></li>
		  <li <?php if($_GET['action'] == 'admin_settings'){ ?> class="active" <?php }?> ><a data-toggle="tab" href="#admin_settings"><?=MOCEANSMS_ADMIN_SETTINGS?></a></li>
		  <li <?php if($_GET['action'] == 'customer_settings'){ ?> class="active" <?php }?> ><a data-toggle="tab" href="#customer_settings"><?=MOCEANSMS_CUSTOMER_SETTINGS?></a></li>
		</ul>

	<div class="tab-content">
	
	  <div id="settings" class="tab-pane fade <?php if(empty($_GET['action']) || $_GET['action'] == 'settings'){ ?> in active <?php }?>">
		<h3><?=BOX_TITLE_MOCEANSMS_CONFIGURATION?></h3>
		<?php if(isset($update_result_settings['error_message'])) { ?>
			<div class="alert alert-danger" role="alert">
			<?php echo $update_result_settings['error_message']; ?>
			</div>
		<?php } ?>
		<?php if(isset($update_result_settings['successfully_message'])) { ?>
			<div class="alert alert-success" role="alert">
			<?php echo $update_result_settings['successfully_message']; ?>
			</div>
		<?php } ?>
		<?php  echo zen_draw_form('status', FILENAME_MOCEANSMS, 'action=settings', 'POST', '', true); ?>
		  <div class="form-group">
			<label class="col-md-12" for="pwd"><?=MOCEANSMS_MESSAGE_FROM?></label>
			<input value="<?=$mocean_message_from?>" type="text" name="message_from" class="form-control">
			<br/>
		  	<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#special_tags"><?=MOCEANSMS_SPECIAL_TAGS?></button>
		  </div>
		  <div class="form-group">
			<label class="col-md-12" for="key"><?=MOCEANSMS_KEY?></label>
			<input value="<?=$mocean_key?>" type="text" class="form-control" name="key" id="email">
		  </div>
		  <div class="form-group">
			<label class="col-md-12" for="pwd"><?=MOCEANSMS_SECRET?></label>
			<input value="<?=$mocean_secret?>" type="password" name="secret" class="form-control">
		  </div>
		  <input type="hidden" value="1" name="submit_settings">
		  <input class="btn btn-default" type="submit" value="<?=MOCEANSMS_SAVE_CHANGES?>">
		</form>
	  </div>
	  
	  
	  <div id="admin_settings" class="tab-pane fade <?php if($_GET['action'] == 'admin_settings'){ ?> in active <?php }?>">
		<h3><?=MOCEANSMS_ADMIN_SETTINGS?></h3>
		<?php if(isset($update_admin_settings_result['error_message'])) { ?>
			<div class="alert alert-danger" role="alert">
			<?php echo $update_admin_settings_result['error_message']; ?>
			</div>
		<?php } ?>
		<?php if(isset($update_admin_settings_result['successfully_message'])) { ?>
			<div class="alert alert-success" role="alert">
			<?php echo $update_admin_settings_result['successfully_message']; ?>
			</div>
		<?php } ?>
		<?php  echo zen_draw_form('status', FILENAME_MOCEANSMS, 'action=admin_settings', 'POST', '', true); ?>
		<div class="form-group row">
			<div class="col-sm-2"><?=MOCEANSMS_ENABLE_ADMIN_NOTIFY?></div>
			<div class="col-sm-10">
			  <div class="form-check">
				<input value="1" name="admin_notify" class="form-check-input" <?php echo ($admin_notify == 'true' ? 'checked':'') ?> type="checkbox" id="gridCheck1">
				<label  class="col-md-12" for="gridCheck1">
				<?=MOCEANSMS_ENABLE_ADMIN_NOTIFY_DETAILS?>
				</label>
			  </div>
			</div>
		</div>
		 <div class="form-group">
			<label class="col-md-12" ><?=MOCEANSMS_ADMIN_PHONE?></label>
			<input name="admin_phone" class="form-control"  value="<?=$admin_phone?>">
		  </div>
		  
		 <div class="form-group">
			<label class="col-md-12" ><?=MOCEANSMS_ADMIN_SMS_MESSAGE?></label>
			<textarea name="admin_notify_message" class="form-control"  rows="3"><?=$admin_message?></textarea><br/>
			<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#special_tags"><?=MOCEANSMS_SPECIAL_TAGS?></button>
		  </div>

		  <input type="hidden" value="1" name="admin_settings">
		  <input class="btn btn-default" type="submit" value="<?=MOCEANSMS_SAVE_CHANGES?>">
		</form>
	  </div>

	  <div id="customer_settings" class="tab-pane <?php if($_GET['action'] == 'customer_settings'){ ?> in active <?php }?> fade">

		<h3><?=MOCEANSMS_CUSTOMER_SETTINGS?></h3>
		<?php if(isset($update_customer_settings_result['successfully_message'])) { ?>
			<div class="alert alert-success" role="alert">
			<?php echo $update_customer_settings_result['successfully_message']; ?>
			</div>
		<?php } ?>
		<?php  echo zen_draw_form('status', FILENAME_MOCEANSMS, 'action=customer_settings', 'POST', '', true); ?>
		<?php
			foreach ($orders_status as $status) {
			$sql_query = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_status_notify_".lcfirst($status['orders_status_name'])."' LIMIT 1");
			$result = $sql_query->fields['value'];
		?>
			<div class="form-group row">
				<div class="col-sm-10">
				  <div class="form-check">
					<input value="1" <?php echo ($result == 'true' ? 'checked':'') ?> name="customer_status_notify_<?=lcfirst($status['orders_status_name']);?>" class="form-check-input" type="checkbox" id="gridCheck1">
					<label class="form-check-label" for="">
					<?=$status['orders_status_name']?>
					</label>
				  </div>
				</div>
			</div>
			<?php
				}
			?>
			<br/>
		 <div class="form-group">
			<label class="col-md-12" ><?=MOCEANSMS_CUSTOMER_DEFAULT_SMS_MESSAGE?></label>
			<?php
				$sql_query = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_default_notify_message' LIMIT 1");
				$result = $sql_query->fields['value'];
			?>
			<textarea name="customer_default_notify_message" class="form-control"  rows="3"><?=$result?></textarea><br/>
			<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#special_tags"><?=MOCEANSMS_SPECIAL_TAGS?></button>
		  </div>
			<?php
				foreach ($orders_status as $status) {
				$sql_query = $db->Execute("SELECT * FROM ".DB_PREFIX."mocean_config WHERE `key`='customer_".lcfirst($status['orders_status_name'])."_notify_message' LIMIT 1");
				$result = $sql_query->fields['value'];
			 ?>
		 <div class="form-group">
			<label class="col-md-12" >Status: <?=lcfirst($status['orders_status_name']);?></label>
			<textarea name="customer_<?=lcfirst($status['orders_status_name'])?>_notify_message" class="form-control"  rows="3"><?=$result?></textarea><br/>
			<button type="button" class="btn btn-info btn-xs" data-toggle="modal" data-target="#special_tags"><?=MOCEANSMS_SPECIAL_TAGS?></button>
		  </div>
			<?php
				}
			?>
		  
		  <input type="hidden" value="1" name="customer_settings">
		  <input class="btn btn-default" type="submit" value="<?=MOCEANSMS_SAVE_CHANGES?>">
		</form>
	  </div>
</div>
	<!-- Modal Special Chars -->
	<div id="special_tags" class="modal fade" role="dialog">
	  <div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<h4 class="modal-title"><?=MOCEANSMS_SPECIAL_TAGS?></h4>
		  </div>
		  <div class="modal-body">
<h1>Shop</h1>
<pre>
[shop_name]
[shop_email]
[shop_url]
</pre>
<h1>Orders</h1>
<pre>
[order_id]
[order_amount]
[order_status]
[order_product]
</pre>
<h1>Biling</h1>
<pre>
[payment_method]
[billing_first_name]
[billing_last_name]
[billing_phone]
[billing_email]
[billing_company]
[billing_address]
[billing_country]
[billing_city]
[billing_state]
[billing_postcode]
</pre>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		  </div>
		</div>

	  </div>
	</div>
    </div>
<footer class="homeFooter">
<!-- The following copyright announcement is in compliance
to section 2c of the GNU General Public License, and
thus can not be removed, or can only be modified
appropriately.

Please leave this comment intact together with the
following copyright announcement. //-->

<div class="copyrightrow"><a href="https://www.zen-cart.com" target="_blank"><img src="images/small_zen_logo.gif" alt="Zen Cart:: the art of e-commerce" border="0" /></a><br /><br />E-Commerce Engine Copyright &copy; 2003-<?php echo date('Y'); ?> <a href="https://www.zen-cart.com" target="_blank">Zen Cart&reg;</a></div><div class="warrantyrow"><br /><br />Zen Cart is derived from: Copyright &copy; 2003 osCommerce<br />This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;<br />without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE<br />and is redistributable under the <a href="https://www.zen-cart.com/license/2_0.txt" target="_blank">GNU General Public License</a><br />
</div>
</footer>
</body>
</html>
<?php require('includes/application_bottom.php');
