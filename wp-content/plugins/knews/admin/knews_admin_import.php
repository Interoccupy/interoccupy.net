<?php
//Security for CSRF attacks
$knews_nonce_action='kn-adm-import';
$knews_nonce_name='_importadm';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks

	ini_set('auto_detect_line_endings', true);
	
	global $wpdb, $Knews_plugin;
	global $knews_delimiters, $knews_enclosure, $knews_encode, $knews_line_endings, $knews_import_errors, $knews_import_users_error, $col_options, $submit_confirmation_id, $confirmation_sql_count;
	
    // specify allowed field delimiters
    $knews_delimiters = array(
        'comma ,'     => ',',
        'semicolon ;' => ';',
        'tab'         => "\t",
        'pipe |'         => '|',
        'colon :'     => ':',
        'space'     => ' ',
    );

    $knews_enclosure = array(
        'double'	=> '"',
        'simple'	=> '\''
    );

    $knews_encode = array(
        'iso-8859-1'	=> 'iso',
        'UTF-8'			=> 'utf8',
        'MS-DOS'		=> 'msdos',
		'Macintosh'		=> 'mac'
    );

    /* specify allowed line endings
    $knews_line_endings = array(
        'rn'         => "\r\n",
        'n'         => "\n",
        'r'         => "\r",
        'nr'         => "\n\r"
    );*/

	$submit_confirmation_id=0;
	$confirmation_sql_count=0;

	$step = $Knews_plugin->post_safe('step', 1);
	$filename = $Knews_plugin->post_safe('filename', '', 'unsafe');
	
	if (isset($_FILES['file_csv']['tmp_name'])) {
		$filename = KNEWS_DIR . '/tmp/' . pathinfo($_FILES['file_csv']['tmp_name'], PATHINFO_FILENAME);
		if ( move_uploaded_file($_FILES['file_csv']['tmp_name'], $filename) ) {
			echo '<p>' . __('File uploaded correctly','knews'). '</p>';
		} else {
			echo '<p>' . __("Error: can't move uploaded file.",'knews') . ' ' . __('The directory /wp-content/plugins/knews/tmp must be writable (chmod 700)', 'knews') . '</p>';
		}
	} else {
		if ($step > 1) {
			if (!is_file($filename)) {
				echo '<p>' . __('You must upload a file.','knews') . ' ' . __('The directory /wp-content/plugins/knews/tmp must be writable (chmod 700)', 'knews') . '</p>';
				$step = 1;
			}
		}
	}

	if (!is_file($filename)) $step = 1;
	
	function put_format_selects() {

		global $knews_delimiters, $knews_enclosure, $knews_encode, $knews_line_endings, $Knews_plugin;

		?>
			<p><?php _e('Separator','knews'); ?>: <select name="knews_delimiters" id="knews_delimiters">
				<?php
				while ($d = current($knews_delimiters)) {
					echo '<option value="' . key($knews_delimiters) . '"' . (( $Knews_plugin->post_safe('knews_delimiters') == key($knews_delimiters)) ? ' selected="selected"' : '') . '>' . key($knews_delimiters) . '</option>';
					next($knews_delimiters);
				}
				?>
			</select>
			<?php _e('Enclosure','knews'); ?>: <select name="knews_enclosure" id="knews_enclosure">
				<?php
				while ($d = current($knews_enclosure)) {
					echo '<option value="' . key($knews_enclosure) . '"' . (( $Knews_plugin->post_safe('knews_enclosure') == key($knews_enclosure)) ? ' selected="selected"' : '') . '>' . key($knews_enclosure) . '</option>';
					next($knews_enclosure);
				}
				?>
			</select>
			<?php _e('Encoding','knews'); ?>: <select name="knews_encode" id="knews_encode">
				<?php
				while ($d = current($knews_encode)) {
					echo '<option value="' . key($knews_encode) . '"' . (( $Knews_plugin->post_safe('knews_encode') == key($knews_encode)) ? ' selected="selected"' : '') . '>' . key($knews_encode) . '</option>';
					next($knews_encode);
				}
				?>
			</select></p>
			<p><input type="checkbox" name="knews_has_header" id="knews_has_header" value="1"<?php if ( $Knews_plugin->post_safe('knews_has_header', 0, 'int') == 1 ) echo ' checked="checked"'; ?> /> <?php _e('The first row is a header','knews'); ?></p>
		<?php
			/*
			Finals de linia: <select name="knews_line_endings" id="knews_line_endings">
				<?php
				while ($d = current($knews_line_endings)) {
					echo '<option value="' . key($knews_line_endings) . '"' . (( $Knews_plugin->post_safe(key($knews_line_endings) ) == key($knews_line_endings)) ? ' selected="selected"' : '') . '>' . key($knews_line_endings) . '</option>';
					next($knews_line_endings);
				}
				?>
			</select>
			*/
	}

?>
<style type="text/css">
table#informimport {
	border:#CCC 1px solid;
	border-bottom:0;
}
table#informimport td {
	padding:5px;
	border-bottom:#CCC 1px solid;
}
table#previewimport{
	border-left:#CCC 1px solid;
	border-top:#CCC 1px solid;
}
table#previewimport td {
	padding:5px;
	border-right:#CCC 1px solid;
	border-bottom:#CCC 1px solid;
}
table#previewimport tr.alt td,
table#informimport tr.alt td {
	background:#F1F1F1;
}
span.help {
	font-size:10px;
	color:#999;
}
p.knews_progress {
	height:40px;
}
p.knews_progress span {
	display:block;
	float:left;
	padding:0 10px 20px 10px;
	color:#333;
	background:url(<?php echo KNEWS_URL; ?>/images/progress_pass.gif) no-repeat center bottom;
}
p.knews_progress span.first {
	background:url(<?php echo KNEWS_URL; ?>/images/progress_hi.gif) no-repeat left bottom;
	padding-left:0;
}

