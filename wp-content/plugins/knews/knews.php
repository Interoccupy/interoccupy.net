<?php
/*
Plugin Name: K-news
Plugin URI: http://www.knewsplugin.com
Description: Finally, newsletters are multilingual, quick and professional.
Version: 1.3.0
Author: Carles Reverter
Author URI: http://www.carlesrever.com
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
*/

if (!class_exists("KnewsPlugin")) {
	class KnewsPlugin {
	
		var $adminOptionsName = "KnewsAdminOptions";
		var $knewsOptions = array();
		var $knewsLangs = array();
		var $initialized = false;
		var $initialized_textdomain = false;
		var $advice='';
		var $KNEWS_MAIN_BLOG_ID = 1;
		var $knews_admin_messages = '';
		var $knews_form_n = 1;
						
		/******************************************************************************************
		/*                                   INICIALITZAR
		******************************************************************************************/
		
		// Carregar opcions de la BBDD / inicialitzar
		function getAdminOptions() {

			$KnewsAdminOptions = array (
				'smtp_knews' => '0',
				'from_mail_knews' => get_bloginfo('admin_email'),
				'from_name_knews' => 'Knews robot',
				'smtp_host_knews' => 'smtp.knewsplugin.com',
				'smtp_port_knews' => '25',
				'smtp_user_knews' => '',
				'smtp_pass_knews' => '',
				'smtp_secure_knews' => '',
				'multilanguage_knews' => 'off',
				'no_warn_ml_knews' => 'no',
				'no_warn_cron_knews' => 'no',
				'config_knews' => 'no',
				'update_knews' => 'no',
				'write_logs' => 'no',
				'knews_cron' => 'cronwp',
				'update_pro' => 'no',
				'videotutorial' => 'no',
				'def_autom_post' => '0',
				'apply_filters_on' => '1',
				'edited_autom_post' => '0',
				'hide_shop' => '0',
				'hide_templates' => '0',
				'bounce_on' => '0',
				'is_sendmail' => '0',
				'registration_email' => '',
				'registration_serial' => '',
				'check_bot' => '1'
				);

			$devOptions = get_option($this->adminOptionsName);
			if (!empty($devOptions)) {
				foreach ($devOptions as $key => $option)
					$KnewsAdminOptions[$key] = $option;
			} else {
				update_option($this->adminOptionsName, $KnewsAdminOptions);
			}

			return $KnewsAdminOptions;
		}
	
		function creaSiNoExisteixDB () {
			if (!$this->tableExists(KNEWS_USERS)) require( KNEWS_DIR . "/includes/knews_installDB.php");
			if (version_compare(get_option('knews_version','0.0.0'), KNEWS_VERSION, '<')) require( KNEWS_DIR . "/includes/knews_updateDB.php");
		}
		
		function get_default_messages() {

			$KnewsDefaultMessages1 = array (
				array ( 'label'=>__('Text direction, Left To Right or Right To Left: put <span style="color:#e00">ltr</span> or <span style="color:#e00">rtl</span>','knews'), 'name'=>'text_direction'),
				array ( 'label'=>__('Widget subscription form title','knews'), 'name'=>'widget_title')
			);
			if ($this->im_pro()) $KnewsDefaultMessages1[] = array ( 'label'=>__('Widget latest newsletters title','knews'), 'name'=>'widgetln_title');

			$extra_fields = $this->get_extra_fields();
			foreach ($extra_fields as $field) {
				$KnewsDefaultMessages1[]=array ( 'label'=> sprintf(__('Widget "%s" label form','knews'), $field->name), 'name'=>'widget_label_' . $field->name);
			}

			$KnewsDefaultMessages2 = array (
				array ( 'label'=> sprintf(__('Widget "%s" label form','knews'), 'email'), 'name'=>'widget_label_email'),
				array ( 'label'=>__('Widget submit button','knews'), 'name'=>'widget_button'),
				array ( 'label'=>__('Wrong e-mail address, please check (AJAX message)','knews'), 'name'=>'ajax_wrong_email'),
				array ( 'label'=>__('We have sent you a confirmation e-mail (AJAX message)','knews'), 'name'=>'ajax_subscription'),
				array ( 'label'=>__('Subscription done, you were already subscribed (AJAX message)','knews'), 'name'=>'ajax_subscription_direct'),
				array ( 'label'=>__('You were already a subscriber (AJAX message)','knews'), 'name'=>'ajax_subscription_oops'),
				array ( 'label'=>__('Subscription error (AJAX message)','knews'), 'name'=>'ajax_subscription_error'),
				array ( 'label'=>__('Confirmation E-mail (subject)','knews'), 'name'=>'email_subscription_subject'),
				array ( 'label'=>__('Confirmation E-mail (body)','knews'), 'name'=>'email_subscription_body'),
				array ( 'label'=>__('E-mail on automatically import (title)','knews'), 'name'=>'email_importation_subject'),
				array ( 'label'=>__('E-mail on automatically import (body)','knews'), 'name'=>'email_importation_body'),
				array ( 'label'=>__('Subscription OK Dialog (Title)','knews'), 'name'=>'subscription_ok_title'),
				array ( 'label'=>__('Subscription OK Dialog (Message)','knews'), 'name'=>'subscription_ok_message'),
				array ( 'label'=>__('Subscription Error Dialog (Title)','knews'), 'name'=>'subscription_error_title'),
				array ( 'label'=>__('Subscription Error Dialog (Message)','knews'), 'name'=>'subscription_error_message'),
				array ( 'label'=>__('UnSubscribe Error Dialog (Title)','knews'), 'name'=>'subscription_stop_error_title'),
				array ( 'label'=>__('UnSubscribe Error Dialog (Message)','knews'), 'name'=>'subscription_stop_error_message'),
				array ( 'label'=>__('UnSubscribe OK Dialog (Title)','knews'), 'name'=>'subscription_stop_ok_title'),
				array ( 'label'=>__('UnSubscribe OK Dialog (Message)','knews'), 'name'=>'subscription_stop_ok_message'),
				array ( 'label'=>__('Close Button Caption','knews'), 'name'=>'dialogs_close_button'),
				array ( 'label'=>__('Default alignment (<span style="color:#e00">left</span> for left to right languages and <span style="color:#e00">right</span> for right to left languages)','knews'), 'name'=>'default_alignment'),
				array ( 'label'=>__('Inverse alignment (<span style="color:#e00">right</span> for left to right languages and <span style="color:#e00">left</span> for right to left languages)','knews'), 'name'=>'inverse_alignment'),
				array ( 'label'=>__('Cant read text 1','knews'), 'name'=>'cant_read_text_1'),
				array ( 'label'=>__('Cant read text link','knews'), 'name'=>'cant_read_text_link'),
				array ( 'label'=>__('Cant read text 2','knews'), 'name'=>'cant_read_text_2'),
				array ( 'label'=>__('Unsubscribe text 1','knews'), 'name'=>'unsubscribe_text_1'),
				array ( 'label'=>__('Unsubscribe text link','knews'), 'name'=>'unsubscribe_text_link'),
				array ( 'label'=>__('Unsubscribe text 2','knews'), 'name'=>'unsubscribe_text_2'),
				array ( 'label'=>__('The read more text link','knews'), 'name'=>'read_more_link'),
			);
			return array_merge($KnewsDefaultMessages1, $KnewsDefaultMessages2);
		}

		function get_custom_text($name, $lang, $restore=false) {
			$lang = str_replace('-','_',$lang);
			$custom = get_option('knews_custom_' . $name . '_' . $lang,'');

			if ($custom == '' || $restore) {
				
				require_once (KNEWS_DIR . '/includes/mo_reader.php');

				if (!is_file(KNEWS_DIR . '/languages/knews-' . $lang . '.mo')) {
					$custom = mo_reader(KNEWS_DIR . '/languages/knews-en_US.mo', $name);
				} else {
					$custom = mo_reader(KNEWS_DIR . '/languages/knews-' . $lang . '.mo', $name);
				}
				
				$custom = str_replace('\"','"',$custom);
				update_option('knews_custom_' . $name . '_' . $lang, $custom);
			}
			return $custom;
		}
		
		function init($blog_id=0) {
			global $knewsOptions, $wpdb;
			
			if ($blog_id != 0 && is_multisite()) switch_to_blog($blog_id);
			
			define('KNEWS_USERS', $wpdb->prefix . 'knewsusers');	
			define('KNEWS_USERS_EXTRA', $wpdb->prefix . 'knewsusersextra');	
			define('KNEWS_EXTRA_FIELDS', $wpdb->prefix . 'knewsextrafields');	
			define('KNEWS_LISTS', $wpdb->prefix . 'knewslists');	
			define('KNEWS_USERS_PER_LISTS', $wpdb->prefix . 'knewsuserslists');	
			define('KNEWS_NEWSLETTERS', $wpdb->prefix . 'knewsletters');	
			define('KNEWS_NEWSLETTERS_SUBMITS_DETAILS', $wpdb->prefix . 'knewsubmitsdetails');
			define('KNEWS_STATS', $wpdb->prefix . 'knewstats');
			define('KNEWS_KEYS', $wpdb->prefix . 'knewskeys');
			define('KNEWS_AUTOMATED', $wpdb->prefix . 'knewsautomated');
			define('KNEWS_AUTOMATED_POSTS', $wpdb->prefix . 'knewsautomatedposts');
			define('KNEWS_AUTOMATED_SELECTION', $wpdb->prefix . 'knewsautomatedsel');

			define('KNEWS_NEWSLETTERS_SUBMITS', $wpdb->base_prefix . 'knewsubmits');

			define('KNEWS_DIR', dirname(__FILE__));
			
			$url = plugins_url();
			if ($blog_id != 0) $url = $this->get_right_blog_path($blog_id) . 'wp-content/plugins';
			define('KNEWS_URL', $url . '/knews');

			$this->knews_load_plugin_textdomain();

			$knewsOptions = $this->getAdminOptions();

			global $KnewsAdminOptions;
			define('KNEWS_MULTILANGUAGE', $this->check_multilanguage_plugin($KnewsAdminOptions['multilanguage_knews']));

			$this->creaSiNoExisteixDB();
			
			$this->knewsLangs = $this->getLangs();

			//LOCALIZED URLS (WPML different domains for language option)
			$knews_localized_url = KNEWS_URL;
			$knews_localized_admin = get_admin_url();
			if ((KNEWS_MULTILANGUAGE) && $knewsOptions['multilanguage_knews']=='wpml') {
				if (function_exists('icl_get_languages')) {
					
					global $sitepress_settings;
					if (isset($sitepress_settings['language_negotiation_type']) && $sitepress_settings['language_negotiation_type']==2) {
					
						$l = $this->pageLang();
						$knews_localized_url = $l['url'];
						if (substr($knews_localized_url, -1) != '/') $knews_localized_url .= '/';
						$knews_localized_admin = $knews_localized_url . 'wp-admin/';
						$knews_localized_url .= 'wp-content/plugins/knews';
					}
				}
			}
			define('KNEWS_LOCALIZED_URL', $knews_localized_url);
			define('KNEWS_LOCALIZED_ADMIN', $knews_localized_admin);

			$this->initialized = true;
		}
		
		function get_right_blog_path($blog_id) {
			global $wpdb;

			$blog_found=array();
			
			if( is_multisite() ) {
				$query = "SELECT * FROM " . $wpdb->base_prefix . 'blogs' . " WHERE blog_id=" . $blog_id;
				$blog_found = $wpdb->get_results( $query );
			}
			
			if (count($blog_found)==0) return get_bloginfo('wpurl') . '/';
			$protocol = 'http://';
			if (substr(get_bloginfo('wpurl'),0,8)=='https://') $protocol = 'https://';
			return $protocol . $blog_found[0]->domain . $blog_found[0]->path;
		}
		
		function get_main_admin_url() {
			
			$url = get_admin_url();

			if( is_multisite() ) {
				$url = $this->get_right_blog_path($this->KNEWS_MAIN_BLOG_ID) . 'wp-admin/';
			}
			
			return $url;
		}
		function get_main_plugin_url() {
			
			$url = plugins_url();

			if( is_multisite() ) {
				$url = $this->get_right_blog_path($this->KNEWS_MAIN_BLOG_ID) . 'wp-content/plugins';
			}
			
			return $url;
		}

		function knews_load_plugin_textdomain() {
			global $initialized_textdomain;

			if ($initialized_textdomain) return;
			load_plugin_textdomain( 'knews', false, 'knews/languages');
			$initialized_textdomain=true;
		}
		
		function check_multilanguage_plugin($plugin='') {
			global $knewsOptions;

			if ($plugin=='') $plugin = $knewsOptions['multilanguage_knews'];

			$multilanguage_plugin = false;
			if ($plugin == 'wpml') $multilanguage_plugin = $this->have_wpml();
			if ($plugin == 'qt') $multilanguage_plugin = $this->have_qtranslate();

			return $multilanguage_plugin;
		}

		/******************************************************************************************
		/*                                 LOGICA DEL PLUGIN
		******************************************************************************************/
		function KnewsPlugin() {
			//Execucio tant a admin com a web
		}
	
		/******************************************************************************************
		/*                                  COMMON FUNCTIONS 
		******************************************************************************************/
		function get_last_cron_time () {
			
			$last_cron_time=0;
			
			if( is_multisite() ) {
				if ( get_current_blog_id() != $this->KNEWS_MAIN_BLOG_ID ) {
					switch_to_blog($this->KNEWS_MAIN_BLOG_ID);
					$last_cron_time = get_option('knews_cron_time',-1);
					restore_current_blog();
				}
			}
			
			if ($last_cron_time == 0) $last_cron_time = get_option('knews_cron_time',0);
			if ($last_cron_time == -1) $last_cron_time = 0;
			
			return $last_cron_time;
		}
		function get_mysql_date($when='now') {
			if ($when=='now') return current_time('mysql');
			return date("Y-m-d H:i:s", $when);
		}
		
		function sql2time($sqldate) {
			return mktime(substr($sqldate,11,2), substr($sqldate,14,2), substr($sqldate,17,2), substr($sqldate,5,2), substr($sqldate,8,2), substr($sqldate,0,4));
		}

		function humanize_dates ($date, $format) {
			
			if ($date == '0000-00-00 00:00:00') return '-';
			
			if ($format=='mysql') $date = $this->sql2time($date);

			//$gmt_offset = intval(get_option('gmt_offset')) * 60 * 60;
			//$date = $date + $gmt_offset;
			
			$day = 60*60*24;
			$today_start = mktime (0,0,0,date('n'),date('j'),date('Y')); // + $gmt_offset;

			$diference = $date - $today_start;

			$hour = intval(($date % $day) / (60*60));
			$minute = intval((($date % $day) - $hour * 60 * 60) / 60);
			if ($hour < 10) $hour = '0' . $hour;
			if ($minute < 10) $minute = '0' . $minute;
			$hour = $hour . ':' . $minute;
			
			$date_readable = date('d',$date) . '/' . date('m',$date) . '/' . date('Y',$date);

			if ($diference > 0) {
				//Future or today
				if ($diference < $day) return __('Today, at','knews') . ' ' . $hour;
				if ($diference < $day*2) return __('Tomorrow, at','knews') . ' ' . $hour;
				return $date_readable . ' ' . __('at','knews') . ' ' . $hour;
			} else {
				//Past
				$diference=$diference * -1;
				if ($diference < $day) return __('Yesterday, at','knews') . ' ' . $hour;
				return $date_readable . ' ' . __('at','knews') . ' ' . $hour;
			}
		}
		
		function get_extra_fields ($extra_sql='') {
			$ef = array( new stdClass, new stdClass );
			$ef[0]->id = 1; $ef[0]->name = 'name'; $ef[0]->show_table =1; $ef[0]->token = '%name%';
			$ef[1]->id = 2; $ef[1]->name = 'surname'; $ef[1]->show_table =1; $ef[1]->token = '%surname%';
			return $ef;
		}
		
		function get_user_field ($user_id, $field_id, $empty='') {
			global $wpdb;
			
			$query = "SELECT * FROM " . KNEWS_USERS_EXTRA . " WHERE user_id=" . $user_id . ' AND field_id=' . $field_id;
			$field_found = $wpdb->get_col( $query, 3 );

			if ($field_found) {
				if ($field_found[0]!='') return $field_found[0];
			}
			return $empty;
		}
		
		function set_user_field ($user_id, $field_id, $cf, $overwrite=true) {
			global $wpdb;

			$query = "SELECT * FROM " . KNEWS_USERS_EXTRA . " WHERE user_id=" . $user_id . ' AND field_id=' . $field_id;
			$field_found = $wpdb->get_results( $query );

			if (count($field_found)==0) {
				
				//Insert field
				if ($cf != '') {
					$query = "INSERT INTO " . KNEWS_USERS_EXTRA . " (value, user_id, field_id) VALUES ('" . $cf . "', " . $user_id . "," . $field_id . ")";
					$result=$wpdb->query( $query );
				} else {
					$result=1;
				}
			} else {
				//Update field
				$result=1;
				if ($overwrite) {
					$query = "UPDATE " . KNEWS_USERS_EXTRA . " SET value='" . $cf . "' WHERE user_id=" . $user_id . ' AND field_id=' . $field_id;
					$result=$wpdb->query( $query );
				}
			}
			return $result;
		}

		function tableExists($table){
			global $wpdb;
			return strcasecmp($wpdb->get_var("show tables like '$table'"), $table) == 0;
		}
		
		function get_safe($field, $un_set='', $mode='paranoid') {
			$value = ((isset($_GET[$field])) ? $_GET[$field] : $un_set);
			if ( get_magic_quotes_gpc()) $value = stripslashes_deep($value);
			if ($mode=='unsafe') return $value;
			if ($mode=='int') return intval($value);
			if ($mode=='paranoid') return mysql_real_escape_string(htmlspecialchars(strip_tags($value)));
		}

		function post_safe($field, $un_set='', $mode='paranoid') {
			$value = ((isset($_POST[$field])) ? $_POST[$field] : $un_set);
			if ( get_magic_quotes_gpc()) $value = stripslashes_deep($value);
			if ($mode=='unsafe') return $value;
			if ($mode=='int') return intval($value);
			if ($mode=='paranoid') return mysql_real_escape_string(htmlspecialchars(strip_tags($value)));
		}
	
		function get_user_lang($email){

			if (! $this->initialized) $this->init();

			global $wpdb;
			
			$query = "SELECT * FROM " . KNEWS_USERS . " WHERE email='" . $email . "'";
			$user_found = $wpdb->get_results( $query );
			return $user_found[0]->lang;
		}
				
		function get_unique_id($long=8) {
			return substr(md5(uniqid()), $long * -1);
		}
		
		function add_user_self(){

			global $knewsOptions;

			//$name = mysql_real_escape_string($_POST['name']);
			$lang = $this->post_safe('lang_user');
			$lang_locale = $this->post_safe('lang_locale_user');
			$email = $this->post_safe('email');
			$id_list_news = $this->post_safe('user_knews_list', 0, 'int');
			
			$custom_fields=array();
			$custom_fields_ok=true;
			$extra_fields = $this->get_extra_fields();
			
			foreach ($extra_fields as $field) {
				if ($this->post_safe($field->name) != '') {
					$custom_fields[$field->id]=$this->post_safe($field->name);
				} else {
					if ($this->post_safe('required_' . $field->name) == '1') $custom_fields_ok=false;
				}
			}

			$stupid_bot = false;
			if (intval($knewsOptions['check_bot'])==1) {
				$key = md5(date('dmY') . wp_create_nonce( 'knews-subscription' ));
				if ($this->post_safe('knewskey') != $key) $stupid_bot = true;
				if (date('G') == 0 && $stupid_bot) {
					$key = md5(date('dmY', strtotime("-1 day")) . wp_create_nonce( 'knews-subscription' ));
					if ($this->post_safe('knewskey') == $key) $stupid_bot = false;
				}
			}
			if ($this->post_safe('knewscomment') != '') $stupid_bot = true;

			echo '<div class="response"><p>';

			if (!$this->validEmail($email) || $stupid_bot || !$custom_fields_ok) {
				echo	$this->get_custom_text('ajax_wrong_email', $lang_locale) . ' <a href="#" class="knews_back">' 
						. $this->get_custom_text('dialogs_close_button', $lang_locale) . '</a>';

			} else {
				$response = $this->add_user($email, $id_list_news, $lang, $lang_locale, $custom_fields);
				
				if ($response==1) echo $this->get_custom_text('ajax_subscription', $lang_locale);
				if ($response==2) echo $this->get_custom_text('ajax_subscription_error', $lang_locale);
				if ($response==3) echo $this->get_custom_text('ajax_subscription_direct', $lang_locale);
				if ($response==4) echo $this->get_custom_text('ajax_subscription_oops', $lang_locale);
			}

			echo '</p></div>';
		}
		
		function add_user($email, $id_list_news, $lang='en', $lang_locale='en_US', $custom_fields=array(), $bypass_confirmation=false){
			
			if (! $this->initialized) $this->init();
			
			global $wpdb;
			$date = $this->get_mysql_date();
			$confkey = $this->get_unique_id();

			$query = "SELECT * FROM " . KNEWS_USERS . " WHERE email='" . $email . "'";
			$user_found = $wpdb->get_results( $query );

			$submit_mail=true;

			if (count($user_found)==0) {
				$query = "INSERT INTO " . KNEWS_USERS . " (email, lang, state, joined, confkey) VALUES ('" . $email . "','" . $lang . "', " . ($bypass_confirmation ? '2' : '1') . ", '" . $date . "','" . $confkey . "');";
				$results = $wpdb->query( $query );
				$user_id=$wpdb->insert_id; $user_id2=mysql_insert_id(); if ($user_id==0) $user_id=$user_id2;

			} else if ($user_found[0]->state=='2') {
				$user_id = $user_found[0]->id;
				$submit_mail=false;
				$results=true;
				
			} else {
				$user_id = $user_found[0]->id;
				$query = "UPDATE " . KNEWS_USERS . " SET state='1', confkey='" . $confkey . "', lang='" . $lang . "' WHERE id=" . $user_id;
				$results = $wpdb->query( $query );
			}
			
			while ($cf = current($custom_fields)) {
				$this->set_user_field ($user_id, key($custom_fields), mysql_real_escape_string($cf), false);
				next($custom_fields);
			}
			
			if ($results) {
				if (count($user_found)==0) {

					$query = "INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $user_id . ", " . $id_list_news . ");";

				} else {

					$query = "SELECT * FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $user_id . " AND id_list=" . $id_list_news;
					$subscription_found = $wpdb->get_results( $query );
					
					if (count($subscription_found)==0) {
						$query = "INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $user_id . ", " . $id_list_news . ");";
					}
				}
				$results = $wpdb->query( $query );
								
				if ($submit_mail) {
					
					if ($bypass_confirmation) return 1;
										
					if ($this->submit_confirmation ($email, $confkey, $lang_locale)) {
						return 1; //Confirmation sent
					} else {
						return 2; //Submit confirmation error
					}
				} else {
					if (count($subscription_found)==0) {
						return 3; //Subscription OK to second mailing list 
					} else {
						return 4; //Error, cant subscribe
					}
				}
				
			} else {
				return 4; //Error, cant subscribe				
			}
			
		}
		
		function submit_confirmation ($email, $confkey, $lang_locale) {

			global $knewsOptions;

			$mailHtml = $this->get_custom_text('email_subscription_body', $lang_locale);
			
			$url_confirm = KNEWS_LOCALIZED_ADMIN . 'admin-ajax.php?action=knewsConfirmUser&k=' . $confkey . '&e=' . urlencode($email);
			$mailHtml = str_replace('#url_confirm#', $url_confirm, $mailHtml);

			$mailText = str_replace('</p>', '</p>\r\n\r\n', $mailHtml);
			$mailText = str_replace('<br>', '<br>\r\n', $mailText);
			$mailText = str_replace('<br />', '<br />\r\n', $mailText);
			$mailText = strip_tags($mailText);

			$result=$this->sendMail( $email, $this->get_custom_text('email_subscription_subject', $lang_locale), $mailHtml, $mailText );
			return ($result['ok']==1);
		}

		function validEmail($email) {
			if (empty($email) || !is_email($email)) {
				return false;
			} else {
				return true;
			}
		}
		
		function localize_lang($langs_array, $lang, $not_found='en_US') {
			$lang_locale=$not_found;
			foreach ($langs_array as $search_lang) {
				if ($search_lang['language_code']==$lang) {
					if (isset($search_lang['localized_code'])) $lang_locale=$search_lang['localized_code'];
					break;
				}
			}
			return $lang_locale;
		}

		function have_wpml() {
			return (function_exists('icl_get_languages'));
		}
		
		function have_qtranslate() {
			return (function_exists( 'qtrans_init'));
		}

		/******************************************************************************************
		/*                                FUNCIONS FRONT END
		******************************************************************************************/

		function confirm_user_self() {
			
			if (! $this->initialized) $this->init();

			global $wpdb;
			
			$confkey = $this->get_safe('k');
			$email = $this->get_safe('e');
			$date = $this->get_mysql_date();
			
			if (!$this->validEmail($email)) return false;
			if ($confkey=='') return false;
			
			$query = "UPDATE ".KNEWS_USERS." SET state='2' WHERE email='" . $email . "' AND confkey='" . $confkey . "'";
			$results = $wpdb->query( $query );
			
			return $results;
		}
		
		function block_user_self() {
			
			if (! $this->initialized) $this->init();

			global $wpdb;
			
			$id_newsletter = $this->get_safe('n', 0, 'int');
			$confkey = $this->get_safe('k');
			$email = $this->get_safe('e');
			$date = $this->get_mysql_date();
			
			if (!$this->validEmail($email)) return false;
			if ($confkey=='') return false;
			
			$query = "SELECT id FROM " . KNEWS_USERS . " WHERE confkey='" . $confkey . "' AND email='" . $email . "'";
			$find_user = $wpdb->get_results( $query );
			
			if (count($find_user) != 1) return false;
	
			$query = "INSERT INTO " . KNEWS_STATS . " (what, user_id, submit_id, date) VALUES (3, " . $find_user[0]->id . ", " . $id_newsletter . ", '" . $date . "')";
			$result=$wpdb->query( $query );

			$query = "UPDATE ".KNEWS_USERS." SET state='3' WHERE id=" . $find_user[0]->id;
			$results = $wpdb->query( $query );
			
			return $results;
		}
		
		function getLangs($need_localized=false) {
			global $knewsOptions;

			if ((KNEWS_MULTILANGUAGE) && $knewsOptions['multilanguage_knews']=='wpml') {
				if (function_exists('icl_get_languages')) {
					$languages = icl_get_languages('skip_missing=0');
					if(!empty($languages)) {

						if ($need_localized) {
							foreach ($languages as $lang) {
								$lang['localized_code'] = $this->wpml_locale($lang['language_code']);
								$languages_localized[]=$lang;
							}
							$languages=$languages_localized;
						}						
						return $languages;
					}
				}
			}
			
			if ((KNEWS_MULTILANGUAGE) && $knewsOptions['multilanguage_knews']=='qt') {
				global $q_config;
				
				if (is_array($q_config)) {
					if (isset($q_config['enabled_languages'])) {
						
						$active_langs = $q_config['enabled_languages'];
						
						if (isset($q_config['language'])) {
							$q_def_lang = $q_config['language'];
						} else {
							$q_def_lang = substr(get_bloginfo('language'), 0, 2);
						}

						foreach ($active_langs as $lang) {
							
							$q_nat_lang = $lang; if (isset($q_config['language_name'][$lang])) $q_nat_lang = $q_config['language_name'][$lang];
							$q_trans_lang = $lang; if (isset($q_config['windows_locale'][$lang])) $q_trans_lang = $q_config['windows_locale'][$lang];
							$q_localized_lang = $lang; if (isset($q_config['locale'][$lang])) $q_localized_lang = $q_config['locale'][$lang];
	
							$wpml_style_langs[$lang] = array (
									'active' 			=> (($q_def_lang==$lang) ? 1 : 0),
									'native_name'		=> $q_nat_lang,
									'translated_name'	=> $q_trans_lang,
									'language_code'		=> $lang,
									'localized_code'	=> $q_localized_lang
								);
						}
						if (count($wpml_style_langs) > 0) return $wpml_style_langs;
					}
				}
			}
			
			$short_lang = substr(get_bloginfo('language'), 0, 2);
			return array (
				$short_lang => array (
					'active'=>1, 
					'native_name'=>__('Unique language','knews') . ' (' . $short_lang . ')', 
					'translated_name'=>__('Unique language','knews') . ' (' . $short_lang . ')', 
					'language_code'=>$short_lang, 
					'localized_code'=>get_bloginfo('language')
				)
			);
			
		}

		function pageLang() {
			foreach($this->knewsLangs as $l) {
				if($l['active']) break;
			}
			return $l;
		}

		function wpml_locale($lang) {
			global $wpdb;
			$default_locale = $wpdb->get_results("SELECT default_locale FROM " . $wpdb->prefix . "icl_languages WHERE code='" . $lang . "'");
			if ($default_locale) return $default_locale[0]->default_locale;
			return '';
		}
		
		function tellMeLists($filter=true) {
		
			if (! $this->initialized) $this->init();
		
			global $wpdb;
			
			$active_lang=$this->pageLang();
			$lists=array();

			$query = "SELECT * FROM " . KNEWS_LISTS;

			if (is_user_logged_in()) {
				$query .= " WHERE open_registered='1'";
			} else {
				$query .= " WHERE open='1'";
			}
			$query .= " ORDER BY orderlist";
			$results = $wpdb->get_results( $query );

			foreach ($results as $list) {
				$valid=true;
				//Primer mirem si hem de descartar per idioma
				if ($active_lang['language_code'] != '' && KNEWS_MULTILANGUAGE) {
					if ($list->langs != '') {
						$lang_sniffer = explode(',', $list->langs);
						if (!in_array($active_lang['language_code'], $lang_sniffer) ) $valid=false;
					}
				}
				if ($valid || !$filter) $lists[$list->id]=$list->name;
								
			}
			return $lists;
			
		}
		
		/* WARNIG: This functions will be deprecated in the future, please, use the get functions instead of print functions */
		function printListsSelector($lists, $mandatory_id=0) {
			echo $this->getListsSelector($lists, $mandatory_id);
		}
		function printAddUserUrl() {
			//This function has wrong name, and is keept for compatibility customisations with old knews versions
			return $this->getAddUserUrl();
		}
		function printLangHidden() {
			echo $this->getLangHidden();
		}
		function printAjaxScript($container, $custom=false) {
			echo $this->getAjaxScript($container, $custom=false);
		}
		/* end print deprecated functions */

		function getListsSelector($lists, $mandatory_id=0) {
			if ($mandatory_id != 0) {
				if (isset($lists[$mandatory_id])) {
					$lists = array();
					$lists[$mandatory_id] = 'mandatory';
				}
			}
			if (count($lists) > 1) {
				$response = '<select name="user_knews_list" id="user_knews_list">';
				while ($list = current($lists)) {
					$response .= '<option value="' . key($lists) . '">' . $list . '</option>';
					next($lists);
				}
				$response .= '</select>';
			} else if (count($lists) == 1) {
				$response = '<input type="hidden" name="user_knews_list" id="user_knews_list" value="' . key($lists) . '" />';
			} else {
				$response = '<input type="hidden" name="user_knews_list" id="user_knews_list" value="-" />';			
			}
			return $response;
		}

		function getAddUserUrl() {
			return KNEWS_LOCALIZED_ADMIN . 'admin-ajax.php';
		}
		
		function getLangHidden($html=true) {
			global $knewsOptions;
			
			$lang = $this->pageLang();
			
			if ((KNEWS_MULTILANGUAGE) && $knewsOptions['multilanguage_knews']=='wpml') $lang['localized_code'] = $this->wpml_locale($lang['language_code']);
			
			if (!$html) return $lang;
			
			$response = '<input type="hidden" name="lang_user" id="lang_user" value="' . $lang['language_code'] . '" />';
			$response .= '<input type="hidden" name="lang_locale_user" id="lang_locale_user" value="' . $lang['localized_code'] . '" />';
			
			return $response;
		}
		
		function getAjaxScript($container, $custom=false) {

			$response = '<script type="text/javascript">
				jQuery(document).ready(function() {
					jQuery(\'#knewsform_' . $this->knews_form_n . ' form\').live(\'submit\', function() {
						if (jQuery(this).attr(\'submitted\') !== "true") {
							save_knews_form = jQuery(\'#knewsform_' . $this->knews_form_n . '\').html();
							jQuery(this).attr(\'submitted\', "true");
							jQuery.post(jQuery(this).attr(\'action\'), jQuery(this).serialize(), function (data) { 
								jQuery(\'#knewsform_' . $this->knews_form_n . '\').html(data);
								jQuery(\'#knewsform_' . $this->knews_form_n . ' a.knews_back\').click( function () {
									jQuery(\'#knewsform_' . $this->knews_form_n . '\').html(save_knews_form);
									return false;								
								});
							});
						}
						return false;
					});
				})
			</script>';

			return $response;
		}
		
		function getForm($mandatory_id=0, $args='', $instance=array(), $container='knews_add_user') {
			global $knewsOptions;
			$stylize = false;
			if ((isset($instance['stylize']) && $instance['stylize']==1) || is_array($args)) $stylize = true;

			$extra_fields = $this->get_extra_fields();
			$response='';
			if (! $this->initialized) $this->init();
			$knews_lists = $this->tellMeLists( (($mandatory_id==0) ? true : false) );

			if (count($knews_lists) > 0) {
				$lang = $this->pageLang();

				if ((KNEWS_MULTILANGUAGE) && $knewsOptions['multilanguage_knews']=='wpml') $lang['localized_code'] = $this->wpml_locale($lang['language_code']);

				if (is_array($args)) $response .= $args['before_widget'] . $args['before_title'] . $this->get_custom_text('widget_title', $lang['localized_code']) . $args['after_title'];

				$response .= '<div class="' . $container . '" id="knewsform_' . $this->knews_form_n . '">
					<style type="text/css">
					div.' . $container . ' textarea#knewscomment {position:absolute; top:-3000px; left:-3000px;}
					</style>
					<form action="' . $this->getAddUserUrl() . '" method="post">';

				foreach ($extra_fields as $field) {
					if (isset($instance[$field->name]) && ($instance[$field->name]=='ask' || $instance[$field->name]=='required')) {
						$response .= '<label for="' . $field->name . '"' . (($stylize) ? ' style="display:block;"' : '') . '>' . $this->get_custom_text('widget_label_' . $field->name, $lang['localized_code']) . '</label>
						<input type="text" id="' . $field->name . '" name="' . $field->name . '" value=""' . (($stylize) ? ' style="display:block; margin-bottom:10px;"' : '') . ' />';
						
						if ($instance[$field->name]=='required') $response .= '<input type="hidden" value="1" name="required_' . $field->name . '" id="required_' . $field->name . '" />';
					}
				}

				$response .= '<label for="email"' . (($stylize) ? ' style="display:block;"' : '') . '>' . $this->get_custom_text('widget_label_email', $lang['localized_code']) . '</label>
						<input type="text" id="email" name="email" value=""' . (($stylize) ? ' style="display:block; margin-bottom:10px;"' : '') . ' />' . $this->getListsSelector($knews_lists, $mandatory_id) . $this->getLangHidden();
				$key = md5(date('dmY') . wp_create_nonce( 'knews-subscription' ));
				$response .= '<input type="hidden" name="knewskey" id="knewskey" value="' . $key . '" />
						<textarea name="knewscomment" id="knewscomment" style="width:150px; height:80px"></textarea>
						<input type="submit" value="' . $this->get_custom_text('widget_button', $lang['localized_code']) . '"' . (($stylize) ? ' style="display:block; margin-bottom:10px;"' : '') . ' />
						<input type="hidden" name="action" value="knewsAddUser" />
					</form>
				</div>';

				if (is_array($args)) $response .=  $args['after_widget'];
				$response .= $this->getAjaxScript('div.' . $container);
			}
			$this->knews_form_n++;
			return $response;
		}

		function printWidget($args, $instance) {
			echo $this->getForm(0, $args, $instance);
		}
		
		function htmlentities_corrected($str_in) {
			$list = get_html_translation_table(HTML_ENTITIES);
			unset($list['"']);
			unset($list['<']);
			unset($list['>']);
			unset($list['&']);
		
			$search = array_keys($list);
			$values = array_values($list);
		
			$search = array_map('utf8_encode', $search);
			$str_in = str_replace($search, $values, $str_in);
			
			return $str_in;
		}


		function sendMail($recipients, $theSubject, $theHtml, $theText='', $test_array='', $fp=false, $mobile=false) {

			$test_smtp=is_array($test_array);
			
			if (!is_array($recipients)) {
				$myobject = new stdClass;
				$myobject->email = $recipients;
				$recipients = array($myobject);
			}
			
			global $knewsOptions, $wpdb;

			if ($knewsOptions['smtp_knews']=='0' && !$test_smtp) {
				
				$headers='';

				$headers .= 'From: ' . $knewsOptions['from_name_knews'] . ' <' . $knewsOptions['from_mail_knews'] . '>' . "\r\n";
				if ($theHtml != '') add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));

			} else {
				
				//include_once (KNEWS_DIR . '/includes/class-phpmailer.php');
				//include_once (KNEWS_DIR . '/includes/class-smtp.php');
				if ( !class_exists("PHPMailer") ) require_once ABSPATH . WPINC . '/class-phpmailer.php';
				if ( !class_exists("SMTP") ) require_once ABSPATH . WPINC . '/class-smtp.php';
				
				if (!$test_smtp) {

					$mail=new PHPMailer();
					if ($knewsOptions['is_sendmail']=='1') {
						$mail->IsSendmail();
					} else {
						$mail->IsSMTP();
					}
					$mail->CharSet='UTF-8';
					$mail->Subject=$theSubject;

					if (isset ($knewsOptions['bounce_on']) && $knewsOptions['bounce_on'] == '1') $mail->Sender=$knewsOptions['bounce_user'];
					
					$mail->From = $knewsOptions['from_mail_knews'];
					$mail->FromName = $knewsOptions['from_name_knews'];
				
					$mail->Host = $knewsOptions['smtp_host_knews'];
					$mail->Port = $knewsOptions['smtp_port_knews'];
					$mail->Timeout = 30;
	
					if ($knewsOptions['smtp_user_knews']!='' || $knewsOptions['smtp_pass_knews'] != '') {
		
						$mail->SMTPAuth=true;
						$mail->Username = $knewsOptions['smtp_user_knews'];
						$mail->Password = $knewsOptions['smtp_pass_knews'];
						if ($knewsOptions['smtp_secure_knews'] != '') $mail->SMTPSecure = $knewsOptions['smtp_secure_knews'];
					}

				} else {

					$mail=new PHPMailer();
					if ($test_array['is_sendmail']=='1') {
						$mail->IsSendmail();
					} else {
						$mail->IsSMTP();
					}
					$mail->CharSet='UTF-8';
					$mail->Subject=$theSubject;

					$mail->From = $knewsOptions['from_mail_knews'];
					$mail->FromName = $knewsOptions['from_name_knews'];

					$mail->Host = $test_array['smtp_host_knews'];
					$mail->Port = $test_array['smtp_port_knews'];
					$mail->Timeout = 30;
	
					if ($test_array['smtp_user_knews']!='' || $test_array['smtp_pass_knews'] != '') {
		
						$mail->SMTPAuth=true;
						$mail->Username = $test_array['smtp_user_knews'];
						$mail->Password = $test_array['smtp_pass_knews'];
						if ($test_array['smtp_secure_knews'] != '') $mail->SMTPSecure = $test_array['smtp_secure_knews'];
					}
					
				}
				
				if (count($recipients) > 1) $mail->SMTPKeepAlive = true;
			}

			$submit_error=0;
			$submit_ok=0;
			$error_info=array();

			foreach ($recipients as $recipient) {
				$customHtml = $theHtml; $customText = $theText;

				if (isset($recipient->confirm)) {
					$customHtml=str_replace('#url_confirm#', $recipient->confirm, $customHtml);
					$customText=str_replace('#url_confirm#', $recipient->confirm, $customText);
				}
				if (isset($recipient->unsubscribe)) {
					$customHtml=str_replace('%unsubscribe_href%', $recipient->unsubscribe, $customHtml);
					$customText=str_replace('%unsubscribe_href%', $recipient->unsubscribe, $customText);
				}

				if (isset($recipient->cant_read)) {
					$customHtml=str_replace('%cant_read_href%', $recipient->cant_read, $customHtml);
					$customText=str_replace('%cant_read_href%', $recipient->cant_read, $customText);

					$customHtml=str_replace('%mobile_version_href%', $recipient->cant_read . (($mobile) ? '&m=dsk' : '&m=mbl'), $customHtml);
				}

				if (isset($recipient->tokens)) {
					foreach ($recipient->tokens as $token) {
						$customHtml=str_replace($token['token'], $token['value'], $customHtml);
						$customText=str_replace($token['token'], $token['value'], $customText);
					}
				}

				$customHtml = str_replace('#blog_name#', get_bloginfo('name'), $customHtml);
				$customText = str_replace('#blog_name#', get_bloginfo('name'), $customText);

				if (isset($recipient->confkey)) {
					$customHtml = str_replace('%confkey%', $recipient->confkey, $customHtml);
					$customText = str_replace('%confkey%', $recipient->confkey, $customText);
				}

				$customHtml = $this->htmlentities_corrected($customHtml); $customText = $this->htmlentities_corrected($customText);

				if ($knewsOptions['smtp_knews']=='0' && !$test_smtp) {

					$message = (($theHtml!='') ? $customHtml : $customText);
					
					if (strpos($recipient->email , '@knewstest.com') === false) {
						$mail_recipient = $recipient->email;
					} else {
						$mail_recipient = get_option('admin_email');
					}

					if (wp_mail($mail_recipient, $theSubject, $message, $headers)) {
						$submit_ok++;
						$error_info[]='submit ok [wp_mail()]';
						$status_submit=1;
					} else {
						$submit_error++;
						$error_info[]='wp_mail() error';
						$status_submit=2;
					}

				} else {

					if (strpos($recipient->email , '@knewstest.com') === false) {
						$mail_recipient = $recipient->email;
					} else {
						$mail_recipient = get_option('admin_email');
					}

					$mail->AddAddress($mail_recipient);

					//if ($theHtml != '') $mail->Body=utf8_encode($customHtml);
					//if ($theText != '') $mail->AltBody=utf8_encode($customText);
					if ($theHtml != '') $mail->Body=$customHtml;
					if ($theText != '') $mail->AltBody=$customText;
					if ($theHtml != '') $mail->IsHTML(true);

					if ($mail->Send()) {
						$submit_ok++;
						$error_info[]='submit ok [smtp]';
						$status_submit=1;
					} else {
						$submit_error++;
						$error_info[]=$mail->ErrorInfo . ' [smtp]';
						$status_submit=2;
					}
						
					$mail->ClearAddresses();
					$mail->ClearAttachments();
					$mail->ClearCustomHeaders();

				}

				if (count($recipients) > 1) {
					if( !ini_get('safe_mode') ) set_time_limit(25);
					echo ' ';
				}

				if (isset($recipient->unique_submit)) {
					$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " SET status=" . $status_submit . " WHERE id=" .$recipient->unique_submit;
					$result = $wpdb->query( $query );
				}
				
				if ($fp) {
					$hour = date('H:i:s', current_time('timestamp'));
					fwrite($fp, '  ' . $hour . ' | ' . $recipient->email . ' | ' . $error_info[count($error_info)-1] . "<br>\r\n");
				}
				
				if ($submit_error != 0) {
					for ($i = $submit_ok+1; $i < count($recipients); $i++) {
						if (isset($recipients[$i]->unique_submit)) {
							$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " SET status=0 WHERE id=" .$recipients[$i]->unique_submit;
							$unlock = $wpdb->query( $query );
						}
					}
					break;
				}
			}
		
			if (count($recipients) > 1 && ($knewsOptions['smtp_knews']!='0') || $test_smtp) $mail->SmtpClose();
			
			return array('ok'=>$submit_ok, 'error'=>$submit_error, 'error_info'=>$error_info);
			
		}

		function im_pro() { return false; }
		
		function read_advice() {
			global $advice;
			if ($advice !='') return $advice;
			
			$last_advice_time = get_option('knews_advice_time',0);
			$now_time = time();
			if ($now_time - $last_advice_time > 86400) {

				$response = wp_remote_get( 'http://www.knewsplugin.com/read_advice.php?v=' . KNEWS_VERSION . '&l=' . WPLANG );

			} else {
				$response = get_option('knews_advice_response', '0');
				return $response;
			}

			if( is_wp_error( $response ) ) {
				$advice='0';

			} else {
				if (isset($response['body'])) {
					$advice=$response['body'];
					if (substr($advice, 0, 7) == 'advice*') {
						if (substr($advice, 7, 1)=='0') {
							$advice='0';
						} else {
							$advice = substr($advice, 7);
						}
					} else {
						$advice = '0';
					}
				} else {
					$advice='0';
				}
			}
			//Save cache
			$advice_time = time();
			update_option('knews_advice_time', $advice_time);
			update_option('knews_advice_response', $advice);
			
			return $advice;
		}

		/******************************************************************************************
		/*                                   PANELS ADMIN
		******************************************************************************************/
		
		function KnewsAdminNews() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_news.php");
		}
		function KnewsAdminLists() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_lists.php");
		}
		function KnewsAdminUsers() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_users.php");
		}
		function KnewsAdminSubmit() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_submits.php");
		}
		function KnewsAdminImport() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_import.php");
		}
		function KnewsAdminExport() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_export.php");
		}
		function KnewsAdminStats() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_stats.php");
		}
		function KnewsAdminAuto() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_auto.php");
		}
		function KnewsAdminConfig() {
			if (! $this->initialized) $this->init();
			require( KNEWS_DIR . "/admin/knews_admin_config.php");
		}
		
		function knews_dashboard_widget(){
			include_once KNEWS_DIR . '/includes/dashboard-widget.php';
		}
		function dashboard_widget_setup(){
			if ($this->read_advice() != '0') {
				if (current_user_can('manage_options')) {
					$dashboard_widgets_order = (array)get_user_option( "meta-box-order_dashboard" );
					$all_widgets = array();
					foreach($dashboard_widgets_order as $k=>$v){
						$all_widgets = array_merge($all_widgets, explode(',', $v));
					}
					if(!in_array('knews_dash_advice', $all_widgets)){
						$install = true;
					} else {
						$install = false;
					}
					wp_add_dashboard_widget('knews_dash_advice', 'Knews Plugin Message', array($this, 'knews_dashboard_widget'), null);	
					if($install){
						$dashboard_widgets_order['side'] = 'knews_dash_advice' . ',' . @strval($dashboard_widgets_order['side']);
						$user = wp_get_current_user();
						update_user_option($user->ID, 'meta-box-order_dashboard', $dashboard_widgets_order, false);
						$dashboard_widgets_order = (array)get_user_option( "meta-box-order_dashboard" );
					}
				}
			}
		}
	}
}

