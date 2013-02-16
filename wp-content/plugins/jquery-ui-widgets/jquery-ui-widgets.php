<?php
/*
Plugin Name: jQuery UI Widgets
Plugin URI: http://www.presscoders.com/jquery-ui-widgets
Description: Simple, clean, and flexible way to add jQuery UI widgets to your site pages.
Version: 0.22
Author: David Gwyer
Author URI: http://www.presscoders.com
License: GPLv2
*/

/*  Copyright 2009 David Gwyer (email : d.v.gwyer@presscoders.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// @todo 0.3:
//
// - The Size and Puff effects do not seem to be included with WP 3.5. Need to check for these in the next release.
// - Add text box to exclude for certain pages ID's.
// - Have options to enqueue scripts on front end and back end? Maybe in a future version when admin jQuery styles have been added to core.
// - Have a 'Select All' and 'Select None' buttons for each checkbox group to select/deselect all checkboxes.
// - Remove all inline styles.
// - Don't show the custom theme textbox by default. Have radio buttons to selectively show theme elements by default.

// Note: jquiw_ prefix is derived from [jq]uery [ui] [d]emo
define( "JQUIW_PLUGIN_URL", WP_PLUGIN_URL.'/'.plugin_basename(dirname(__FILE__)) );

// --------------------
// --  PLUGIN HOOKS  --
// --------------------

add_action( 'wp_head', 'jquiw_initialize_scripts' );
add_action( 'wp_enqueue_scripts', 'jquiw_enqueue_scripts' );
register_activation_hook( __FILE__, 'jquiw_add_defaults' );
register_uninstall_hook( __FILE__, 'jquiw_delete_plugin_options' );
add_action( 'admin_init', 'jquiw_init' );
add_action( 'admin_menu', 'jquiw_add_options_page' );
add_filter( 'plugin_action_links', 'jquiw_plugin_action_links', 10, 2 );
add_action( 'activated_plugin', 'jquiw_save_error' );

// --------------------------------------
// --  DEFINE DEFAULT OPTION SETTINGS  --
// --------------------------------------

function jquiw_add_defaults() {

	$tmp = get_option('jquiw_options');

	if( ( (isset($tmp['chk_default_options_db']) && $tmp['chk_default_options_db']=='1')) || (!is_array($tmp)) ) {
		delete_option('jquiw_options');
		$arr = array(	"txt_custom_theme_path" => "",
						"txtar_override_css" => ".ui-widget {\r\nfont-family: inherit;\r\nfont-size: inherit;\r\n}",
						"drp_jquery_theme" => "base",
						"chk_inc_accordion" => "1",
						"chk_inc_autocomplete" => "1",
						"chk_inc_button" => "1",
						"chk_inc_uicore" => "1",
						"chk_inc_datepicker" => "1",
						"chk_inc_dialog" => "1",
						"chk_inc_draggable" => "1",
						"chk_inc_droppable" => "1",
						"chk_inc_menu" => "1",
						"chk_inc_mouse" => "1",
						"chk_inc_position" => "1",
						"chk_inc_progressbar" => "1",
						"chk_inc_resizable" => "1",
						"chk_inc_selectable" => "1",
						"chk_inc_slider" => "1",
						"chk_inc_spinner" => "1",
						"chk_inc_sortable" => "1",
						"chk_inc_tabs" => "1",
						"chk_inc_tooltip" => "1",
						"chk_inc_widget" => "1",
						"chk_inc_blind" => "1",
						"chk_inc_bounce" => "1",
						"chk_inc_clip" => "1",
						"chk_inc_coreeffects" => "1",
						"chk_inc_drop" => "1",
						"chk_inc_explode" => "1",
						"chk_inc_fade" => "1",
						"chk_inc_fold" => "1",
						"chk_inc_highlight" => "1",
						"chk_inc_pulsate" => "1",
						"chk_inc_scale" => "1",
						"chk_inc_shake" => "1",
						"chk_inc_slide" => "1",
						"chk_inc_transfer" => "1",
						"txtar_jquery_code" => "",
						"txt_custom_theme" => "",
						"chk_default_options_db" => ""
		);
		update_option('jquiw_options', $arr);
	}
}

// ------------------------------------------------
// --  ADD INITIALIZATION SCRIPTS TO THE HEADER  --
// ------------------------------------------------

function jquiw_initialize_scripts() {

	$options = get_option('jquiw_options');

	/* If jQuery code text box not empty then add to header. */
	if( !empty( $options['txtar_jquery_code'] ) ) {
		echo "<script type=\"text/javascript\">\r\n";
		echo $options['txtar_jquery_code']."\r\n";
		echo "</script>\r\n";
	}

	/* If custom CSS text box not empty then add to header. */
	if( !empty( $options['txtar_override_css'] ) ) {
		echo "<style type=\"text/css\">\r\n";
		echo $options['txtar_override_css'];
		echo "\r\n</style>\r\n";
	}
}

