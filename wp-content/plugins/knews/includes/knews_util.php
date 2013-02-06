<?php
// Returns true if $string is valid UTF-8 and false otherwise.
function is_utf8($str) {
    $c=0; $b=0;
    $bits=0;
    $len=strlen($str);
    for($i=0; $i<$len; $i++){
        $c=ord($str[$i]);
        if($c > 128){
            if(($c >= 254)) return false;
            elseif($c >= 252) $bits=6;
            elseif($c >= 248) $bits=5;
            elseif($c >= 240) $bits=4;
            elseif($c >= 224) $bits=3;
            elseif($c >= 192) $bits=2;
            else return false;
            if(($i+$bits) > $len) return false;
            while($bits > 1){
                $i++;
                $b=ord($str[$i]);
                if($b < 128 || $b > 191) return false;
                $bits--;
            }
        }
    }
    return true;
}

function cut_code($start, $end, $code, $delete) {
	$start_pos = strpos($code, $start);
	$end_pos = strpos($code, $end, $start_pos+strlen($start));

	if ($delete) {
		$start_pos = $start_pos + strlen($start);
	} else {
		$end_pos = $end_pos + strlen($end);
	}
	
	if ($start_pos === false || $end_pos === false) return '';
	return substr($code, $start_pos, $end_pos-$start_pos);	
}

function extract_code($start, $end, $code, $delete) {

	$start_pos = strpos($code, $start);
	$end_pos = strpos($code, $end, $start_pos+strlen($start));

	if (!$delete) {
		$start_pos = $start_pos + strlen($start);
	} else {
		$end_pos = $end_pos + strlen($end);
	}
	
	if ($start_pos === false || $end_pos === false) return $code;
	return substr($code, 0, $start_pos) . substr($code, $end_pos);	
}

function iterative_extract_code($start, $end, $code, $delete) {
	$pre = $code;
	$post = extract_code($start, $end, $code, $delete);
	while ($pre != $post) {
		$pre=$post;
		$post = extract_code($start, $end, $post, $delete);
	}
	return $post;
}

function prettyCut($text, $amountChars, $termination) {
	$text = strip_tags($text);
	$text = trim(str_replace("  ", " ", $text));

	if (strlen($text) > $amountChars) {
		$subChain = substr($text, 0, $amountChars);
		$indexLastSpace = strrpos($subChain," ");
		$text = substr($text,0, $indexLastSpace) . $termination;	
	}
	return $text;
}

function extractAndCut($inic, $end, $theHtml) {
	$pos = strpos($theHtml, $inic);
	$pos2 = strpos($theHtml, $end);
	
	$module='';
	if ($pos === false || $pos2 === false) {
	} else {
		$module = substr($theHtml, $pos+strlen($inic), $pos2 - ($pos + strlen($inic)));
	}
	
	return $module;
}
/*
function normalize($text) {
	return utf8tohtml($text, false);
}

// Thanks to silverbeat -eat- gmx -hot- at
function utf8tohtml($utf8, $encodeTags) {
    $result = '';
    for ($i = 0; $i < strlen($utf8); $i++) {
        $char = $utf8[$i];
        $ascii = ord($char);
        if ($ascii < 128) {
            // one-byte character
            $result .= ($encodeTags) ? htmlentities($char) : $char;
        } else if ($ascii < 192) {
            // non-utf8 character or not a start byte
        } else if ($ascii < 224) {
            // two-byte character
            $result .= htmlentities(substr($utf8, $i, 2), ENT_QUOTES, 'UTF-8');
            $i++;
        } else if ($ascii < 240) {
            // three-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $unicode = (15 & $ascii) * 4096 +
                       (63 & $ascii1) * 64 +
                       (63 & $ascii2);
            $result .= "&#$unicode;";
            $i += 2;
        } else if ($ascii < 248) {
            // four-byte character
            $ascii1 = ord($utf8[$i+1]);
            $ascii2 = ord($utf8[$i+2]);
            $ascii3 = ord($utf8[$i+3]);
            $unicode = (15 & $ascii) * 262144 +
                       (63 & $ascii1) * 4096 +
                       (63 & $ascii2) * 64 +
                       (63 & $ascii3);
            $result .= "&#$unicode;";
            $i += 3;
        }
    }
    return $result;
}*/

