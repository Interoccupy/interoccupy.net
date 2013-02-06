<?php
//Security for CSRF attacks
$knews_nonce_action='kn-admin-stats';
$knews_nonce_name='_statsadm';
if (!empty($_POST)) $w=check_admin_referer($knews_nonce_action, $knews_nonce_name);
//End Security for CSRF attacks
?>
<link href="<?php echo KNEWS_URL; ?>/admin/styles.css" rel="stylesheet" type="text/css" />
<div class="wrap knews_stats">
	<div class="icon32" style="background:url(<?php echo KNEWS_URL; ?>/images/icon32.png) no-repeat 0 0;"><br></div>
	<h2><?php _e('Stats','knews'); ?></h2>
<?php 
function drawPie($values, $captions, $filename, $palette="softtones.txt", $background=true, $legend=true) {

	// Dataset definition
	$DataSet = new pData;
	$DataSet->AddPoint($values,"Serie1");
	$DataSet->AddPoint($captions,"Serie2");
	$DataSet->AddAllSeries();
	$DataSet->SetAbsciseLabelSerie("Serie2");
	
	// Initialise the graph  
	$Test = new pChart(430,200);
	$Test->loadColorPalette(KNEWS_DIR . "/includes/pChart/" . $palette);
	if ($background) {
		$Test->drawFilledRoundedRectangle(7,7,423,193,5,240,240,240); //Fons recuadre exterior, X1, Y1, X2, Y2, radi, r, g, b
		$Test->drawRoundedRectangle(5,5,425,195,5,230,230,230); //Filet exterior, X1, Y1, X2, Y2, radi, r, g, b 
	}
	
	// Draw the pie chart
	$Test->setFontProperties(KNEWS_DIR . "/includes/pChart/tahoma.ttf",8);
	if ($legend) {
		$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),215,90,110,PIE_PERCENTAGE_LABEL,TRUE,50,20,0);
	} else {
		$Test->drawPieGraph($DataSet->GetData(),$DataSet->GetDataDescription(),215,90,110,'',TRUE,50,20,0);
	}
	//$Test->drawPieLegend(290,30,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
	
	$Test->Render(KNEWS_DIR . "/tmp/" . $filename);
}

function knews_drawLine($values, $captions, $filename, $palette="softtones.txt", $legend=true) {

	// Dataset definition      
	$DataSet = new pData;
	$n=1;
	foreach ($values as $value_serie) {
		$DataSet->AddPoint($value_serie, "Serie" . $n);
		$DataSet->SetSerieName($captions[$n], "Serie" . $n);
		if ($captions[$n]=='') $DataSet->RemoveSerie( "Serie" . $n); 
		$DataSet->AddSerie("Serie" . $n);
		$n++;
	}

	//$DataSet->AddAllSeries();
	$DataSet->SetAbsciseLabelSerie("Serie" . ($n-1));
	
	$DataSet->SetYAxisName($captions[0]);
	// $DataSet->SetYAxisUnit("u");
	
	// Initialise the graph
	$Test = new pChart(850,230);
	$Test->loadColorPalette(KNEWS_DIR . "/includes/pChart/" . $palette);
	$Test->setFontProperties(KNEWS_DIR . "/includes/pChart/tahoma.ttf",8);
	$Test->setGraphArea(60,20,820,200);
	$Test->drawFilledRoundedRectangle(7,7,843,223,5,240,240,240);
	$Test->drawRoundedRectangle(5,5,845,225,5,230,230,230);
	$Test->drawGraphArea(255,255,255,TRUE);
	$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
	$Test->drawGrid(4,TRUE,230,230,230,50);
	
	// Draw the 0 line
	$Test->setFontProperties(KNEWS_DIR . "/includes/pChart/tahoma.ttf",6);
	$Test->drawTreshold(0,143,55,72,TRUE,TRUE);
	
	// Draw the line graph
	$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
	$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);
	
	// Finish the graph
	//$DataSet->RemoveSerie("Serie1");
	$Test->setFontProperties(KNEWS_DIR . "/includes/pChart/tahoma.ttf",8);
	if ($legend) $Test->drawLegend(75,35,$DataSet->GetDataDescription(),255,255,255);
	$Test->setFontProperties(KNEWS_DIR . "/includes/pChart/tahoma.ttf",10);
	//$Test->drawTitle(60,22,"example 1",50,50,50,585);
	$Test->Render(KNEWS_DIR . "/tmp/" . $filename);
}

