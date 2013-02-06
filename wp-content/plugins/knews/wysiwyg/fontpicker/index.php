<?php
global $Knews_plugin;

if ($Knews_plugin) {

	if (!$Knews_plugin->initialized) $Knews_plugin->init();

	$font_family = array (	'Times New Roman, Times, serif',
							'Arial, Helvetica, sans-serif',
							'Verdana, Geneva, sans-serif',
							'Georgia, Times New Roman, Times, serif',
							'Courier New, Courier, monospace',
							'Tahoma, Geneva, sans-serif',
							'Trebuchet MS, Arial, Helvetica, sans-serif',
							'Arial Black, Gadget, sans-serif',
							'Palatino Linotype, Book Antiqua, Palatino, serif',
							'Lucida Sans Unicode, Lucida Grande, sans-serif'
						);
	
	$input_ff=$font_family[2]; if (isset($_GET['ff'])) $input_ff=htmlspecialchars ($_GET['ff']);
	$input_fs=2; if (isset($_GET['fs'])) $input_fs=intval($_GET['fs']);
	$input_ss=0; if (isset($_GET['ss'])) $input_ss=intval($_GET['ss']);
	$input_lh=0; if (isset($_GET['lh'])) $input_lh=intval($_GET['lh']);
	
	if (!in_array($input_ff, $font_family)) $font_family[]=$input_ff;
	
	$font_size = array (	array('1 / 10px','1','10'),
							array('2 / 13px','2','13'),
							array('3 / 16px','3','16'),
							array('4 / 18px','4','18'),
							array('5 / 24px','5','24'),
							array('6 / 32px','6','32'),
							array('7 / 48px','7','48')
						);
	
	if ($input_fs>7 || $input_fs<1) $input_fs=2;
	
	if ($input_ss!=0) {
		$found_fs=false;
		foreach ($font_size as $ffs) {
			if ($ffs[0]==$input_fs . ' / ' . $input_ss . 'px') $found_fs=true;
		}
		if (!$found_fs) $font_size[]=array($input_fs . ' / ' . $input_ss . 'px',$input_fs,$input_ss);
	}
	
	$demo_text = 'One morning, when Gregor Samsa woke from troubled dreams, he found himself transformed in his bed into a horrible vermin. He lay on his armour-like back, and if he lifted his head a little he could see his brown belly, slightly domed and divided by arches into stiff sections. The bedding was hardly able to cover it and seemed ready to slide off any moment. His many legs, pitifully thin compared with the size of the rest of him, waved about helplessly as he looked.';
	
	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>Font picker</title>
	<script type="text/javascript">
	
	demo_text = '<?php echo $demo_text; ?>';
	
	font_size = [
	<?php 
	$opt=0;
	foreach ($font_size as $fs) {
		if ($opt != 0) echo ',';
		echo "['" . $fs[0] . "','" . $fs[1] . "','" . $fs[2] . "']";
		$opt++;
	}
	?>
	];
	</script>
	<script type="text/javascript" src="<?php echo KNEWS_URL; ?>/wysiwyg/fontpicker/scripts.js"></script>
	<link href="<?php echo KNEWS_URL; ?>/wysiwyg/fontpicker/estils.css" rel="stylesheet" type="text/css" />
	</head>
	
	<body>
	<div class="container">
		<form method="post" action=".">
		<p><label>Font family:</label><select name="fontfamily" id="fontfamily">
		<?php 
		$opt=0;
		foreach ($font_family as $ff) {
			echo '<option value="' . $ff . '"';
			if ($input_ff==$ff) echo ' selected="selected"';
			echo '>' . $ff . '</option>';
			$opt++;
		}
		?>
		</select></p>
		<p><label>Font size:</label><select name="fontsize" id="fontsize">
		<?php
		$opt=0;
		foreach ($font_size as $fs) {
			echo '<option value="' . $opt . '"';
			if ($input_ss!=0) {
				if ($input_fs . ' / ' . $input_ss . 'px'==$fs[0]) echo ' selected="selected"';
			} else {
				if ($input_fs==$fs[1]) echo ' selected="selected"';
			}
	
			echo '>' . $fs[0] . '</option>';
			$opt++;
		}
		?>
		</select></p>
		<?php
		if ($input_lh !=0) {
		?>
		<p><label>Line height:</label><input type="text" name="lineheight" id="lineheight" value="<?php echo $input_lh; ?>" style="border:#000 1px solid;"> px (0 for auto)</p>
		<?php 
		} else {
		?>
		<input type="hidden" name="lineheight" id="lineheight" value="0" />
		<?php
		}
		?>
		<p>Text demo (CSS mail enabled):</p>
		<div class="preview_text">
			<?php
			if ($input_ss!=0) {
				echo '<p style="font-family:' . $input_ff . '; font-size:' . $input_ss . 'px;';
				if ($input_lh !=0) echo ' line-height:' . $input_lh . 'px';
				echo '">' . $demo_text . '</p>';
			} else {
				echo '<font size="' . $input_fs . '" face="' . $input_ff . '">' . $demo_text . '</font>';
			}
			?>
		</div>
		<p>Text demo (without CSS):</p>
		<div class="preview_text_no_css">
			<?php
				echo '<font size="' . $input_fs . '" face="' . $input_ff . '">' . $demo_text . '</font>';
			?>
		</div>
		<div class="buttons">
			<input type="button" value="Save" onclick="select_font()" /> <input type="button" value="Cancel" onclick="parent.tb_remove();" />
		</div>
		</form>
	</div>
	</body>
	</html>
<?php
}

die();
?>