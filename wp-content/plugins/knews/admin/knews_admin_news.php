<?php
	require_once( KNEWS_DIR . '/includes/knews_util.php');

	global $wpdb, $Knews_plugin;

	$langs_code = array();
	$langs_name = array();
	
	//if (KNEWS_MULTILANGUAGE) {
		
		$languages = $Knews_plugin->getLangs(true);
		
		if(!empty($languages)){
			foreach($languages as $l){
				$langs_code[] = $l['language_code'];
				$langs_name[] = $l['native_name'];
			}
		}
	//}

	$section = $Knews_plugin->get_safe('section');
	$id_edit = $Knews_plugin->get_safe('idnews', 0, 'int');

	$mobile=false;
	if ($Knews_plugin->post_safe('action')=='add_news_mobile') $mobile=true;

	if ($section=='' && ($Knews_plugin->post_safe('action')=='add_news' || $mobile)) {

		$name = $Knews_plugin->post_safe('new_news');
		$lang = $Knews_plugin->post_safe('lang');
		$url_template = $Knews_plugin->post_safe('url_' . $Knews_plugin->post_safe('template'));
		$path_template = $Knews_plugin->post_safe('path_' . $Knews_plugin->post_safe('template'));

		$lang_localized='';
		if(!empty($languages)){
			foreach ($languages as $l) {
				if ($l['language_code']==$lang) {
					$lang_localized = $l['localized_code'];
					break;
				}
			}
		}

		if ($lang_localized != '' || $lang =='') {
			if ($name != '' || $mobile) {
	
				if ($mobile) {
					$query = "SELECT * FROM " . KNEWS_NEWSLETTERS . " WHERE id_mobile=0 AND id=" . $Knews_plugin->post_safe('parent', 0, 'int');
				} else {
					$query = "SELECT * FROM " . KNEWS_NEWSLETTERS . " WHERE name='" . $name . "'";
				}
				$results = $wpdb->get_results( $query );
				
				if ((!$mobile && count($results)==0) || ($mobile && count($results)!=0)) {
					
					$template = $Knews_plugin->post_safe('template');
	
					if ($template != '') {
	
						$fileTemplate = $path_template . $Knews_plugin->post_safe('template') . (($mobile) ? '/mobile.html' : '/template.html');
						$fh = fopen($fileTemplate, 'r');
						$codeTemplate = fread($fh, filesize($fileTemplate));
						fclose($fh);
		
						$codeTemplate = str_replace('  ', ' ', $codeTemplate);
						$codeTemplate = str_replace('<!-- ', '<!--', $codeTemplate);
						$codeTemplate = str_replace(' -->', '-->', $codeTemplate);
						$codeTemplate = str_replace('<!--[ ', '<!--[', $codeTemplate);
						$codeTemplate = str_replace(' ]-->', ']-->', $codeTemplate);
						
						if ($lang_localized =='' ) $lang_localized='en_US';
						$codeTemplate = str_replace('%cant_read_text_1%', $Knews_plugin->get_custom_text('cant_read_text_1', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%cant_read_text_link%', $Knews_plugin->get_custom_text('cant_read_text_link', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%cant_read_text_2%', $Knews_plugin->get_custom_text('cant_read_text_2', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%unsubscribe_text_1%', $Knews_plugin->get_custom_text('unsubscribe_text_1', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%unsubscribe_text_link%', $Knews_plugin->get_custom_text('unsubscribe_text_link', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%unsubscribe_text_2%', $Knews_plugin->get_custom_text('unsubscribe_text_2', $lang_localized), $codeTemplate);
						$codeTemplate = str_replace('%read_more_link%', $Knews_plugin->get_custom_text('read_more_link', $lang_localized), $codeTemplate);
						
						if ($Knews_plugin->get_custom_text('text_direction', $lang_localized)=='rtl') {
							$codeTemplate = str_replace('dir="ltr"', 'dir="rtl"', $codeTemplate);							
						}

						$codeTemplate = str_replace('align = "left"', 'align="left"', $codeTemplate);							
						$codeTemplate = str_replace('align= "left"', 'align="left"', $codeTemplate);							
						$codeTemplate = str_replace('align ="left"', 'align="left"', $codeTemplate);							

						$codeTemplate = str_replace('align = "right"', 'align="right"', $codeTemplate);							
						$codeTemplate = str_replace('align= "right"', 'align="right"', $codeTemplate);							
						$codeTemplate = str_replace('align ="right"', 'align="right"', $codeTemplate);							

						if ($Knews_plugin->get_custom_text('default_alignment', $lang_localized)=='right') {
							$codeTemplate = str_replace('align="left"', 'align = "right"', $codeTemplate);							
						}

						if ($Knews_plugin->get_custom_text('inverse_alignment', $lang_localized)=='left') {
							$codeTemplate = str_replace('align="right"', 'align = "left"', $codeTemplate);							
						}

						$codeTemplate = str_replace('<!--[start_editable_content]-->', '<span class="content_editable">', $codeTemplate);
						$codeTemplate = str_replace('<!--[end_editable_content]-->', '</span>', $codeTemplate);
		
						$headTemplate = substr($codeTemplate, 0, strpos($codeTemplate, '</head>')+7);
	
						$bodyTemplate = cut_code('<body>', '</body>', $codeTemplate, true);
	
						$bodyTemplate = str_replace('"images/', '"' . $url_template . $Knews_plugin->post_safe('template') . '/images/', $bodyTemplate);
						$bodyTemplate = str_replace('url(images', 'url(' . $url_template . $Knews_plugin->post_safe('template') . '/images/', $bodyTemplate);
		
						$count_modules=0; $found_module=true; $codeModule='';
						while ($found_module) {
							$found_module=false;
		
							if (strpos($bodyTemplate, '[start module ' . ($count_modules + 1) . ']') !== false) {
								$found_module=true;
	
								$codeModule .= '<div class="insertable"><img src="' . $url_template . $Knews_plugin->post_safe('template') . '/modules/module' . $Knews_plugin->post_safe('vp_' . $Knews_plugin->post_safe('template')) . '_' . ($count_modules + 1) . '.jpg" width="220" height="90" alt="" /><div class="html_content">';
								
								$extracted_module = cut_code('<!--[start module ' . ($count_modules + 1) . ']-->', '<!--[end module ' . ($count_modules + 1) . ']-->', $bodyTemplate, true);
								$codeModule .= $extracted_module . '</div></div>';
								
								if (strpos($extracted_module, 'dont_cut_module') === false) {
									$bodyTemplate = extract_code('<!--[start module ' . ($count_modules + 1) . ']-->', '<!--[end module ' . ($count_modules + 1) . ']-->', $bodyTemplate, true);
								}
								$count_modules++;
							}
						}
						
						$containerModulesTemplate =	cut_code('<!--[open_insertion_container_start]-->', '<!--[close_insertion_container_start]-->', $bodyTemplate, true) .
													cut_code('<!--[open_insertion_container_end]-->', '<!--[close_insertion_container_end]-->', $bodyTemplate, true);
						
						$bodyTemplate = iterative_extract_code('<!--[open_ignore_code]-->', '<!--[close_ignore_code]-->', $bodyTemplate, true);
						$bodyTemplate = iterative_extract_code('<!--[', ']-->', $bodyTemplate, true);
						$codeTemplate = str_replace('  ', ' ', $codeTemplate);
		
						$date = $Knews_plugin->get_mysql_date();
						
						if (!is_utf8($bodyTemplate)) $bodyTemplate=utf8_encode($bodyTemplate);
						if (!is_utf8($headTemplate)) $headTemplate=utf8_encode($headTemplate);
						if (!is_utf8($codeModule)) $codeModule=utf8_encode($codeModule);
	
						$bodyTemplate = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($bodyTemplate));
						$headTemplate = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($headTemplate));
						$codeModule = mysql_real_escape_string($Knews_plugin->htmlentities_corrected($codeModule));
	
						$sql = "INSERT INTO " . KNEWS_NEWSLETTERS . "(name, created, modified, template, html_mailing, html_head, html_modules, html_container, subject, lang, automated, mobile, id_mobile) VALUES ('" . $name . "', '" . $date . "', '" . $date . "','" . $template . "','" . $bodyTemplate . "','" . $headTemplate . "','" . $codeModule . "','" . $containerModulesTemplate . "','', '" . $Knews_plugin->post_safe('lang') . "', 0, " . (($mobile) ? "1" : "0") . ", 0)";
						if ($wpdb->query($sql)) {
							$id_edit=$wpdb->insert_id; $id_edit2=mysql_insert_id(); if ($id_edit==0) $id_edit=$id_edit2;
							
							$section='add_news';
							echo '<div class="updated"><p>' . __('The newsletter has been created successfully','knews') . '</p></div>';
						} else {
							echo '<div class="error"><p><strong>' . __('Error','knews') . ': </strong>' . __('Failed to create the Newsletter','knews') . ' : ' . $wpdb->last_error . '</p></div>';
						}
					} else {
						echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('You must choose a template!','knews') . '</p></div>';
					}
				} else {
					echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('There is already another newsletter with the same name!','knews') . '</p></div>';
				}
			} else {
				echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __('You must choose a name for the new newsletter!','knews') . '</p></div>';
			}
		} else {
			echo '<div class="error"><p><strong>' . __('Error','knews') . ':</strong> ' . __("The language can't be localized",'knews') . '</p></div>';
		}
		
	}

	if ($section=='edit') {
		require( KNEWS_DIR . '/admin/knews_admin_news_edit.php');
	} else if ($section=='send') {
		require( KNEWS_DIR . '/admin/knews_admin_news_send.php');		
	} else if ($section=='add_news') {
		?>
		<script type="text/javascript">
			function goto_editor() {
				location.href = '<?php echo get_admin_url(); ?>admin.php?page=knews_news&section=edit&idnews=<?php echo $id_edit; ?>';
			}
			jQuery(document).ready ( function() {
				setTimeout ('goto_editor()', 1000); // 1 second
			});
		</script>
		<p><a href="<?php bloginfo('url')?>/wp-admin/admin.php?page=knews_news&section=edit&idnews=<?php echo $id_edit; ?>"><?php _e('Redirecting to editor...','knews'); ?></a></p>
		<?php
	} else {
		require( KNEWS_DIR . '/admin/knews_admin_news_list.php');
	}
?>
