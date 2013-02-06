<?php
global $Knews_plugin;

if ($Knews_plugin->get_safe('subscription')=='ok' || $Knews_plugin->get_safe('subscription')=='error' || $Knews_plugin->get_safe('unsubscribe')=='ok' ||$Knews_plugin->get_safe('unsubscribe')=='error') {
	?>
	<script type="text/javascript">
	function knews_deleteLayer(id) {
		if (document.getElementById && document.getElementById(id)) {
			var theNode = document.getElementById(id);
			theNode.parentNode.removeChild(theNode);
		}
		else if (document.all && document.all[id]) {
			document.all[id].innerHTML='';
			document.all[id].outerHTML='';
		}
	}
	</script>
	<style type="text/css">
		#knews_dialog p { margin:0; padding:0 0 20px 0;}
		#knews_dialog { position:fixed; left:0; top:0; width:100%; height:100%; z-index:10000; background:url(<?php echo KNEWS_URL; ?>/images/bg_dialog.png) repeat 0 0; }
		#knews_dialog_bg { left:50%; top:50%; margin-left:-250px; margin-top:-100px; width:458px; height:158px; padding:30px 20px 0 20px; border:#eee 1px solid; background:#fff; color:#000; font-family:Verdana, Geneva, sans-serif; font-size:12px; line-height:15px; text-align:center; position:absolute; }
		#knews_dialog_button { display:inline-block; background:#333; color:#fff; font-weight:bold; padding:6px 20px; text-decoration:none; }
		#knews_dialog_button:hover { background:#aaa; color:#000; }
	</style>
	<div id="knews_dialog">
		<div id="knews_dialog_bg">
			<?php 
			$lang_locale = $Knews_plugin->localize_lang($Knews_plugin->getLangs(true), $Knews_plugin->get_safe('lang', substr(get_bloginfo('language'), 0, 2)), get_bloginfo('language'));

			if ($Knews_plugin->get_safe('subscription')=='ok') {
			?>
				<p style="font-size:14px;"><strong><?php echo $Knews_plugin->get_custom_text('subscription_ok_title', $lang_locale); ?></strong></p>
				<p><?php echo $Knews_plugin->get_custom_text('subscription_ok_message', $lang_locale); ?></p>
				<p><a href="#" id="knews_dialog_button" onclick="knews_deleteLayer('knews_dialog')"><?php echo $Knews_plugin->get_custom_text('dialogs_close_button', $lang_locale); ?></a></p>
			<?php 
			} else if ($Knews_plugin->get_safe('subscription')=='error') {
			?>
				<p style="font-size:14px;"><strong><?php echo $Knews_plugin->get_custom_text('subscription_error_title', $lang_locale); ?></strong></p>
				<p><?php echo $Knews_plugin->get_custom_text('subscription_error_message', $lang_locale); ?></p>
				<p><a href="#" id="knews_dialog_button" onclick="knews_deleteLayer('knews_dialog')"><?php echo $Knews_plugin->get_custom_text('dialogs_close_button', $lang_locale); ?></a></p>
			<?php 
			} else if ($Knews_plugin->get_safe('unsubscribe')=='ok') {
			?>
				<p style="font-size:14px;"><strong><?php echo $Knews_plugin->get_custom_text('subscription_stop_ok_title', $lang_locale); ?></strong></p>
				<p><?php echo $Knews_plugin->get_custom_text('subscription_stop_ok_message', $lang_locale); ?></p>
				<p><a href="#" id="knews_dialog_button" onclick="knews_deleteLayer('knews_dialog')"><?php echo $Knews_plugin->get_custom_text('dialogs_close_button', $lang_locale); ?></a></p>
			<?php 
			} else if ($Knews_plugin->get_safe('unsubscribe')=='error') {
			?>
				<p style="font-size:14px;"><strong><?php echo $Knews_plugin->get_custom_text('subscription_stop_error_title', $lang_locale); ?></strong></p>
				<p><?php echo $Knews_plugin->get_custom_text('subscription_stop_error_message', $lang_locale); ?></p>
				<p><a href="#" id="knews_dialog_button" onclick="knews_deleteLayer('knews_dialog')"><?php echo $Knews_plugin->get_custom_text('dialogs_close_button', $lang_locale); ?></a></p>
			<?php 
			}
			?>
		</div>
	</div>
	<?php
	}
?>