p.knews_progress span.wait {
	color:#999;
	background:url(<?php echo KNEWS_URL; ?>/images/progress.gif) no-repeat center bottom;
}
p.knews_progress span.on {
	color:#21759B;
	background:url(<?php echo KNEWS_URL; ?>/images/progress_hi.gif) no-repeat center bottom;
	font-weight:bold;
	font-size:13px;
}

</style>
<script type="text/javascript">
	jQuery(document).ready( function () {
		jQuery('select.custom_field').change( function() {
			if (jQuery(this).val()=='custom') {
				jQuery('#c_' + jQuery(this).attr('id')).show().focus();
			} else {
				jQuery('#c_' + jQuery(this).attr('id')).hide();							
			}
		});
		jQuery('select#joined_col_val').change( function() {
			if (jQuery(this).val()!='now') {
				jQuery('span.date_order_container').show();
			} else {
				jQuery('span.date_order_container').hide();
			}
		});
		
		jQuery('input#date_test').click( function() {
				the_form=jQuery(this).closest('form');
				jQuery('input#step', the_form).val(parseInt(jQuery('input#step', the_form).val())-1);
				jQuery(the_form).submit();
		});
		
		jQuery('#back_import').click( function() {
			the_form=jQuery(this).closest('form');
			jQuery('input#step', the_form).val(parseInt(jQuery('input#step', the_form).val())-2);
			the_form.submit();
		});
		jQuery('form.assign').submit( function() {
			if (parseInt(jQuery('input#step', this).val()) == parseInt(jQuery('input#next_step', this).val())) {
				if (jQuery('#email_col').val()=='') {
					alert("E-mail");
					return false;
				}
				somelist=false;
				jQuery('input.check_list').each(function(n, obj) {
					if (jQuery(obj).attr("checked")) somelist=true;
				});
	
				if (!somelist) {
					/* Traduction pending */
					if (!confirm('<?php _e('Warning! You arent selected any mailing list, do you want to continue?','knews'); ?>')) return false;
				}
			}
		});
			
	})
