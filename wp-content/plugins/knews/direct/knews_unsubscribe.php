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
global $Knews_plugin, $knewsOptions;

if ($Knews_plugin) {

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$url_home = '';
	
	if (KNEWS_MULTILANGUAGE) {
		if ($knewsOptions['multilanguage_knews']=='wpml') {
			global $sitepress;

			if (method_exists($sitepress, 'language_url')) {
				$user_lang = $Knews_plugin->get_user_lang($Knews_plugin->get_safe('e'));
				$url_home = $sitepress->language_url($user_lang);
			}
		}
		if ($knewsOptions['multilanguage_knews']=='qt') {

			if (function_exists('qtrans_convertURL')) {
				$user_lang = $Knews_plugin->get_user_lang($Knews_plugin->get_safe('e'));
				$url_home = qtrans_convertURL(get_bloginfo('url'), $user_lang);
			}
		}
	}

	if ($url_home == '') $url_home = get_bloginfo('url');
	
	if (strpos($url_home, '?')===false) {
		$url_home .= '?unsubscribe=';
	} else {
		$url_home .= '&unsubscribe=';		
	}

	if ($Knews_plugin-> block_user_self()) {
		$url_home .= 'ok';
	} else {
		$url_home .= 'error';
	}

	wp_redirect( $url_home );
	exit;

} else {

	wp_redirect( get_bloginfo('url'));
	exit;
}
?>