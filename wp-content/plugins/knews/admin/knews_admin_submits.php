<?php
global $Knews_plugin, $wpdb, $knewsOptions;

if ($Knews_plugin->get_safe('da')=='priority') {
	$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS." SET priority='" . $Knews_plugin->get_safe('np') . "' WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('sid', 0, 'int');

	//echo $query;

	$result=$wpdb->query( $query );
	echo '<div class="updated"><p>' . __('Submit priority updated','knews') . '</p></div>';
}

if ($Knews_plugin->get_safe('da')=='startnow') {
	$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS." SET start_time='" . $Knews_plugin->get_mysql_date() . "' WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('sid', 0, 'int');
	$result=$wpdb->query( $query );
	echo '<div class="updated"><p>' . __('Submit start time updated','knews') . '</p></div>';
}

if ($Knews_plugin->get_safe('da')=='retry') {
	$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS_DETAILS." SET status=0 WHERE blog_id=" . get_current_blog_id() . " AND status=2 AND submit=" . $Knews_plugin->get_safe('rid', 0, 'int');
	$result=$wpdb->query( $query );
	
	if ($result > 0) {
		$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS." SET finished=0, paused=0, end_time='0000-00-00 00:00:00', users_error=0 WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('rid', 0, 'int');
		$result=$wpdb->query( $query );

		if ($knewsOptions['write_logs']=='yes') {
			$fp = fopen(KNEWS_DIR . '/tmp/cronlog_' . $Knews_plugin->get_safe('rid', 0, 'int'), 'a');
			if ($fp) {
				$hour = date('H:i:s', current_time('timestamp'));
				fwrite($fp, "\r\n" . '**' . $hour . ' | ' . __('Knews will try to resend those messages which have reported error','knews') . "<br>\r\n\r\n");
			}
		}
		echo '<div class="updated"><p>' . __('Knews will try to resend those messages which have reported error','knews') . '</p></div>';
	}
}

if ($Knews_plugin->get_safe('da')=='continue') {
	$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS." SET paused=0 WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('sid', 0, 'int');
	$result=$wpdb->query( $query );
	echo '<div class="updated"><p>' . __('Submit start time updated','knews') . '</p></div>';
}

if ($Knews_plugin->get_safe('da')=='pause') {
	$query = "UPDATE ".KNEWS_NEWSLETTERS_SUBMITS." SET paused=1 WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('sid', 0, 'int');
	$result=$wpdb->query( $query );
	echo '<div class="updated"><p>' . __('Submit start time updated','knews') . '</p></div>';
}

if ($Knews_plugin->get_safe('da')=='delete') {
	$query="DELETE FROM " . KNEWS_NEWSLETTERS_SUBMITS . " WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('sid', 0, 'int');
	$results = $wpdb->query( $query );
	echo '<div class="updated"><p>' . __('Submit deleted','knews') . '</p></div>';
}

