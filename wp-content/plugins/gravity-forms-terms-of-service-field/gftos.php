<?php 
/**
 * Plugin Name: Gravity Forms Terms of Service Field
 * Terms of Service Gravity Forms Custom Form Field
 * Plugin URI: http://wpsmith.net/2011/plugins/how-to-create-a-custom-form-field-in-gravity-forms-with-a-terms-of-service-form-field-example/
 * Donate link: http://wpsmith.net/donation
 * Description: Adds a Terms of Service button for use in Gravity Forms
 * Version: 1.0
 * Author: Travis Smith
 * Author URI: http://www.wpsmith.net/
 * License: GPLv2
 * 
 *     Copyright 2012  Travis Smith  (email : http://www.wpsmith.net/contact)
 * 
 *     This program is free software; you can redistribute it and/or modify
 *     it under the terms of the GNU General Public License, version 2, as 
 *     published by the Free Software Foundation.
 * 
 *     This program is distributed in the hope that it will be useful,
 *     but WITHOUT ANY WARRANTY; without even the implied warranty of
 *     MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *     GNU General Public License for more details.
 * 
 *     You should have received a copy of the GNU General Public License
 *     along with this program; if not, write to the Free Software
 * 
 *     Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
*/


// Add a custom field button to the advanced to the field editor
add_filter( 'gform_add_field_buttons', 'wps_add_tos_field' );
function wps_add_tos_field( $field_groups ) {
    foreach( $field_groups as &$group ){
        if( $group["name"] == "advanced_fields" ){ // to add to the Advanced Fields
        //if( $group["name"] == "standard_fields" ){ // to add to the Standard Fields
        //if( $group["name"] == "post_fields" ){ // to add to the Standard Fields
            $group["fields"][] = array(
                "class"=>"button",
                "value" => __("Terms of Service", "gravityforms"),
                "onclick" => "StartAddField('tos');"
            );
            break;
        }
    }
    return $field_groups;
}
 
// Adds title to GF custom field
add_filter( 'gform_field_type_title' , 'wps_tos_title' );
function wps_tos_title( $type ) {
    if ( $type == 'tos' )
        return __( 'Terms of Service' , 'gravityforms' );
}
 
// Adds the input area to the external side
add_action( "gform_field_input" , "wps_tos_field_input", 10, 5 );
function wps_tos_field_input ( $input, $field, $value, $lead_id, $form_id ){
 
    if ( $field["type"] == "tos" ) {
        $max_chars = "";
        if(!IS_ADMIN && !empty($field["maxLength"]) && is_numeric($field["maxLength"]))
            $max_chars = self::get_counter_script($form_id, $field_id, $field["maxLength"]);
 
        $input_name = $form_id .'_' . $field["id"];
        $tabindex = GFCommon::get_tabindex();
		$css = isset( $field['cssClass'] ) ? $field['cssClass'] : '';
        return sprintf("<div class='ginput_container'><textarea readonly name='input_%s' id='%s' class='textarea gform_tos %s' $tabindex rows='10' cols='50'>%s</textarea></div>{$max_chars}", $field["id"], 'tos-'.$field['id'] , $field["type"] . ' ' . esc_attr($css) . ' ' . $field['size'] , esc_html($value));
 
    }
 
    return $input;
}
 
// Now we execute some javascript technicalitites for the field to load correctly
add_action( "gform_editor_js", "wps_gform_editor_js" );
function wps_gform_editor_js(){
?>
 
<script type='text/javascript'>
 
    jQuery(document).ready(function($) {
        //Add all textarea settings to the "TOS" field plus custom "tos_setting"
        // fieldSettings["tos"] = fieldSettings["textarea"] + ", .tos_setting"; // this will show all fields that Paragraph Text field shows plus my custom setting
 
        // from forms.js; can add custom "tos_setting" as well
        fieldSettings["tos"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting, .tos_setting"; //this will show all the fields of the Paragraph Text field minus a couple that I didn't want to appear.
 
        //binding to the load field settings event to initialize the checkbox
        $(document).bind("gform_load_field_settings", function(event, field, form){
            jQuery("#field_tos").attr("checked", field["field_tos"] == true);
            $("#field_tos_value").val(field["tos"]);
        });
    });
 
</script>
<?php
}
 
// Add a custom setting to the tos advanced field
add_action( "gform_field_advanced_settings" , "wps_tos_settings" , 10, 2 );
function wps_tos_settings( $position, $form_id ){
 
    // Create settings on position 50 (right after Field Label)
    if( $position == 50 ){
    ?>
 
    <li class="tos_setting field_setting">
 
        <input type="checkbox" id="field_tos" onclick="SetFieldProperty('field_tos', this.checked);" />
        <label for="field_tos" class="inline">
            <?php _e("Disable Submit Button", "gravityforms"); ?>
            <?php gform_tooltip("form_field_tos"); ?>
        </label>
 
    </li>
    <?php
    }
}
 
//Filter to add a new tooltip
add_filter('gform_tooltips', 'wps_add_tos_tooltips');
function wps_add_tos_tooltips($tooltips){
   $tooltips["form_field_tos"] = "<h6>Disable Submit Button</h6>Check the box if you would like to disable the submit button.";
   $tooltips["form_field_default_value"] = "<h6>Default Value</h6>Enter the Terms of Service here.";
   return $tooltips;
}
 
// Add a script to the display of the particular form only if tos field is being used
add_action( 'gform_enqueue_scripts' , 'wps_gform_enqueue_scripts' , 10 , 2 );
function wps_gform_enqueue_scripts( $form, $ajax ) {
    // cycle through fields to see if tos is being used
    foreach ( $form['fields'] as $field ) {
        if ( ( $field['type'] == 'tos' ) && ( isset( $field['field_tos'] ) ) ) {
			$url = plugins_url( 'gform_tos.js' , __FILE__ );
            wp_enqueue_script( "gform_tos_script", $url , array("jquery"), '1.0' ); 
            break;
        }
    }
}
 
// Add a custom class to the field li
add_action("gform_field_css_class", "custom_class", 10, 3);
function custom_class($classes, $field, $form){
 
    if( $field["type"] == "tos" ){
        $classes .= " gform_tos";
    }
 
    return $classes;
}