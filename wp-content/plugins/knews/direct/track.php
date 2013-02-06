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

	$mysqldate = $Knews_plugin->get_mysql_date();
	
	$key_user = substr($Knews_plugin->get_safe('t'), 0, 8);
	$key_submit = substr($Knews_plugin->get_safe('t'), -16);
	
	//echo $key_user . ' ** ' . $key_submit;

	$query = "SELECT * FROM " . KNEWS_KEYS . " WHERE keyy='" . $key_submit . "'";
	$key_track = $wpdb->get_results( $query );

	if (count($key_track) == 1) {

		$user_id = 0;
		$query = "SELECT * FROM " . KNEWS_USERS . " WHERE confkey='" . $key_user . "'";
		$user = $wpdb->get_results( $query );
		
		if (count($user) ==1) $user_id=$user[0]->id;
		
		if ($key_track[0]->type==1) {
			$query = "INSERT INTO " . KNEWS_STATS . " (what, user_id, submit_id, date) VALUES (1, " . $user_id . ", " . $key_track[0]->submit_id . ", '" . $mysqldate . "')";
			$result=$wpdb->query( $query );
		}
		
		wp_redirect( $key_track[0]->href ); exit;
		
	} else {
		echo '<p>Not found</p>';
	}
} else {
	echo '<p>Knews is not active</p>';
}
die();
?>