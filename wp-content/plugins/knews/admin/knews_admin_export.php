<?php
//Security for CSRF attacks
$knews_nonce_action='kn-adm-export';
$knews_nonce_name='_admexp';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	global $wpdb, $Knews_plugin, $knews_delimiters, $knews_enclosure, $knews_encode, $knews_line_endings;
//	global $knews_delimiters, $knews_enclosure, $knews_encode, $knews_line_endings, $knews_import_errors, $knews_import_users_error, $col_options, $submit_confirmation_id, $confirmation_sql_count;

    // specify allowed field delimiters
    $knews_delimiters = array(
        'comma ,'     => ',',
        'semicolon ;' => ';',
        'tab'         => "\t",
        'pipe |'         => '|',
        'colon :'     => ':',
        'space'     => ' ',
    );

    $knews_enclosure = array(
        'double'	=> '"',
        'simple'	=> '\''
    );

	function put_format_selects() {

		global $knews_delimiters, $knews_enclosure, $knews_encode, $knews_line_endings, $Knews_plugin;

		?>
			<p><?php _e('Separator','knews'); ?>: <select name="knews_delimiters" id="knews_delimiters">
				<?php
				while ($d = current($knews_delimiters)) {
					echo '<option value="' . key($knews_delimiters) . '"' . (( $Knews_plugin->post_safe('knews_delimiters') == key($knews_delimiters)) ? ' selected="selected"' : '') . '>' . key($knews_delimiters) . '</option>';
					next($knews_delimiters);
				}
				?>
			</select>
			<?php _e('Enclosure','knews'); ?>: <select name="knews_enclosure" id="knews_enclosure">
				<?php
				while ($d = current($knews_enclosure)) {
					echo '<option value="' . key($knews_enclosure) . '"' . (( $Knews_plugin->post_safe('knews_enclosure') == key($knews_enclosure)) ? ' selected="selected"' : '') . '>' . key($knews_enclosure) . '</option>';
					next($knews_enclosure);
				}
				?>
			</select>
			</p>
		<?php
	}

	$step = $Knews_plugin->post_safe('step', 1);
	$knews_has_header = $Knews_plugin->post_safe('knews_has_header', 0, 'int');
	
	$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
	$lists = $wpdb->get_results( $query );
	$languages = $Knews_plugin->getLangs();
	
	if ($step==2) {
		$no_list=true; $no_lang=true;
		
		foreach ($lists as $list) {
			if ($Knews_plugin->post_safe('list_' . $list->id) == 1) $no_list=false;
		}
		
		foreach ($languages as $lang) {
			if ($Knews_plugin->post_safe('lang_' . $lang['language_code']) == 1) $no_lang=false;
		}
		
		if ($no_lang || $no_list) {
			$step=1;
			echo '<div class="error"><p>' . __('Select at least one list and one language.','knews') . '</p></div>';
		}
	}