</script>
<?php
function print_state($step, $where) {

	global $knews_nonce_action, $knews_nonce_name, $Knews_plugin;

	if ($where < $step) return;
	if ($where == $step) {
		echo ' class="on"';
		return;
	}
	echo ' class="wait"';
}
?>
	<div class="wrap">
		<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div><h2><?php _e('Import CSV','knews');?></h2>
		<p class="knews_progress">
			<span class="first"><?php _e('Progress','knews'); ?>:</span>
			<span<?php print_state($step, 1); ?>>1. <?php _e('CSV Upload','knews'); ?></span>
			<span<?php print_state($step, 2); ?>>2. <?php _e('Formatting','knews'); ?></span>
			<span<?php print_state($step, 3); ?>>3. <?php _e('Fields assignement','knews'); ?></span>
			<span<?php print_state($step, 4); ?>>4. <?php _e('Preview','knews'); ?></span>
			<span<?php print_state($step, 5); ?>>5. <?php _e('Import','knews'); ?></span>
		</p>
		<?php
		if ($step=='1') {
		?>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" enctype="multipart/form-data">
			<input type="hidden" name="step" id="step" value="2" />
			<p><?php _e('CSV File','knews'); ?>: <input type="file" name="file_csv" id="file_csv" /></p>
			<p><?php _e('If you do not know what values ​​to put, leave these initially, later you can change it','knews'); ?>:</p>
			<?php put_format_selects(); ?>
			<div class="submit">
				<input type="submit" value="<?php _e('File upload','knews');?>" class="button-primary" />
			</div>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
			</form>
		<?php
		} elseif ($step=='2') {
			echo "<p>" . __("You should be seeing the header and the four first lines of the CSV:", 'knews') . '</p><p>&middot; ' .
			__('Properly tabulated', 'knews') . '<br>&middot; ' . __('No delimiters','knews') . '<br>&middot; ' . __('Using special characters (accents) correctly coded','knews') . '</p><p>' . 
			__('If not, change the separator values and / or closing. Then press "Scan CSV again":','knews') . "</p>";
			
			//PHP 5.3 en endavant: $csv_data=fgetcsv($filename, 10000, $knews_delimiters[$_POST['knews_delimiters']], $knews_enclosure[$_POST['knews_enclosure']], $knews_line_endings[$_POST['knews_line_endings']]);
			
			if (($handle = fopen($filename, "r")) !== FALSE) {
				echo '<table border="0" cellpadding="0" cellspacing="0" id="previewimport">';
				$what_row = 0;
				
				while (($csv_data = fgetcsv($handle, 10000, $knews_delimiters[$_POST['knews_delimiters']], $knews_enclosure[$_POST['knews_enclosure']])) !== FALSE) {
					$what_row++;
					if ($what_row%2 != 0) echo '<tr class="alt">'; else echo '<tr>';

					if ($Knews_plugin->post_safe('knews_has_header', 0, 'int')==0 && $what_row==1) {
						$what_row++;
						$what_col=0;
						foreach ($csv_data as $my_col) {
							$what_col++;
							echo '<td><strong>Col ' . $what_col . '</strong></td>';
						}
						echo '</tr>';
						echo '<tr>';
					}

					foreach ($csv_data as $my_col) {
						echo '<td>' . (($what_row==1) ? '<strong>' : '') . re_de_encode($my_col) . (($what_row==1) ? '</strong>' : '') . '</td>';
					}
					echo '</tr>';
					if ($what_row==5) break;
				}
				echo '</table>';
			} else {
				echo '<p>' . __("Error: can't open the file", 'knews') . '</p>';
			}
		?>
			<p>&nbsp;</p>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
			<input type="hidden" name="step" id="step" value="2" />
			<input type="hidden" name="filename" id="filename" value="<?php echo $filename ?>" />
			<?php put_format_selects(); ?>
			<div class="submit">
				<input type="submit" value="<?php _e('Scan CSV again','knews'); ?>" class="button-secondary" />
			</div>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
			</form>

			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
			<input type="hidden" name="step" id="step" value="3" />
			<input type="hidden" name="next_step" id="next_step" value="3" />
			<input type="hidden" name="filename" id="filename" value="<?php echo $filename ?>" />
			<p><?php _e('If  you see the correct values, press "Continue"','knews'); ?></p>
			<input type="hidden" name="knews_delimiters" id="knews_delimiters" value="<?php echo $Knews_plugin->post_safe('knews_delimiters'); ?>" />
			<input type="hidden" name="knews_enclosure" id="knews_enclosure" value="<?php echo $Knews_plugin->post_safe('knews_enclosure'); ?>" />
			<input type="hidden" name="knews_encode" id="knews_encode" value="<?php echo $Knews_plugin->post_safe('knews_encode'); ?>" />
			<input type="hidden" name="knews_has_header" id="knews_has_header" value="<?php echo $Knews_plugin->post_safe('knews_has_header'); ?>" />

			<div class="submit">
				<input type="submit" value="<?php _e('Continue','knews'); ?>" class="button-primary" />
			</div>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
			</form>
		<?php
		} elseif ($step=='3') {
		?>
			<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" class="assign" >
			<input type="hidden" name="step" id="step" value="4" />
			<input type="hidden" name="next_step" id="next_step" value="4" />
			<input type="hidden" name="filename" id="filename" value="<?php echo $filename ?>" />
			<input type="hidden" name="knews_delimiters" id="knews_delimiters" value="<?php echo $Knews_plugin->post_safe('knews_delimiters'); ?>" />
			<input type="hidden" name="knews_enclosure" id="knews_enclosure" value="<?php echo $Knews_plugin->post_safe('knews_enclosure'); ?>" />
			<input type="hidden" name="knews_encode" id="knews_encode" value="<?php echo $Knews_plugin->post_safe('knews_encode'); ?>" />
			<input type="hidden" name="knews_has_header" id="knews_has_header" value="<?php echo $Knews_plugin->post_safe('knews_has_header'); ?>" />

		<?php
			$col_options=array();
			
			if (($handle = fopen($filename, "r")) !== FALSE) {
				echo '<table border="0" cellpadding="0" cellspacing="0" id="previewimport">';
				$what_row = 0;
				while (($csv_data = fgetcsv($handle, 10000, $knews_delimiters[$_POST['knews_delimiters']], $knews_enclosure[$_POST['knews_enclosure']])) !== FALSE) {
					$what_row++;
					if ($what_row%2 != 0) echo '<tr class="alt">'; else echo '<tr>';
					$what_col = 0;

					if ($Knews_plugin->post_safe('knews_has_header', 0, 'int')==0 && $what_row==1) {
						$what_row++;
						$what_col=0;
						foreach ($csv_data as $my_col) {
							$what_col++;
							echo '<td><strong>Col ' . $what_col . '</strong></td>';
							$col_options[$what_col] = 'Col ' . $what_col;
						}
						echo '</tr>';
						echo '<tr>';
					}

					foreach ($csv_data as $my_col) {
						$what_col++;
	
						if ($what_row==1) {
							$col_options[$what_col] = re_de_encode($my_col);
						}
						echo '<td>' . (($what_row==1) ? '<strong>' : '');
						
						if ($what_row!=1 && $Knews_plugin->post_safe('joined_col_val', 0, 'int') == $what_col) {
							echo data_process($my_col, true);
						} else {
							echo re_de_encode($my_col);
						}
						
						echo (($what_row==1) ? '</strong>' : '') . '</td>';
					}
					echo '</tr>';
					if ($what_row==5) break;
				}
				echo '</table>';
			} else {
				echo '<p>' . __("Error: can't open the file", 'knews') . '</p>';
			}
		?>
			<p><strong>E-mail</strong>: <select name="email_col" id="email_col"><option value=""><?php _e('Select a column','knews'); ?></option><?php print_col_options('email_col'); ?></select><br /><span class="help"><?php _e('This field is required to take it in some column','knews'); ?></span></p>
			<p><strong><?php _e('State','knews'); ?></strong>: <select name="state_col_val" id="state_col_val">
			<option value="val_1"<?php if ($Knews_plugin->post_safe('state_col_val')=='val_1') echo ' selected="selected"'; ?>><?php echo __('All','knews') . ': ' . __('not confirmed','knews');?></option>
			<option value="val_2"<?php if ($Knews_plugin->post_safe('state_col_val')=='val_2') echo ' selected="selected"'; ?>><?php echo __('All','knews') . ': ' . __('confirmed','knews');?></option>
			<option value="val_3"<?php if ($Knews_plugin->post_safe('state_col_val')=='val_3') echo ' selected="selected"'; ?>><?php echo __('All','knews') . ': ' . __('blocked','knews');?></option><?php print_col_options('state_col_val'); ?></select><br /><span class="help"><?php _e('If you choose a column, allowed values ​​are:','knews') . ' <strong>1</strong>: ' . __('Not confirmed','knews') . ', <strong>2</strong>: ' . __('Confirmed','knews') . ', <strong>3</strong>: ' . __('Blocked','knews');?></span></p>
			<p><strong><?php _e('Permission','knews');?></strong>: <input type="checkbox" name="confirm" value="1" id="confirm" <?php if ($Knews_plugin->post_safe('confirm')=='1') echo ' checked="checked"'; ?> /> <?php _e('Send a confirmation e-mail to the unconfirmed users (not to the locked ones)','knews');?></p>
			<p><strong><?php _e('Language','knews');?></strong>: <select name="lang_col_val" id="lang_col_val"><?php print_col_options('lang_col_val');
			$languages = $Knews_plugin->getLangs();
			foreach ($languages as $lang) {
				echo '<option value="' . $lang['language_code'] . '"' . (($Knews_plugin->post_safe('lang_col_val')==$lang['language_code']) ? ' selected="selected"' : '') . '>' . __('All in','knews') . ' ' . $lang['translated_name'] . '</option>';
			}
			?></select>
			<br /><span class="help"><?php _e('If you choose a column, allowed values ​​are:','knews'); ?> <strong>fr</strong>: fran&ccedil;ais, <strong>en</strong>: english, <strong>es</strong>: espa&ntilde;ol, etc.</span></p>
			<p><strong><?php _e('User join date','knews');?></strong>: <select name="joined_col_val" id="joined_col_val"><option value="now"<?php if ($Knews_plugin->post_safe('date_order') == 'now') echo ' selected="selected"'; ?>><?php _e('All: today','knews');?></option><?php print_col_options('joined_col_val'); ?></select><span class="date_order_container" <?php if ($Knews_plugin->post_safe('joined_col_val', 0, 'int') == 0) echo ' style="display:none"'; ?>>
			<select name="date_order" id="date_order">
			<option value="dd-mm-yy"<?php if ($Knews_plugin->post_safe('date_order') == 'dd-mm-yy') echo ' selected="selected"'; ?>><?php _e('day-month-year','knews'); ?></option>
			<option value="mm-dd-yy"<?php if ($Knews_plugin->post_safe('date_order') == 'mm-dd-yy') echo ' selected="selected"'; ?>><?php _e('month-day-year','knews'); ?></option>
			<option value="yy-mm-dd"<?php if ($Knews_plugin->post_safe('date_order') == 'yy-mm-dd') echo ' selected="selected"'; ?>><?php _e('year-month-day','knews'); ?></option></select>
			<input type="button" value="<?php _e('Check dates','knews'); ?>" id="date_test" />
			</span>
			</p>
			<?php
			$extra_fields = $Knews_plugin->get_extra_fields();
			foreach ($extra_fields as $ef) {
				echo '<p><strong>' . $ef->name . '</strong>: <select class="custom_field" name="ef_' . $ef->name . '" id="ef_' . $ef->name . '">' . 
				'<option value="empty"' . (($Knews_plugin->post_safe('ef_' . $ef->name) == 'empty') ? ' selected="selected"' : '') . '>' . __('Leave empty','knews') . '</option>';
				print_col_options('ef_' . $ef->name);
				echo '<option value="custom"' . (($Knews_plugin->post_safe('ef_' . $ef->name) == 'custom') ? ' selected="selected"' : '') . '>' . __('introduce value','knews') . '</option></select><input type="text"' . (($Knews_plugin->post_safe('ef_' . $ef->name) != 'custom') ? ' style="display:none"' : '') . ' name="c_ef_' . $ef->name . '" id="c_ef_' . $ef->name . '" value="' . $Knews_plugin->post_safe('c_ef_' . $ef->name) . '"><br>
				 <span class="help">' . __('If you enter a value manually, this will be the same for all users imported','knews') . '</span></p>';
			}
			?>
			<p><strong><?php _e('Subscribe all users to the following mailing lists:','knews'); ?></strong><br />
			<?php
			$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
			$lists = $wpdb->get_results( $query );
			foreach ($lists as $ln) {
				echo '<input type="checkbox" value="1" name="list_' . $ln->id . '" id="list_' . $ln->id . '" class="check_list"';
				if ($Knews_plugin->post_safe('list_' . $ln->id, 0, 'int') == 1) echo ' checked="checked"';
				echo '>' . $ln->name . '<br>';
			}
			?>
			<p><strong><?php _e('If a user already exists (matches e-mail)','knews'); ?></strong>: <select name="overwrite" id="overwrite">
			<option value="no"<?php if ($Knews_plugin->post_safe('overwrite') == 'no') echo ' selected="selected"'; ?>><?php _e('Maintain current data','knews'); ?></option>
			<option value="yes"<?php if ($Knews_plugin->post_safe('overwrite') == 'yes') echo ' selected="selected"'; ?>><?php _e('Overwrite','knews'); ?></option>
			<option value="add"<?php if ($Knews_plugin->post_safe('overwrite') == 'add') echo ' selected="selected"'; ?>><?php _e('Mantain current data and add new mailing lists','knews'); ?></option></select>
			<div class="submit">
				<input type="button" value="<?php _e('Go back','knews'); ?>" id="back_import" />
				<input type="submit" value="<?php _e('Make preview','knews'); ?>" class="button-primary" />
			</div>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
			</form>
		<?php
		} elseif ($step=='4' || $step=='5') {
			if ($step=='4') {
			?>
				<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" >
				<input type="hidden" name="step" id="step" value="5" />
				<input type="hidden" name="next_step" id="next_step" value="5" />
				<input type="hidden" name="filename" id="filename" value="<?php echo $filename ?>" />
				<input type="hidden" name="knews_delimiters" id="knews_delimiters" value="<?php echo $Knews_plugin->post_safe('knews_delimiters'); ?>" />
				<input type="hidden" name="knews_enclosure" id="knews_enclosure" value="<?php echo $Knews_plugin->post_safe('knews_enclosure'); ?>" />
				<input type="hidden" name="knews_encode" id="knews_encode" value="<?php echo $Knews_plugin->post_safe('knews_encode'); ?>" />
				<input type="hidden" name="knews_has_header" id="knews_has_header" value="<?php echo $Knews_plugin->post_safe('knews_has_header'); ?>" />
				<input type="hidden" name="email_col" id="email_col" value="<?php echo $Knews_plugin->post_safe('email_col'); ?>" />
				<input type="hidden" name="state_col_val" id="state_col_val" value="<?php echo $Knews_plugin->post_safe('state_col_val'); ?>" />
				<input type="hidden" name="confirm" id="confirm" value="<?php echo $Knews_plugin->post_safe('confirm'); ?>" />
				<input type="hidden" name="lang_col_val" id="lang_col_val" value="<?php echo $Knews_plugin->post_safe('lang_col_val'); ?>" />
				<input type="hidden" name="joined_col_val" id="joined_col_val" value="<?php echo $Knews_plugin->post_safe('joined_col_val'); ?>" />
				<input type="hidden" name="date_order" id="date_order" value="<?php echo $Knews_plugin->post_safe('date_order'); ?>" />
				
				<?php
				//Security for CSRF attacks
				wp_nonce_field($knews_nonce_action, $knews_nonce_name); 

				$extra_fields = $Knews_plugin->get_extra_fields();
				foreach ($extra_fields as $ef) {
					echo '<input type="hidden" name="ef_' . $ef->name . '" id="ef_' . $ef->name . '" value="' . $Knews_plugin->post_safe('ef_' . $ef->name) . '">';
					echo '<input type="hidden" name="c_ef_' . $ef->name . '" id="c_ef_' . $ef->name . '" value="' . $Knews_plugin->post_safe('c_ef_' . $ef->name) . '">';
				}
				
				$query = "SELECT * FROM " . KNEWS_LISTS . " ORDER BY orderlist";
				$lists = $wpdb->get_results( $query );
				foreach ($lists as $ln) {
					echo '<input type="hidden" name="list_' . $ln->id . '" id="list_' . $ln->id . '" value="' . $Knews_plugin->post_safe('list_' . $ln->id) . '">';
				}
				?>
				<input type="hidden" name="overwrite" id="overwrite" value="<?php echo $Knews_plugin->post_safe('overwrite'); ?>" />
			<?php
			}
			$knews_import_errors=array();
			$import_users_total=0;
			$import_users_ok=0;
			$knews_import_users_error=0;

			$import_users_new=0;
			$import_users_overwrite=0;

			$import_users_confirm=0;
			$import_users_blocked=0;
			
			$max_fields=0;
			
			if (($handle = fopen($filename, "r")) !== FALSE) {
				$what_row = 0;

				$query = "SELECT id FROM " . KNEWS_LISTS . " ORDER BY orderlist";
				$lists_name = $wpdb->get_results( $query );
				$extra_fields = $Knews_plugin->get_extra_fields();

				while (($csv_data = fgetcsv($handle, 10000, $knews_delimiters[$_POST['knews_delimiters']], $knews_enclosure[$_POST['knews_enclosure']])) !== FALSE) {
					
					if ($max_fields < count($csv_data)) $max_fields = count($csv_data);
					
					$what_row++;
					$user_csv=array();
					if (($what_row != 1 || $Knews_plugin->post_safe('knews_has_header', 0, 'int')==0)) {
					//if (($what_row != 1 || intval($Knews_plugin->post_safe('knews_has_header'))==0) && count($csv_data)==$max_fields) {
						$import_users_total++;
						foreach ($csv_data as $my_col) {
							$user_csv[] = re_de_encode($my_col);
						}

						$confkey = $Knews_plugin->get_unique_id();
						$email=$user_csv[$Knews_plugin->post_safe('email_col', 0, 'int')-1];

						if (substr($_POST['state_col_val'],0,4)=='val_') {
							$state=intval(substr($_POST['state_col_val'],4));
						} else {
							$state=intval($user_csv[$Knews_plugin->post_safe('state_col_val', 0, 'int')-1]);
						}
						if ($Knews_plugin->post_safe('lang_col_val', 0, 'int') > 0) {
							$lang=$user_csv[$Knews_plugin->post_safe('lang_col_val', 0, 'int')-1];
						} else {
							$lang=$Knews_plugin->post_safe('lang_col_val');
						}
						if ($Knews_plugin->post_safe('joined_col_val') == 'now') {
							$date = $Knews_plugin->get_mysql_date();
						} else {
							if ($step=='4') {
								$date=data_process($user_csv[$Knews_plugin->post_safe('joined_col_val', 0, 'int')-1], true);
								if ($date=='#error#') addError (__('Sign up date user cant be understood','knews'), false); 
							} else {
								$date=data_process($user_csv[$Knews_plugin->post_safe('joined_col_val', 0, 'int')-1]);
							}
						}

						if ($Knews_plugin->validEmail($email)) {
							if ($state == 1 || $state == 2 || $state ==3) {
								$languages = $Knews_plugin->getLangs(true);
								
								if ( $Knews_plugin->localize_lang($languages, $lang, '') != '' ) {
	
									$query = "SELECT * FROM " . KNEWS_USERS . " WHERE email='" . $email . "'";
									$user_found = $wpdb->get_results( $query );
									
									if (count($user_found)==0 || $Knews_plugin->post_safe('overwrite')=='yes' || $Knews_plugin->post_safe('overwrite')=='add') {

										$import_users_ok++;

										if (count($user_found)==0) {
											$import_users_new++;
											if ($step=='5') {
												//Add new user
												$query = "INSERT INTO " . KNEWS_USERS . " (email, lang, state, joined, confkey) VALUES ('" . 
															$email . "','" . $lang . "', $state, '" . $date . "','" . $confkey . "');";
												$results = $wpdb->query( $query );
												$id_new_user=$wpdb->insert_id; $id_new_user2=mysql_insert_id(); if ($id_new_user==0) $id_new_user=$id_new_user2;


												if ($results) {

													//The lists
													foreach ($lists_name as $ln) {
														if (isset($_POST['list_'.$ln->id])) {
															if ($Knews_plugin->post_safe('list_'.$ln->id)=='1') {
										
																$query="INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $id_new_user . ", " . $ln->id . ")";
																$results = $wpdb->query( $query );
																
															}
														}
													}
													
													foreach ($extra_fields as $ef) {
										
														//Insert fields
														$cf=$Knews_plugin->post_safe('ef_' . $ef->name);
														if ($cf != '') {
															if ($cf=='custom') {
																$cf=$Knews_plugin->post_safe('c_ef_' . $ef->name);
															} elseif ($cf=='empty') {
																$cf='';
															} else {
																$cf=$user_csv[intval($cf)-1];
															}
															$Knews_plugin->set_user_field ($id_new_user, $ef->id, mysql_real_escape_string($cf));
														}
													}
													
													//Confirm
													if ($state==1 && $Knews_plugin->post_safe('confirm', 0, 'int')==1) {
														add_confirm($id_new_user);
													}
												} else {
													$import_users_ok--;
													$import_users_new--;
													addError (__('SQL Error while inserting user','knews'));
												}
											}
										} else {
											if ($Knews_plugin->post_safe('overwrite') == 'yes') {

												$import_users_overwrite++;
												if ($step=='5') {
													//Update user
													$id_updated_user=$user_found[0]->id;
													$query = "UPDATE " . KNEWS_USERS . " SET lang='" . $lang . "', state=" . $state . ", joined='" . $date . "' WHERE id=" . $id_updated_user;
													$results = $wpdb->query( $query );
													if ($results) {
	
														$query="DELETE FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $id_updated_user;
														$results = $wpdb->query( $query );
	
														//The lists
														foreach ($lists_name as $ln) {
															if ($Knews_plugin->post_safe('list_'.$ln->id)=='1') {
											
																$query="INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $id_updated_user . ", " . $ln->id . ")";
																$results = $wpdb->query( $query );
															}
														}
		
														foreach ($extra_fields as $ef) {
											
															//Insert fields
															$cf=$Knews_plugin->post_safe('ef_' . $ef->name);
															if ($cf=='custom') {
																$cf=$Knews_plugin->post_safe('c_ef_' . $ef->name);
															} elseif ($cf=='empty') {
																$cf='';
															} else {
																$cf=$user_csv[intval($cf)-1];
															}
															$Knews_plugin->set_user_field ($id_updated_user, $ef->id, mysql_real_escape_string($cf));
														}
																												
														//Confirm
														if ($state==1 && $Knews_plugin->post_safe('confirm', 0, 'int')==1) {
															add_confirm($id_updated_user);
														}
													} else {
														$import_users_ok--;
														$import_users_overwrite--;
														addError (__('SQL Error while updating user','knews'));
													}
												}
											} elseif ($Knews_plugin->post_safe('overwrite') == 'add') {

												$import_users_overwrite++;
												if ($step=='5') {
													$id_updated_user=$user_found[0]->id;
	
													//The lists
													foreach ($lists_name as $ln) {
														if ($Knews_plugin->post_safe('list_'.$ln->id)=='1') {
										
															$query="SELECT * FROM " . KNEWS_USERS_PER_LISTS . " WHERE id_user=" . $id_updated_user . " AND id_list=" . $ln->id;
															$results = $wpdb->get_results( $query );

															if ( count($results) == 0 ) {

																$query="INSERT INTO " . KNEWS_USERS_PER_LISTS . " (id_user, id_list) VALUES (" . $id_updated_user . ", " . $ln->id . ")";
																$results = $wpdb->query( $query );
															}
														}
													}
	
													
													//Confirm
													if ($state==1 && $Knews_plugin->post_safe('confirm', 0, 'int')==1) {
														add_confirm($id_updated_user);
													}
												}
											}
										}
										if ($state==3) $import_users_blocked++;
										if ($state==1) $import_users_confirm++;
										
										//Alta usuari
									} else {
										addError(__('The user already exists','knews'));
									}
								} else {
									addError(__('Unknown language','knews'));
								}
							} else {
								addError(__('Wrong state','knews'));
							}
						} else {
							addError(__('Wrong e-mail','knews'));
						}
					}
				}

				if ($step=='5' && $confirmation_sql_count!=0) {
					$query = "UPDATE " . KNEWS_NEWSLETTERS_SUBMITS . " SET paused=0, users_total=" . $confirmation_sql_count . " WHERE id=" . $submit_confirmation_id;
					$results = $wpdb->query( $query );
				}

				//Informe
				if ($step=='4') {
					echo '<p>' . __('Import preview (we have not made ​​any changes yet:','knews') . '</p>';
				} else {
					echo '<p>' . __('Import results','knews') . '</p>';
				}
				?>
				<table border="0" cellpadding="0" cellspacing="0" id="informimport">
				<tr class="alt"><td>&nbsp;</td><td><?php _e('CSV total:','knews');?></td><td><?php echo $import_users_total; ?> <?php _e('users','knews');?></td></tr>
				<?php if ($import_users_ok != 0) { ?>
				<tr><td><img src="<?php echo KNEWS_URL; ?>/images/green_led.gif" width="20" height="20" alt="OK" /></td><td><?php _e('Of which have been successfully imported:','knews');?></td><td><?php echo $import_users_ok; ?> <?php _e('users','knews');?></td></tr>
				<?php } ?>
				<?php if ($knews_import_users_error != 0) { ?>
				<tr><td><img src="<?php echo KNEWS_URL; ?>/images/red_led.gif" width="20" height="20" alt="ERROR" /></td><td><?php _e('And there have not been imported:','knews')?></td><td><?php echo $knews_import_users_error; ?> <?php _e('users','knews');?></td></tr>
				<?php } ?>
				<tr><td>&nbsp;</td><td><?php _e('There have been created:','knews'); ?></td><td><?php echo $import_users_new; ?> <?php _e('users','knews');?></td></tr>
				<?php if ($import_users_overwrite != 0) { ?>
				<tr><td>&nbsp;</td><td><?php _e('There have been updated:','knews'); ?></td><td><?php echo $import_users_overwrite; ?> <?php _e('users','knews');?></td></tr>
				<?php } ?>
				<?php if ($import_users_confirm != 0) { ?>
				<tr>
					<td><img src="<?php echo KNEWS_URL; ?>/images/yellow_led.gif" width="20" height="20" alt="WARNING" /></td><td>
					<?php
					if ($Knews_plugin->post_safe('confirm', 0, 'int')==1) {
						_e('Automatically send confirmation e-mails:','knews');
					} else {
						_e('Users unconfirmed (You should do confirmation manually):','knews');
					}
					?>
				</td><td><?php echo $import_users_confirm; ?> <?php _e('users','knews');?></td></tr>
				<?php } ?>
				<?php if ($import_users_blocked != 0) { ?>
				<tr><td><img src="<?php echo KNEWS_URL; ?>/images/yellow_led.gif" width="20" height="20" alt="WARNING" /></td><td><?php _e('And have been blocked in this import:','knews');?></td><td><?php echo $import_users_blocked; ?> <?php _e('users','knews');?></td></tr>
				<?php }
				if (count($knews_import_errors)!=0) {
					echo '<tr class="alt"><td>&nbsp;</td><td>' . __('Total errors:','knews') . '</td><td>&nbsp;</td></tr>';
					$mykeys = array_keys($knews_import_errors);
					foreach ($mykeys as $mk) {
						echo '<tr><td><img src="' . KNEWS_URL . '/images/red_led.gif" width="20" height="20" alt="ERROR" /></td><td>' . $mk . '</td><td>' . $knews_import_errors[$mk] . ' ' . __('users','knews') . '</td></tr>';
					}
				}
				?>
				</table>
				<?php
			} else {
				echo '<p>' . __("Error: can't open the file",'knews') . '</p>';
			}

			if ($step=='4') {
				?>
				<div class="submit">
					<input type="button" value="<?php _e('Go back','knews');?>" id="back_import" /> <input type="submit" value="<?php _e('Do the import','knews');?>" class="button-primary" />
				</div>
				<?php
			} else {
				?>
				<p><?php _e('Import finished','knews');?></p>
				<?php
			}
		}
		
