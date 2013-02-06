<?php

/*
Plugin Name: jQuery UI Widgets
Plugin URI: http://interoccupy.net
Description: WordPress Plugin to make it super easy to add-in jQuery UI widgets (tabs, accordion and datepicker) to your posts/pages via a shortcode. Based on the code from David Gwyer at http://www.presscoders.com/
Version: 0.1
Author: Pea
Author URI: http://www.misfist.com
*/

// Note: jquid_ prefix is derived from [jq]uery [ui] [d]emo
define("JQUID_PLUGIN_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)));

add_action( 'init', 'jquid_init' );

// Register the hub scripts

function jquid_init(){
	if(!is_admin()){ // Only load these scripts on non-admin pages
		// Register scripts
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'google.hub.jquery-ui.min', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.6/jquery-ui.min.js', array('jquery') );
 
		
		//Deregister WP Views bad style sheets
		//wp_deregister_style( 'date-picker-style' );
		
		// Register style sheets
		wp_register_style( 'jquid_jquery_hub_stylesheet', 'http://code.jquery.com/ui/1.10.0/themes/base/jquery-ui.css' );
		wp_register_style( 'jquid_jquery_hub_custom_stylesheet', '/wp-content/jquery-ui/ui-interocc-theme/jquery-ui-1.10.0.custom.min.css', array('jquid_jquery_hub_stylesheet') );
 
		// Enqueue them
		wp_enqueue_script( 'google.hub.jquery-ui.min' );
		wp_enqueue_style( 'jquid_jquery_hub_stylesheet' );
		wp_enqueue_style( 'jquid_jquery_hub_custom_stylesheet' );
	}
}

add_shortcode('jQuery-UI-tabs', 'jquid_tabs_shortcode');
add_shortcode('jQuery-UI-accordion', 'jquid_accordion_shortcode');
add_shortcode('jQuery-UI-datepicker', 'jquid_datepicker_shortcode');


function jquid_tabs_shortcode(){
	echo '
	<script>
	jQuery(document).ready(function($) {
		$( "#tabs" ).tabs();
	});
	</script>
	';
}
 
function jquid_accordion_shortcode(){
	echo '
	<script>
	jQuery(document).ready(function($) {
		$( "#accordion" ).accordion({ animated: \'bounceslide\' });
	});
	</script>
	';
}
 
function jquid_datepicker_shortcode(){
	echo '
	<script>
	jQuery(document).ready(function($) {
		$( "#datepicker" ).datepicker();
	});
	</script>
	';
}

?>