?>
	<div class="wrap">
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Export users','knews');?></h2>
		<?php
		$exported_users = 0;

		if ($step=='2') {
			$csv_code = '';
			$delimiter = $knews_delimiters[$Knews_plugin->post_safe('knews_delimiters')];
			$enclosure = $knews_enclosure[$Knews_plugin->post_safe('knews_enclosure')];

			if ($knews_has_header==1) {
					$csv_code .= $enclosure . __('E-mail','knews') . $enclosure . $delimiter;
					$csv_code .= $enclosure . __('Language','knews') . $enclosure . $delimiter;
					$csv_code .= $enclosure . __('State','knews') . $enclosure . $delimiter;
					$csv_code .= $enclosure . __('Lists','knews') . $enclosure . "\r\n";
			}
			
			$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
			$lists = $wpdb->get_results( $query );
			$languages = $Knews_plugin->getLangs();

			$query = "SELECT * FROM " . KNEWS_USERS;
			$users = $wpdb->get_results( $query );
			
			foreach ($users as $user) {
				$in_lists=false;
				$lists_user='';
				foreach ($lists as $list) {
					if ($Knews_plugin->post_safe('list_' . $list->id) == 1) {
						
						$query = "SELECT * FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $user->id . " AND id_list=" . $list->id;
						$subscription_found = $wpdb->get_results( $query );
					
						if (count($subscription_found)>0) {
							$in_lists=true;
							if ($lists_user != '') $lists_user .=', ';
							$lists_user .= $list->name;
						}
					}
				}
				$in_langs=false;
				if ($in_lists) {
					foreach ($languages as $lang) {
						 if ($Knews_plugin->post_safe('lang_' . $lang['language_code']) == 1) {
							 if ($user->lang == $lang['language_code']) $in_langs=true;
						 }
					}
				}
				if ($in_langs && $in_lists)  {
					$exported_users++;
					$csv_code .= $enclosure . $user->email . $enclosure . $delimiter;
					$csv_code .= $enclosure . $user->lang . $enclosure . $delimiter;
					$csv_code .= $enclosure . $user->state . $enclosure . $delimiter;
					$csv_code .= $enclosure . $lists_user . $enclosure . "\r\n";
				}
			}
			
			if ($exported_users != 0) {

				$file_name = uniqid() . '.csv';
				$fp = fopen(KNEWS_DIR . '/tmp/' . $file_name, 'w');
				if ($fp) {
					fwrite($fp, $csv_code);
				}

				echo '<div class="updated"><p>';
				printf(__('%s users have been exported.','knews'), $exported_users);
				echo '</p></div>';
				
				if ($fp) {
					echo '<p>';
					printf(__('To download the .CSV, click %s here','knews'), '<a href="' . get_admin_url() . 'admin-ajax.php?action=knewsSafeDownload&file=' . $file_name . '">');
					echo '</a></p>';
				} else {
					echo '<p>' . __("Error: I can't write the .CSV file.",'knews') . ' ' . __('The directory /wp-content/plugins/knews/tmp must be writable (chmod 700)', 'knews') . '</p>';
				}
		?>
				<textarea style="width:75%; height:400px;"><?php echo $csv_code; ?></textarea>
		<?php
			}
		}
		if ($step=='1' || ($step=='2' && $exported_users == 0)) {
			if ($step=='2') echo '<div class="updated"><p>' . __('No users match criteria','knews') . '</p></div>';
		?>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
			<input type="hidden" name="step" id="step" value="2" />

			<h3><?php 
			
			_e('1. Select the users you want to export:','knews');
			
			echo '</h3><p>';
			
			_e('1.1. Select the lists to export:','knews');
			
			echo '<blockquote>';
			
			foreach ($lists as $ln) {
				echo '<input type="checkbox" value="1" name="list_' . $ln->id . '" id="list_' . $ln->id . '" class="check_list"';
				if ($Knews_plugin->post_safe('list_' . $ln->id, 0, 'int') == 1) echo ' checked="checked"';
				echo '>' . $ln->name . '<br>';
			}
			
			echo '</blockquote></p><p>';
			
			_e('1.2. Select the languages to export:','knews');
			
			echo '<blockquote>';
			
			foreach ($languages as $lang) {
				echo '<input type="checkbox" value="1" name="lang_' . $lang['language_code'] . '"' . (($Knews_plugin->post_safe('lang_' . $lang['language_code'])==1) ? ' checked="checked"' : '') . '>' . $lang['translated_name'] . '<br>';
			}
			
			echo '</blockquote></p>';

			_e ('Note: mark all lists and the desired language/s to filter by language, or all languages and desired lists to filter by list,','knews');
			echo '<br>';
			_e ('...or all lists and all languages to export all users.','knews');

			echo '</p><h3>';

			_e('2. Format options:','knews');
			
			?></h3>
			<p><input type="checkbox" name="knews_has_header" id="knews_has_header" value="1"<?php if ( $knews_has_header==1 ) echo ' checked="checked"'; ?> /> <?php _e('Insert a header in the first row','knews'); ?></p>
			<?php
			
			put_format_selects();
			?>
			<div class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Export users','knews');?>">
			</div>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
			</form>
			<?php
		}
		?>
	</div>
