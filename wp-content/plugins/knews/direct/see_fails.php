<?php
global $Knews_plugin, $wpdb;

if ($Knews_plugin) {


	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS . " WHERE blog_id=" . get_current_blog_id() . " AND id=" . $Knews_plugin->get_safe('id');
	$results = $wpdb->get_results( $query );
	
	$submit=$results[0];
?>
<html>
<head>
<title>See fails</title>
<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:13px;
}
a {
	color:#000;
}
a:hover {
	color:#e00;
}

</style>
</head>
<body>
<h1>Submit fails</h1>
<p>Start time: <?php echo $Knews_plugin->humanize_dates($submit->start_time,'mysql'); ?><br />
End time: <?php echo $Knews_plugin->humanize_dates($submit->end_time,'mysql'); ?><br />
Submits OK: <?php echo $submit->users_ok; ?><br />
Submits ERROR: <?php echo $submit->users_error; ?></p>
<p>List emails can't be submitted:</p>
<?php
$query = "SELECT * FROM " . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . " WHERE submit=" . $Knews_plugin->get_safe('id') . " AND status=2";
$results = $wpdb->get_results( $query );

$user_count=0;
foreach ($results as $user) {
	$user_count++;
	$query = "SELECT * FROM " . KNEWS_USERS . " WHERE id=" . $user->user;
	$user_data = $wpdb->get_results( $query );
	if (count($user_data) > 0) {
		echo '<p>#' . $user_count . ' <a href="' . get_admin_url() . 'admin.php?page=knews_users&edit_user=' . $user_data[0]->id . '" target="_blank">';
		echo $user_data[0]->email . '</a></p>';
	} else {
		echo 'info deleted</br>';
	}
}
?>
</body>
</html>
<?php
}
die();
?>