function print_options($start, $end, $active) {
	for ($a = $start; $a <= $end; $a++) {
		echo '<option value="' . $a . '"';
		if ($a==$active) echo ' selected="selected"';
		echo '>' . $a . '</option>';
	}
}
function safe_percent($p1, $p2) {
	if ($p2==0) return 0;
	$p3 = round($p1 * 1000 / $p2);
	if ($p3==0) return 0;
	return $p3 / 10;
}

@$fp = fopen(KNEWS_DIR . '/tmp/testwrite.txt', 'w');
if ($fp) {

	global $Knews_plugin, $wpdb;

	// Standard inclusions pChart
	include(KNEWS_DIR . "/includes/pChart/pData.class");  
	include(KNEWS_DIR . "/includes/pChart/pChart.class");  

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	$today = mktime(0, 0, 0, date('n'), date('j'), date('Y'));
	$yesterday = strtotime ('-1 day', $today);

	// Min & max user date
	$query='SELECT MIN(joined) AS min_joined FROM ' . KNEWS_USERS;
	$results=$wpdb->get_results($query);
	$min_joined=$Knews_plugin->sql2time($results[0]->min_joined);
	if (intval($results[0]->min_joined)==0) $min_joined = $yesterday;

/*	$query='SELECT MAX(joined) AS max_joined FROM ' . KNEWS_USERS;
	$results=$wpdb->get_results($query);
	$max_joined=$Knews_plugin->sql2time($results[0]->max_joined);
	if (intval($results[0]->max_joined)==0) $max_joined = time();*/
	
	// Min & max stats
	$query='SELECT MIN(date) AS min_block FROM ' . KNEWS_STATS;
	$results=$wpdb->get_results($query);
	$min_block=$Knews_plugin->sql2time($results[0]->min_block);
	if (intval($results[0]->min_block)==0) $min_block = $yesterday;
	
/*	$query='SELECT MAX(date) AS max_block FROM ' . KNEWS_STATS;
	$results=$wpdb->get_results($query);
	$max_block=$Knews_plugin->sql2time($results[0]->max_block);
	if (intval($results[0]->max_block)==0) $max_block = time();*/

	// Min & max submit date
	$query='SELECT MIN(start_time) AS min_submit FROM ' . KNEWS_NEWSLETTERS_SUBMITS . ' WHERE blog_id=' . get_current_blog_id() . ' AND users_ok <> 0 OR users_error <> 0';
	$results=$wpdb->get_results($query);
	$min_submit=$Knews_plugin->sql2time($results[0]->min_submit);
	if (intval($results[0]->min_submit)==0) $min_submit = $yesterday;

/*	$query='SELECT MAX(end_time) AS max_submit FROM ' . KNEWS_NEWSLETTERS_SUBMITS . ' WHERE users_ok <> 0 OR users_error <> 0';
	$results=$wpdb->get_results($query);
	$max_submit=$Knews_plugin->sql2time($results[0]->max_submit);
	if (intval($results[0]->max_submit)==0) $max_submit = time();*/

	$min_date_chart = $min_joined;
	if ($min_block < $min_joined) $min_date_chart = $min_block;
	if ($min_submit < $min_date_chart) $min_date_chart = $min_submit;
	
/*	$max_date_chart = $max_joined;
	if ($max_block > $max_joined) $max_date_chart = $max_block;
	if ($max_submit > $max_date_chart) $max_date_chart = $max_submit;*/
	
	$max_date_chart=time();

	$select_min_year = date("Y", $min_date_chart);
	$select_max_year = date("Y", $max_date_chart);

	if ($Knews_plugin->post_safe('day_1') != '') {
	
		$selected_min_date_chart = mktime(0, 0, 0, $Knews_plugin->post_safe('month_1'), $Knews_plugin->post_safe('day_1'), $Knews_plugin->post_safe('year_1'));
		$selected_max_date_chart = mktime(0, 0, 0, $Knews_plugin->post_safe('month_2'), $Knews_plugin->post_safe('day_2'), $Knews_plugin->post_safe('year_2'));
		$fast_select='';
				
	} else {
		$fast_select = $Knews_plugin->get_safe('sel','all');
		
		if ($fast_select=='all') {
			$selected_min_date_chart = $min_date_chart;
			$selected_max_date_chart = $max_date_chart;

		} elseif($fast_select=='7') {
			$selected_min_date_chart = strtotime ('-7 day', $today);
			$selected_max_date_chart = time();

		} elseif($fast_select=='30') {
			$selected_min_date_chart = strtotime ('-30 day', $today);
			$selected_max_date_chart = time();

		} elseif($fast_select=='week') {
			$selected_max_date_chart = $today + 1 - date('w') * 60 * 60 *24;
			$selected_min_date_chart = strtotime ('-6 day', $selected_max_date_chart);

		} elseif($fast_select=='month') {
			$month = date('m')-1;
			$year = date('Y');
			if ($month == 0) { $month=12; $year--; }
			$selected_min_date_chart = mktime(0, 0, 0, $month, 1, $year);
			$selected_max_date_chart = mktime(0, 0, 0, $month, date('t',$selected_min_date_chart), $year);
		}
	
	}

	if ($selected_min_date_chart > $selected_max_date_chart) {
		if ($fast_select != 'all' && $fast_select != '') $fast_select=='error';
		$aux = $selected_min_date_chart;
		$selected_min_date_chart = $selected_max_date_chart;
		$selected_max_date_chart = $aux;
	}
	
	if ($selected_min_date_chart > $max_date_chart || $selected_max_date_chart < $min_date_chart) {
		if ($fast_select != 'all' && $fast_select != '') $fast_select=='error';
		$selected_min_date_chart = $min_date_chart;
		$selected_max_date_chart = $max_date_chart;		
	}
	
	$selected_max_date_chart = mktime(23, 59, 59, date('n', $selected_max_date_chart), date('j', $selected_max_date_chart), date('Y', $selected_max_date_chart));

	?>
		<div class="stats_filter">
		<form method="post" action="admin.php?page=knews_stats">
			<p><?php _e('From date:','knews'); ?>
			<select name="day_1"><?php print_options(1,31,date("d", $selected_min_date_chart)); ?></select>
			<select name="month_1"><?php print_options(1,12,date("m", $selected_min_date_chart)); ?></select>
			<select name="year_1"><?php print_options($select_min_year, $select_max_year, date("Y", $selected_min_date_chart)); ?></select>
			
			| <?php _e('to date:','knews'); ?> <select name="day_2"><?php print_options(1,31,date("d", $selected_max_date_chart)); ?></select>
			<select name="month_2"><?php print_options(1,12,date("m", $selected_max_date_chart)); ?></select>
			<select name="year_2"><?php print_options($select_min_year, $select_max_year, date("Y", $selected_max_date_chart)); ?></select>

			<input type="submit" value="<?php _e('Filter','knews');?>" class="button-secondary" /></p>
			<p class="selector"><?php _e('Fast selection:','knews'); if ($fast_select=='all') echo '<strong>';?><a href="admin.php?page=knews_stats&sel=all"><?php _e('All','knews'); ?></a><?php if ($fast_select=='all') echo '</strong>';?> | 
			<?php if ($fast_select=='7') echo '<strong>';?><a href="admin.php?page=knews_stats&sel=7"><?php _e('last 7 days','knews'); ?></a><?php if ($fast_select=='7') echo '</strong>';?> | 
			<?php if ($fast_select=='30') echo '<strong>';?><a href="admin.php?page=knews_stats&sel=30"><?php _e('last 30 days','knews'); ?></a><?php if ($fast_select=='30') echo '</strong>';?> | 
			<?php if ($fast_select=='week') echo '<strong>';?><a href="admin.php?page=knews_stats&sel=week"><?php _e('last week','knews'); ?></a><?php if ($fast_select=='week') echo '</strong>';?> | 
			<?php if ($fast_select=='month') echo '<strong>';?><a href="admin.php?page=knews_stats&sel=month"><?php _e('last month','knews'); ?></a><?php if ($fast_select=='month') echo '</strong>';?></p>
			<?php 
			//Security for CSRF attacks
			wp_nonce_field($knews_nonce_action, $knews_nonce_name); 
			?>
		</form>
		</div>
		<?php
		
		if ($fast_select=='error') {
			echo '<div class="updated"><p>' . __('The date range you have selected is not available.','knews') . '</p></div>';
		}
		/////////////////////////// General users
		
		// Not confirmed users
		$query='SELECT COUNT(state) AS notconf FROM ' . KNEWS_USERS . ' WHERE state=1';
		$results=$wpdb->get_results($query);
		$not_confirmed_users=$results[0]->notconf;

		// Active users
		$query='SELECT COUNT(state) AS active FROM ' . KNEWS_USERS . ' WHERE state=2';
		$results=$wpdb->get_results($query);
		$active_users=$results[0]->active;
		
		// Blocked users
		$query='SELECT COUNT(state) AS blocked FROM ' . KNEWS_USERS . ' WHERE state=3';
		$results=$wpdb->get_results($query);
		$blocked_users=$results[0]->blocked;
		
		$total_users = $not_confirmed_users + $active_users + $blocked_users;

		?>		
		<h3><span><?php _e('Current status of subscriptions','knews'); ?></span></h3>
		<?php
		if ($total_users != 0) {
			drawPie(array($active_users, $not_confirmed_users, $blocked_users), array("", "", ""), 'chart1.png', 'softtones.txt', false, false);
		?>
			<div class="table_float">
				<table border="0" cellpadding="0" cellspacing="0" width="350">
					<tr class="alt">
						<td><img src="<?php echo KNEWS_URL; ?>/images/legend_1.gif" width="13" height="13" alt="1" /></td>
						<td><?php _e('Active users:','knews'); ?></td><td align="right"><?php echo $active_users; ?></td><td align="right"><?php echo safe_percent($active_users, $total_users); ?>%</td>
					</tr>
					<tr>
						<td><img src="<?php echo KNEWS_URL; ?>/images/legend_2.gif" width="13" height="13" alt="2" /></td>
						<td><?php _e('Not confirmed:','knews'); ?></td><td align="right"><?php echo $not_confirmed_users; ?></td><td align="right"><?php echo safe_percent($not_confirmed_users, $total_users); ?>%</td>
					</tr>
					<tr class="alt">
						<td><img src="<?php echo KNEWS_URL; ?>/images/legend_3.gif" width="13" height="13" alt="3" /></td>
						<td><?php _e('Unsubscribed users:','knews'); ?></td><td align="right"><?php echo $blocked_users; ?></td><td align="right"><?php echo safe_percent($blocked_users, $total_users); ?>%</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><?php _e('Total:','knews'); ?></td><td align="right"><?php echo $total_users; ?></td><td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<div class="pie_float">
				<img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart1.png" />
			</div>
			<div class="clear"></div>
		<?php
		} else {
			echo '<div class="updated"><p>' . __('Currently have no subscribers.','knews') . '</p></div>';
		}

		/////////////////////////// Subscriptions / Blocks
		?>
		<h3><span><?php _e('Accumulated sign ups and unsubscribes from date:','knews'); echo ' ' . date("d-m-Y", $selected_min_date_chart) . ' '; _e('to date:','knews'); echo ' ' . date("d-m-Y", $selected_max_date_chart); ?></span></h3>
		<?php		

		$cols=21;
		$days = ($selected_max_date_chart - $selected_min_date_chart) / (60 * 60 * 24);
		
	if ($days < 1) {
		
		echo '<div class="error"><p>' . __('You must select a right date range','knews') . '</p></div>';

	} else {
			 
		if ($days < $cols) $cols=$days;
		
		$interval = intval(($days / $cols) * (60 * 60 * 24));
		
		$s_joined=0;
		$s_blocked=0;

		// Subscriptions previous
		$query='SELECT COUNT(joined) AS joined FROM ' . KNEWS_USERS . " WHERE joined < '" . $Knews_plugin->get_mysql_date($selected_min_date_chart) . "'";
		$results=$wpdb->get_results($query);
		$s_joined=$results[0]->joined;
		
		// Blocks previous
		$query='SELECT COUNT(id) AS blocked FROM ' . KNEWS_STATS . " WHERE what=3 AND date < '" . $Knews_plugin->get_mysql_date($selected_min_date_chart) . "'";
		$results=$wpdb->get_results($query);
		$s_blocked=$results[0]->blocked;

		for ($i=0; $i<$cols; $i++) {
			$date1 = $Knews_plugin->get_mysql_date($selected_min_date_chart + $i * $interval);
			$date2 = $Knews_plugin->get_mysql_date($selected_min_date_chart + ($i + 1) * $interval);
			if ($i%2 == 0) {
				$date_caption[] =  date("d/m/y", $selected_min_date_chart + ($i + 0.5) * $interval);
			} else {
				$date_caption[] = '';
			}
			
			// Subscriptions
			$query='SELECT COUNT(joined) AS joined FROM ' . KNEWS_USERS . " WHERE joined BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$s_joined += $results[0]->joined;
			$serie1[]=$s_joined;
			
			// Blocks
			$query='SELECT COUNT(id) AS blocked FROM ' . KNEWS_STATS . " WHERE what=3 AND date BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$s_blocked += $results[0]->blocked;
			$serie2[]=$s_blocked;

		}
		if ($Knews_plugin->get_custom_text('text_direction', get_bloginfo('language')) == 'ltr') {
			knews_drawLine ( array($serie1, $serie2, $date_caption), array (__('Users','knews'), __('Sign ups','knews'), __('Unsubscriptions','knews'), ''), 'chart2.png');
			knews_drawLine ( array($serie1, $date_caption), array (__('Sign ups','knews'), '', ''), 'chart3.png', 'softtones.txt', false);
			knews_drawLine ( array($serie2, $date_caption), array (__('Unsubscriptions','knews'), '', ''), 'chart4.png', 'softtones_2.txt', false);
		} else {
			knews_drawLine ( array($serie1, $serie2, $date_caption), array ('Users', 'Sign ups', 'Unsubscriptions', ''), 'chart2.png');
			knews_drawLine ( array($serie1, $date_caption), array ('Sign ups', '', ''), 'chart3.png', 'softtones.txt', false);
			knews_drawLine ( array($serie2, $date_caption), array ('Unsubscriptions', '', ''), 'chart4.png', 'softtones_2.txt', false);
		}
 ?>
<script type="text/javascript">
	function view_graph(n_custom, n_lang) {
		jQuery('div.pestanyes_'+n_custom+' a').removeClass('on');
		jQuery('a.link_'+n_custom+'_'+n_lang).addClass('on');
	
		target='div.pregunta_'+n_custom+' div.on';
		jQuery('div.pregunta_'+n_custom+' div').css('display','none').removeClass('on');
		jQuery('div.custom_lang_'+n_custom+'_'+n_lang).css('display','block').addClass('on');
	}
</script>
 		<div class="pestanyes pestanyes_1">
			<a onclick="view_graph(1,1); return false;" class="link_1_1 on" href="#"><?php _e('Sign Ups & Blocks','knews'); ?></a>
			<a onclick="view_graph(1,2); return false;" class="link_1_2" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_1.gif" width="13" height="13" alt="1" /> <?php _e('Sign ups','knews'); ?></a>
			<a onclick="view_graph(1,3); return false;" class="link_1_3" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_2.gif" width="13" height="13" alt="2" /> <?php _e('Unsubscriptions','knews'); ?></a>
		</div>
		<div class="pregunta pregunta_1">
			<div class="custom_lang_1_1 on"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart2.png" /></div>
			<div class="custom_lang_1_2"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart3.png" /></div>
			<div class="custom_lang_1_3"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart4.png" /></div>
		</div>
<?php

/* ------------------------------------------------------------------------------------------ */

		$enviaments_ok=0;
		$enviaments_error=0;
		$cant_read=0;
		$blocks=0;
		$clicks=0;

		for ($i=0; $i<$cols; $i++) {
			$date1 = $Knews_plugin->get_mysql_date($selected_min_date_chart + $i * $interval);
			$date2 = $Knews_plugin->get_mysql_date($selected_min_date_chart + ($i + 1) * $interval);
			
			// Enviaments OK i Error
			$query='SELECT users_ok, users_error FROM ' . KNEWS_NEWSLETTERS_SUBMITS . " WHERE blog_id=" . get_current_blog_id() . " AND start_time BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$e_ok=0;
			$e_err=0;
			foreach ($results as $r) {
				$e_ok += $r->users_ok;
				$e_err += $r->users_error;
			}
			$enviaments_ok += $e_ok;
			$enviaments_error += $e_err;
			$e_serie1[] = $e_ok;
			$e_serie2[] = $e_err;

			// Cant read
			$query='SELECT COUNT(*) AS cant_read FROM ' . KNEWS_STATS . " WHERE what = 2 AND date BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$cant_read += $results[0]->cant_read;
			$e_serie3[] = $results[0]->cant_read;

			// Block
			$query='SELECT COUNT(*) AS blocks FROM ' . KNEWS_STATS . " WHERE what = 3 AND date BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$blocks += $results[0]->blocks;
			$e_serie4[] = $results[0]->blocks;

			// Click
			$query='SELECT COUNT(*) AS clicks FROM ' . KNEWS_STATS . " WHERE what = 1 AND date BETWEEN '" . $date1 . "' AND '" . $date2 . "'";
			$results=$wpdb->get_results($query);
			$clicks += $results[0]->clicks;
			$e_serie5[] = $results[0]->clicks;
		}

		if ($Knews_plugin->get_custom_text('text_direction', get_bloginfo('language')) == 'ltr') {
			knews_drawLine ( array($e_serie1, $e_serie2, $e_serie3, $e_serie4, $e_serie5, $date_caption), array (__('Sendings','knews'), __('Sendings OK','knews'), __('Sendings Error','knews'), __('Cant read','knews'), __('Unsubscriptions','knews'), __('Total clicks','knews'), ''), 'chart5.png');
			knews_drawLine ( array($e_serie1, $date_caption), array (__('Sendings OK','knews'), '', ''), 'chart6.png', 'softtones.txt', false);
			knews_drawLine ( array($e_serie2, $date_caption), array (__('Sendings Error','knews'), '', ''), 'chart7.png', 'softtones_2.txt', false);
			knews_drawLine ( array($e_serie3, $date_caption), array (__('Cant read','knews'), '', ''), 'chart8.png', 'softtones_3.txt', false);
			knews_drawLine ( array($e_serie4, $date_caption), array (__('Unsubscriptions','knews'), '', ''), 'chart9.png', 'softtones_4.txt', false);
			knews_drawLine ( array($e_serie5, $date_caption), array (__('Total clicks','knews'), '', ''), 'chart10.png', 'softtones_5.txt', false);
		} else {
			knews_drawLine ( array($e_serie1, $e_serie2, $e_serie3, $e_serie4, $e_serie5, $date_caption), array ('Sendings', 'Sendings OK', 'Sendings Error', 'Cant read', 'Unsubscriptions', 'Total clicks', ''), 'chart5.png');
			knews_drawLine ( array($e_serie1, $date_caption), array ('Sendings OK', '', ''), 'chart6.png', 'softtones.txt', false);
			knews_drawLine ( array($e_serie2, $date_caption), array ('Sendings Error', '', ''), 'chart7.png', 'softtones_2.txt', false);
			knews_drawLine ( array($e_serie3, $date_caption), array ('Cant read', '', ''), 'chart8.png', 'softtones_3.txt', false);
			knews_drawLine ( array($e_serie4, $date_caption), array ('Unsubscriptions', '', ''), 'chart9.png', 'softtones_4.txt', false);
			knews_drawLine ( array($e_serie5, $date_caption), array ('Total clicks', '', ''), 'chart10.png', 'softtones_5.txt', false);			
		}
	?>
		<p>&nbsp;</p>
		<h3><span><?php _e('Newsletters & clicks from date:','knews'); echo ' ' . date("d-m-Y", $selected_min_date_chart) . ' '; _e('to date:','knews'); echo ' ' . date("d-m-Y", $selected_max_date_chart); ?></span></h3>
 		<div class="pestanyes pestanyes_2">
			<a onclick="view_graph(2,1); return false;" class="link_2_1 on" href="#"><?php _e('All','knews'); ?></a>
			<a onclick="view_graph(2,2); return false;" class="link_2_2" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_1.gif" width="13" height="13" alt="1" /> <?php _e('Sendings OK','knews'); ?></a>
			<a onclick="view_graph(2,3); return false;" class="link_2_3" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_2.gif" width="13" height="13" alt="2" /> <?php _e('Sendings Error','knews'); ?></a>
			<a onclick="view_graph(2,4); return false;" class="link_2_4" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_3.gif" width="13" height="13" alt="3" /> <?php _e('Cant read','knews'); ?></a>
			<a onclick="view_graph(2,5); return false;" class="link_2_5" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_4.gif" width="13" height="13" alt="4" /> <?php _e('Unsubscriptions','knews'); ?></a>
			<a onclick="view_graph(2,6); return false;" class="link_2_6" href="#"><img src="<?php echo KNEWS_URL; ?>/images/legend_5.gif" width="13" height="13" alt="5" /> <?php _e('Link clicks','knews'); ?></a>
		</div>
		<div class="pregunta pregunta_2">
			<div class="custom_lang_2_1 on"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart5.png" /></div>
			<div class="custom_lang_2_2"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart6.png" /></div>
			<div class="custom_lang_2_3"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart7.png" /></div>
			<div class="custom_lang_2_4"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart8.png" /></div>
			<div class="custom_lang_2_5"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart9.png" /></div>
			<div class="custom_lang_2_6"><img src="<?php echo get_admin_url(); ?>admin-ajax.php?action=knewsSafeDownload&file=chart10.png" /></div>
		</div>
		<div style="background:#fff; padding:20px 0 20px 100px;">
			<table border="0" cellpadding="0" cellspacing="0" width="650">
				<tr class="alt">
					<td><?php _e('Sendings OK','knews'); ?>:</td><td align="right"><?php echo $enviaments_ok; ?></td><td align="right"><?php echo safe_percent($enviaments_ok, $enviaments_ok + $enviaments_error); ?>%</td>
					<td width="20">&nbsp;</td>
					<td><?php _e('Cant read','knews'); ?>:</td><td align="right"><?php echo $cant_read; ?></td><td align="right"><?php echo safe_percent($cant_read, $enviaments_ok + $enviaments_error); ?>%</td>
				</tr>
				<tr>
					<td><?php _e('Sendings Error','knews'); ?>:</td><td align="right"><?php echo $enviaments_error; ?></td><td align="right"><?php echo safe_percent($enviaments_error, $enviaments_ok + $enviaments_error); ?>%</td>
					<td>&nbsp;</td>
					<td><?php _e('Unsubscriptions','knews'); ?>:</td><td align="right"><?php echo $blocks; ?></td><td align="right"><?php echo safe_percent($blocks, $enviaments_ok + $enviaments_error); ?>%</td>
				</tr>
				<tr class="alt">
					<td><?php _e('Total submits','knews'); ?>:</td><td align="right"><?php echo ($enviaments_ok + $enviaments_error); ?></td><td align="right">&nbsp;</td>
					<td>&nbsp;</td>
					<td><?php _e('Total clicks','knews'); ?>:</td><td align="right"><?php echo $clicks; ?></td><td align="right">&nbsp;</td>
				</tr>
			</table>
		</div>
<?php
	}
} else {
	printf( '<div class="error"><p>' . __('Error: make sure the directory %s exists and has write permissions (chmod 700).','knews') . '</p></div>', '/wp-content/plugins/knews/tmp');
}
?>
</div>