// ---------------------------------------------------------
// --  REGISTER THE BLOG SCRIPTS AND PLUGIN SETTINGS API  --
// ---------------------------------------------------------

function jquiw_init(){

	/* Make sure we always have a theme selected. */
	$tmp = get_option('jquiw_options');
	if( !isset($tmp['drp_jquery_theme']) ) {
		$tmp["drp_jquery_theme"] = 'base';
		update_option('jquiw_options', $tmp);
	}
	register_setting( 'jquiw_plugin_options', 'jquiw_options' );
}

// --------------------------------------------------
// --  ENQUEUE SCRIPTS ON FRONT FACING PAGES ONLY  --
// --------------------------------------------------

function jquiw_enqueue_scripts() {

	$options = get_option('jquiw_options');
	$jquery_theme = empty( $options['drp_jquery_theme'] ) ? 'base' : $options['drp_jquery_theme'];
	$jquery_css_base = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/themes/'.$jquery_theme.'/jquery-ui.css';

	/* Register jQuery scripts if selected in Plugin options. */
	if( isset($options['chk_inc_accordion']) && $options['chk_inc_accordion'] ) wp_enqueue_script( 'jquery-ui-accordion' );
	if( isset($options['chk_inc_autocomplete']) && $options['chk_inc_autocomplete'] ) wp_enqueue_script( 'jquery-ui-autocomplete' );
	if( isset($options['chk_inc_button']) && $options['chk_inc_button'] ) wp_enqueue_script( 'jquery-ui-button' );
	if( isset($options['chk_inc_uicore']) && $options['chk_inc_uicore'] ) wp_enqueue_script( 'jquery-ui-core' );
	if( isset($options['chk_inc_datepicker']) && $options['chk_inc_datepicker'] ) wp_enqueue_script( 'jquery-ui-datepicker' );
	if( isset($options['chk_inc_dialog']) && $options['chk_inc_dialog'] ) wp_enqueue_script( 'jquery-ui-dialog' );
	if( isset($options['chk_inc_draggable']) && $options['chk_inc_draggable'] ) wp_enqueue_script( 'jquery-ui-draggable' );
	if( isset($options['chk_inc_droppable']) && $options['chk_inc_droppable'] ) wp_enqueue_script( 'jquery-ui-droppable' );
	if( isset($options['chk_inc_menu']) && $options['chk_inc_menu'] ) wp_enqueue_script( 'jquery-ui-menu' );
	if( isset($options['chk_inc_mouse']) && $options['chk_inc_mouse'] ) wp_enqueue_script( 'jquery-ui-mouse' );
	if( isset($options['chk_inc_position']) && $options['chk_inc_position'] ) wp_enqueue_script( 'jquery-ui-position' );
	if( isset($options['chk_inc_progressbar']) && $options['chk_inc_progressbar'] ) wp_enqueue_script( 'jquery-ui-progressbar' );
	if( isset($options['chk_inc_resizable']) && $options['chk_inc_resizable'] ) wp_enqueue_script( 'jquery-ui-resizable' );
	if( isset($options['chk_inc_selectable']) && $options['chk_inc_selectable'] ) wp_enqueue_script( 'jquery-ui-selectable' );
	if( isset($options['chk_inc_slider']) && $options['chk_inc_slider'] ) wp_enqueue_script( 'jquery-ui-slider' );
	if( isset($options['chk_inc_spinner']) && $options['chk_inc_spinner'] ) wp_enqueue_script( 'jquery-ui-spinner' );
	if( isset($options['chk_inc_sortable']) && $options['chk_inc_sortable'] ) wp_enqueue_script( 'jquery-ui-sortable' );
	if( isset($options['chk_inc_tabs']) && $options['chk_inc_tabs'] ) wp_enqueue_script( 'jquery-ui-tabs' );
	if( isset($options['chk_inc_tooltip']) && $options['chk_inc_tooltip'] ) wp_enqueue_script( 'jquery-ui-tooltip' );
	if( isset($options['chk_inc_widget']) && $options['chk_inc_widget'] ) wp_enqueue_script( 'jquery-ui-widget' );
	if( isset($options['chk_inc_blind']) && $options['chk_inc_blind'] ) wp_enqueue_script( 'jquery-effects-blind' );
	if( isset($options['chk_inc_bounce']) && $options['chk_inc_bounce'] ) wp_enqueue_script( 'jquery-effects-bounce' );
	if( isset($options['chk_inc_clip']) && $options['chk_inc_clip'] ) wp_enqueue_script( 'jquery-effects-clip' );
	if( isset($options['chk_inc_coreeffects']) && $options['chk_inc_coreeffects'] ) wp_enqueue_script( 'jquery-effects-core' );
	if( isset($options['chk_inc_drop']) && $options['chk_inc_drop'] ) wp_enqueue_script( 'jquery-effects-drop' );
	if( isset($options['chk_inc_explode']) && $options['chk_inc_explode'] ) wp_enqueue_script( 'jquery-effects-explode' );
	if( isset($options['chk_inc_fade']) && $options['chk_inc_fade'] ) wp_enqueue_script( 'jquery-effects-fade' );
	if( isset($options['chk_inc_fold']) && $options['chk_inc_fold'] ) wp_enqueue_script( 'jquery-effects-fold' );
	if( isset($options['chk_inc_highlight']) && $options['chk_inc_highlight'] ) wp_enqueue_script( 'jquery-effects-highlight' );
	if( isset($options['chk_inc_pulsate']) && $options['chk_inc_pulsate'] ) wp_enqueue_script( 'jquery-effects-pulsate' );
	if( isset($options['chk_inc_scale']) && $options['chk_inc_scale'] ) wp_enqueue_script( 'jquery-effects-scale' );
	if( isset($options['chk_inc_shake']) && $options['chk_inc_shake'] ) wp_enqueue_script( 'jquery-effects-shake' );
	if( isset($options['chk_inc_slide']) && $options['chk_inc_slide'] ) wp_enqueue_script( 'jquery-effects-slide' );
	if( isset($options['chk_inc_transfer']) && $options['chk_inc_transfer'] ) wp_enqueue_script( 'jquery-effects-transfer' );

	/* Register style sheet. */
	if( empty($options['txt_custom_theme']) ) {
		wp_enqueue_style ( 'jquery-ui-standard-css', $jquery_css_base );
	}
	else {
		/* Enqueue custom theme rolled styles. */
		$upload_dir = wp_upload_dir();
		$relative_path = trim($options['txt_custom_theme'], "/");
		$full_path_url = trailingslashit($upload_dir['baseurl']).$relative_path;
		$full_path_dir = trailingslashit($upload_dir['basedir']).$relative_path;
		if( file_exists($full_path_dir) ) {
			wp_enqueue_style ( 'jquery-ui-custom-css', $full_path_url );
		}
		else {
			wp_enqueue_style ( 'jquery-ui-standard-css', $jquery_css_base );
		}
	}
}

