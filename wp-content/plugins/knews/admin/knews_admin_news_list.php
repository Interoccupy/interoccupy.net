<?php
//Security for CSRF attacks
$knews_nonce_action='kn-news-list';
$knews_nonce_name='_newslist';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

function duplicate_news($results) {

	global $wpdb, $Knews_plugin;
	
	if ($results) {
		
		$sql = "INSERT INTO " . KNEWS_NEWSLETTERS . "(name, subject, created, modified, template, html_mailing, html_head, html_modules, html_container, lang, automated, mobile, id_mobile) VALUES ('(copy)" . mysql_real_escape_string($results[0]->name) . "', '" . mysql_real_escape_string($results[0]->subject) . "', '" . $Knews_plugin->get_mysql_date() . "', '" . $Knews_plugin->get_mysql_date() . "','" . $results[0]->template . "','" . mysql_real_escape_string($results[0]->html_mailing) . "','" . mysql_real_escape_string($results[0]->html_head) . "','" . mysql_real_escape_string($results[0]->html_modules) . "','" . mysql_real_escape_string($results[0]->html_container) . "', '" . $results[0]->lang . "', 0, " . $results[0]->mobile . ", " . $results[0]->id_mobile . ")";
			
		$results = $wpdb->query($sql);
		
		if ($results) {

			$news_id=$wpdb->insert_id; $news_id2=mysql_insert_id(); if ($news_id==0) $news_id=$news_id2;
			return $news_id;

		}
	}
	return false;
}


	//global $Knews_plugin;
	require_once( KNEWS_DIR . '/includes/knews_util.php');

	$languages = $Knews_plugin->getLangs(true);

	$tab=$Knews_plugin->get_safe('tab');

	if ($Knews_plugin->get_safe('da')=='rename') {
		$query = "UPDATE ".KNEWS_NEWSLETTERS." SET name='" . $Knews_plugin->get_safe('nn') . "' WHERE id=" . $Knews_plugin->get_safe('nid', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . __('Newsletter name updated','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='delete') {
		$query="DELETE FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $Knews_plugin->get_safe('nid', 0, 'int');
		$results = $wpdb->query( $query );
		echo '<div class="updated"><p>' . __('Newsletter deleted','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('da')=='duplicate') {
		
		$query="SELECT * FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $Knews_plugin->get_safe('did', 0, 'int');
		$results = $wpdb->get_results( $query );

		if ($new=duplicate_news($results)) {
			echo '<div class="updated"><p>' . __('Newsletter duplicated','knews') . '</p></div>';
		}
	}

	if ($Knews_plugin->post_safe('action')=='delete_news') {
		$query = "SELECT * FROM " . KNEWS_NEWSLETTERS;
		$results = $wpdb->get_results( $query );
		foreach ($results as $list) {
			if ($Knews_plugin->post_safe('batch_' . $list->id)=='1') {
				$query="DELETE FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $list->id;
				$results=$wpdb->query($query);
			}
		}
		echo '<div class="updated"><p>' . __('Newsletter list updated','knews') . '</p></div>';
	}

?>
<script type="text/javascript">
function enfocar() {
	setTimeout("jQuery('#new_news').focus();", 100);
}
</script>
	<div class=wrap>
			<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div>
			<h2 class="nav-tab-wrapper"><a class="nav-tab<?php if ($tab=='') echo ' nav-tab-active'; ?>" href="admin.php?page=knews_news"><?php _e('Manual Newsletters','knews');?></a><a class="nav-tab<?php if ($tab=='auto') echo ' nav-tab-active'; ?>" href="admin.php?page=knews_news&tab=auto"><?php _e('Auto-created Newsletters','knews'); ?></a></h2>
			<?php 
					$paged = $Knews_plugin->get_safe('paged', 1, 'int');
									
					if ($tab=='') {
						echo '<p><a class="add-new-h2" href="#newnews" onclick="enfocar()">' . __('Create new newsletter','knews') . '</a></p>';
						$results_per_page=10;
					} else {
						echo '<p>&nbsp;</p>';
						$results_per_page=20;
					}

					$query = "SELECT id, name, created, modified, template, lang, id_mobile FROM " . KNEWS_NEWSLETTERS . " WHERE mobile=0 AND automated=" . (($tab=='') ? '0' : '1') . " ORDER BY modified DESC";
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
						
						jQuery('td.name_' + n).html('<input type="text" value="' + jQuery('td.name_' + n + ' a').html() + '"><input type="button" value="Rename" class="rename_do"><input type="button" value="Cancel" class="rename_cancel">');
						
						jQuery('td.name_' + n + ' input')[0].focus();

						jQuery('input.rename_cancel').click(function() {
							rename_cancel();
							return false;
						});

						jQuery('input.rename_do').click(function() {
							location.href="admin.php?page=knews_news&da=rename&nid=" + save_id + '&nn=' + encodeURIComponent(jQuery('td.name_' + save_id + ' input').val());
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
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
					<table class="widefat">
						<thead>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th align="left"><?php _e('Newsletter name','knews');?></th>
								<th><?php _e('Created','knews');?></th>
								<th><?php _e('Modified','knews');?></th>
								<th><?php _e('Template','knews');?></th>
								<th><?php _e('Language','knews');?></th>
							</tr>
						</thead>
						<tbody>
				<?php
						$alt=false;
						$results_counter=0;
						foreach ($results as $list) {
							$results_counter++;
							if ($results_per_page * ($paged-1)<$results_counter) {
								echo '<tr' . (($alt) ? ' class="alt"' : '') . '><th class="check-column"><input type="checkbox" name="batch_' . $list->id . '" value="1"></th>';
								echo '<td class="name_' . $list->id  . '"><strong><a href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . $list->name . '</a></strong>';
	
								echo '<div class="row-actions" style="position:absolute;"><span><a title="' . __('Edit this newsletter', 'knews') . '" href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . __('Edit', 'knews') . '</a> | </span>';
								
								echo '<span><a href="#" title="' . __('Rename this newsletter', 'knews') . '" onclick="rename(' . $list->id . '); return false;">' . __('Rename', 'knews') . '</a> | </span>';
								echo '<span><a href="' . get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $list->id . '&preview=1" target="_blank" title="' . __('Open a preview in a new window', 'knews') . '">' . __('Preview', 'knews') . '</a> | </span>';
								echo '<span><a href="admin.php?page=knews_news&section=send&id=' . $list->id . '" title="' . __('Submit this newsletter', 'knews') . '">' . __('Submit', 'knews') . '</a> | </span>';
								echo '<span><a href="admin.php?page=knews_news&da=duplicate&did=' . $list->id . '" title="' . __('Duplicate this newsletter', 'knews') . '">' . __('Duplicate', 'knews') . '</a> | </span>';
								echo '<span class="trash"><a href="admin.php?page=knews_news&da=delete&nid=' . $list->id . '" title="' . __('Delete definitively this newsletter', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div></td>';
	
								echo '<td>' . $Knews_plugin->humanize_dates($list->created, 'mysql') . '</td>';
								echo '<td>' . $Knews_plugin->humanize_dates($list->modified, 'mysql') . '</td>';
								echo '<td>' . $list->template . '</td>';
								echo '<td>' . (($list->lang == '') ? __('Multilanguage','knews') : $list->lang) . '</td>';
								//echo '<td><a href="admin.php?page=knews_news&section=edit&idnews=' . $list->id . '">' . __('Edit', 'knews') . '</a></td>';
								//echo '<td align="center"><input type="checkbox" value="1" name="' . $list->id . '_delete" id="' . $list->id . '_delete" /></td>';
								echo '</tr>';
	
								$alt=!$alt;
								if ($results_counter == $results_per_page * $paged) break;
							}
						}
				?>
						</tbody>
						<tfoot>
							<tr>
								<th class="manage-column column-cb check-column"><input type="checkbox" /></th>
								<th align="left"><?php _e('Newsletter name','knews');?></th>
								<th><?php _e('Created','knews');?></th>
								<th><?php _e('Modified','knews');?></th>
								<th><?php _e('Template','knews');?></th>
								<th><?php _e('Language','knews');?></th>
							</tr>
						</tfoot>
					</table>
					<div class="submit">
						<select name="action">
							<option selected="selected" value=""><?php _e('Batch actions','knews'); ?></option>
							<option value="delete_news"><?php _e('Delete','knews'); ?></option>
						</select>
						<input type="submit" value="<?php _e('Apply','knews'); ?>" class="button-secondary" />
					</div>
					<?php 
					//Security for CSRF attacks
					wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
					?>
					</form>
				<?php
					//Pagination
						$maxPage=ceil(count($results) / $results_per_page);
						$link_params='admin.php?page=knews_news&tab='.$tab.'&paged=';
						if ($maxPage > 1) {
				?>		
						<div class="tablenav bottom">
	
							<div class="tablenav-pages">
								<span class="displaying-num"><?php echo count($results); ?> <?php _e('newsletters','knews'); ?></span>
								<?php if ($paged > 1) { ?>
								<a href="<?php echo $link_params; ?>1" title="<?php _e('Go to first page','knews'); ?>" class="first-page">&laquo;</a>
								<a href="<?php echo $link_params . ($paged-1); ?>" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page">&lsaquo;</a>
								<?php } else { ?>
								<a href="<?php echo $link_params; ?>" title="<?php _e('Go to first page','knews'); ?>" class="first-page disabled">&laquo;</a>
								<a href="<?php echo $link_params; ?>" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page disabled">&lsaquo;</a>
								<?php } ?>
								<span class="paging-input"><?php echo $paged; ?> de <span class="total-pages"><?php echo $maxPage; ?></span></span>
								<?php if ($maxPage > $paged) { ?>
								<a href="<?php echo $link_params . ($paged+1); ?>" title="<?php _e('Go to next page','knews'); ?>" class="next-page">&rsaquo;</a>
								<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to last page','knews'); ?>" class="last-page">&raquo;</a>
								<?php } else { ?>
								<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to next page','knews'); ?>" class="next-page disabled">&rsaquo;</a>
								<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to last page','knews'); ?>" class="last-page disabled">&raquo;</a>					
								<?php } ?>
							</div>
						<br class="clear">
						</div>
			<?php
						}
					} else {
						if ($tab=='') {
							echo '<p>' . __('At the moment there is no newsletter, you can create new ones','knews') . '</p>';
						} else {
							echo '<p>' . __('There is no auto-created newsletter.','knews') . '</p>';							
						}
					}
					
					if ($tab=='') {
				?>
					<hr />
					<a id="newnews"></a>
					<h2><?php _e('Create new newsletter','knews');?> <a href="<?php _e('http://www.knewsplugin.com/tutorial/','knews'); ?>" style="background:url(<?php echo KNEWS_URL; ?>/images/help.png) no-repeat 5px 0; padding:3px 0 3px 30px; color:#0646ff; font-size:15px;" target="_blank"><?php _e('Make your own templates how-to','knews'); ?></a></h2>
					<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" class="new_newsletter">
						<input type="hidden" name="action" id="action" value="add_news" />
						<p><label for="new_news"><?php _e('Name','knews');?>: </label><input type="text" name="new_news" id="new_news" class="regular-text" />
						<?php
						$lang_listed = false;
						/*foreach ($languages as $l) {
							if ($l['language_code'] == $users[0]->lang) $lang_listed = true;
						}
						*/
						if (KNEWS_MULTILANGUAGE) $languages['multi'] = array ('translated_name'=>  __('Multilanguage','knews'), 'language_code'=>'', 'active'=>0);
						
						if (count($languages) > 1) {
							
							echo '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;' . __('Language','knews') . ': <select name="lang" id="lang">';
							foreach($languages as $l){
								echo '<option value="' . $l['language_code'] . '"' . ( ($l['active']==1) ? ' selected="selected"' : '' ) . '>' . $l['translated_name'] . '</option>';
							}
							echo '</select>';
			
						} else if (count($languages) == 1) {
							foreach ($languages as $l) {
								echo '<input type="hidden" name="lang" id="lang" value="' . $l['language_code'] . '" />';
							}
						} else {
							echo  __('Error','knews') . ": " . __('Language not detected!','knews');
						}
						?>
						</p>
						<p><?php _e('Choose a template','knews');?>: </p>
						<?php
						knews_display_templates(); 
						?>
						<div style="clear:both;"></div>
						<div class="submit">
							<input type="submit" value="<?php _e('Add newsletter','knews');?>" class="button-primary" />
						</div>
						<?php
						global $knewsOptions;
						if ($knewsOptions['hide_shop']=='0') {
						?>
							<div id="knewsshop">
								<?php 
								$look = wp_remote_get( 'http://www.knewsplugin.com/shop/look.php' );
								if (!is_wp_error($look)) {
									if (isset($look['body'])) echo $look['body'];
								} else {
								?>
								<script type="text/javascript">
									if ('https:' == document.location.protocol) {
										document.write('<p>Please, go to <a href="http://www.knewsplugin.com/shop" target="_blank">our shop</a> and see our latest premium templates</p>');
									} else {
										var knewsscript = document.createElement('script'); knewsscript.type = 'text/javascript'; knewsscript.async = true;
										knewsscript.src = 'http://www' + '.knewsplugin.com/shop/look.js?w=<?php echo urlencode(get_bloginfo('version'));?>&v=<?php echo urlencode(KNEWS_VERSION); ?>&l=<?php echo WPLANG; ?>';
										var knewsscript_s = document.getElementsByTagName('script')[0]; knewsscript_s.parentNode.insertBefore(knewsscript, knewsscript_s);
									}
								</script>
								<?php
								}
								?>
							</div>
						<?php
						}
						?>
						<script type="text/javascript">
							jQuery(document).ready(function() {
								jQuery('form.new_newsletter div.template').each(function() {
									folder = jQuery('input:radio', this).val();
									version = jQuery('input#ver_' + folder, this).val();
									version = version.replace(".","_"); 
									jQuery ('#knewsshop div.' + folder + '_' + version).hide();
								});
							});
						</script>
						<?php 
						//Security for CSRF attacks
						wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
						?>
					</form>
				<?php
					} else {
				?>
				<p><a href="<?php _e('http://www.knewsplugin.com/automated-newsletter-creation/','knews'); ?>" style="background:url(<?php echo KNEWS_URL; ?>/images/help.png) no-repeat 5px 0; padding:3px 0 3px 30px; color:#0646ff; font-size:15px;" target="_blank"><?php _e('Auto-create Newsletters Tutorial','knews'); ?></a></p>
				<?php
					}
				?>
	</div>
