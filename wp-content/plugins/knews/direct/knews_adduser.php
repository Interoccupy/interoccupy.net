<?php
global $Knews_plugin;

if ($Knews_plugin) {

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	function different_locale_for_ajax( $locale ) {
		global $Knews_plugin;
		return $Knews_plugin->post_safe('lang_locale_user');
	}
	
	if (KNEWS_MULTILANGUAGE) add_filter('locale', 'different_locale_for_ajax'); 
	
	$Knews_plugin->add_user_self();
}
die();
?>