<?php
global $knewsOptions, $Knews_plugin, $wpdb;

if ($Knews_plugin) {

	if ( get_current_blog_id() != $Knews_plugin->KNEWS_MAIN_BLOG_ID ) {
		die("You must call the main blog www.yourdomain.com/wp-admin/admin-ajax.php?action=knewsCron URL");
	}
	
	$js=$Knews_plugin->get_safe('js', 0, 'int');

	$mysqldate = $Knews_plugin->get_mysql_date();
	
	$query = "SELECT * FROM " . $wpdb->base_prefix . "knewsubmits WHERE finished=0 AND paused=0 AND start_time <= '" . $mysqldate . "' ORDER BY priority DESC LIMIT 1";
	$submit_pend = $wpdb->get_results( $query );

	if (count($submit_pend) == 1) {

		if ($Knews_plugin->initialized) die("Knews internal error: can't be initialized before!");

		if( is_multisite() ) {
			$Knews_plugin->init($submit_pend[0]->blog_id);
		} else {
			$Knews_plugin->init();
		}

		$id_newsletter = $submit_pend[0]->newsletter;
		
		if ($submit_pend[0]->special == '') {
			require( KNEWS_DIR . "/includes/knews_compose_email.php");
		} else {
			$langs_array=$Knews_plugin->getLangs(true);
		}

		//Estadistiques
		$query = "SELECT * FROM " . KNEWS_KEYS . " WHERE submit_id=" . $submit_pend[0]->id;
		$links_submit = $wpdb->get_results( $query );
		$url_track = get_admin_url() . 'admin-ajax.php?action=knewsTrack&t=%confkey%_';
		
		foreach ($links_submit as $link_submit) {
			$theHtml = str_replace('"' . $link_submit->href . '"', '"' . $url_track . $link_submit->keyy . '"', $theHtml);
		}

		$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " WHERE status=0 AND submit=" . $submit_pend[0]->id . " LIMIT " . $submit_pend[0]->emails_at_once;
		$submits = $wpdb->get_results( $query );

		$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " SET status=2 WHERE status=0 AND submit=" . $submit_pend[0]->id . " LIMIT " . $submit_pend[0]->emails_at_once;
		$block = $wpdb->query( $query );
		
		$ok_count = $submit_pend[0]->users_ok;
		$error_count = $submit_pend[0]->users_error;

		if ($knewsOptions['write_logs']=='yes') {
			@$fp = fopen(KNEWS_DIR . '/tmp/cronlog_' . $submit_pend[0]->id, 'a');
		} else {
			$fp=false;
		}

		if (count($submits)>0) {
			if ($fp) {
				$hour = date('d/m/Y H:i:s', current_time('timestamp'));
				fwrite($fp, '* ' . $hour . ' | ok: ' . $ok_count . ' | error: ' . $error_count . "<br>\r\n");
			}
		} else {
			$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " WHERE status=2 AND submit=" . $submit_pend[0]->id;
			$real_error_count = $wpdb->get_results( $query );
			$error_count = count($real_error_count);
			$ok_count = $submit_pend[0]->users_total - $error_count;
			
		}

		$users = array();
		$users_index = 0;

		foreach ($submits as $unique_submit) {
			$users[$users_index]=array();
			$users[$users_index]=$wpdb->get_row("SELECT * FROM " . KNEWS_USERS . " WHERE id=" . $unique_submit->user);
			$users[$users_index]->unique_submit = $unique_submit->id;

			if ($submit_pend[0]->special == 'import_confirm') {
		
				$localized_lang = $Knews_plugin->localize_lang($langs_array, $users[$users_index]->lang);

				$theSubject = $Knews_plugin->get_custom_text('email_importation_subject', $localized_lang);
				$theHtml = '<head><title>' . $theSubject . '</title></head><body>'.$Knews_plugin->get_custom_text('email_importation_body', $localized_lang).'</body>';

				$users[$users_index]->confirm = KNEWS_LOCALIZED_ADMIN . 'admin-ajax.php?action=knewsConfirmUser&k=' . $users[$users_index]->confkey . '&e=' . urlencode($users[$users_index]->email);
				//$theHtml = str_replace('#url_confirm#', $url_confirm, $theHtml);

				//$result=$Knews_plugin->sendMail( array( $user ), $theSubject, $theHtml );

			} else {

				$aux_array=array();

				foreach ($used_tokens as $token) {
					$aux_array[] = array( 'token' => $token['token'], 'value' => $Knews_plugin->get_user_field($users[$users_index]->id, $token['id'], $token['defaultval']) );
				}
				$users[$users_index]->tokens = $aux_array;
				$users[$users_index]->unsubscribe = get_admin_url() . 'admin-ajax.php?action=knewsUnsubscribe&e=' . urlencode($users[$users_index]->email) . '&k=' . $users[$users_index]->confkey . '&n=' . $id_newsletter;
				$users[$users_index]->cant_read = get_admin_url() . 'admin-ajax.php?action=knewsReadEmail&id=' . $id_newsletter . '&e=' . urlencode($users[$users_index]->email);
				
				//$result=$Knews_plugin->sendMail( array( array('email' => $user->email, 'unsubscribe'=>get_bloginfo('url') ) ), $theSubject, $theHtml );
				//$result=$Knews_plugin->sendMail( array( $user ), $theSubject, $theHtml );
			}

//echo '* ' . $user->email . ' - ' . $result['ok'] . ' * ';

			/*if ($result['ok']==1) {
				$ok_count++;
				$status_submit=1;
			} else {
				$error_count++;
				$status_submit=2;			
			}

			if ($fp) {
				$hour = date('H:i:s');
				fwrite($fp, '  ' . $hour . ' | ok: ' . $ok_count . ' | error: ' . $error_count . ' | email: ' . $user->email . ' | ' . $result['error_info'][0] . "<br>\r\n");
			}
			
			$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " SET status=" . $status_submit . " WHERE id=" . $unique_submit->id;
			$result = $wpdb->query( $query );*/
			$users_index++;
		}
		
		//print_r($users);
		
		$result=$Knews_plugin->sendMail( $users, $theSubject, $theHtml, '', '', $fp );
		$ok_count += $result['ok'];
		$error_count += $result['error'];

		//print_r($result);
	
		$end_sql=', finished=0, end_time=\'0000-00-00 00:00:00\'';
		
		$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " WHERE status=1 AND submit=" . $submit_pend[0]->id;
		$recount = $wpdb->get_results( $query );
		$ok_count = count($recount);

		$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " WHERE status=2 AND submit=" . $submit_pend[0]->id;
		$recount = $wpdb->get_results( $query );
		$error_count = count($recount);

		if ($submit_pend[0]->users_total <= ($ok_count + $error_count)) {
			$end_sql=', finished=1, end_time=\'' . $Knews_plugin->get_mysql_date() . '\'';
			if ($fp) {
				$hour = date('H:i:s', current_time('timestamp'));
				fwrite($fp, '  ' . $hour . ' | ok: ' . $ok_count . ' | error: ' . $error_count . ' | FINISHED SUBMIT' . "<br>\r\n");
			}
		} else {
			if ($fp) {
				$hour = date('H:i:s', current_time('timestamp'));
				fwrite($fp, '  ' . $hour . ' | total: ' . $submit_pend[0]->users_total . ' | ok: ' . $ok_count . ' | error: ' . $error_count . ' | CONTINUE SUBMIT' . "<br>\r\n\r\n");
			}			
		}
		
		$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS . " SET users_ok=" . $ok_count . ", users_error=" . $error_count . $end_sql . " WHERE id=" . $submit_pend[0]->id;

		$result = $wpdb->query( $query );


		
		$pend=true;
	} else {
		$pend=false;
	}
}