function addError($txt, $fatal=true) {
	global $knews_import_errors, $knews_import_users_error;
	
	if (isset($knews_import_errors[$txt])) {
		$knews_import_errors[$txt]++;
	} else {
		$knews_import_errors[$txt]=1;
	}
	if ($fatal) $knews_import_users_error++;
}

function re_de_encode($text) {
	global $Knews_plugin;
	
	if ($Knews_plugin->post_safe('knews_encode') == 'iso-8859-1') {

		return utf8_encode($text);

	} elseif ($Knews_plugin->post_safe('knews_encode') == 'UTF-8') {
		
		return $text;
		
	} elseif ($Knews_plugin->post_safe('knews_encode') == 'MS-DOS') {

		//Extended ascii: from 128 to 175
		$ascii=array(	'Ç','ü',
						'é','â','ä','à','å','ç','ê','ë','è','ï',
						'î','ì','Ä','Å','É','æ','Æ','ô','ö','ò',
						'û','ù','ÿ','Ö','Ü','ø','£','Ø','×','ƒ',
						'á','í','ó','ú','ñ','Ñ','ª','º','¿','®',
						'¬','½','¼','¡','«','»');
	
		for ($i=0; $i<strlen($text); $i++) {
			$char = $text{$i};
			$asciivalue = ord($char);
			if ($asciivalue > 127 && $asciivalue < 176) {
				$text = substr($text, 0, $i) . $ascii[$asciivalue-128] . substr($text, $i+1);
				$i=$i + strlen($ascii[$asciivalue-128]) -1;
			}
		}
		return $text;

	} elseif ($Knews_plugin->post_safe('knews_encode') == 'Macintosh') {

		//Extended mac ascii: from 128 to 250
		$mac=array(	'Ä','Å',
					'Ç','É','Ñ','Ö','Ü','á','à','â','ä','ã',
					'å','ç','é','è','ê','ë','í','ì','î','ï',
					'ñ','ó','ò','ô','ö','õ','ú','ù','û','ü',
					'†','°','¢','£','§','•','¶','ß','®','©',
					'™','´','¨','­','Æ','Ø','°','±','_','_',
					'¥','µ','¶','·','¸','_','º','ª','º','_',
					'æ','ø','¿','¡','¬','Ã','ƒ','Å','Æ','«',
					'»','…','_','À','Ã','Õ','Œ','œ','–','—',
					'"','"',"'","'",'÷','_','ÿ','Y','/','_',
					'<','>','_','_','‡','·',"'",'"','‰','Â',
					'Ê','Á','Ë','È','Í','Î','Ï','Ì','Ó','Ô',
					'_','Ò','Ú','Û','Ù','õ','ö','÷','¯','ù');

		for ($i=0; $i<strlen($text); $i++) {
			$char = $text{$i};
			$asciivalue = ord($char);
			if ($asciivalue > 127 && $asciivalue < 251) {
				$text = substr($text, 0, $i) . $mac[$asciivalue-128] . substr($text, $i+1);
				$i=$i + strlen($mac[$asciivalue-128]) -1;
			}
		}
		return $text;

	} else {
		
		return $text;
	}
}

