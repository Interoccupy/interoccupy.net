<?php
//Security for CSRF attacks
$knews_nonce_action='kn-save-news';
$knews_nonce_name='_savenews';
//End Security for CSRF attacks
?>
<!--[if lte IE 7]>
<script type="text/javascript">
alert('<?php _e("Warning! IE 6/7 can't edit newsletters! The editor uses HTML5 properties, you need upgrade at least to IE8, or use an modern Firefox, Chrome or Safari.",'knews');?>');
</script>
<![endif]-->

<script type="text/javascript" src="<?php echo KNEWS_URL; ?>/wysiwyg/parent_editor.js?ver=<?php echo KNEWS_VERSION; ?>"></script>

<link rel="stylesheet" href="<?php echo KNEWS_URL; ?>/wysiwyg/parent_editor.css?ver=<?php echo KNEWS_VERSION; ?>" type="text/css" media="all" />
<?php
	$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_edit;
	$results_news = $wpdb->get_results( $query );
	if (count($results_news) == 0) {
?>

	<div class=wrap>
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Newsletters','knews'); ?></h2>
		<h3><?php echo __('Error','knews') . ': ' . __("Newsletter doesn't exists",'knews'); ?></h3>
	</div>
<?php
	} else {
		$parentid=0;
?>
<script type="text/javascript">
	url_plugin = '<?php echo KNEWS_URL; ?>';
	news_lang='<?php echo $results_news[0]->lang; ?>';
	droppable_code='<?php echo $results_news[0]->html_container; ?>';
	id_news='<?php echo $Knews_plugin->get_safe('idnews');?>';
	<?php
	$one_post = get_posts(array('numberposts' => 1) );
	if (count($one_post)!=1) $one_post = get_pages();
	echo 'one_post_id=' . intval($one_post[0]->ID) . ';';
	?>
	submit_news='<?php echo get_admin_url(); ?>admin.php?page=knews_news&section=send&id=<?php echo (($parentid==0) ? $Knews_plugin->get_safe('idnews') : $parentid);?>';
	reload_news='<?php echo get_admin_url(); ?>admin.php?page=knews_news&section=edit&idnews=<?php echo $Knews_plugin->get_safe('idnews') ;?>';
	
	must_apply_undo = "<?php _e('You are in image edition mode. You must press Apply or Undo image changes (or press ESC key) before doing anything.','knews'); ?>";
	edit_image= "<?php echo __('Edit image','knews'); ?>";
	sharp_image= "<?php echo __('Apply changes and refresh image','knews'); ?>";
	undo_image= "<?php echo __('Undo image changes','knews'); ?>";
	properties_image= "<?php echo __('Properties of image','knews'); ?>";
	post_handler= "<?php echo __('Insert post/page content','knews'); ?>";
	free_handler= "<?php echo __('Free text content','knews'); ?>";
	move_handler= "<?php echo __('Move module','knews'); ?>";
	delete_handler= "<?php echo __('Delete module','knews'); ?>";
	unsaved_message= "<?php echo addslashes(__('If you leave now this page, the Newsletter changes will be lost. Please, cancel and press the "Save" button (blue coloured).','knews')); ?>";
	url_admin = "<?php echo get_admin_url(); ?>";
	error_resize = "<?php echo __('Error','knews') . ': ' . __('Check the directory permissions for','knews'); ?> '/wp-content/uploads'";
	error_save = "<?php  echo __('Error saving','knews'); ?>";
	ok_save = "<?php  echo __('Newsletter saved','knews'); ?>";
	button_continue_editing = "<?php  echo __('Continue editing','knews'); ?>";
	button_submit_newsletter = "<?php  echo __('Submit newsletter','knews'); ?>";

	confirm_delete = "<?php echo __('Do you really want to delete this module?','knews'); ?>";
	button_yes = "<?php echo __('Yes','knews'); ?>";
	button_no = "<?php echo __('No','knews'); ?>";
	button_continue = "<?php echo __('Continue','knews'); ?>";
	
	opera_no = "<?php echo __("Warning! Opera can't edit newsletters. You must use a modern Firefox, Chrome, Safari or at least Internet Explorer 8.",'knews'); ?>";
</script>
	<div class="wrap">
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Editing newsletter','knews'); ?>: <?php echo $results_news[0]->name; ?></h2>
		<div id="poststuff">
			<div id="titlediv">
				<?php
				$lang_attr='';
				if ($Knews_plugin->get_safe('lang') != '') {
					$lang_attr='&lang=' . $Knews_plugin->get_safe('lang');
				}
				?>
				<div class="wysiwyg_toolbar">
					<?php /*
					<a href="#" class="move" title="move"></a>
					<a href="#" class="minimize" title="minimize"></a>
					<span class="clear"></span>*/?>
					<div class="image_properties">
						<span class="img_handler">
							<?php /*<input type="button" value="Edit image" class="change_image button" />
							<a title="Edit image" class="change_image" href="#"></a>
							<a title="Apply changes and refresh image" class="rredraw_image" href="#"></a>
							<a title="Undo image changes" class="uundo_image" href="#"></a>*/?>
						</span>
						<p><label><?php _e('Image URL:','knews'); ?></label><a href="#" class="change_image"></a><input type="text" name="image_url" id="image_url" readonly="readonly" /></p>
						<p><label><?php _e('Image link:','knews'); ?> <a href="#" title="<?php _e('Put a url if you want to put link around the image, for example: http://www.mysite.com/mypage.html.','knews'); ?>"><img src="<?php echo KNEWS_URL; ?>/images/help2.gif" width="16" height="16" alt="" /></a></label><input type="text" name="image_link" id="image_link" /></p>
						<p><label><?php _e('Image alt:','knews'); ?> <a href="#" title="<?php _e('The alternate text is very important, because most mail clients block initial image load and show the alternate image text.','knews'); ?>"><img src="<?php echo KNEWS_URL; ?>/images/help2.gif" width="16" height="16" alt="" /></a></label><textarea name="image_alt" id="image_alt"></textarea></p>
						<div class="alignable"><p><?php _e('Image align:','knews'); ?> <select name="image_align" id="image_align"><option value="">none</option>
						<option value="left">left</option>
						<option value="right">right</option>
						<option value="top">top</option>
						<option value="texttop">texttop</option>
						<option value="middle">middle</option>
						<option value="absmiddle">absmiddle</option>
						<option value="baseline">baseline</option>
						<option value="bottom">bottom</option>
						<option value="absbottom">absbottom</option>
						</select></p></div>
						<?php /*<p class="line size"><label>Width:</label><input type="text" name="image_w" id="image_w" /> x <label>Height:</label><input type="text" name="image_h" id="image_h" /></p>*/ ?>
						<p class="line extra" dir="ltr"><label>Border:</label><input type="text" name="image_b" id="image_b" /> <label>Hspace:</label><input type="text" name="image_hs" id="image_hs" /> <label>Vspace:</label><input type="text" name="image_vs" id="image_vs" /></p>
						<span class="clear"></span>
						<p class="buttons">
						<input type="button" value="<?php _e('Apply changes','knews'); ?>" class="rredraw_image button-primary" /><input type="button" value="<?php _e('Undo','knews');?>" class="uundo_image button" /></p>
					</div>
					<div class="tools">
						<?php /*<a href="#" class="toggle_handlers toggle_handlers_off" title="<?php _e('Show/hide handlers','knews'); ?>"></a>*/?>
						<span class="clear"></span>
					</div>
					<div class="save_button">
						<a href="#" class="button-primary" onClick="save_news(); return false;"><?php _e('Save','knews');?></a>
					</div>
					<div class="plegable">
					<?php 
					$query = "SELECT * FROM ".KNEWS_NEWSLETTERS." WHERE id=" . $id_edit;
					$results_news = $wpdb->get_results( $query );

					//if (count($results_news) != 0) {
						$code = $results_news[0]->html_modules;
						/*for ($a=1; $a<20; $a++) {
							$code = str_replace('modules/module_' . $a . '.jpg','modules/module_' . $a . '.jpg?r=' . uniqid(),$code);
						}*/
						echo $code;
					//}
					?>
					</div>
					<div class="resize">
						<a href="#" title="<?php _e('Resize toolbox','knews');?>">&nbsp;</a>
					</div>
				</div>
				<div id="titlewrap">
					<label for="title" id="title-prompt-text" style="" class="hide-if-no-js"><?php _e('Subject','knews'); ?></label>
					<input type="text" autocomplete="off" id="title" value="<?php echo $results_news[0]->subject; ?>" tabindex="1" size="30" name="post_title">
				</div>
				<div class="editor_iframe">
					<div id="botonera">
						<div class="right_icons">
							<a href="#" title="hidden CSS preview" class="previewCSS" onclick="b_preview('css'); return false;">-</a>
							<a href="#" title="hidden images preview" class="previewIMG" onclick="b_preview('img'); return false;">-</a>
							<select name="zoom" id="zoom" autocomplete="off"><option value="0.5">50%</option><option value="0.75">75%</option><option value="1" selected="selected">100%</option><option value="1.5">150%</option><option value="2">200%</option><option value="4">400%</option></select>
						</div>
						<div class="standard_buttons desactivada">
							<a href="#" title="bold" class="bold" onclick="b_simple('Bold'); return false;">B</a>
							<a href="#" title="italic" class="italic" onclick="b_simple('Italic'); return false;">I</a>
							<a href="#" title="strike-through" class="strike" onclick="b_simple('StrikeThrough'); return false;">St</a>
							<a href="#" title="insert image" class="image" onclick="b_insert_image(); return false;">i</a>
							<a href="#" title="link" class="link" onclick="b_link(); return false;">A</a>
							<a href="#" title="UN-link" class="no_link" onclick="b_del_link(); return false;">(A)</a>
						</div>
						<div class="justify_buttons desactivada">
							<a href="#" title="align: Left" class="just_l" onclick="b_justify('left'); return false;">L</a>
							<a href="#" title="align: Center" class="just_c" onclick="b_justify('center'); return false;">C</a>
							<a href="#" title="align: Right" class="just_r" onclick="b_justify('right'); return false;">R</a>
							<a href="#" title="align: Justify" class="just_j" onclick="b_justify('justify'); return false;">J</a>
						</div>
						<div class="standard_buttons desactivada">
							<a href="#" class="sup" title="superscript" onclick="b_simple('Superscript'); return false;">sp</a>
							<a href="#" class="sub" title="subscript" onclick="b_simple('Subscript'); return false;">sb</a>
							<a href="#" class="color" title="change color" onclick="b_color(); return false;">C</a>
						</div>
						<div class="do_undo_buttons">
							<a href="#" class="undo" title="undo" onclick="b_simple('undo'); return false;">U</a>
							<a href="#" class="redo" title="redo" onclick="b_simple('redo'); return false;">R</a>
						</div>

						<span class="clear"></span>
					</div>
					<div class="iframe_container"><iframe class="knews_editor" id="knews_editor" name="knews_editor" style="width:100%; height:100px" src="<?php echo get_admin_url() . 'admin-ajax.php?action=knewsEditNewsletter&idnews=' . $id_edit . '&r=' . uniqid() . $lang_attr; ?>"></iframe></div>
					<div id="tagsnav"></div>
				</div>
				<div class="drag_preview"></div>
			</div>
		</div>
	</div>
<?php
	//Security for CSRF attacks
	wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
	}
?>