function rgb2hex($code) {
	for ($pos_char = 0; $pos_char < strlen($code); $pos_char++) {

		if (substr($code, $pos_char, 3)=='rgb') {
			
			$start_pos = strpos($code, '(', $pos_char);
			$end_pos = strpos($code, ')', $pos_char);
						
			if ($start_pos < $end_pos && $pos_char + 6 > $start_pos && $start_pos + 16 > $end_pos) {
				
				$rgb_detected = substr($code, $start_pos +1 , $end_pos-$start_pos-1);

				$rgb_detected = str_replace(' ', '', $rgb_detected);
				$rgb_detected = explode(',', $rgb_detected);
				
				if (is_array($rgb_detected) && sizeof($rgb_detected) == 3) {
					list($r, $g, $b) = $rgb_detected;

					$r = dechex($r<0?0:($r>255?255:$r));
					$g = dechex($g<0?0:($g>255?255:$g));
					$b = dechex($b<0?0:($b>255?255:$b));
					
					$colorhex = (strlen($r) < 2?'0':'').$r;
					$colorhex.= (strlen($g) < 2?'0':'').$g;
					$colorhex.= (strlen($b) < 2?'0':'').$b;

					$colorhex = '#' . strtoupper ($colorhex);
					
					$code = substr($code, 0, $pos_char) . $colorhex . substr($code, $end_pos + 1);
				}
			}
		}
	}
	return $code;
}
function examine_template($folder, $templates_path, $templates_url, $popup=false) {
	$xml_info = array (
		'shortname' => $folder,
		'fullname' => 'Not defined',
		'version' => '1.0',
		'url' => '',
		'date' => 'Unknown',
		'author' => 'Unknown',
		'urlauthor' => '',
		'minver' => '1.0.0',
		'onlypro' => 'no',
		'description' => 'Not defined',
		'desktop' => 'no',
		'mobile' => 'no',
		'responsive' => 'no'
	);

	$xml = simplexml_load_file($templates_path . $folder . '/info.xml');

	foreach($xml->children() as $child) {
		$xml_info[$child->getName()] = $child;
	}
	
	if (!$popup || ($xml_info['responsive'] == 'yes' || $xml_info['mobile'] == 'yes')) { 
?>
		<div style="padding:10px 10px 0 10px; float:left; width:250px; height:350px;" class="template">
<?php
		$selectable=false;
		if (version_compare( KNEWS_VERSION, $xml_info['minver'] ) >= 0) {
			if ($xml_info['onlypro'] != 'yes' || $Knews_plugin->im_pro()==true) {
				$selectable=true;
				
				echo '<div style="text-align:center"><a href="#" onclick="'. (($popup) ? 'parent.parent.' : '') . 'jQuery(\'input\', '. (($popup) ? 'parent.parent.' : '') . 'jQuery(this).parent().parent()).attr(\'checked\', true); return false;" title="' . __('Select this template','knews') . '">';
			}
		}
?>
		<img src="<?php echo $templates_url . $folder; ?>/thumbnail.jpg" style="padding-right:20px;" />
		<?php if ($selectable) echo '</a>'; ?></div>
		<div>
			<h1 style="font-size:20px; padding:0 0 10px 0; margin:0">
			<?php
			if ($selectable) echo '<input type="radio" name="template" value="' . $folder . '" />';

			echo $xml_info['shortname'] . ' <span style="font-weight:normal">v' . $xml_info['version'] . '</span></h1>';
			if (version_compare( KNEWS_VERSION, $xml_info['minver'] ) < 0) {
				echo '<p style="color:#e00; font-weight:bold;">';
				printf(__('This template requires Knews version %s you must update Knews before use this template'), $xml_info['minver'] . (($xml_info['onlypro'] == 'yes') ? ' Pro' : ''));
				echo '</p>';
			} else {
				if ($xml_info['onlypro'] == 'yes' && !$Knews_plugin->im_pro()) {
					echo '<p style="color:#e00; font-weight:bold;">';
					printf( __('This template requires the professional version of Knews. You can get it %s here','knews'),'<a href="http://www.knewsplugin.com" target="_blank">');
					echo '</a></p>';
				}
			}
			?>
			<h2 style="font-size:16px; padding:0 0 6px 0; margin:0; line-height:20px;"><?php echo $xml_info['fullname']; ?></h2>
			<p style="font-size:13px; padding:0 0 0 0; margin:0"><strong><?php echo (($xml_info['urlauthor'] != '') ? '<a href="' . $xml_info['urlauthor'] . '" target="_blank">' : '') . $xml_info['author'] . (($xml_info['urlauthor'] != '') ? '</a>' : '') . '</strong> (' . $xml_info['date'] . ')'; ?></p>
			<?php
			if ($xml_info['url'] != '') {
			?>
			<p style="font-size:13px; padding:0 0 0 0; margin:0"><a href="<?php echo $xml_info['url']; ?>" target="_blank"><?php _e('Go to template page','knews'); ?></a></p>
			<?php
			}
			$v=$xml_info['version'];
			$v=substr($v, 0, strpos($v, '.'));
			if ($v=='1') $v='';
			?>
			<input type="hidden" name="vp_<?php echo $folder; ?>" id="vp_<?php echo $folder; ?>" value="<?php echo $v; ?>" />
			<input type="hidden" name="path_<?php echo $folder; ?>" id="path_<?php echo $folder; ?>" value="<?php echo $templates_path; ?>" />
			<input type="hidden" name="url_<?php echo $folder; ?>" id="url_<?php echo $folder; ?>" value="<?php echo $templates_url; ?>" />
			<input type="hidden" name="ver_<?php echo $folder; ?>" id="ver_<?php echo $folder; ?>" value="<?php echo $xml_info['version']; ?>" />
			<p style="margin:0; padding:0; font-size:11px; color:#333"><?php echo $xml_info['description']; ?></p>
		</div>
	</div>
<?php
		return true;
	} else {
		return false;
	}
}

