<?php
//Security for CSRF attacks
$knews_nonce_action='kn-save-news';
$knews_nonce_name='_savenews';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

global $Knews_plugin, $wpdb;

if ($Knews_plugin) {


	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	require_once( KNEWS_DIR . '/includes/knews_util.php');

	$id=	$Knews_plugin->post_safe('idnews');
	$title=	$Knews_plugin->post_safe('title', '', 'unsafe');
	$code=	$Knews_plugin->post_safe('code', '', 'unsafe');
	
	$date=	$Knews_plugin->get_mysql_date();
	
	$code=str_replace('#@!', '<', $code);
	
	//WYSIWYG editor issues
	$code=rgb2hex($code);
	if (!is_utf8($code)) $codeModule=utf8_encode($code);
	$code=$Knews_plugin->htmlentities_corrected($code);
	
	if (strlen($Knews_plugin->post_safe('testslash', '', 'unsafe'))==5) {
		
		$title = mysql_real_escape_string($title);
		$query = "UPDATE " . KNEWS_NEWSLETTERS . " SET html_mailing='" . mysql_real_escape_string($code) . "', modified='" . $date . "', subject='" . $title . "' WHERE id=" . $id;
	} else {
		
		$query = "UPDATE " . KNEWS_NEWSLETTERS . " SET html_mailing='" . $code . "', modified='" . $date . "', subject='" . $title . "' WHERE id=" . $id;
	}
	
	if ($wpdb->query($query)) {
		$query = "SELECT id FROM " . KNEWS_NEWSLETTERS . " WHERE id_mobile=" . $id;
		$newsparent = $wpdb->get_results( $query );
		if (count($newsparent) > 0) {
			$query = "UPDATE " . KNEWS_NEWSLETTERS . " SET modified='" . $date . "', subject='" . $title . "' WHERE id=" . $newsparent[0]->id;
			$wpdb->query($query);
		}
		echo 'knews:ok';
	} else {
		echo $wpdb->last_error;
	}
}
die();
?>
