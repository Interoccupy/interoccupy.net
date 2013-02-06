<?php
$targets = $wpdb->get_results( $query );

if (count($targets) > 0) {
						
	$start_time = mktime($batch_opts['hour'], $batch_opts['minute'], 0, $batch_opts['month'], $batch_opts['day'], $batch_opts['year']);
	
	$mysqldate = $Knews_plugin->get_mysql_date($start_time);
	
	$query = 'INSERT INTO ' . KNEWS_NEWSLETTERS_SUBMITS . ' (blog_id, newsletter, finished, paused, start_time, users_total, users_ok, users_error, priority, strict_control, emails_at_once, special, end_time) VALUES (' . get_current_blog_id() . ', ' . $id_newsletter . ', 0, ' . $batch_opts['paused'] . ', \'' . $mysqldate . '\', ' . count($targets) . ', 0, 0, ' . $batch_opts['priority'] . ', \'' . $batch_opts['strict_control'] . '\', ' . $batch_opts['emails_at_once'] . ', \'\', \'0000-00-00 00:00:00\')';
	$results = $wpdb->query( $query );
	
	$submit_id=$wpdb->insert_id; $submit_id2=mysql_insert_id(); if ($submit_id==0) $submit_id=$submit_id2;

	foreach ($targets as $target) {
		
		//$target->id;
		$query = 'INSERT INTO ' . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . ' (submit, user, status) VALUES (' . $submit_id . ', ' . $target->id . ', 0)';
		$results = $wpdb->query( $query );
		
	}
	
	// Extraiem links per estadistiques
	require( KNEWS_DIR . "/includes/knews_compose_email.php");
	// Thanks to http://www.web-max.ca/PHP/misc_23.php
	/*preg_match_all ("/a[\s]+[^>]*?href[\s]?=[\s\"\']+".
		"(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/a>/", */

	preg_match_all ("/(a|A)[\s]+[^>]*?href[\s]?=[\s\"\']+".
		"(.*?)[\"\']+.*?>"."([^<]+|.*?)?<\/(a|A)>/", 
		$theHtml, $matches);

	$matches = $matches[2];

	foreach($matches as $link) {
		if (
			strpos($link,'<') === false &&
			strpos($link,'>') === false &&
			strpos($link,' ') === false &&
			strpos($link,'\'') === false &&
			strpos($link,'"') === false &&
			strpos($link,'{') === false &&
			strpos($link,'}') === false &&
			strpos($link,'[') === false &&
			strpos($link,']') === false &&
			strpos($link,'%') === false &&
			strpos($link,'#') === false &&
			strpos($link,'mailto:') === false 
		) {
		//if ($link != '%cant_read_href%' && $link != '%unsubscribe_href%' && $link != '#') {
			$link_key = substr(md5(uniqid()),-16);
			$query = 'INSERT INTO ' . KNEWS_KEYS . ' (keyy, type, submit_id, href) VALUES (\'' . $link_key . '\', 1, ' . $submit_id . ', \'' . $link . '\')';
			$results = $wpdb->query( $query );
		}
	}
	echo '<div class="updated"><p>' . __('Batch submit process has been properly scheduled.','knews') . '</p></div>';				
	$submit_enqueued=true;
} else {
	echo '<div class="error"><p>' . __('No active users in the selected list, nothing programmed to send.','knews') . '</p></div>';				
}
?>
	