// ---------------------
// --  ADD MENU PAGE  --
// ---------------------

function jquiw_add_options_page() {

	global $jquiw_page;

	$jquiw_page = add_options_page('jQuery UI Widgets Options Page', 'jQuery UI Widgets', 'manage_options', __FILE__, 'jquiw_render_form');
}

// --------------------------------------
// --  SAVE ACTIVATION ERROR MESSAGES  --
// --------------------------------------

function jquiw_save_error(){
    update_option('pc_plugin_error', ob_get_contents());
}

// -----------------------------------------------------------------------------
// --  DELETE OPTIONS TABLE ENTRIES ONLY WHEN PLUGIN DEACTIVATED AND DELETED  --
// -----------------------------------------------------------------------------

function jquiw_delete_plugin_options() {
	delete_option('jquiw_options');
}

// --------------------------------------
// --  RENDER THE PLUGIN OPTIONS PAGE  --
// --------------------------------------

function jquiw_render_form() {
	?>
	<div class="wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>jQuery UI Widgets Options</h2>

		<form method="post" action="options.php">
			<?php settings_fields('jquiw_plugin_options'); ?>
			<?php $options = get_option('jquiw_options'); ?>
			<style type="text/css">
				.info_image {	position: relative;
								top: 3px;
								left: 2px;
								width: 14px;
								height: 14px;
				}
			</style>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Custom jQuery Code</th>
					<td colspan="3">
						<?php $res_jquery = jquiw_get_textarea_rows($options['txtar_jquery_code'], $min = 5, $max = 30); ?>
						<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_jquery_code]" rows="<?php echo $res_jquery['rows']; ?>" type='textarea'><?php if ( isset( $options['txtar_jquery_code'] ) ) echo $options['txtar_jquery_code']; ?></textarea><br />
						<p style="margin:5px 0 0 0;">Add custom jQuery code above, e.g. <code>$( "#accordion" ).accordion();</code> and it will be added automatically to your site. Don't forget to add your jQuery inside the proper wrapper such as <code>jQuery(document).ready(function($) { // Add jQuery code here...	});</code></p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Use These jQuery UI Scripts</th>
					<td width="200">
						<label><input name="jquiw_options[chk_inc_accordion]" type="checkbox" value="1" <?php if (isset($options['chk_inc_accordion'])) { checked('1', $options['chk_inc_accordion']); } ?> /> Accordion</label><a href="http://api.jqueryui.com/accordion/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_autocomplete]" type="checkbox" value="1" <?php if (isset($options['chk_inc_autocomplete'])) { checked('1', $options['chk_inc_autocomplete']); } ?> /> Autocomplete</label><a href="http://api.jqueryui.com/autocomplete/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_button]" type="checkbox" value="1" <?php if (isset($options['chk_inc_button'])) { checked('1', $options['chk_inc_button']); } ?> /> Button</label><a href="http://api.jqueryui.com/button/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_uicore]" type="checkbox" value="1" <?php if (isset($options['chk_inc_uicore'])) { checked('1', $options['chk_inc_uicore']); } ?> /> UI Core</label><a href="http://api.jqueryui.com/category/ui-core/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_datepicker]" type="checkbox" value="1" <?php if (isset($options['chk_inc_datepicker'])) { checked('1', $options['chk_inc_datepicker']); } ?> /> Datepicker</label><a href="http://api.jqueryui.com/datepicker/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_dialog]" type="checkbox" value="1" <?php if (isset($options['chk_inc_dialog'])) { checked('1', $options['chk_inc_dialog']); } ?> /> Dialog</label><a href="http://api.jqueryui.com/dialog/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_draggable]" type="checkbox" value="1" <?php if (isset($options['chk_inc_draggable'])) { checked('1', $options['chk_inc_draggable']); } ?> /> Draggable</label><a href="http://api.jqueryui.com/draggable/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
					<td width="200">
						<label><input name="jquiw_options[chk_inc_droppable]" type="checkbox" value="1" <?php if (isset($options['chk_inc_droppable'])) { checked('1', $options['chk_inc_droppable']); } ?> /> Droppable</label><a href="http://api.jqueryui.com/droppable/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_menu]" type="checkbox" value="1" <?php if (isset($options['chk_inc_menu'])) { checked('1', $options['chk_inc_menu']); } ?> /> Menu</label><a href="http://api.jqueryui.com/menu/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_mouse]" type="checkbox" value="1" <?php if (isset($options['chk_inc_mouse'])) { checked('1', $options['chk_inc_mouse']); } ?> /> Mouse</label><a href="http://api.jqueryui.com/mouse/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_position]" type="checkbox" value="1" <?php if (isset($options['chk_inc_position'])) { checked('1', $options['chk_inc_position']); } ?> /> Position</label><a href="http://api.jqueryui.com/position/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_progressbar]" type="checkbox" value="1" <?php if (isset($options['chk_inc_progressbar'])) { checked('1', $options['chk_inc_progressbar']); } ?> /> Progress Bar</label><a href="http://api.jqueryui.com/progressbar/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_resizable]" type="checkbox" value="1" <?php if (isset($options['chk_inc_resizable'])) { checked('1', $options['chk_inc_resizable']); } ?> /> Resizable</label><a href="http://api.jqueryui.com/resizable/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_selectable]" type="checkbox" value="1" <?php if (isset($options['chk_inc_selectable'])) { checked('1', $options['chk_inc_selectable']); } ?> /> Selectable</label><a href="http://api.jqueryui.com/selectable/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
					<td>
						<label><input name="jquiw_options[chk_inc_slider]" type="checkbox" value="1" <?php if (isset($options['chk_inc_slider'])) { checked('1', $options['chk_inc_slider']); } ?> /> Slider</label><a href="http://api.jqueryui.com/slider/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_spinner]" type="checkbox" value="1" <?php if (isset($options['chk_inc_spinner'])) { checked('1', $options['chk_inc_spinner']); } ?> /> Spinner</label><a href="http://api.jqueryui.com/spinner/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_sortable]" type="checkbox" value="1" <?php if (isset($options['chk_inc_sortable'])) { checked('1', $options['chk_inc_sortable']); } ?> /> Sortable</label><a href="http://api.jqueryui.com/sortable/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_tabs]" type="checkbox" value="1" <?php if (isset($options['chk_inc_tabs'])) { checked('1', $options['chk_inc_tabs']); } ?> /> Tabs</label><a href="http://api.jqueryui.com/tabs/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_tooltip]" type="checkbox" value="1" <?php if (isset($options['chk_inc_tooltip'])) { checked('1', $options['chk_inc_tooltip']); } ?> /> Tooltip</label><a href="http://api.jqueryui.com/tooltip/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_widget]" type="checkbox" value="1" <?php if (isset($options['chk_inc_widget'])) { checked('1', $options['chk_inc_widget']); } ?> /> Widget Factory</label><a href="http://api.jqueryui.com/jQuery.widget/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td colspan="3">Note: Scripts such as UI Core, Mouse, Position etc. are included by default with other widget scripts.</td>
				</tr>
				<tr valign="top">
					<th scope="row">Use These jQuery Effect Scripts</th>
					<td width="200">
						<label><input name="jquiw_options[chk_inc_blind]" type="checkbox" value="1" <?php if (isset($options['chk_inc_blind'])) { checked('1', $options['chk_inc_blind']); } ?> /> Blind</label><a href="http://api.jqueryui.com/blind-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_bounce]" type="checkbox" value="1" <?php if (isset($options['chk_inc_bounce'])) { checked('1', $options['chk_inc_bounce']); } ?> /> Bounce</label><a href="http://api.jqueryui.com/bounce-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_clip]" type="checkbox" value="1" <?php if (isset($options['chk_inc_clip'])) { checked('1', $options['chk_inc_clip']); } ?> /> Clip</label><a href="http://api.jqueryui.com/clip-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_coreeffects]" type="checkbox" value="1" <?php if (isset($options['chk_inc_coreeffects'])) { checked('1', $options['chk_inc_coreeffects']); } ?> /> Core Effects</label><a href="http://api.jqueryui.com/category/effects-core/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_drop]" type="checkbox" value="1" <?php if (isset($options['chk_inc_drop'])) { checked('1', $options['chk_inc_drop']); } ?> /> Drop</label><a href="http://api.jqueryui.com/drop-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
					<td width="200">
						<label><input name="jquiw_options[chk_inc_explode]" type="checkbox" value="1" <?php if (isset($options['chk_inc_explode'])) { checked('1', $options['chk_inc_explode']); } ?> /> Explode</label><a href="http://api.jqueryui.com/explode-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_fade]" type="checkbox" value="1" <?php if (isset($options['chk_inc_fade'])) { checked('1', $options['chk_inc_fade']); } ?> /> Fade</label><a href="http://api.jqueryui.com/fade-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_fold]" type="checkbox" value="1" <?php if (isset($options['chk_inc_fold'])) { checked('1', $options['chk_inc_fold']); } ?> /> Fold</label><a href="http://api.jqueryui.com/fold-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_highlight]" type="checkbox" value="1" <?php if (isset($options['chk_inc_highlight'])) { checked('1', $options['chk_inc_highlight']); } ?> /> Highlight</label><a href="http://api.jqueryui.com/highlight-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_pulsate]" type="checkbox" value="1" <?php if (isset($options['chk_inc_pulsate'])) { checked('1', $options['chk_inc_pulsate']); } ?> /> Pulsate</label><a href="http://api.jqueryui.com/pulsate-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
					<td>
						<label><input name="jquiw_options[chk_inc_scale]" type="checkbox" value="1" <?php if (isset($options['chk_inc_scale'])) { checked('1', $options['chk_inc_scale']); } ?> /> Scale</label><a href="http://api.jqueryui.com/scale-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_shake]" type="checkbox" value="1" <?php if (isset($options['chk_inc_shake'])) { checked('1', $options['chk_inc_shake']); } ?> /> Shake</label><a href="http://api.jqueryui.com/shake-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
						<label><input name="jquiw_options[chk_inc_slide]" type="checkbox" value="1" <?php if (isset($options['chk_inc_slide'])) { checked('1', $options['chk_inc_slide']); } ?> /> Slide</label><a href="http://api.jqueryui.com/slide-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />				
						<label><input name="jquiw_options[chk_inc_transfer]" type="checkbox" value="1" <?php if (isset($options['chk_inc_transfer'])) { checked('1', $options['chk_inc_transfer']); } ?> /> Transfer</label><a href="http://api.jqueryui.com/transfer-effect/" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/info.png" class="info_image" /></a><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td colspan="3">Note: The Core Effects script is included by default with other effect scripts.</td>
				</tr>
				<tr valign="top">
					<th scope="row">Select jQuery UI Theme</th>
					<td colspan="3">
						<select name='jquiw_options[drp_jquery_theme]'>
							<option value='base' <?php selected('star', $options['drp_jquery_theme']); ?>>Base</option>
							<option value='black-tie' <?php selected('black-tie', $options['drp_jquery_theme']); ?>>Black Tie</option>
							<option value='blitzer' <?php selected('blitzer', $options['drp_jquery_theme']); ?>>Blitzer</option>
							<option value='cupertino' <?php selected('cupertino', $options['drp_jquery_theme']); ?>>Cupertino</option>
							<option value='dark-hive' <?php selected('dark-hive', $options['drp_jquery_theme']); ?>>Dark Hive</option>
							<option value='dot-luv' <?php selected('dot-luv', $options['drp_jquery_theme']); ?>>Dot Luv</option>
							<option value='eggplant' <?php selected('eggplant', $options['drp_jquery_theme']); ?>>Eggplant</option>
							<option value='excite-bike' <?php selected('excite-bike', $options['drp_jquery_theme']); ?>>Excite Bike</option>
							<option value='flick' <?php selected('flick', $options['drp_jquery_theme']); ?>>Flick</option>
							<option value='hot-sneaks' <?php selected('hot-sneaks', $options['drp_jquery_theme']); ?>>Hot Sneaks</option>
							<option value='humanity' <?php selected('humanity', $options['drp_jquery_theme']); ?>>Humanity</option>
							<option value='le-frog' <?php selected('le-frog', $options['drp_jquery_theme']); ?>>Le Frog</option>
							<option value='mint-choc' <?php selected('mint-choc', $options['drp_jquery_theme']); ?>>Mint Choc</option>
							<option value='overcast' <?php selected('overcast', $options['drp_jquery_theme']); ?>>Overcast</option>
							<option value='pepper-grinder' <?php selected('pepper-grinder', $options['drp_jquery_theme']); ?>>Pepper Grinder</option>
							<option value='redmond' <?php selected('redmond', $options['drp_jquery_theme']); ?>>Redmond</option>
							<option value='smoothness' <?php selected('smoothness', $options['drp_jquery_theme']); ?>>Smoothness</option>
							<option value='south-street' <?php selected('south-street', $options['drp_jquery_theme']); ?>>South Street</option>
							<option value='start' <?php selected('start', $options['drp_jquery_theme']); ?>>Start</option>
							<option value='sunny' <?php selected('sunny', $options['drp_jquery_theme']); ?>>Sunny</option>
							<option value='swanky-purse' <?php selected('swanky-purse', $options['drp_jquery_theme']); ?>>Swanky Purse</option>
							<option value='trontastic' <?php selected('trontastic', $options['drp_jquery_theme']); ?>>Trontastic</option>
							<option value='ui-darkness' <?php selected('ui-darkness', $options['drp_jquery_theme']); ?>>UI Darkness</option>
							<option value='ui-lightness' <?php selected('ui-lightness', $options['drp_jquery_theme']); ?>>UI Lightness</option>
							<option value='vader' <?php selected('vader', $options['drp_jquery_theme']); ?>>Vader</option>
						</select><br />
						Choose a standard jQuery UI theme to render widgets. Preview these themes on the jQuery UI <a href="http://jqueryui.com/themeroller/#themeGallery" target="_blank">ThemeRoller page</a>.<br /><br />
						<input class="widefat" type="text" size="57" name="jquiw_options[txt_custom_theme]" value="<?php echo $options['txt_custom_theme']; ?>" /><br />
						<?php $upload_dir = wp_upload_dir(); ?>

						Create a <a href="http://jqueryui.com/themeroller/" target="_blank">custom theme</a> to override the standard theme. Upload to <code style="font-style:normal;"><?php echo $upload_dir['baseurl']; ?>/</code> and enter the path/name of the custom stylesheet above, relative to this folder. See the Plugin <a href="http://wordpress.org/extend/plugins/jquery-ui-widgets/faq/" target="_blank">FAQ</a> for detailed instructions on using custom themes.<br />
						<?php
							/* Enqueue custom theme rolled styles. */
							$upload_dir = wp_upload_dir();
							$relative_path = trim($options['txt_custom_theme'], "/");
							$full_path_url = trailingslashit($upload_dir['baseurl']).$relative_path;
							$full_path_dir = trailingslashit($upload_dir['basedir']).$relative_path;
							if( !file_exists($full_path_dir) )
								echo "<div class=\"error inline\">Cannot find the file: <code style=\"background-color: #ffebe8;\">".$full_path_url."</code>. Reverting to the standard theme. Please enter a valid custom stylesheet path.</div>";
						?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">Override jQuery UI Theme Styles</th>
					<td colspan="3">
						<?php $res_css = jquiw_get_textarea_rows($options['txtar_override_css'], $min = 5, $max = 20); ?>
						<textarea class="widefat" style="font-family: Lucida Console;" name="jquiw_options[txtar_override_css]" rows="<?php echo $res_css['rows']; ?>" type='textarea'><?php echo $options['txtar_override_css']; ?></textarea><br />Edit the custom CSS rules above to override the current jQuery UI theme (whether a standard or custom theme). This is a great place to tweak the theme styles.
					</td>
				</tr>
				<tr><td colspan="3"><div style="margin-top:0;"></div></td></tr>
				<tr valign="top" style="border-top:#dddddd 1px solid;">
					<th scope="row">Database Options</th>
					<td colspan="3">
						<label><input name="jquiw_options[chk_default_options_db]" type="checkbox" value="1" <?php if (isset($options['chk_default_options_db'])) { checked('1', $options['chk_default_options_db']); } ?> /> Restore defaults upon Plugin deactivation/reactivation</label>
						<br /><span class="description">Only check this if you want to reset Plugin settings upon reactivation.</span>
					</td>
				</tr>
			</table>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>

		<div style="margin-top:15px;">
			<p style="margin-bottom:10px;">If you find this Plugin useful <b><em>please</em></b> consider making a <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MTVAN3NBV3HCA" target="_blank">donation</a> to support continued development.</p>
		</div>

		<div style="clear:both;">
			<p>
				<a href="http://www.facebook.com/PressCoders" title="Our Facebook page" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/facebook.png" /></a><a href="http://www.twitter.com/dgwyer" title="Follow on Twitter" target="_blank"><img src="<?php echo plugins_url(); ?>/jquery-ui-widgets/images/twitter.png" /></a>&nbsp;<input class="button" style="vertical-align:12px;" type="button" value="Visit Our Site" onClick="window.open('http://www.presscoders.com')">&nbsp;<input class="button" style="vertical-align:12px;" type="button" value="Free Responsive Theme!" onClick="window.open('http://www.presscoders.com/designfolio')">
			</p>
		</div>

	</div>
	<?php	
}

