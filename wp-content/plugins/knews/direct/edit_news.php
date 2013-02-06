<?php
global $Knews_plugin, $wpdb;

if ($Knews_plugin) {

	
	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$id_news = $Knews_plugin->get_safe('idnews', 0, 'int');

	$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_news;
	$results_news = $wpdb->get_results( $query );
	if (count($results_news) == 0) {
?>
			<h3><?php _e('Error: This newsletter does not exist','knews');?></h3>
<?php
	} else {

	$head_code=$results_news[0]->html_head;
	echo substr($head_code, 0, strlen($head_code)-7);
	?>	
	<script type="text/javascript" src="<?php echo KNEWS_URL; ?>/wysiwyg/editor.js?ver=<?php echo KNEWS_VERSION; ?>"></script>
	<link href="<?php echo KNEWS_URL; ?>/wysiwyg/editor.css?ver=<?php echo KNEWS_VERSION; ?>" rel="stylesheet" type="text/css" />
	</head>
	<body>
		<noscript>
		<h1><?php _e('Warning! You should activate JavaScript to edit newsletters!','knews');?></h1>
		</noscript>
		<div class="wysiwyg_editor">
			<?php echo $results_news[0]->html_mailing; ?>
		</div>
		<div id='modalDiv' style='display:none'></div>
	</body>
	</html>
<?php
	}
}
die();
?>
