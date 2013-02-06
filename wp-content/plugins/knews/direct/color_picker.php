<?php
global $Knews_plugin;

if ($Knews_plugin) {


	if (! $Knews_plugin->initialized) $Knews_plugin->init();

?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<script type='text/javascript' src='<?php echo get_admin_url(); ?>load-scripts.php?c=1&amp;load=jquery'></script>
	<link rel="Stylesheet" type="text/css" href="<?php echo KNEWS_URL; ?>/wysiwyg/jpicker/css/jPicker-1.1.6.min.css" />
	<link rel="Stylesheet" type="text/css" href="<?php echo KNEWS_URL; ?>/wysiwyg/jpicker/jPicker.css" />
	<script src="<?php echo KNEWS_URL; ?>/wysiwyg/jpicker/jpicker-1.1.6.min.js" type="text/javascript"></script>

	</head>
	<body style="background:#fff; margin:0; padding:10px 0 0 15px; overflow:hidden;">
		<script type="text/javascript">        
		jQuery(document).ready (function() {
			jQuery('#Inline').jPicker(
				{images: { clientPath: parent.url_plugin + '/wysiwyg/jpicker/images/'}},
				function(color, context) {
					//Sel
					var all = color.val('all');
					var hex = (all && '#' + all.hex || 'none');
					<?php if ($Knews_plugin->get_safe('editor')==1) {
						echo 'parent.CallBackColourEditor(hex);';
					} else {
						echo 'parent.CallBackColour(hex);';
					}?>
				},
				function(color, context) {
					//Live
				},
				function(color, context) {
					//Cancel
					parent.tb_remove();
				}
			);
			jQuery.jPicker.List[0].color.active.val('ahex', '<?php echo $_GET['hex']; ?>FF');
		});
		</script>
		<div id="Inline"></div>
	</body>
	</html>
<?php
}
die();
?>