function knews_display_templates($popup=false) {
	
	global $knewsOptions;
	
	$wp_dirs = wp_upload_dir();
	//$absolute_dir = substr($_SERVER['SCRIPT_FILENAME'], 0, strpos($_SERVER['SCRIPT_FILENAME'], 'wp-content'));
	//$wp_dirs['basedir'] = substr($wp_dirs['basedir'], strpos($wp_dirs['basedir'], $absolute_dir));
	if (is_dir($wp_dirs['basedir'] . '/knewstemplates')) {
		chdir ($wp_dirs['basedir'] . '/knewstemplates');
		$folders = scandir( '.' );
		$anytemplate=false;
		foreach ($folders as $folder) {
			if ($folder != '..' && $folder != '.' && is_dir($folder) && is_file($wp_dirs['basedir'] . '/knewstemplates/' . $folder . '/info.xml') && is_file($wp_dirs['basedir'] . '/knewstemplates/' . $folder . '/template.html')) {
				if (examine_template($folder, $wp_dirs['basedir'] . '/knewstemplates/', $wp_dirs['baseurl'] . '/knewstemplates/', $popup)) $anytemplate=true;
			}
		}
	}
	
	if ($anytemplate && $knewsOptions['hide_templates']=='1') return;
	
	chdir (KNEWS_DIR . '/templates');
	$folders = scandir( '.' );
	foreach ($folders as $folder) {
		if ($folder != '..' && $folder != '.' && is_dir($folder) && is_file(KNEWS_DIR . '/templates/' . $folder . '/info.xml') && is_file(KNEWS_DIR . '/templates/' . $folder . '/template.html')) {
			if (examine_template($folder, KNEWS_DIR . '/templates/', KNEWS_URL . '/templates/', $popup)) $anytemplate=true;
		}
	}
	
	if (!$anytemplate && $popup) echo '<p>' . _e('You dont have any mobile template!','knews') . '</p>';
}

?>