if ($js != 0) {
?>
	<html>
	<head><title>JS Cron</title></head>
	<script type="text/javascript">
	var start = new Date().getTime();
	var total_time = 1000*60*5 // 5 minutes;
	function jump() {
	<?php 
		if ($pend) {
			if (is_multisite()) switch_to_blog($Knews_plugin->KNEWS_MAIN_BLOG_ID);
	?>
			location.href="<?php echo $Knews_plugin->get_main_admin_url() . 'admin-ajax.php?action=knewsCronDo&js=' . (intval($js)+1); ?>"
	<?php
		}
	?>
	}
	setTimeout ('jump()',total_time); 

	function counter() {
	<?php 
		if ($pend) {
	?>
		elapsed = new Date().getTime() - start;
		timeleft = total_time - elapsed;
		
		minutes=Math.floor(timeleft/60000);
		seconds=Math.floor((timeleft-minutes*60000)/1000);
		if (seconds<10) seconds= "0" + seconds;
		
		document.getElementById("clock").innerHTML=minutes+":"+seconds;
		setTimeout ('counter()',1000); // 1 second
	<?php
		}
	?>
	}
	</script>
	<body onLoad="counter()">
	<h1><?php echo __('Step','knews') . ' #' . $js; ?></h1>
	<?php
	if ($pend) {
	?>
	<h2 id="clock">5:00</h2>
	<p><?php _e("Cron-JS in the process, don't close this window...",'knews');?></p>
	<?php
	} else {
	?>
	<p><?php _e('Cron-JS finished, you can close the window','knews');?></p>
	<?php
	}
	?>
	</body>
	</html>
<?php
}
die();
?>
