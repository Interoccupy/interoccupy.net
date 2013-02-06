<?php
//Security for CSRF attacks
$knews_nonce_action='kn-usr-adm';
$knews_nonce_name='_admusr';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	global $wpdb;
	global $Knews_plugin;
	
	$languages = $Knews_plugin->getLangs(true);
	$extra_fields = $Knews_plugin->get_extra_fields();
	$filter_list = $Knews_plugin->get_safe('filter_list', 0, 'int');
	$filter_state = $Knews_plugin->get_safe('filter_state', 0, 'int');
	$search_user = $Knews_plugin->get_safe('search_user', '');
	$paged = $Knews_plugin->get_safe('paged', 1, 'int');
	
	$users=$wpdb->get_results('SELECT id FROM ' . KNEWS_USERS . ' ORDER BY id');
	$users_cf=$wpdb->get_results('SELECT ku.id FROM ' . KNEWS_USERS . ' ku, ' . KNEWS_USERS_EXTRA . ' kue WHERE kue.user_id=ku.id ORDER BY ku.id');
	
	if ($search_user != '' && count($extra_fields) != 0) {
		$full_insertion='';
		foreach ($extra_fields as $ef) {
			if ($full_insertion !='') $full_insertion .= ', ';
			$full_insertion .= '(%s, ' . $ef->id . ",'')";
		}
		$full_insertion = 'INSERT INTO ' . KNEWS_USERS_EXTRA . ' (user_id, field_id, value) VALUES ' . $full_insertion;
		$index_cf=0;
		$incomplete_users=array();
		foreach ($users as $u) {
			$ok_cf=0;
			while (isset($users_cf[$index_cf]) && $users_cf[$index_cf]->id == $u->id) {
				$ok_cf++;
				$index_cf++;
			}
			if ($ok_cf==0) {
				$query = str_replace('%s', $u->id, $full_insertion);
				$wpdb->get_results($query);
			} elseif ($ok_cf != count($extra_fields)) {
				$incomplete_users[]=$u->id;
			}
		}
		foreach ($incomplete_users as $iu) {
			foreach ($extra_fields as $ef) {
				$look = $wpdb->get_results('SELECT * FROM ' . KNEWS_USERS_EXTRA . ' WHERE user_id=' . $iu . ' AND field_id=' . $ef->id);
				if (count($look) == 0) {
					$wpdb->get_results( 'INSERT INTO ' . KNEWS_USERS_EXTRA . ' (user_id, field_id, value) VALUES (' . $iu . ', ' . $ef->id . ", '')");
				}
			}
		}
	}
	$results_per_page=20;
	$mass=0;
	$link_params = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	//'admin.php?page=knews_users&filter_list='.$filter_list.'&filter_state='.$filter_state.'&search_user='.$search_user.'&paged=';

	$filtered_query = "FROM " . KNEWS_USERS . " ku";
	$ef_order=0;
	$where=false;
	
	if ($Knews_plugin->get_safe('orderby') != '' && $Knews_plugin->get_safe('orderby') != 'email') {
		foreach ($extra_fields as $ef) {
			if ($ef->name==$Knews_plugin->get_safe('orderby')) {
				$ef_order = $ef->id;
				break;
			}
		}
	}

	if (($search_user != '' && count($extra_fields) > 0) || $ef_order != 0) {
		$filtered_query .= ', ' . KNEWS_USERS_EXTRA . " kue";					
	}
	if ($filter_list != 0) {
		$where=true;
		$filtered_query .= ", " . KNEWS_USERS_PER_LISTS . " kupl WHERE ku.id = kupl.id_user AND kupl.id_list=" . $filter_list;
	
	}
	if ($search_user != '') {
		if ($where) {
			$filtered_query .= " AND";
		} else {
			$filtered_query .= " WHERE";
			$where=true;
		}
		if (count($extra_fields) > 0) {
			$filtered_query .= " ( ku.email LIKE '%" . $search_user . "%'";
			foreach ($extra_fields as $ef) {
				$filtered_query .= " OR (kue.value LIKE '%" . $search_user . "%' AND kue.field_id=" . $ef->id . ")";
			}
			$filtered_query .= ") AND kue.user_id=ku.id AND kue.field_id=" . (($ef_order == 0) ? $extra_fields[0]->id : $ef_order);
		} else {
			$filtered_query .= " ku.email LIKE '%" . $search_user . "%'";
		}
	}
	if ($filter_state != 0) {
		if ($where) {
			$filtered_query .= " AND ";
		} else {
			$filtered_query .= " WHERE ";
			$where=true;
		}
		$filtered_query .= "ku.state=" . $filter_state;
	}
	if ($Knews_plugin->get_safe('orderby') != '') {
		if ($Knews_plugin->get_safe('orderby') == 'email') {
			$filtered_query .= ' ORDER BY ku.email ' . $Knews_plugin->get_safe('order', 'asc');
		} elseif ($ef_order != 0) {
			if ($search_user == '') {
				if ($where) {
					$filtered_query .= " AND ";
				} else {
					$filtered_query .= " WHERE ";
					$where=true;
				}
				$filtered_query .= "ku.id = kue.user_id AND kue.field_id=" . $ef_order ;
			}
			$filtered_query .= " ORDER BY kue.value " . $Knews_plugin->get_safe('order', 'asc');
		}
	}

	$query = "SELECT id, name FROM " . KNEWS_LISTS . " ORDER BY orderlist";
	$lists_name = $wpdb->get_results( $query );
	$lists_indexed=array();
	foreach ($lists_name as $ln) {
		$lists_indexed[$ln->id] = $ln->name;
	}
	
	if ($Knews_plugin->get_safe('da')=='activate') {
		$query = "UPDATE ".KNEWS_USERS." SET state='2' WHERE id=" . $Knews_plugin->get_safe('uid', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('User data updated','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='block') {
		$query = "UPDATE ".KNEWS_USERS." SET state='3' WHERE id=" . $Knews_plugin->get_safe('uid', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('User data updated','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='delete') {
		$query="DELETE FROM " . KNEWS_USERS . " WHERE id=" . $Knews_plugin->get_safe('uid', 0, 'int');
		$results = $wpdb->query( $query );
		echo '<div class="updated"><p>' . __('User deleted','knews') . '</p></div>';
	}
	if ($Knews_plugin->get_safe('da')=='bounce') {
		$query = "UPDATE ".KNEWS_USERS." SET state='4' WHERE id=" . $Knews_plugin->get_safe('uid', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('User data updated','knews') . '</p></div>';
	}
	
	if (isset($_POST['action'])) {
		if ($Knews_plugin->post_safe('action')=='update_user') {
			
			$email = $Knews_plugin->post_safe('email');
			$state = $Knews_plugin->post_safe('state');
			$lang = $Knews_plugin->post_safe('lang');
			$id=$Knews_plugin->post_safe('id_user', 0, 'int');

			$query = "UPDATE ".KNEWS_USERS." SET email='" . $email . "', state='" . $state . "', lang='" . $lang . "' WHERE id=" . $id;
			$result=$wpdb->query( $query );
			
			$query="DELETE FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $id;
			$results = $wpdb->query( $query );

			foreach ($lists_name as $ln) {
				if (isset($_POST['list_'.$ln->id])) {
					if ($_POST['list_'.$ln->id]=='1') {

						$query="INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $id . ", " . $ln->id . ")";
						$results = $wpdb->query( $query );
						
					}
				}
			}
			
			foreach ($extra_fields as $ef) {
				$Knews_plugin->set_user_field ($id, $ef->id, $Knews_plugin->post_safe('cf_' . $ef->id));
			}

			echo '<div class="updated"><p>' . __('User data updated','knews') . '</p></div>';

		} else if ($_POST['action']=='delete_users') {
			
			$query = 'SELECT id FROM ' . KNEWS_USERS;
			if ($mass==1) $query = 'SELECT ku.id ' . $filtered_query;
			$result=$wpdb->get_results( $query );
			
			$n_users=0;
			foreach ($result as $look_user) {

				if ($mass==1 || $Knews_plugin->post_safe('batch_' . $look_user->id, 0, 'int') == 1) {
					$n_users++;
					$query= 'DELETE FROM ' . KNEWS_USERS . ' WHERE id=' . $look_user->id;
					$delete=$wpdb->query( $query );
				}
			}
			
			echo '<div class="updated"><p>' . sprintf(__('%s users deleted.','knews'), $n_users) . '</p></div>';
			
		} else if ($_POST['action']=='add_user') {
		
			$lang = $Knews_plugin->post_safe('lang');
			$email = $Knews_plugin->post_safe('email');
			$date = $Knews_plugin->get_mysql_date();
			$confkey = $Knews_plugin->get_unique_id();
			$id_list_news = $Knews_plugin->post_safe('id_list_news', 0, 'int');
			$submit_confirm = $Knews_plugin->post_safe('submit_confirm', 0, 'int');
			
			if ($submit_confirm==1) {
				$state='1';
			} else {
				$state='2';
			}	

			if ($Knews_plugin->validEmail($email)) {
				
				$query = "SELECT * FROM " . KNEWS_USERS . " WHERE email='" . $email . "'";
				$user_found = $wpdb->get_results( $query );
	
	
				if (count($user_found)==0) {
					$query = "INSERT INTO " . KNEWS_USERS . " (email, lang, state, joined, confkey) VALUES ('" . $email . "','" . $lang . "', $state, '" . $date . "','" . $confkey . "');";
					$results = $wpdb->query( $query );
	
					if ($results) {
						$query = "INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (LAST_INSERT_ID(), " . $id_list_news . ");";
						$results = $wpdb->query( $query );

						if ($submit_confirm) {
							
							$lang_locale = $Knews_plugin->localize_lang($languages, $lang);
												
							if ($Knews_plugin->submit_confirmation ($email, $confkey, $lang_locale)) {
								echo '<div class="updated"><p>' . __('The user has been added and an e-mail confirmation has been sent','knews') . '</p></div>';
							} else {
								echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('The user has been added but an error occurred in sending e-mail confirmation','knews') . '</p></div>';
							}
						} else {
							echo '<div class="updated"><p>' . __('The user has been added and from now on he will receive the newsletters','knews') . '</p></div>';
						}
					} else {
						echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('The user has not been added','knews') . '</p></div>';
					}
				} else {
					echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('The user was already introduced','knews') . '</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('Wrong e-mail','knews') . '.</p></div>';
			}
		}
	}
?>
	<div class=wrap>
<?php 
	if ($Knews_plugin->im_pro()) {
?>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2 class="nav-tab-wrapper"><a href="admin.php?page=knews_users" class="nav-tab nav-tab-active"><?php _e('Subscribers','knews'); ?></a><a href="admin.php?page=knews_users&tab=extra_fields" class="nav-tab"><?php _e('Extra fields','knews'); ?></a></h2>
<?php 
	} else {
?>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Subscribers','knews'); ?></h2>
<?php
	}
			$edit_user = $Knews_plugin->get_safe('edit_user', 0, 'int');
			
			if ($edit_user!=0) {
				//Edit user
				$query = "SELECT id, email, state, lang FROM " . KNEWS_USERS . ' WHERE id=' . $edit_user;
				$users = $wpdb->get_results( $query );
	
				if (count($users) != 0) {
					?>
					<form action="admin.php?page=knews_users" method="post">
					<input type="hidden" name="action" id="action" value="update_user" />
					<input type="hidden" name="id_user" id="id_user" value="<?php echo $edit_user; ?>" />
					<p>&nbsp;</p>
					<table class="wp-list-table widefat" style="width:400px;">
						<thead><th><?php _e('Field','knews');?></th><th><?php _e('Value','knews');?></th></thead>
						<tr><td>E-mail:</td><td><input type="text" name="email" id="email" value="<?php echo $users[0]->email; ?>" class="regular-text" /></td>
						<?php
						$lang_listed = false;
						foreach ($languages as $l) {
							if ($l['language_code'] == $users[0]->lang) $lang_listed = true;
						}
						
						if (!$lang_listed) $languages[$users[0]->lang] = array ('translated_name'=>  __('Inactive language','knews') . ' (' . $users[0]->lang . ')', 'language_code'=>$users[0]->lang);
						
						if (count($languages) > 1) {
							
							echo '<tr><td>' . __('Language','knews') . ':</td><td><select name="lang" id="lang">';
							foreach($languages as $l){
								echo '<option value="' . $l['language_code'] . '"' . ( ($users[0]->lang==$l['language_code']) ? ' selected="selected"' : '' ) . '>' . $l['translated_name'] . '</option>';
							}
							echo '</select></td></tr>';
			
						} else if (count($languages) == 1) {
							foreach ($languages as $l) {
								echo '<input type="hidden" name="lang" id="lang" value="' . $l['language_code'] . '" />';
							}
						} else {
							echo "<p>" . __('Error','knews') . ": " . __('Language not detected!','knews') . "</p>";
						}
						?>
						<tr><td><?php _e('State','knews');?>:</td><td><select name="state" id="state">
							<option value="1"<?php if ($users[0]->state=='1') echo ' selected="selected"'; ?>><?php _e('not confirmed','knews');?></option>
							<option value="2"<?php if ($users[0]->state=='2') echo ' selected="selected"'; ?>><?php _e('confirmed','knews');?></option>
							<option value="3"<?php if ($users[0]->state=='3') echo ' selected="selected"'; ?>><?php _e('blocked','knews');?></option>
							<?php if ($Knews_plugin->im_pro()) { ?><option value="4"<?php if ($users[0]->state=='4') echo ' selected="selected"'; ?>><?php _e('Bounced','knews');?></option><?php } ?>
						</select></td></tr>
						<?php
						foreach ($extra_fields as $ef) {
							echo '<tr><td>' . $ef->name . '</td><td><input type="text" name="cf_' . $ef->id . '" id="cf_' . $ef->id . '" value="' . $Knews_plugin->get_user_field($edit_user, $ef->id) . '" class="regular-text" ></td></tr>';
						}						?>
						<tr><td colspan="2"><?php _e('Subscriptions','knews');?>:</td></tr>
						<?php
						$query = "SELECT id_list FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $edit_user;
						$lists = $wpdb->get_results( $query );
						foreach ($lists_name as $ln) {
							$active=false;
							foreach ($lists as $list_user) {
								if ($list_user->id_list==$ln->id) $active=true;
							}
							echo '<tr><td>&nbsp;</td><td><input type="checkbox" value="1" name="list_' . $ln->id . '" id="list_' . $ln->id . '"' . (($active) ? ' checked="checked"' : '') . '>' . $ln->name . '</td></tr>';
						}
						?>
						<tfoot><th><?php _e('Field','knews');?></th><th><?php _e('Value','knews');?></th></tfoot>
					</table>
					<div class="submit">
						<input type="submit" value="<?php _e('Update user','knews');?>" class="button-primary" />&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php _e('Go back','knews');?>" onclick="window.history.go(-1)" class="button-secondary" />
					</div>
					<?php 
					//Security for CSRF attacks
					wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
					?>
					</form>
					<?php	
				} else {
					echo '<p>' . __('User not found','knews') . '</p>';
				}
				
			} else {
				//List users
				//echo '<p>' . 'SELECT ku.* ' . $filtered_query . ' LIMIT ' . $results_per_page . ' OFFSET ' . $results_per_page * ($paged - 1) . '</p>';
				$users = $wpdb->get_results( 'SELECT ku.* ' . $filtered_query . ' LIMIT ' . $results_per_page . ' OFFSET ' . $results_per_page * ($paged - 1) );
				
				$filtered_users = $wpdb->get_results( 'SELECT COUNT(*) AS n ' . $filtered_query );
				$filtered_users = $filtered_users[0]->n;
				$total_users = $wpdb->get_results( 'SELECT COUNT(*) AS n FROM ' . KNEWS_USERS);
				$total_users = $total_users[0]->n;
				?>
				<div class="top" style="height:40px; overflow:hidden;">
					<div class="alignleft actions">
						<ul class="subsubsub">
							<li class="all"><a <?php if ($filtered_users == $total_users) echo 'class="current" '; ?>href="admin.php?page=knews_users"><?php _e('All subscribers','knews'); ?> <span class="count">(<?php echo $total_users; ?>)</span></a></li>
						</ul>
					</div>
					<div class="alignright actions">
						<form action="admin.php" method="get">
							<input type="hidden" name="page" id="page" value="knews_users" />
							<p><?php _e('Find','knews');?>: <input type="text" name="search_user" id="search_user" value="<?php echo $search_user; ?>" /><input type="submit" value="<?php _e('Find','knews');?>" class="button-secondary" /></p>
							<?php
							if ($Knews_plugin->get_safe('orderby') != '' && $Knews_plugin->get_safe('order') != '') {
								echo '<input type="hidden" name="orderby" id="orderby" value="' . $Knews_plugin->get_safe('orderby') . '" />';
								echo '<input type="hidden" name="order" id="order" value="' . $Knews_plugin->get_safe('order') . '" />';
							}
							if ($filter_list != 0) {
								echo '<input type="hidden" name="filter_list" id="filter_list" value="' . $filter_list . '" />';
							}
							if ($filter_state != 0) {
								echo '<input type="hidden" name="filter_state" id="filter_state" value="' . $filter_state . '" />';
							}
							?>
						</form>
					</div>
				</div>
				<div class="tablenav">
				<div class="alignleft actions">
					<form action="admin.php" method="get">
						<input type="hidden" name="page" id="page" value="knews_users" />
						<?php _e('Filter by mailing list','knews'); ?>: <select name="filter_list" id="filter_list" style="float:none">
						<option value="0"<?php if ($filter_list==0) echo ' selected="selected"'; ?>><?php _e('All','knews');?></option>
						<?php
							foreach ($lists_name as $ln) {
								echo '<option value="' . $ln->id . '"' . (($filter_list == $ln->id) ? ' selected="selected"' : '') . '>' . $ln->name . '</option>';
							}
						?>
						</select>&nbsp;&nbsp;&nbsp;
						<?php _e('Filter by state','knews'); ?>: <select name="filter_state" id="filter_state" style="float:none">
							<option value="0"<?php if ($filter_state==0) echo ' selected="selected"'; ?>><?php _e('All','knews'); ?></option>
							<option value="1"<?php if ($filter_state==1) echo ' selected="selected"'; ?>><?php _e('Not confirmed','knews'); ?></option>
							<option value="2"<?php if ($filter_state==2) echo ' selected="selected"'; ?>><?php _e('Confirmed','knews'); ?></option>
							<option value="3"<?php if ($filter_state==3) echo ' selected="selected"'; ?>><?php _e('Blocked','knews'); ?></option>
							<?php if ($Knews_plugin->im_pro()) { ?><option value="4"<?php if ($filter_state==4) echo ' selected="selected"'; ?>><?php _e('Bounced','knews'); ?></option><?php } ?>
						</select>
						<input type="submit" value="<?php _e('Filter','knews'); ?>" class="button-secondary" />
						<?php
						if ($Knews_plugin->get_safe('orderby') != '' && $Knews_plugin->get_safe('order') != '') {
							echo '<input type="hidden" name="orderby" id="orderby" value="' . $Knews_plugin->get_safe('orderby') . '" />';
							echo '<input type="hidden" name="order" id="order" value="' . $Knews_plugin->get_safe('order') . '" />';
						}
						if ($search_user != '') {
							echo '<input type="hidden" name="search_user" id="search_user" value="' . $search_user . '" />';
						}
						?>
					</form>
				</div>				
				<?php 
				knews_pagination($paged, ceil($filtered_users/ $results_per_page), $filtered_users);
				?>
				</div>
				<?php if (count($users) != 0) {	?>
				<form action="<?php echo $link_params;?>" method="post">
				<?php
					$alt=false;
					?>
					<table class="widefat"><thead><tr><th class="manage-column column-cb check-column"><input type="checkbox" /></th>
					<?php
					knews_th_orderable('E-mail','email','asc');
					$colspan=4;
					foreach ($extra_fields as $ef) {
						if ($ef->show_table == 1) {
							knews_th_orderable($ef->name, $ef->name, 'asc');
							$colspan++;
						}
					}

					echo '<th>' . __('Language','knews') . '</th><th>' . __('State','knews') . '</th><th>' . __('Subscriptions','knews') . '</th></tr></thead><tbody>';
	
					foreach ($users as $user) {
						$query = "SELECT id_list FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $user->id;
						$lists = $wpdb->get_results( $query );
						
						echo '<tr' . (($alt) ? ' class="alt"' : '') . '><th class="check-column"><input type="checkbox" name="batch_' . $user->id . '" value="1"></th>' . 
						'<td><strong><a href="admin.php?page=knews_users&edit_user=' . $user->id . '">' . $user->email . '</a></strong>';
						
						echo '<div class="row-actions"><span><a title="' . __('Edit this user', 'knews') . '" href="admin.php?page=knews_users&edit_user=' . $user->id . '">' . __('Edit', 'knews') . '</a> | </span>';
						
						if ($user->state!=2) echo '<span><a href="' . $link_params . '&da=activate&uid=' . $user->id . '" title="' . __('Activate this user', 'knews') . '">' . __('Activate', 'knews') . '</a> | </span>';							
						if ($user->state!=3) echo '<span><a href="' . $link_params . '&da=block&uid=' . $user->id . '" title="' . __('Block this user', 'knews') . '">' . __('Block', 'knews') . '</a> | </span>';
						if ($user->state!=4 && $Knews_plugin->im_pro()) echo '<span><a href="' . $link_params . '&da=bounce&uid=' . $user->id . '" title="' . __('Mark as bounced email', 'knews') . '">' . __('Bounce', 'knews') . '</a> | </span>';						
						
						echo '<span class="trash"><a href="' . $link_params . '&da=delete&uid=' . $user->id . '" title="' . __('Delete definitively this user', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div></td>';

						reset($extra_fields);
						foreach ($extra_fields as $ef) {
							if ($ef->show_table == 1) echo '<td>' . $Knews_plugin->get_user_field($user->id, $ef->id, '&nbsp;') . '</td>';
						}
						echo '<td>' . (($user->lang!='') ? $user->lang : '/') . '</td><td>';
						if ($user->state==1) echo '<img src="' . KNEWS_URL . '/images/yellow_led.gif" width="20" height="20" title="' . __('Unconfirmed','knews') . '" /></td>';
						if ($user->state==2) echo '<img src="' . KNEWS_URL . '/images/green_led.gif" width="20" height="20" title="' . __('Confirmed','knews') . '" /></td>';
						if ($user->state==3) echo '<img src="' . KNEWS_URL . '/images/red_led.gif" width="20" height="20" title="' . __('Blocked','knews') . '" /></td>';
						if ($user->state==4) echo '<img src="' . KNEWS_URL . '/images/gray_led.gif" width="20" height="20" title="' . __('Bounced','knews') . '" /></td>';
						echo '</td><td>';
						
						if (count($lists) != 0) {
							$first_comma=true;
							foreach ($lists as $list) {
								if (!$first_comma) echo ', ';
								if (isset($lists_indexed[$list->id_list])) {
									echo $lists_indexed[$list->id_list];
								} else {
									echo '<i>';
									_e('deleted list','knews');
									echo '</i>';
								}
								$first_comma=false;
							}
						}
						//echo '<td><input type="checkbox" value="1" name="user_delete_' . $user->id . '" id="user_delete_' . $user->id . '"></td>';
						echo '</tr>';
	
						$alt=!$alt;
					}

					echo '</tbody><tfoot><tr><th class="manage-column column-cb check-column"><input type="checkbox" /></th>';
					
					knews_th_orderable('E-mail','email','asc');

					reset($extra_fields);
					foreach ($extra_fields as $ef) {
						if ($ef->show_table == 1) knews_th_orderable($ef->name, $ef->name, 'asc');
					}
					echo '<th>' . __('Language','knews') . '</th><th>' . __('State','knews') . '</th><th>' . __('Subscriptions','knews') . '</th></tr></tfoot>';
					echo '</table>';
				?>
					<div class="tablenav bottom">
						<div class="alignleft actions">
						<select name="action" id="batch_action">
							<option selected="selected" value=""><?php _e('Batch actions','knews'); ?></option>
							<option value="delete_users"><?php _e('Delete','knews'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply','knews'); ?>" class="button-secondary" />
						</div>
				<?php 
				//Security for CSRF attacks
				wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
				?>
				<?php knews_pagination($paged, ceil($filtered_users/ $results_per_page), $filtered_users); ?>
					</div>
				</form>
				<?php
				} else {
					echo '<p>&nbsp;</p>';
					if ($total_users != 0) {
						echo '<p>' . __('No users match the search criteria','knews') . '</p>';
						echo '<p><input type="button" class="button" value="Reset criteria" onclick="location.href=\'admin.php?page=knews_users\'"></p>';
					} else {
						echo '<p>' . __('There are not yet users','knews') . '</p>';
					}
					echo '<p>&nbsp;</p>';
				}
			?>
		<br />
		<hr />
		<h2><?php _e('Create a subscriber manually','knews'); ?></h2>
		<form action="admin.php?page=knews_users" method="post">
		<input type="hidden" name="action" id="action" value="add_user" />
		<p>E-mail: <input type="text" name="email" id="email" class="regular-text" /></p>
		<?php
			if (count($languages) > 1) {
				
				echo '<p>' . __('Language','knews') . ': <select name="lang" id="lang">';
				foreach($languages as $l){
					echo '<option value="' . $l['language_code'] . '">' . $l['translated_name'] . '</option>';
				}
				echo '</select></p>';

			} else if (count($languages) == 1) {
				foreach ($languages as $l) {
					echo '<input type="hidden" name="lang" id="lang" value="' . $l['language_code'] . '" />';
				}
			} else {
				echo "<p>" . __('Error','knews') . ": " . __('Language not detected!','knews') . "</p>";
			}

			$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
			$results = $wpdb->get_results( $query );

			if (count($results) > 1) {
				echo '<p>' . __('Mailing list','knews') . ': <select name="id_list_news" id="id_list_news">';
				foreach ($results as $list) {
					echo '<option value="' . $list->id . '">' . $list->name . '</option>';
				}
				echo '</select></p>';
			} else if (count($results) == 1) {
				echo '<input type="hidden" name="id_list_news" id="id_list_news" value="' . $results[0]->id . '">';
			}
		?>
		<p><input type="radio" name="submit_confirm" id="submit_confirm_yes" value="1" checked="checked" /><?php _e('Send e-mail confirmation','knews');?> | <input type="radio" name="submit_confirm" id="submit_confirm_no" value="0" /><?php _e("Activate user directly (don't send e-mail confirmation)",'knews');?></p>
		<div class="submit">
			<input type="submit" value="<?php _e('Create a user','knews'); ?>" class="button-primary" />
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
<?php

function knews_th_orderable ($label, $orderby, $order) {
	global $Knews_plugin;
	
	if ($Knews_plugin->get_safe('orderby') == $orderby) {
		$order = (($Knews_plugin->get_safe('order')=='asc') ? 'desc' : 'asc');
		$sortable = 'sorted';
	} else {
		$sortable = 'sortable';
	}
	$sorted = (($order=='asc') ? 'desc' : 'asc');
	
	$current_url = ( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	$current_url = remove_query_arg( array( 'orderby', 'order' ), $current_url );
	$current_url = add_query_arg( 'orderby', $orderby, $current_url );
	$current_url = add_query_arg( 'order', $order, $current_url );
	
	echo '<th class="manage-column ' . $sortable . ' ' . $sorted . '"><a href="' . esc_url( $current_url ) . '"><span>' . $label . '</span><span class="sorting-indicator"></span></a></th>';
}

function knews_pagination($paged, $maxPage, $items) {
	//$link_params .= '&paged=';
	//$maxPage=ceil(count($users) / $results_per_page);
	$link_params = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	$link_params = remove_query_arg('paged', $link_params) . '&paged=';

		
?>		
		<div class="tablenav-pages">
			<span class="displaying-num"><?php echo $items; ?> items</span>
<?php
	if ($maxPage > 1) {
		 /*<span class="displaying-num"><?php echo count($users); ?> <?php _e('users','knews'); ?></span>*/ ?>
		<?php if ($paged > 1) { ?>
		<a href="<?php echo $link_params; ?>1" title="<?php _e('Go to first page','knews'); ?>" class="first-page">&laquo;</a>
		<a href="<?php echo $link_params . ($paged-1); ?>" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page">&lsaquo;</a>
		<?php } else { ?>
		<a href="#" title="<?php _e('Go to first page','knews'); ?>" class="first-page disabled">&laquo;</a>
		<a href="#" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page disabled">&lsaquo;</a>
		<?php } ?>
		<span class="paging-input"><?php echo $paged; ?> <?php _e('of','knews'); ?> <span class="total-pages"><?php echo $maxPage; ?></span></span>
		<?php if ($maxPage > $paged) { ?>
		<a href="<?php echo $link_params . ($paged+1); ?>" title="<?php _e('Go to next page','knews'); ?>" class="next-page">&rsaquo;</a>
		<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to last page','knews'); ?>" class="last-page">&raquo;</a>
		<?php } else { ?>
		<a href="#" title="<?php _e('Go to next page','knews'); ?>" class="next-page disabled">&rsaquo;</a>
		<a href="#" title="<?php _e('Go to last page','knews'); ?>" class="last-page disabled">&raquo;</a>					
<?php 
		}
	}
?>
		</div>
	<br class="clear">
<?php
}

?>