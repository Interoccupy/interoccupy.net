<?php
global $knewsOptions, $Knews_plugin;

if ($Knews_plugin) {


	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$warn=$Knews_plugin->get_safe('w');
	
	if ($warn == 'no_warn_cron_knews' || $warn == 'no_warn_ml_knews' || $warn == 'config_knews' || $warn == 'update_knews' || $warn == 'update_pro' || $warn == 'videotutorial') {
		$knewsOptions[$warn]='yes';
		update_option($Knews_plugin->adminOptionsName, $knewsOptions);
	}

	wp_redirect( $Knews_plugin->get_safe('b'));
	exit;
}
?>