// --------------------------------------------------------
// --  DISPLAY A SETTINGS LINK ON THE MAIN PLUGINS PAGE  --
// --------------------------------------------------------

function jquiw_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$posk_links = '<a href="'.get_admin_url().'options-general.php?page=jquery-ui-widgets/jquery-ui-widgets.php">'.__('Settings').'</a>';
		/* Make the 'Settings' link appear first. */
		array_unshift( $links, $posk_links );
	}

	return $links;
}

// ------------------------------------------------------------------------------------
// --  CALCULATE NUMBER OF TEXTAREA ROWS, AND CLASS FOR WIDTH, DEPENDING ON CONTENT  --
// ------------------------------------------------------------------------------------

function jquiw_get_textarea_rows($content=null, $min = 3, $max = 25, $default_class = 'gray', $extra_class = 'gray-medium-textarea') {

	$res = array(); // Initialize array
	if( empty($content) ) {
		$res['rows'] = $min;
		$res['class'] = $default_class;
	}
	else {
		$arr = explode("\n", $content);
		$rows = ( count($arr) < ($min + 1) ) ? $min : count($arr); // Min of 3 lines.
		$rows = ( count($arr) > $max ) ? $max : $rows; // Max of 25 lines.
		$res['rows'] = $rows;
		$res['class'] = $extra_class;
	}

	return $res;
}

?>