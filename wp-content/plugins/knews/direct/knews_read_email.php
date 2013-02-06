<?php 
//This method is maintained for old Knews submitted newsletters (1.0.x and 1.1.x Knews versions)
if (!function_exists('add_action')) {
	$path='./';
	for ($x=1; $x<6; $x++) {
		$path .= '../';
		if (@file_exists($path . 'wp-config.php')) {
		    require_once($path . "wp-config.php");
			break;
		}
	}
}
global $Knews_plugin, $wpdb;

if ($Knews_plugin) {

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$id_newsletter = $Knews_plugin->get_safe('id', 0, 'int');
	$email = $Knews_plugin->get_safe('e');
	$user_id=0;
	
	if ($email != '') {
		$user=$wpdb->get_row("SELECT id, email, confkey FROM " . KNEWS_USERS . " WHERE email='" . $email . "'");

		if (count($user)==1) {
			$user_id=$user->id;
			$mysqldate = $Knews_plugin->get_mysql_date();
			
			$query = "INSERT INTO " . KNEWS_STATS . " (what, user_id, submit_id, date) VALUES (2, " . $user_id . ", " . $id_newsletter . ", '" . $mysqldate . "')";
			$result=$wpdb->query( $query );

		}
	} else {
		$user=array();
	}
	
	require( KNEWS_DIR . "/includes/knews_compose_email.php");

	if (count($user)==1) {
		$aux_array=array();
		//array('token'=>$token->token, 'id'=>$token->id, 'default'=>$tokenfound[1])

		foreach ($used_tokens as $token) {
			$theHtml = str_replace($token['token'], $Knews_plugin->get_user_field($user->id, $token['id']), $theHtml);
			//$aux_array[] = array( 'token' => $token['token'], 'value' => $Knews_plugin->get_user_field($user->id, $token['id'], $token['defaultval']) );
		}
		$theHtml = str_replace('%unsubscribe_href%', get_admin_url() . 'admin-ajax.php?action=knewsUnsubscribe&e=' . urlencode($user->email) . '&k=' . $user->confkey, $theHtml);

		$theHtml = str_replace('%mobile_version_href%', get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $id_newsletter . '&e=' . urlencode($user->email) . '&m=' . (($results[0]->mobile==0) ? 'mbl' : 'dsk'), $theHtml);

	} else {
		foreach ($used_tokens as $token) {
			$theHtml = str_replace($token['token'], $token['defaultval'], $theHtml);
		}

		$theHtml = str_replace('%unsubscribe_href%', '#', $theHtml);

		$theHtml = str_replace('%mobile_version_href%', get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $id_newsletter . '&m=' . (($results[0]->mobile==0) ? 'mbl' : 'dsk'), $theHtml);
	}
	$theHtml = str_replace('%cant_read_href%', '#' , $theHtml);

	if ($Knews_plugin->get_safe('preview',0)!=1) $theHtml = extract_code('<!--cant_read_block_start-->','<!--cant_read_block_end-->',$theHtml,true);
?>
<?php echo $theHtml; ?>
<?php
} else {
	echo 'Knews is not active';
}
die();
?>