?>
	<script type="text/javascript">
	function reload_page() {
		location.href = '<?php echo get_admin_url(); ?>admin.php?page=knews_submit';
	}
	setTimeout ('reload_page()',60000 * 15); // 15 minutes

	var save_text='';
	var save_id='';
	
	function change_priority(n) {
		if (save_id != '') priority_cancel();
		save_id = n;
		save_text = jQuery('td.priority_' + n).html();
		save_value = parseInt(jQuery('td.priority_' + n).html(), 10);
		jQuery('td.priority_' + n).html('<select name="priority"><option value="1">1 <?php _e('(lowest)','knews');?></option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5" selected="selected">5 <?php _e('(normal)','knews');?></option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10 <?php _e('(highest)','knews');?></option></select><br><input type="button" value="Update" class="priority_do"><input type="button" value="Cancel" class="priority_cancel">');
		
		jQuery('td.priority_' + n + ' select').val(save_value);
		jQuery('td.priority_' + n + ' select')[0].focus();

		jQuery('input.priority_cancel').click(function() {
			priority_cancel();
			return false;
		});

		jQuery('input.priority_do').click(function() {
			location.href="admin.php?page=knews_submit&da=priority&sid=" + save_id + '&np=' + encodeURIComponent(jQuery('td.priority_' + save_id + ' select').val());
		});

		return false;
	}
	
	function priority_cancel() {
		if (save_id != '') {
			jQuery('td.priority_' + save_id).html(save_text);
			save_id='';
		}
	}
	
	function see_log(id) {
		tb_show('<?php  _e('See the complete submit log', 'knews'); ?>', '<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=cronlog_' + id + '&amp;TB_iframe=true&amp;width=640&amp;height=' + (parseInt(jQuery(parent.window).height(), 10)-100));
	}

	function see_errors(id) {
		tb_show('<?php   _e('See report fails by SMTP server'); ?>', '<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSeeFails&amp;id=' + id + '&amp;TB_iframe=true&amp;width=640&amp;height=' + (parseInt(jQuery(parent.window).height(), 10)-100));
	}

	</script>
	<div class=wrap>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Submits','knews');?></h2>
			<table class="widefat">
				<thead>
					<tr>
						<th align="left"><?php _e('Newsletter','knews');?></th>
						<th><?php _e('Priority','knews');?></th>
						<th><?php _e('Submit start','knews');?></th>
						<th><?php _e('Submit end','knews');?></th>
						<th><?php _e('Users','knews');?></th>
						<th style="text-align:center;"><?php _e('Progress / Results','knews');?></th>
						<th><?php _e('Status','knews');?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$pending=false;
				$alt=false;
				$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS . " WHERE blog_id=" . get_current_blog_id() . " ORDER BY finished, paused, start_time DESC";
				$results = $wpdb->get_results( $query );

				if (count($results) == 0) echo '<tr><td colspan="7"><p>' . __('No submits yet. First go to Newsletters, create one, and then submit it.','knews') . '</p></td></tr>';
				
				foreach ($results as $submit) {
					echo '<tr' . (($alt) ? ' class="alt"' : '') . '><td>';
					if ($submit->special != '') {
						
						echo '<strong>' . __('Special','knews') . ':</strong> [' . $submit->special . ']';
						
					} else {
						$look_name = $wpdb->get_row("SELECT name FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $submit->newsletter);
						if ($look_name) { 
							echo '<strong>' . $look_name->name . '</strong>';
						} else {
							echo '[' . __('deleted','knews') . ']';
						}
					}
					
					echo '<div class="row-actions">';
					
					if ( $submit->finished==0) {
						echo '<span><a href="#" title="' . __('Change the priority', 'knews') . '" onclick="change_priority(' . $submit->id . '); return false;">' . __('Priority', 'knews') . '</a> | </span>';

						if ( $Knews_plugin->sql2time($submit->start_time) > time() ) {
							echo '<span><a href="admin.php?page=knews_submit&da=startnow&sid=' . $submit->id . '" title="' . __('Set the current date and time as start submit time', 'knews') . '">' . __('Start now', 'knews') . '</a> | </span>';
						}
							
						if ( $submit->paused==0) {
							echo '<span><a href="admin.php?page=knews_submit&da=pause&sid=' . $submit->id . '" title="' . __('Pause this submit', 'knews') . '">' . __('Pause', 'knews') . '</a> | </span>';
						} else {
							echo '<span><a href="admin.php?page=knews_submit&da=continue&sid=' . $submit->id . '" title="' . __('Remove pause, continue submit', 'knews') . '">' . __('Continue', 'knews') . '</a> | </span>';							
						}
					} else {
						if ($submit->users_error != 0 ) {
							echo '<span><a href="#" title="' . __('See report fails by SMTP server', 'knews') . '" onclick="see_errors(' . $submit->id . '); return false;">' . __('See fails', 'knews') . '</a> | </span>';
							echo '<span><a href="admin.php?page=knews_submit&da=retry&rid=' . $submit->id . '" title="' . __('Resubmit those which have reported error', 'knews') . '">' . __('Retry', 'knews') . '</a> | </span>';
						}
					}

					if (is_file(KNEWS_DIR . '/tmp/cronlog_' . $submit->id)) {
						echo '<span><a href="#" title="' . __('See the complete submit log', 'knews') . '" onclick="see_log(' . $submit->id . '); return false;">' . __('See log', 'knews') . '</a> | </span>';
					}
					
					echo '<span class="trash"><a href="admin.php?page=knews_submit&da=delete&sid=' . $submit->id . '" title="' . __('Stop and delete this submit now', 'knews') . '" class="submitdelete">' . __('Delete', 'knews') . '</a></span></div></td>';
					
					echo '<td class="priority_' . $submit->id . '">' . $submit->priority . '</td>';
					echo '<td>' . $Knews_plugin->humanize_dates($submit->start_time,'mysql') . '</td>';
					echo '<td>' . $Knews_plugin->humanize_dates($submit->end_time,'mysql') . '</td>';
					echo '<td>' . $submit->users_total . '</td>';
					echo '<td align="center">';
					if ( $submit->finished==0) {
						//Progres
						$percent = round(($submit->users_ok+$submit->users_error) / $submit->users_total * 100) ;
						echo $percent . '%';
						echo '<div style="width:100px; height:11px; border:#fff 1px solid; outline:#bdbdbd 1px solid; background:url(' . KNEWS_URL . '/images/submit.gif) no-repeat ' . (intval($percent) - 100) . 'px 0px; line-height:1px; font-size:1px;">&nbsp;</div>';
					} else {
						//Results
						if ($submit->users_ok != 0 ) echo '<span style="color:#048210">' . $submit->users_ok . ' OK</span>';
						if ($submit->users_ok != 0 && $submit->users_error != 0) echo ' / ';
						if ($submit->users_error != 0 ) {
							echo '<span style="color:#b30000">' . $submit->users_error . ' ERROR</span>';
							//echo ' <a href="' . get_admin_url() . 'admin-ajax.php?action=knewsSeeFails&id=' . $submit->id . '" target="_blank">[' . __('See fails','knews') . ']</a>';
						}
					}
					echo '</td>';
					echo '<td>';
					if ( $submit->finished==1) {
						echo __('Finished','knews');
					} else {
						//Resultats
						if ( $submit->paused==0) {
							echo __('Ongoing','knews');
							$pending=true;
						} else {
							echo __('Paused','knews');
						}
					}
					echo '</td></tr>';
					$alt=!$alt;
				}
				?>
				</tbody>
				<tfoot>
					<tr>
						<th align="left"><?php _e('Newsletter','knews');?></th>
						<th><?php _e('Priority','knews');?></th>
						<th><?php _e('Submit start','knews');?></th>
						<th><?php _e('Submit end','knews');?></th>
						<th><?php _e('Users','knews');?></th>
						<th style="text-align:center;"><?php _e('Progress / Results','knews');?></th>
						<th><?php _e('Status','knews');?></th>
					</tr>
				</tfoot>
			</table>
			<?php
			if ($pending) {
			?>
			<div class="updated">
				<p><?php _e('Knews submit runs every 10 minutes and sends a portion of the list.','knews'); ?></p>
				<p><?php echo sprintf(__('You can manually trigger the JavaScript CRON if your newsletter is not being sent for any reason: %s Run JS-Cron Now','knews'), '<a href="' . $Knews_plugin->get_main_admin_url() . 'admin-ajax.php?action=knewsCron&js=1" class="button" target="_blank">'); ?></a></p>
			</div>
			<?php
			}
			?>
