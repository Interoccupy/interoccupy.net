<?php
//Security for CSRF attacks
$knews_nonce_action='kn-admin-lists';
$knews_nonce_name='_admlist';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	global $wpdb, $Knews_plugin;

	$langs_code = array();
	$langs_name = array();

	if ($Knews_plugin->get_safe('da')=='rename') {
		$query = "UPDATE ".KNEWS_LISTS." SET name='" . $Knews_plugin->get_safe('nn') . "' WHERE id=" . $Knews_plugin->get_safe('lid', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('List name updated','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='delete') {
		$query="DELETE FROM " . KNEWS_LISTS . " WHERE id=" . $Knews_plugin->get_safe('lid', 0, 'int');
		$results = $wpdb->query( $query );
		echo '<div class="updated"><p>' . __('List deleted','knews') . '</p></div>';
	}

	if (KNEWS_MULTILANGUAGE) {
		
		$languages = $Knews_plugin->getLangs();
				
		if(!empty($languages)){
			foreach($languages as $l){
				$langs_code[] = $l['language_code'];
				$langs_name[] = $l['native_name'];
			}
		}
	}

	if (isset($_POST['action'])) {
		if ($_POST['action']=='add_list') {

			$name = $Knews_plugin->post_safe('new_list');

			$query = "SELECT * FROM " . KNEWS_LISTS . " WHERE name='" . $name . "'";
			$results = $wpdb->get_results( $query );
			
			if (count($results)==0) {
				$sql = "INSERT INTO " . KNEWS_LISTS . "(name, open, open_registered, langs, orderlist) VALUES ('" . $name . "', 0, 0, '', 99)";
				if ($wpdb->query($sql)) {
					echo '<div class="updated"><p>' . __('Mailing list created','knews') . '</p></div>';
				} else {
					echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __("can't create the mailing list",'knews') . ' : ' . $wpdb->last_error . '</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('there is already a list with this name!','knews') . '</p></div>';
			}
		} else if ($_POST['action']=='delete_lists' || $_POST['action']=='update_lists') {
			$query = "SELECT * FROM " . KNEWS_LISTS;
			$results = $wpdb->get_results( $query );
			foreach ($results as $list) {
				if (isset($_POST['find_' . $list->id])) {
					if ($_POST['action']=='delete_lists') {
						//Delete only
						if ($Knews_plugin->post_safe('batch_' . $list->id)=='1') {
							$query="DELETE FROM " . KNEWS_LISTS . " WHERE id=" . $list->id;
							$results=$wpdb->query($query);
						}
					} else if ($_POST['action']=='update_lists') {
						//Update only
						$open = (($Knews_plugin->post_safe($list->id . '_open')=='1') ? '1' : '');
						$open_registered = (($Knews_plugin->post_safe($list->id . '_open_registered')=='1') ? '1' : '');
						$order = $Knews_plugin->post_safe($list->id . '_order', 0, 'int');
						$langs='none';

						if (KNEWS_MULTILANGUAGE) {

							foreach ($langs_code as $lang_code) {
								if ($Knews_plugin->post_safe($list->id . '_' . $lang_code)=='1') {
									if ($langs == 'none') {
										$langs = '';
									} else {
										$langs .= ',';
									}
									$langs .= $lang_code;
								}
							}
						}
						
						$query  = "UPDATE ".KNEWS_LISTS." SET open='" . $open . "', open_registered = '" . $open_registered . "', orderlist = '" . $order;
						if (KNEWS_MULTILANGUAGE) $query .= "', langs='" . $langs;
						$query .= "' WHERE id=" . $list->id;
						$results=$wpdb->query($query);
					}
				}
			}
			echo '<div class="updated"><p>' . __('Mailing lists updated','knews') . '</p></div>';
		}
	}

?>
<script type="text/javascript">
function enfocar() {
	setTimeout("jQuery('#new_list').focus();", 100);
}
</script>
	<div class=wrap>
			<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Mailing lists','knews'); ?><a class="add-new-h2" href="#newlist" onclick="enfocar()"><?php _e('Create new mailing list','knews'); ?></a></h2>
				<?php
					$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
					$results = $wpdb->get_results( $query );
					if (count($results) != 0) {
				?>
					<script type="text/javascript">
					var save_link='';
					var save_id='';
					
					function rename(n) {
						if (save_id != '') rename_cancel();
						save_id = n;
						save_link = jQuery('td.name_' + n).html();
						
						jQuery('td.name_' + n).html('<input type="text" value="' + jQuery('td.name_' + n + ' strong').html() + '"><input type="button" value="Rename" class="rename_do"><input type="button" value="Cancel" class="rename_cancel">');
						
						jQuery('td.name_' + n + ' input')[0].focus();

						jQuery('input.rename_cancel').click(function() {
							rename_cancel();
							return false;
						});

						jQuery('input.rename_do').click(function() {
							location.href="admin.php?page=knews_lists&da=rename&lid=" + save_id + '&nn=' + encodeURIComponent(jQuery('td.name_' + save_id + ' input').val());
						});

						return false;
					}
					
					function rename_cancel() {
						if (save_id != '') {
							jQuery('td.name_' + save_id).html(save_link);
							save_id='';
						}
					}

					</script>

					<form method="post" action="admin.php?page=knews_lists">
					<table class="widefat">
						<thead>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th>ID</th>
								<th><?php _e('Name list','knews'); ?></th>
								<th><?php _e('Open','knews'); ?></th>
								<th><?php _e('Open for registered users','knews'); ?></th>
								<?php
									if (KNEWS_MULTILANGUAGE) {
										foreach ($langs_name as $lang_name) {
											echo '<th>' . $lang_name . '</th>';
										}
									}
								?>
								<th><?php _e('Active users','knews'); ?></th>
								<th><?php _e('Order','knews'); ?></th>
							</tr>
						</thead>
						<tbody>
				<?php
						$alt=false;
						$anyopened=false;
						$anyopened_logged=false;

						foreach ($results as $list) {

							if ($list->open == '1') $anyopened=true;
							if ($list->open_registered == '1') $anyopened_logged=true;

							echo '<tr' . (($alt) ? ' class="alt"' : '') . '><th class="check-column"><input type="checkbox" name="batch_' . $list->id . '" value="1"><input type="hidden" name="find_' . $list->id . '" value="1"></th>';
							echo '<td>' . $list->id . '</td>';
							echo '<td class="name_' . $list->id  . '"><strong>' . $list->name . '</strong>';
							echo '<div class="row-actions"><span><a href="#" title="' . __('Rename this list', 'knews') . '" onclick="rename(' . $list->id . '); return false;">' . __('Rename', 'knews') . '</a> | </span>';
							echo '<span><a href="admin.php?page=knews_users&filter_list=' . $list->id . '" title="' . __('See this list users', 'knews') . '" >' . __('See users', 'knews') . '</a> | </span>';
							echo '<span class="trash"><a href="admin.php?page=knews_lists&da=delete&lid=' . $list->id . '" title="' . __('Delete definitively this newsletter', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div></td>';

							echo '<td><input type="checkbox"' . (($list->open == '1') ? ' checked="checked"' : '') .' value="1" name="' . $list->id . '_open" id="' . $list->id . '_open" /></td>';
							echo '<td><input type="checkbox"' . (($list->open_registered == '1') ? ' checked="checked"' : '') .' value="1" name="' . $list->id . '_open_registered" id="' . $list->id . '_open_registered" /></td>';
							if (KNEWS_MULTILANGUAGE) {
								$lang_sniffer = explode(',', $list->langs);
								foreach ($langs_code as $lang_code) {
									echo '<td><input type="checkbox"' . ((in_array($lang_code, $lang_sniffer) || $list->langs=='') ? ' checked="checked"' : '') .' value="1" name="' . $list->id . '_' . $lang_code . '" id="' . $list->id . '_' . $lang_code . '" /></td>';
								}
							}

							$query = "SELECT COUNT(" . KNEWS_USERS . ".id) AS HOW_MANY FROM " . KNEWS_USERS . ", " . KNEWS_USERS_PER_LISTS . " WHERE " . KNEWS_USERS_PER_LISTS . ".id_user=" . KNEWS_USERS . ".id AND " . KNEWS_USERS . ".state='2' AND  " . KNEWS_USERS_PER_LISTS . ".id_list=" . $list->id;

							$count = $wpdb->get_results( $query );
							echo '<td>' . $count[0]->HOW_MANY . '</td>';
							
							//echo '<td align="center"><input type="checkbox" value="1" name="' . $list->id . '_delete" id="' . $list->id . '_delete" /></td>';
							echo '<td><input type="text" value="' . $list->orderlist . '" name="' . $list->id . '_order" id="' . $list->id . '_order" style="width:35px;" /></td>';
							$alt=!$alt;
						}
				?>
						<tbody>
						<tfoot>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th>ID</th>
								<th align="left"><?php _e('List name','knews');?></th>
								<th align="center"><?php _e('Open','knews');?></th>
								<th align="center"><?php _e('Open for registered users','knews');?></th>
								<?php
									if (KNEWS_MULTILANGUAGE) {
										foreach ($langs_name as $lang_name) {
											echo '<th align="center">' . $lang_name . '</th>';
										}
									}
								?>
								<th align="center"><?php _e('Active users','knews'); ?></th>
								<th><?php _e('Order','knews'); ?></th>
							</tr>
						</tfoot>
					</table>
					<?php
						if (!$anyopened) {
							echo '<div class="error"><p>' . __("Warning: if you haven't any mailing list opened, the subscription widget will not shown",'knews') . '</p></div>';
						} else {
							if (!$anyopened_logged) {
								echo '<div class="error"><p>' . __("Warning: you haven't any mailing list opened for logged users, the subscription widget will not shown until you make log out",'knews') . '</p></div>';
							}
						}
					?>
					<div class="submit">
						<select name="action">
							<option selected="selected" value="update_lists"><?php _e('Only update','knews'); ?></option>
							<option value="delete_lists"><?php _e('Only delete','knews'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply','knews'); ?>" class="button-secondary" />
					</div>
					<?php 
					//Security for CSRF attacks
					wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
					?>
					</form>
					<hr />
				<?php
					} else {
						?>
							<p><?php _e('At the moment there is no list, you can create new ones','knews'); ?></p>
						<?php
					}
				?>
					<a id="newlist"></a>
					<h2><?php _e('Create new mailing list','knews'); ?></h2>
					<form method="post" action="admin.php?page=knews_lists">
					<input type="hidden" name="action" id="action" value="add_list" />
					<p><label for="new_list"><?php _e('Mailing list','knews'); ?>:</label><input type="text" name="new_list" id="new_list" class="regular-text" /></p>
					<div class="submit">
						<input type="submit" value="<?php _e('Create a mailing list','knews'); ?>" class="button-primary" />
					</div>
					<?php 
					//Security for CSRF attacks
					wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
					?>
					</form>
					
	</div>
