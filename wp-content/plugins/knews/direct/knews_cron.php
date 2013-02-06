<?php
//This file is maintained for old CRON configurations (1.0.x and 1.1.x Knews versions)
//New Knews submit system are called trough www.yourdomain.com/wp-admin/admin-ajax.php?action=knewsCron
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
global $Knews_plugin;

if ($Knews_plugin) {
	
	//global $knewsOptions;

	if ( get_current_blog_id() != $Knews_plugin->KNEWS_MAIN_BLOG_ID ) die("You must call the main blog www.yourdomain.com/wp-admin/admin-ajax.php?action=knewsCron URL");

	$cron_time = time();
	update_option('knews_cron_time', $cron_time);

	//if (! $Knews_plugin->initialized) $Knews_plugin->init();
	
	require ('knews_cron_do.php');
}
die();
?>