//Initialize the plugin
if (!function_exists("Knews_plugin_ap")) {

	if (class_exists("KnewsPlugin")) {
		$Knews_plugin = new KnewsPlugin();
		define('KNEWS_VERSION', '1.3.0');

		function Knews_plugin_ap() {
			global $Knews_plugin;
			if (!isset($Knews_plugin)) return;
	
			if (is_admin()) {
				$Knews_plugin->knews_load_plugin_textdomain();
			}
	
			//Can't see the Knews admin menu? Try to define KNEWS_MENU_POS with another random value in your functions.php theme!!!
			$menu_order=103.2;
			if (defined('KNEWS_MENU_POS')) $menu_order=KNEWS_MENU_POS;

			$pro_menus=false;

			add_menu_page( 'K-news', $Knews_plugin->im_pro() ? 'K-news Pro' : 'K-news', 'edit_posts', 'knews_news', array(&$Knews_plugin, 'KnewsAdminNews'), plugins_url() . '/knews/images/icon16.png', $menu_order);
			add_submenu_page( 'knews_news', __('Newsletters','knews'), __('Newsletters','knews'), ($pro_menus ? 'knews_manage_newsletters' : 'edit_posts'), 'knews_news', array(&$Knews_plugin, 'KnewsAdminNews'), '');
			add_submenu_page( 'knews_news', __('Mailing lists','knews'), __('Mailing lists','knews'), ($pro_menus ? 'knews_manage_users' : 'edit_posts'), 'knews_lists', array(&$Knews_plugin, 'KnewsAdminLists'), '');
			$hook_asm = add_submenu_page( 'knews_news', __('Subscribers','knews'), __('Subscribers','knews'), ($pro_menus ? 'knews_manage_users' : 'edit_posts'), 'knews_users', array(&$Knews_plugin, 'KnewsAdminUsers'), '');
			add_submenu_page( 'knews_news', __('Submits','knews'), __('Submits','knews'), ($pro_menus ? 'knews_manage_newsletters' : 'edit_posts'), 'knews_submit', array(&$Knews_plugin, 'KnewsAdminSubmit'), '');
			add_submenu_page( 'knews_news', __('Import CSV','knews'), __('Import CSV','knews'), ($pro_menus ? 'knews_manage_users' : 'edit_posts'), 'knews_import', array(&$Knews_plugin, 'KnewsAdminImport'), '');
			add_submenu_page( 'knews_news', __('Export CSV','knews'), __('Export CSV','knews'), ($pro_menus ? 'knews_manage_users' : 'edit_posts'), 'knews_export', array(&$Knews_plugin, 'KnewsAdminExport'), '');
			add_submenu_page( 'knews_news', __('Auto-create','knews'), __('Auto-create','knews'), ($pro_menus ? 'knews_configure' : 'edit_posts'), 'knews_auto', array(&$Knews_plugin, 'KnewsAdminAuto'), '');
			add_submenu_page( 'knews_news', __('Stats','knews'), __('Stats','knews'), ($pro_menus ? 'knews_see_stats' : 'edit_posts'), 'knews_stats', array(&$Knews_plugin, 'KnewsAdminStats'), '');
			add_submenu_page( 'knews_news', __('Configuration','knews'), __('Configuration','knews'), ($pro_menus ? 'knews_configure' : 'edit_posts'), 'knews_config', array(&$Knews_plugin, 'KnewsAdminConfig'), '');

	        add_action('wp_dashboard_setup', array(&$Knews_plugin, 'dashboard_widget_setup'));
			if ($Knews_plugin->im_pro()) add_action( "load-$hook_asm", 'knews_asm_add_option' );
		}

		//WP Cron :: http://blog.slaven.net.au/2007/02/01/timing-is-everything-scheduling-in-wordpress/
		function knews_wpcron_function() {
			require(dirname(__FILE__) . '/direct/knews_cron_do.php');
			if( is_multisite() ) {
				$s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
				$url = 'http' . $s . '://' . $_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
				if(!headers_sent()) {
					//If headers not sent yet... then do php redirect
					header('Location: '.$url, true, 302);
					exit;
				} else {
					//If headers are sent... do javascript redirect... if javascript disabled, do html redirect.
					echo '<script type="text/javascript">';
					echo 'window.location.href="'.$url.'";';
					echo '</script>';
					echo '<noscript>';
					echo '<meta http-equiv="refresh" content="0;url='.$url.'" />';
					echo '</noscript>';
					exit;
				}
			}
		}
		function knews_wpcron_automate() {
			global $Knews_plugin;
			require(dirname(__FILE__) . '/includes/automated_jobs.php');
			if ($Knews_plugin->im_pro()) require(dirname(__FILE__) . '/includes/knews_bounce.php');
		}
		function knews_more_reccurences($schedules) {
			$schedules['knewstime'] = array('interval' => 600, 'display' => 'Knews 10 minutes wpcron submit');
			return $schedules;
		}
		add_filter('cron_schedules', 'knews_more_reccurences');
		add_action( 'knews_wpcron_function_hook', 'knews_wpcron_function' );
		add_action( 'knews_wpcron_automate_hook', 'knews_wpcron_automate' );

		function knews_deactivate() {
			if (wp_next_scheduled('knews_wpcron_function_hook')) wp_clear_scheduled_hook('knews_wpcron_function_hook');
			if (wp_next_scheduled('knews_wpcron_automate_hook')) wp_clear_scheduled_hook('knews_wpcron_automate_hook');
		}
		register_deactivation_hook(__FILE__, 'knews_deactivate');

		function knews_activate() {
			
			//if (!wp_next_scheduled('knews_wpcron_automate_hook')) wp_schedule_event( time(), 'twicedaily', 'knews_wpcron_automate_hook');
			if (!wp_next_scheduled('knews_wpcron_automate_hook')) wp_schedule_event( time(), 'hourly', 'knews_wpcron_automate_hook');

			$look_options = get_option('KnewsAdminOptions');
			if (isset($look_options['knews_cron'])) {
				if ($look_options['knews_cron']!='cronwp') return;
			}
			if (!wp_next_scheduled('knews_wpcron_function_hook')) wp_schedule_event( time(), 'knewstime', 'knews_wpcron_function_hook' );
		}
		register_activation_hook(__FILE__, 'knews_activate');

	}

	if (isset($Knews_plugin)) {
		add_action(basename(__FILE__), array(&$Knews_plugin, 'init'));
		add_action('admin_menu', 'Knews_plugin_ap');
		add_action("widgets_init", create_function( '', 'register_widget( "knews_widget" );' ) );
		if ($Knews_plugin->im_pro()) add_action("widgets_init", create_function( '', 'register_widget( "knewssn2_widget" );' ) );
	}

	function knews_load_jquery() {
		if (!is_admin()) wp_enqueue_script( 'jquery' );
	}    
	add_action('init', 'knews_load_jquery');
	
	function knews_admin_enqueue() {
		global $Knews_plugin;
		if ($Knews_plugin->get_safe('page')=='knews_news' || $Knews_plugin->get_safe('page')=='knews_submit') {
			add_thickbox();
		}
		//wp_enqueue_script('thickbox',null,array('jquery'));
		//wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	}
	add_action('admin_enqueue_scripts', 'knews_admin_enqueue');
	
	function knews_popup() {
		if (isset($_GET['subscription']) || isset($_GET['unsubscribe'])) {
			global $Knews_plugin;
			if (! $Knews_plugin->initialized) $Knews_plugin->init();
			require( KNEWS_DIR . '/includes/dialogs.php');
		}
	}
	add_action('wp_footer', 'knews_popup');
	
	function knews_admin_notice() {
	
		$div='<div style="background-color:#FFFBCC; border:#E6DB55 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left">';
		$div_error='<div style="background-color:#FFEBE8; border:#CC0000 1px solid; color:#555555; border-radius:3px; padding:5px 10px; margin:20px 15px 10px 0; text-align:left">';
		
		global $Knews_plugin, $knewsOptions;
		if (! $Knews_plugin->initialized) $Knews_plugin->init();

		if ($Knews_plugin->knews_admin_messages != '') {
			echo $div . $Knews_plugin->knews_admin_messages . '</div>';
		} else {

			if (strpos($_SERVER['REQUEST_URI'],'knews_news&section=edit') !== false && $knewsOptions['videotutorial'] == 'no') {
				echo $div . sprintf(__('There is a videotutorial about Knews WYSIWYG Editor, %s view it in Youtube','knews'), '<a href="http://www.youtube.com/watch?v=axDO5ZIW-9s" target="_blank">') . '</a>';
				echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=videotutorial&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
			} else {

				if (version_compare( KNEWS_VERSION, get_option('knews_version' )) < 0 || get_option('knews_pro') == 'yes') {
					if ($knewsOptions['update_knews'] == 'no' && version_compare( KNEWS_VERSION, get_option('knews_version' )) < 0) {
						echo $div_error . __('You are downgraded the version of Knews, you can lose data, please update quickly','knews');
						echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=update_knews&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
					} else {
						if (get_option('knews_pro') == 'yes') {
							if ($knewsOptions['update_pro'] == 'no') {
								echo $div;
								printf( __('You are downgraded to the free version of Knews, you can lose data, please update quickly! You can get the professional version %s here','knews'), '<a href="http://www.knewsplugin.com" target="_blank">');
								echo '</a>';
								echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=update_pro&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
							}
						}
					}
				}
			}
			
			if (strpos($_SERVER['REQUEST_URI'],'knews_config') === false) {
				if ($knewsOptions['config_knews'] == 'no') {
					
					printf($div . __('Welcome to Knews.','knews') . ' ' . __('Please, go to %s configuration page','knews') . "</a>", 
						'<a href="' . get_admin_url() . 'admin.php?page=knews_config">');
					echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=config_knews&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
			
				} else {
		
					if (!$Knews_plugin->check_multilanguage_plugin() && $knewsOptions['multilanguage_knews'] != 'off' && $knewsOptions['no_warn_ml_knews'] == 'no') {
		
						printf($div_error . __('The multilanguage plugin has stopped working.','knews') . ' ' . __('Please, go to %s configuration page','knews') . "</a>", 
							'<a href="' . get_admin_url() . 'admin.php?page=knews_config">');
						echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=no_warn_ml_knews&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
		
					} elseif ($knewsOptions['knews_cron']=='cronjob') {
						$last_cron_time = $Knews_plugin->get_last_cron_time();
						$now_time = time();
						if ($now_time - $last_cron_time > 1000 && $last_cron_time != 0 && $knewsOptions['no_warn_cron_knews'] == 'no') {
		
							printf($div_error . __('CRON has stopped working.','knews') . ' ' . __('Please, go to %s configuration page','knews') . "</a>", 
								'<a href="' . get_admin_url() . 'admin.php?page=knews_config">');
							echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsOffWarn&w=no_warn_cron_knews&b=' . urlencode('http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']) . '" style="float:right">' . __("Don't show this message again [x]",'knews') . '</a></div>';
						}
					}
				}
			}
		}
	}
	add_action( 'admin_notices', 'knews_admin_notice' );
	
	function knews_plugin_form($atts) {
		global $Knews_plugin; if (!isset($Knews_plugin)) return '';
		
		$id_list = ((isset($atts['id'])) ? intval($atts['id']) : 0);
		return $Knews_plugin->getForm($id_list, '', $atts);
	}
	add_shortcode("knews_form", "knews_plugin_form");
	
	function knews_post_options_fn() {
		global $Knews_plugin, $post, $knewsOptions, $wpdb;
		if (! $Knews_plugin->initialized) $Knews_plugin->init();
		
		$value = intval(get_post_meta($post->ID, '_knews_automated', true));
		if ($value==='') $value = $knewsOptions['def_autom_post'];
		
		echo '<p>' . __('Include this post in the automated newsletters?','knews') . ' <select name="knews_automated_post" id="knews_automated_post">';
		echo '<option value="1"' . (($value == 1) ? ' selected="selected"' : '') . '>' . __('Yes','knews') . '</option>';
		echo '<option value="0"' . (($value == 0) ? ' selected="selected"' : '') . '>' . __('No','knews') . '</option>';
		echo '</select></p>';
		
		$sql = 'SELECT kn.name, kap.id_news, kn.mobile FROM ' . KNEWS_NEWSLETTERS . ' as kn, ' . KNEWS_AUTOMATED_POSTS . ' as kap WHERE kn.mobile=0 AND kap.id_post=' . $post->ID . ' AND kap.id_news = kn.id';
		$results=$wpdb->get_results($sql);
		if (count($results) > 0) {
			echo '<p><strong>' . __('This post has been included into:','knews') . '</strong></p>';
			foreach ($results as $r) {
				echo '<p style="margin:0; padding:0;">&#8226; <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $r->id_news . '&preview=1" target="_blank">' . $r->name . '</a></p>';
			}
		} else {
			echo '<p>' . __('This post still not included into any automated newsletter.','knews') . '</p>';
		}
	}
	function knews_options_box() {
		add_meta_box('knews_post_options', __('Knews Post Options','knews'), 'knews_post_options_fn', 'post', 'side', 'core');
	}
	function knews_options_save($postID){
		global $Knews_plugin, $knewsOptions;
		if (! $Knews_plugin->initialized) $Knews_plugin->init();

		$value=$Knews_plugin->post_safe('knews_automated_post', $knewsOptions['def_autom_post'], 0, 'int');
		update_post_meta($postID, '_knews_automated', $value);
	}
	add_action('admin_menu', 'knews_options_box');
	add_action('save_post', 'knews_options_save');

	/************************************************** AJAX CALLS ******************************************/

	function knews_ajax_select_post() {
		require( dirname(__FILE__) . "/direct/select_post.php");
	}
	function knews_ajax_pick_color() {
		require( dirname(__FILE__) . "/direct/color_picker.php");
	}
	function knews_ajax_pick_font() {
		require( dirname(__FILE__) . "/wysiwyg/fontpicker/index.php");
	}
	function knews_safe_download() {
		require( dirname(__FILE__) . "/direct/download.php");
	}
	function knews_edit_news() {
		require( dirname(__FILE__) . "/direct/edit_news.php");
	}
	function knews_add_user() {
		require( dirname(__FILE__) . "/direct/knews_adduser.php");
	}
	function knews_confirm_user() {
		require( dirname(__FILE__) . "/direct/knews_confirmuser.php");
	}
	function knews_cron() {
		global $Knews_plugin;
		if ( get_current_blog_id() != $Knews_plugin->KNEWS_MAIN_BLOG_ID ) die("You must call the main blog www.yourdomain.com/wp-admin/admin-ajax.php?action=knewsCron URL");
		$cron_time = time();
		update_option('knews_cron_time', $cron_time);

		knews_cron_do();
	}
	function knews_cron_do() {
		require( dirname(__FILE__) . "/direct/knews_cron_do.php");
	}
	function knews_read_email() {
		require( dirname(__FILE__) . "/direct/knews_read_email.php");
	}
	function knews_unsubscribe() {
		require( dirname(__FILE__) . "/direct/knews_unsubscribe.php");
	}
	function knews_off_warn() {
		require( dirname(__FILE__) . "/direct/off_warn.php");
	}
	function knews_resize_img() {
		global $Knews_plugin;
	
		if (! $Knews_plugin->initialized) $Knews_plugin->init();
	
		$url_img= $Knews_plugin->get_safe('urlimg');
		$width= intval($Knews_plugin->get_safe('width'));
		$height= intval($Knews_plugin->get_safe('height'));
	
		require( dirname(__FILE__) . "/includes/resize_img.php");

		$jsondata = knews_resize_img_fn($url_img, $width, $height);
		echo json_encode($jsondata);

		die();

	}
	function knews_save_news() {
		require( dirname(__FILE__) . "/direct/save_news.php");
	}
	function knews_see_fails() {
		require( dirname(__FILE__) . "/direct/see_fails.php");
	}
	function knews_test_smtp() {
		require( dirname(__FILE__) . "/direct/test_smtp.php");
	}
	function knews_track() {
		require( dirname(__FILE__) . "/direct/track.php");
	}
	function knews_ajax_deny() {
		die();
	}

	add_action('wp_ajax_knewsSelPost', 'knews_ajax_select_post' );
	add_action('wp_ajax_nopriv_knewsSelPost', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsPickColor', 'knews_ajax_pick_color' );
	add_action('wp_ajax_nopriv_knewsPickColor', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsPickFont', 'knews_ajax_pick_font' );
	add_action('wp_ajax_nopriv_knewsPickFont', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsSafeDownload', 'knews_safe_download' );
	add_action('wp_ajax_nopriv_knewsSafeDownload', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsEditNewsletter', 'knews_edit_news' );
	add_action('wp_ajax_nopriv_knewsEditNewsletter', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsAddUser', 'knews_add_user' );
	add_action('wp_ajax_nopriv_knewsAddUser', 'knews_add_user' );

	add_action('wp_ajax_knewsConfirmUser', 'knews_confirm_user' );
	add_action('wp_ajax_nopriv_knewsConfirmUser', 'knews_confirm_user' );

	add_action('wp_ajax_knewsCron', 'knews_cron' );
	add_action('wp_ajax_nopriv_knewsCron', 'knews_cron' );

	add_action('wp_ajax_knewsCronDo', 'knews_cron_do' );
	add_action('wp_ajax_nopriv_knewsCronDo', 'knews_cron_do' );

	add_action('wp_ajax_knewsReadEmail', 'knews_read_email' );
	add_action('wp_ajax_nopriv_knewsReadEmail', 'knews_read_email' );

	add_action('wp_ajax_knewsUnsubscribe', 'knews_unsubscribe' );
	add_action('wp_ajax_nopriv_knewsUnsubscribe', 'knews_unsubscribe' );

	add_action('wp_ajax_knewsOffWarn', 'knews_off_warn' );
	add_action('wp_ajax_nopriv_knewsOffWarn', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsResizeImg', 'knews_resize_img' );
	add_action('wp_ajax_nopriv_knewsResizeImg', 'knews_ajax_deny' );
	
	add_action('wp_ajax_knewsSaveNews', 'knews_save_news' );
	add_action('wp_ajax_nopriv_knewsSaveNews', 'knews_ajax_deny' );
	
	add_action('wp_ajax_knewsSeeFails', 'knews_see_fails' );
	add_action('wp_ajax_nopriv_knewsSeeFails', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsTestSMTP', 'knews_test_smtp' );
	add_action('wp_ajax_nopriv_knewsTestSMTP', 'knews_ajax_deny' );

	add_action('wp_ajax_knewsTrack', 'knews_track' );
	add_action('wp_ajax_nopriv_knewsTrack', 'knews_track' );

	add_action('wp_ajax_knewsForceAutomated', 'knews_wpcron_automate' );
	add_action('wp_ajax_nopriv_knewsForceAutomated', 'knews_ajax_deny' );
	class knews_widget extends WP_Widget {
	
		public function __construct() {
			parent::__construct(
				'knews_widget', // Base ID
				'Knews Subscription Form Widget', // Name
				array( 'description' => __( 'Add a subscription form into the sidebar', 'knews' ), ) // Args
			);
		}
	
		public function widget( $args, $instance ) {
			global $Knews_plugin;
			$Knews_plugin->printWidget($args, $instance);
		}
	
		public function update( $new_instance, $old_instance ) {
			return $new_instance;
		}
	
		public function form( $instance ) {
			global $Knews_plugin;
			if (! $Knews_plugin->initialized) $Knews_plugin->init();			
			$extra_fields = $Knews_plugin->get_extra_fields();
			
			foreach ($extra_fields as $field) {
				$val=1;
				if (isset($instance[ $field->name ])) $val=$instance[ $field->name ];
				
				echo '<p><label for="' . $this->get_field_id($field->name) . '">' . $field->name . '</label>';
				echo '<select id="' . $this->get_field_id($field->name) . '" name="' . $this->get_field_name($field->name) . '" style="float:right;">';
				echo '<option value="off"' . (($val=="off") ? ' selected="selected"' : '') . '>' . __('Dont ask','knews') . '</option>';
				echo '<option value="ask"' . (($val=="ask") ? ' selected="selected"' : '') . '>' . __('Not required','knews') . '</option>';
				echo '<option value="required"' . (($val=="required") ? ' selected="selected"' : '') . '>' . __('Required','knews') . '</option>';
				echo '</select></p>';
			}
				
			echo '<a href="admin.php?page=knews_config&tab=custom">' . __('Customize widget messages','knews') . '</a>';
		}
	
	} // class Knews_Widget

	function knews_aj_posts_where( $where ) {
		global $knews_aj_look_date, $knewsOptions;
   		return $where . " AND " . ((intval($knewsOptions['edited_autom_post'])==1) ? 'post_modified' : 'post_date') . " > '" . $knews_aj_look_date . "' ";
	}

}

?>