function print_col_options($field) {

	global $col_options, $Knews_plugin;

	for ($x=1; $x<=count($col_options); $x++) {
		echo '<option value="' . $x . '"';
		if ($Knews_plugin->post_safe($field, 0, 'int')==$x) echo ' selected="selected"';
		echo '>Col ' . $x . ' [' . $col_options[$x] . ']</option>';
	}
}

function data_process($txt, $human=false) {
	
	global $Knews_plugin;
	
	if (strpos($txt, '/') !== false) {
		$separator='/';
	} else {
		$separator='-';
	}
	$separate=explode($separator,$txt);
	
	if (count($separate) >2) {
	
		if ($Knews_plugin->post_safe('date_order')=='dd-mm-yy') {
	
			$year = intval($separate[2]);
			$month = intval($separate[1]);
			$day = intval($separate[0]);
	
		} elseif ($Knews_plugin->post_safe('date_order')=='mm-dd-yy') {
	
			$year = intval($separate[2]);
			$month = intval($separate[0]);
			$day = intval($separate[1]);
	
		} else {
	
			$year = intval($separate[0]);
			$month = intval($separate[1]);
			$day = intval($separate[2]);
	
		}

		if ($year < 100) {
			if ($year > 50) {
				$year = $year + 1900;
			} else {
				$year = $year + 2000;
			}
		}
	} else {
		$year = 0;
		$month = 0;
		$day = 0;
	}
	
	if (checkdate($month, $day, $year)) {
		if ($human) {
			 $date=date("d-M-Y", mktime(0, 0, 0, $month, $day, $year));
		} else {
			$date = $year . '-' . $month . '-' . $day . ' 00:00:00';
		}
	} else {
		if ($human) {
			$date="#error#";
		} else {
			$date = $Knews_plugin->get_mysql_date();
		}
	}
	
	return $date;
}

