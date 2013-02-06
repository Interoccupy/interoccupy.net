<?php
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
		$url_home .= '?subscription=';
	} else {
		$url_home .= '&subscription=';		
	}

	if ($Knews_plugin-> confirm_user_self()) {
		$url_home .= 'ok';
	} else {
		$url_home .= 'error';
	}

	wp_redirect( $url_home );

} else {

	wp_redirect( get_bloginfo('url'));
}
die();
?>