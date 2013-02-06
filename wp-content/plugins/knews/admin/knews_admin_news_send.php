<?php
//Security for CSRF attacks
$knews_nonce_action='kn-news-send';
$knews_nonce_name='_nwsend';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	global $knewsOptions, $Knews_plugin;
	
	$submit_enqueued=false;

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$id_newsletter = $Knews_plugin->get_safe('id');

	if (isset($_POST['action'])) {
		if ($_POST['action']=='submit_manual') {
			
			require( KNEWS_DIR . "/includes/knews_compose_email.php");

			$user=$wpdb->get_row("SELECT id, email, confkey FROM " . KNEWS_USERS . " WHERE email='" . $Knews_plugin->post_safe('email') . "'");
			
			if (count($user)==1) {
				$aux_array=array();
				//array('token'=>$token->token, 'id'=>$token->id, 'default'=>$tokenfound[1])

				foreach ($used_tokens as $token) {
					$aux_array[] = array( 'token' => $token['token'], 'value' => $Knews_plugin->get_user_field($user->id, $token['id'], $token['defaultval']) );
				}
				$user->tokens = $aux_array;
				$user->unsubscribe = get_admin_url() . 'admin-ajax.php?action=knewsUnsubscribe&e=' . urlencode($user->email) . '&k=' . $user->confkey;
				$user->cant_read = get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $id_newsletter . '&e=' . urlencode($user->email);

				$result=$Knews_plugin->sendMail( array( $user ), $theSubject, $theHtml );
			} else {
				$user = new stdClass;
				$user->unsubscribe = '#';
				$user->cant_read = get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $id_newsletter;
				$user->email = $Knews_plugin->post_safe('email');
				$result=$Knews_plugin->sendMail( array( $user ), $theSubject, $theHtml );
			}

			if ($result['ok']==1) {

				echo '<div class="updated"><p>' . __('The single e-mail has been sent to:','knews') . ' ' . $Knews_plugin->post_safe('email') . '.</p></div>';

			} else {

				echo '<div class="error"><p><strong>' . __('Error','knews') . ': </strong> ' . __("I can't submit an e-mail to:",'knews') . ' ' . $Knews_plugin->post_safe('email') . '.</p></div>';
			}
			
		} else if ($_POST['action']=='submit_batch') {

			// Enviament per CRON			
			$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
			$lists = $wpdb->get_results( $query );

			$query = "SELECT DISTINCT(" . KNEWS_USERS . ".id) FROM " . KNEWS_USERS . ", " . KNEWS_USERS_PER_LISTS . " WHERE " . KNEWS_USERS . ".id=" . KNEWS_USERS_PER_LISTS . ".id_user AND " . KNEWS_USERS . ".state='2'";
			
			$args_cats_sql='';
			
			foreach ($lists as $list) {
				if (isset($_POST['list_'.$list->id])) {
					if ($_POST['list_'.$list->id]=='1') {
						
						if ($args_cats_sql == '') {
							$args_cats_sql = ' AND (';
						} else {
							$args_cats_sql .= ' OR ';
						}	
						$args_cats_sql .= KNEWS_USERS_PER_LISTS . ".id_list=" . $list->id;
					}
				}
			}
			
			if ($args_cats_sql != '') {
				$query .= $args_cats_sql . ')';

				$batch_opts = array (
					'minute' => $Knews_plugin->post_safe('minute', 0, 'int'),
					'hour' => $Knews_plugin->post_safe('hour', 0, 'int'),
					'day' => $Knews_plugin->post_safe('day', 0, 'int'),
					'month' => $Knews_plugin->post_safe('month', 0, 'int'),
					'year' => $Knews_plugin->post_safe('year', 0, 'int'),
					'paused' => $Knews_plugin->post_safe('paused'),
					'priority' => $Knews_plugin->post_safe('priority'),
					'strict_control' => $Knews_plugin->post_safe('strict_control'),
					'emails_at_once' => $Knews_plugin->post_safe('emails_at_once')
				);
				
				require( KNEWS_DIR . "/includes/submit_batch.php");

			} else {

				echo '<div class="error"><p>' . __('Select one or more lists','knews') . '</p></div>';
			}
			// Fi Enviament per CRON			
		}
	} 

	$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_newsletter;
	$results_news = $wpdb->get_results( $query );
	if (count($results_news) == 0) {
?>

	<div class=wrap>
			<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Newsletters','knews'); ?></h2>
			<h3><?php echo __('Error','knews') . ': ' . __("The newsletter doesn't exists",'knews'); ?></h3>
	</div>
<?php
	} else {
?>		
	<div class=wrap>
		<?php
		if ($results_news[0]->subject=='' && !isset($_POST['action'])) {
			echo '<div class="error"><p>'; 
			printf(__('Warning: the email has no subject! %s Edit it again before submit!','knews'),'<a href="admin.php?page=knews_news&section=edit&idnews=' . $id_newsletter . '">'); 
			echo '</a></p></div>';
		}
		
		if ($knewsOptions['from_name_knews']=='Knews robot' && !isset($_POST['action'])) {
			echo '<div class="error"><p>'; 
			printf(__('Warning: %sConfigure sender name before submit!','knews'),'<a href="admin.php?page=knews_config">'); 
			echo '</a></p></div>';
		}
		?>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Sending newsletter','knews'); ?>: <?php echo $results_news[0]->name; ?></h2>
		<p><?php _e('Send the newsletter to the following lists','knews'); ?>:</p>
		<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
		<input type="hidden" name="action" id="action" value="submit_batch" />
		<input type="hidden" name="idnews" id="idnews" value="<?php echo $id_newsletter; ?>" />
		<?php
		$query = "SELECT id, name FROM " . KNEWS_LISTS . " ORDER BY orderlist";
		$lists_name = $wpdb->get_results( $query );

		$col=count($lists_name)+1; $n=0;
		if (count($lists_name) > 8) {
			echo '<table><tr><td valign="top" style="padding-right:50px">';
			$col = ceil($col / 3);
		}
		foreach ($lists_name as $ln) {
			$n++;
			if ($n > $col) {
				$n=1;
				echo '</td><td valign="top" style="padding-right:50px">';
			}
			echo '<p><input type="checkbox" value="1" name="list_' . $ln->id . '" id="list_' . $ln->id . '" class="checklist"> ' . $ln->name . '</p>';
		}
		if (count($lists_name) > 8) {
			echo '</td></tr></table>';
		}
		if (count($lists_name) > 1) {
			?>
			<p style="margin:0; padding:0;"><a href="#" onclick="jQuery('input.checklist').attr('checked', true)"><?php _e('Check all mailing lists','knews'); ?></a> | 
			<a href="#" onclick="jQuery('input.checklist').attr('checked', false)"><?php _e('Uncheck all mailing lists','knews'); ?></a></p>
			<?php
		}
		
		$cron=true;
		if ($knewsOptions['knews_cron']=='cronjob') {
			$last_cron_time = $Knews_plugin->get_last_cron_time();
			$now_time = time();
			if ($now_time - $last_cron_time > 800) $cron=false;
		}
		if ($knewsOptions['knews_cron']=='cronjs') $cron=false;

		if ($submit_enqueued && !$cron) {
			echo '<h2><a href="' . $Knews_plugin->get_main_admin_url() . 'admin-ajax.php?action=knewsCronDo&js=1" target="_blank">' . __('Now you must click here, then a window that emulates CRON with JavaScript will open. You should leave it open till sending ends.','knews') . '</a></h2>';
		}
		/*if (ini_get('safe_mode') && !$cron) {
	?>
		<div class="error">
			<p><strong><?php _e('WARNING','knews'); ?>!</strong></p>
			<p><?php _e("CRON not working and you have the PHP directive 'safe_mode' on, the bulk fail (approximately after 10 recipients)",'knews'); ?></p>
		</div>
	<?php
		} else {*/
			
		if (($knewsOptions['smtp_knews']=='cronjob' && !$cron) || $knewsOptions['smtp_knews']=='cronjs') {
			echo '<div class="updated">';
			//if (!$cron) echo '<p>' . __('CRON is not working. Depending on the number of subscribers can take some time to post.','knews') . '</p>
			echo '<p>' . __('You cannot schedule a delayed delivery. You must leave an auxiliary window open (JavaScript CRON Emulation) until the sending ends','knews') . '</p>';
			if ($knewsOptions['smtp_knews']!='1') echo '<p>' . __('Sending SMTP is not enabled, the shipments are less reliable.','knews') . '</p>';
			echo '</div>';
		}
	?>
	<p>&nbsp;</p>
	<?php
		if ($cron) {
		?>
		<p><?php _e('Start (now or deferred)?','knews'); ?> <?php _e('Time','knews');?>: <input type="text" name="hour" value="<?php echo date( 'H', current_time('timestamp')); ?>" style="width:30px;" />:<input type="text" name="minute" value="<?php echo date( 'i', current_time('timestamp')); ?>" style="width:30px;" /> |  <?php _e('Date (day/month/year)','knews');?>: <input type="text" name="day" value="<?php echo date( 'd', current_time('timestamp')); ?>" style="width:30px;" />/<input type="text" name="month" value="<?php echo date( 'm', current_time('timestamp')); ?>" style="width:30px;" />/<input type="text" name="year" value="<?php echo date( 'Y', current_time('timestamp')); ?>" style="width:50px;" /></p>
		<p><?php _e('Paused?','knews');?>? <select name="paused"><option value="0" selected="selected"><?php _e('No','knews');?></option><option value="1"><?php _e('Yes','knews');?></option></select> | <?php _e('Priority','knews');?>: <select name="priority"><option value="1">1 <?php _e('(lowest)','knews');?></option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5" selected="selected">5 <?php _e('(normal)','knews');?></option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10 <?php _e('(highest)','knews');?></option></select> | <?php _e('E-mails sent at once','knews');?>: <select name="emails_at_once"><option value="2">2 <?php _e('test mode','knews');?></option><option value="10">10</option><option value="25">25</option><option value="50" selected="selected">50 <?php _e('(normal)','knews');?></option><option value="100">100</option><option value="250">250 (high performance SMTP)</option><option value="500">500 (high performance SMTP)</option></select></p>
		<?php
		} else {
		?>
		<input type="hidden" name="hour" value="<?php echo date( 'H', current_time('timestamp')); ?>" /><input type="hidden" name="minute" value="<?php echo date( 'i', current_time('timestamp')); ?>" /><input type="hidden" name="day" value="<?php echo date( 'd', current_time('timestamp')); ?>" /><input type="hidden" name="month" value="<?php echo date( 'm', current_time('timestamp')); ?>" /><input type="hidden" name="year" value="<?php echo date( 'Y', current_time('timestamp')); ?>" />
		<input type="hidden" name="paused" value="0" /><input type="hidden" name="priority" value="5"  /><input type="hidden" name="emails_at_once" value="10" />
		<?php
		}
	/*
	?>
	<p><?php _e('E-mail for close supervision','knews'); ?>: <input type="text" name="strict_control" /></p>
	*/
	?>
	<div class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Schedule submit','knews'); ?>">
	</div>
	<?php 
	//Security for CSRF attacks
	wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
	?>
	</form>
	<hr />
	<h2><?php _e('Send the newsletter manually','knews');?>:</h2>
	<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
	<input type="hidden" name="action" id="action" value="submit_manual" />
	<input type="hidden" name="idnews" id="idnews" value="<?php echo $id_newsletter; ?>" />
	<p>E-mail: <input type="text" name="email" class="regular-text" /></p>
	<div class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Submit newsletter','knews'); ?>"/>
	</div>
	<?php 
	//Security for CSRF attacks
	wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
	?>
	</form>
</div>
<?php
	}
?>
	