function add_confirm($id_user) {
	
	global $submit_confirmation_id, $confirmation_sql_count, $wpdb, $Knews_plugin;
	
	if ($submit_confirmation_id == 0) {
		$mysqldate = $Knews_plugin->get_mysql_date();
		$query = 'INSERT INTO ' . KNEWS_NEWSLETTERS_SUBMITS . ' (blog_id, newsletter, finished, paused, start_time, users_total, users_ok, users_error, priority, strict_control, emails_at_once, special, end_time) VALUES (' . get_current_blog_id() . ', 0, 0, 1, \'' . $mysqldate . '\', 0, 0, 0, 5, \'\', 10, \'import_confirm\', \'0000-00-00 00:00:00\')';
		$results = $wpdb->query( $query );
		$submit_confirmation_id=$wpdb->insert_id; $submit_confirmation_id2=mysql_insert_id(); if ($submit_confirmation_id==0) $submit_confirmation_id=$submit_confirmation_id2;
		//echo $query;
	}
	
	$query = 'INSERT INTO ' . KNEWS_NEWSLETTERS_SUBMITS_DETAILS . ' (submit, user, status) VALUES (' . $submit_confirmation_id . ', ' . $id_user . ', 0)';
		//echo $query;
	$results = $wpdb->query( $query );
	$confirmation_sql_count++;
}
		?>
	</div>
