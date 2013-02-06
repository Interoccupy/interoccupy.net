<?php
//Security for CSRF attacks
$knews_nonce_action='kn-adm-auto';
$knews_nonce_name='_autokn';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	global $Knews_plugin, $wpdb;

	$pending=false;

	$languages = $Knews_plugin->getLangs(true);
	
	if ($Knews_plugin->get_safe('da')=='delete') {
		$query="DELETE FROM " . KNEWS_AUTOMATED . " WHERE id=" . $Knews_plugin->get_safe('idauto', 0, 'int');
		$results = $wpdb->query( $query );
		echo '<div class="updated"><p>' . __('Automated process deleted','knews') . '</p></div>';
	}

	if ($Knews_plugin->get_safe('activated')==1 || $Knews_plugin->get_safe('activated',2)==0) {
		$query = "UPDATE ".KNEWS_AUTOMATED." SET paused=" . $Knews_plugin->get_safe('activated') . " WHERE id=" . $Knews_plugin->get_safe('idauto', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . (($Knews_plugin->get_safe('activated')==1) ? __('Automated process activated','knews') : __('Automated process deactivated','knews')) . '</p></div>';
	}

	if ($Knews_plugin->get_safe('auto')==1 || $Knews_plugin->get_safe('auto',2)==0) {
		$query = "UPDATE ".KNEWS_AUTOMATED." SET auto=" . $Knews_plugin->get_safe('auto') . " WHERE id=" . $Knews_plugin->get_safe('idauto', 0, 'int');
		$result=$wpdb->query( $query );
		echo '<div class="updated"><p>' . (($Knews_plugin->get_safe('auto')==1) ? __('Automated submit activated','knews') : __('Manual submit activated','knews')) . '</p></div>';
	}

	if ($Knews_plugin->post_safe('action')=='add_auto') {

		$name = $Knews_plugin->post_safe('auto_name');
		$lang = $Knews_plugin->post_safe('auto_lang');
		$news = $Knews_plugin->post_safe('auto_newsletter');
		$target = $Knews_plugin->post_safe('auto_target');
		$paused = $Knews_plugin->post_safe('auto_paused');
		$auto = $Knews_plugin->post_safe('auto_auto');
		$mode = $Knews_plugin->post_safe('auto_mode');
		$posts = $Knews_plugin->post_safe('auto_posts', 0);
		$time = $Knews_plugin->post_safe('auto_time', 0);
		$day = $Knews_plugin->post_safe('auto_dayweek', 0);
		$at_once = $Knews_plugin->post_safe('emails_at_once', 50);
		
		if ($name =='' || $news=='' || $target=='') {
			
			echo '<div class="error"><p><strong>';
			if ($name=='') {
				_e('Error: the name cant be empty','knews');
			} else {
				_e('Error: Please, fill all the form','knews');
			}
			echo '</strong></p></div>';

		} else {
			$query = "SELECT * FROM " . KNEWS_AUTOMATED . " WHERE name='" . $name . "'";
			$results = $wpdb->get_results( $query );
			
			if (count($results)==0) {
				$sql = "INSERT INTO " . KNEWS_AUTOMATED . " (name, selection_method, target_id, newsletter_id, lang, paused, auto, every_mode, every_time, what_dayweek, every_posts, last_run, emails_at_once) VALUES (";
				$sql .= "'" . $name . "', 1, " . $target . ", " . $news . ", '" . $lang . "', " . $paused . ", " . $auto . ", " . $mode . ", " . $time . ", " . $day . ", " . $posts . ", '" . $Knews_plugin->get_mysql_date() . "', " . $at_once . ")";
				
				if ($wpdb->query($sql)) {
					echo '<div class="updated"><p>' . __('Automated submit created','knews') . '</p></div>';
				} else {
					echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __("Cant create the automated submit",'knews') . ' : ' . $wpdb->last_error . '</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>';
				_e('Error: there is already an automated submit with this name','knews');
				echo '</strong></p></div>';
			}
		}
	}

	$results_per_page=10;
	$paged = $Knews_plugin->get_safe('paged', 1, 'int');

	$query = "SELECT id, name, lang, html_mailing FROM " . KNEWS_NEWSLETTERS . " WHERE automated=0 AND mobile=0 ORDER BY modified DESC";
	$news = $wpdb->get_results( $query );

	$frequency = array ('daily','weekly','every 15 days','monthly','every 2 months','every 3 months');
	$dayname = array ('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday');
	
	$query = "SELECT id, name FROM " . KNEWS_LISTS . " ORDER BY orderlist";
	$lists_name = $wpdb->get_results( $query );
?>
<script type="text/javascript">
function enfocar() {
	setTimeout("jQuery('#auto_name').focus();", 100);
}
</script>
	<div class=wrap>
			<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div>
			<h2><?php _e('Auto-create Newsletters','knews');?><a href="#newauto" class="add-new-h2" onclick="enfocar()"><?php _e('New Auto-creation Process','knews'); ?></a></h2>
			<?php 

			$query = "SELECT * FROM " . KNEWS_AUTOMATED; 
			$results = $wpdb->get_results( $query );
			if (count($results) != 0) {
			?>
				<form method="post" action="admin.php?page=knews_auto">
				<table class="widefat">
					<thead>
						<tr>
							<?php /*<th class="manage-column column-cb check-column"><input type="checkbox" /></th>*/ ?>
							<th align="left"><?php _e('Automated process name','knews');?></th>
							<th><?php _e('Target','knews');?></th>
							<th><?php _e('Newsletter','knews');?></th>
							<th><?php _e('Language','knews');?></th>
							<th><?php _e('Activated','knews');?></th>
							<th><?php _e('Automatic submit','knews');?></th>
							<th><?php _e('Method','knews');?></th>
							<th><?php _e('Last run','knews');?></th>
						</tr>
					</thead>
					<tbody>
					<?php
					$alt=false;
					$results_counter=0;
					foreach ($results as $automated) {
						$results_counter++;
						if ($results_per_page * ($paged-1)<$results_counter) {
							echo '<tr' . (($alt) ? ' class="alt"' : '') . '>'; //<th class="check-column"><input type="checkbox" name="batch_' . $list->id . '" value="1"></th>';
							echo '<td class="name_' . $automated->id  . '"><strong>' . $automated->name . '</strong>';
							
							echo '<div class="row-actions"><span><a title="' . __('Activate/deactivate the automated task', 'knews') . '" href="admin.php?page=knews_auto&activated=' . (($automated->paused==1) ? '0' : '1') . '&idauto=' . $automated->id . '">' . (($automated->paused==1) ? __('Activate', 'knews') : __('Deactivate', 'knews')) . '</a> | </span>';

							echo '<span><a title="' . __('Automatic/Manual submits of auto-created newsletters', 'knews') . '" href="admin.php?page=knews_auto&auto=' . (($automated->auto==1) ? '0' : '1') . '&idauto=' . $automated->id . '">' . (($automated->auto==1) ? __('Manual submit', 'knews') : __('Submit automatic', 'knews')) . '</a> | </span>';

							echo '<span class="trash"><a href="admin.php?page=knews_auto&da=delete&idauto=' . $automated->id . '" title="' . __('Delete definitively this automated task', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div>';

							echo '</td><td>';
							foreach ($lists_name as $ln) {
								if ($ln->id==$automated->target_id) {
									echo $ln->name;
									break;
								}
							}
							echo '</td>';
							echo '<td>';
							foreach ($news as $n) {
								if ($n->id==$automated->newsletter_id) {
									echo $n->name;
									break;
								}
							}
							echo '</td>';
							echo '<td>' . $automated->lang . '</td>';
							echo '<td>' . (($automated->paused==1) ? __('Off', 'knews') : __('On', 'knews')) . '</td>';
							if ($automated->paused!=1) $pending=true;
							echo '<td>' . (($automated->auto==1) ? __('Automated submit', 'knews') : __('Manual submit', 'knews')) . '</td>';
							echo '<td>';
							if ($automated->every_mode ==1) {
								printf( 'every %s posts', $automated->every_posts);
							} else {
								echo $frequency[$automated->every_time - 1];
								if ($automated->every_time > 1) echo ' on ' . $dayname[$automated->what_dayweek-1];
							}
							echo '</td>';
							echo '<td>' . $automated->last_run . '</td></tr>';
						}
						$alt=!$alt;
						if ($results_counter == $results_per_page * $paged) break;
					}
					?>
					</tbody>
					<tfoot>
						<tr>
							<th align="left"><?php _e('Automated process name','knews');?></th>
							<th><?php _e('Target','knews');?></th>
							<th><?php _e('Newsletter','knews');?></th>
							<th><?php _e('Language','knews');?></th>
							<th><?php _e('Activated','knews');?></th>
							<th><?php _e('Automatic submit','knews');?></th>
							<th><?php _e('Method','knews');?></th>
							<th><?php _e('Last run','knews');?></th>
						</tr>
					</tfoot>
				</table>
				<?php 
				//Security for CSRF attacks
				wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
				?>
				<?php /*
				<div class="submit">
					<select name="action">
						<option selected="selected" value=""><?php _e('Batch actions','knews'); ?></option>
						<option value="delete_news"><?php _e('Delete','knews'); ?></option>
					</select>
					<input type="submit" value="<?php _e('Apply','knews'); ?>">
				</div>*/
				?>
				</form>
				<?php
				//Pagination
				$maxPage=ceil(count($results) / $results_per_page);
				$link_params='admin.php?page=knews_auto&paged=';
				if ($maxPage > 1) {
				?>		
				<div class="tablenav bottom">

					<div class="tablenav-pages">
						<span class="displaying-num"><?php echo count($results); ?> <?php _e('Automated submits','knews'); ?></span>
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
				if ($pending) {
				?>
				<div class="updated">
					<p><?php _e('Knews runs every hour the automated newsletter creation jobs.','knews'); ?></p>
					<p><?php echo sprintf(__('You can manually trigger this task now (only recommended for testing purposes) %s Run Automated Creation Now','knews'), '<a href="' . $Knews_plugin->get_main_admin_url() . 'admin-ajax.php?action=knewsForceAutomated&manual=1" class="button" target="_blank">'); ?></a></p>
				</div>
				<?php
				}
			} else {
			?>
				<p><?php _e('At the moment there is no automated task, you can create new ones','knews'); ?></p>
			<?php
			}
			?><p>&nbsp;</p>
			<hr />
			<a id="newauto"></a>
			<h2><?php _e('New Auto-creation Process','knews');?> <a href="<?php _e('http://www.knewsplugin.com/automated-newsletter-creation/','knews'); ?>" style="background:url(<?php echo KNEWS_URL; ?>/images/help.png) no-repeat 5px 0; padding:3px 0 3px 30px; color:#0646ff; font-size:15px;" target="_blank"><?php _e('Auto-create Newsletters Tutorial','knews'); ?></a></h2>
			<form method="post" action="admin.php?page=knews_auto" id="create_auto">
				<input type="hidden" name="action" id="action" value="add_auto" />
				<h3><?php _e('General options','knews'); ?></h3>
				<p><label for="auto_name"><?php _e('Automated process name:','knews');?> </label><input type="text" name="auto_name" id="auto_name" class="regular-text" /></p> 
				<p><label for="auto_paused"><?php _e('Active process:','knews');?></label> <select name="auto_paused" id="auto_paused"><option value="0" selected="selected"><?php _e('On','knews');?></option><option value="1"><?php _e('Off','knews');?></option></select></p>
				<h3><?php _e('Newsletter creation options','knews');?></h3>
				<?php
				$lang_listed = false;
				
				if (count($languages) > 1) {
					
					echo '<p><label for="auto_lang">' . __('Get posts from language:','knews') . '</label> <select name="auto_lang" id="auto_lang">';
					foreach($languages as $l){
						echo '<option value="' . $l['language_code'] . '"' . '>' . $l['translated_name'] . '</option>';
					}
					echo '</select></p>';
		
				} else if (count($languages) == 1) {
					foreach ($languages as $l) {
						echo '<input type="hidden" name="auto_lang" id="auto_lang" value="' . $l['language_code'] . '" />';
					}
				} else {
					echo  '<p>' . __('Error','knews') . ": " . __('Language not detected!','knews') . '</p>';
				}
				?>
				<p><label for="auto_newsletter"><?php _e('Use as template:','knews');?></label> 
				<?php
				$disponible_news=array();
				foreach ($news as $n) {
					if (strrpos($n->html_mailing, '%the_title') !== false || strrpos($n->html_mailing, '%the_excerpt') !== false || strrpos($n->html_mailing, '%the_content') !== false) {
						$disponible_news[]=$n;
					}
				}
				if (count($disponible_news) != 0) {
					echo '<select name="auto_newsletter" id="auto_newsletter">';
					foreach ($disponible_news as $n) {
						echo '<option value="' . $n->id . '">' . $n->name . ' (' . $n->lang . ')</option>';
					}
					echo '</select></p>';
				} else {
					echo __('You must first create a newsletter with insertable info (leave the %the_content%, %the_title% etc.)','knews') . '</p>';
				}
				?>
				<p><input type="radio" name="auto_mode" autocomplete="off" value="1" checked="checked" /><?php printf(__('Create a newsletter every %s posts','knews'), '<input type="text" name="auto_posts" id="auto_posts" value="5" style="width:30px;" />');?></p>
				<p><input type="radio" name="auto_mode" autocomplete="off" value="2" /> <?php _e('Create a newsletter every x amount of time','knews');?> 
				<?php /*<span id="auto_mode_1">Every <input type="text" name="auto_posts" id="auto_posts" value="5" style="width:30px;" /> posts</span>*/?>
				<span id="auto_mode_2" style="display:none;"><label for="auto_time"><?php _e('Submit','knews'); ?></label> <select name="auto_time" id="auto_time" autocomplete="off">
				<?php
				$f=0;
				foreach ($frequency as $fre) {
					$f++;
					echo '<option value="' . $f . '">' . $fre . '</option>';
				}
				?>
				</select><span id="dayweek">, <label for="auto_dayweek">on</label> <select name="auto_dayweek" id="auto_dayweek">
				<?php
				$d=0;
				foreach ($dayname as $day_name) {
					$d++;
					echo '<option value="' . $d . '">' . $day_name . '</option>';
				}
				?>
				</select></span></p>
				<h3><?php _e('Newsletter submit options','knews'); ?></h3>
				<?php
				if (count($lists_name) != 0) {
					?>
					<p><label for="auto_target"><?php _e('Target for newsletter:','knews');?></label> 
					<select name="auto_target" id="auto_target">
					<?php
					foreach ($lists_name as $ln) {
						echo '<option value="' . $ln->id . '">' . $ln->name . '</option>';
					}
					?>
					</select>
					</p>
					<?php
				} else {
					echo '<p>' . __('Error: there are no mailing lists','knews'). '</p>';
				}
				?>
				<p><label for="auto_auto"><?php _e('Submit method:','knews');?></label> <select name="auto_auto" id="auto_auto"><option value="0" selected="selected"><?php _e('Manual submit','knews');?></option><option value="1"><?php _e('Automated submit','knews');?></option></select></p>
<?php if ($Knews_plugin->im_pro()) {?>
<div id="at_once" style="display:none;"><p><?php _e('E-mails sent at once','knews');?>: <select name="emails_at_once"><option value="2">2 <?php _e('test mode','knews');?></option><option value="10">10</option><option value="25">25</option><option value="50" selected="selected">50 <?php _e('(normal)','knews');?></option><option value="100">100</option><option value="250">250 <?php _e('(high performance SMTP)','knews');?></option><option value="500">500 <?php _e('(high performance SMTP)','knews');?></option></select></p></div>
<?php } ?>
				<p><input type="submit" value="<?php _e('New Auto-create Newsletters Process','knews'); ?>" class="button-primary" /></p>
				<?php 
				//Security for CSRF attacks
				wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
				?>
			</form>
	</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#create_auto input, #create_auto select').change(function() {
			val=jQuery('input[name=auto_mode]:checked', '#create_auto').val();
			//jQuery('span#auto_mode_1, span#auto_mode_2').hide();
			//jQuery('span#auto_mode_' + val).show();
			if (val==2) {
				jQuery('span#auto_mode_2').show();
				if (jQuery('#auto_time').val() != 1) {
					jQuery('#dayweek').show();
				} else {
					jQuery('#dayweek').hide();					
				}
			} else {
				jQuery('span#auto_mode_2').hide();
			}
		});
<?php if ($Knews_plugin->im_pro()) {?>
		jQuery('#auto_auto').change(function() {
			val=jQuery(this).val();
			if (val==0) {
				jQuery('div#at_once').hide();
			} else {
				jQuery('div#at_once').show();
			}
		});
<?php } ?>
	});
</script>