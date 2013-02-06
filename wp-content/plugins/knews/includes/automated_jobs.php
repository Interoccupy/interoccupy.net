<?php
global $knewsOptions, $Knews_plugin, $wpdb, $knews_aj_look_date;

if ($Knews_plugin) {
	
	add_filter('posts_where', 'knews_aj_posts_where' );
	
	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	if ($knewsOptions['write_logs']=='yes') {
		$hour = date('d-m-Y_H-i', current_time('timestamp'));
		@$fp = fopen(KNEWS_DIR . '/tmp/automated_' . $hour . '.log', 'a');
		$hour = date('d/m/Y H:i', current_time('timestamp'));
		knews_debug('Knews automated jobs script, started at: ' . $hour . "\r\n");
	} else {
		$fp=false;
	}
	
	$mysqldate = $Knews_plugin->get_mysql_date();
	
	$query = "SELECT * FROM " . KNEWS_AUTOMATED . " WHERE paused=0";
	$automated_jobs = $wpdb->get_results( $query );

	if (KNEWS_MULTILANGUAGE) {
		global $sitepress;
		$save_lang = $sitepress->get_current_language();
	}
	
	foreach ($automated_jobs as $aj) {
		knews_debug("\r\n" . 'Automated job (' . $aj->name . ')' . "\r\n");
		$doit=false;
		
		//$sql="SELECT wp_posts.ID from wp_posts, wp_postmeta WHERE wp_posts.post_status='publish' AND wp_posts.post_type='post' AND wp_posts.post_modified > '" . $aj->last_run . "' AND wp_posts.ID=wp_postmeta.post_id AND wp_postmeta.meta_key='_knews_automated' AND wp_postmeta.meta_value='1' AND NOT EXISTS ( SELECT * FROM wp_knewsautomatedposts WHERE wp_knewsautomatedposts.id_automated=" . $aj->id . " AND wp_knewsautomatedposts.id_post=wp_posts.ID )";
		
		if (KNEWS_MULTILANGUAGE) {
			$sitepress->switch_lang($aj->lang);
		}

		if ($aj->every_mode ==1) {
			$knews_aj_look_date = $aj->last_run;
			$pend_posts = knews_search_posts($aj->id, $aj->every_posts);
			if (count($pend_posts) == $aj->every_posts) $doit = true;
			knews_debug('- posts to send: ' .count($pend_posts) . "\r\n");
	
		} else {
			$time_lapsus = time();
			if ($aj->every_time == 1) $time_lapsus = $time_lapsus - 24 * 60 * 60; //Daily
			if ($aj->every_time == 2) $time_lapsus = $time_lapsus - 7 * 24 * 60 * 60; //Weekly
			if ($aj->every_time == 3) $time_lapsus = $time_lapsus - 14 * 24 * 60 * 60; //2 weeks
			if ($aj->every_time == 4) $time_lapsus = $time_lapsus - 30 * 24 * 60 * 60; //Monthly
			if ($aj->every_time == 5) $time_lapsus = $time_lapsus - 60 * 24 * 60 * 60; //2 Monthly
			if ($aj->every_time == 6) $time_lapsus = $time_lapsus - 90 * 24 * 60 * 60; //3 Monthly
			
			if ($Knews_plugin->sql2time($aj->last_run) < $time_lapsus) {
				if ($aj->every_time == 1) {
					$doit=true;
				} else {
					$daynumber=date('w');
					if ($daynumber==0) $daynumber=7;
					if ($daynumber == $aj->what_dayweek) $doit=true;
					knews_debug('- must submit dayweek number: ' . $daynumber . ' and today is: ' . $aj->what_dayweek . ' dayweek number. ' . "\r\n");
				}
			}
			
			if ($doit) {
				$knews_aj_look_date = $aj->last_run;
				$pend_posts = knews_search_posts($aj->id, -1);
				knews_debug('- posts to send: ' .count($pend_posts) . "\r\n");
			}
		}
		
		if ($doit && count($pend_posts) != 0) {
			
			$query="SELECT * FROM " . KNEWS_NEWSLETTERS . " WHERE id=" . $aj->newsletter_id;
			$news = $wpdb->get_results( $query );
			
			$rightnews = false;
			if (count($news) != 0) $rightnews=true;
	
			if ($rightnews) {
				knews_debug('- there is a newsletter to build: ' . $news[0]->name . "\r\n");
				require (KNEWS_DIR . '/includes/knews_util.php');
				
				if (!$Knews_plugin->im_pro()) $news_id = knews_create_news($aj, $pend_posts, $news, $fp, false, 0);
			}
		}
	}
	remove_filter('posts_where', 'knews_aj_posts_where' );
	if (KNEWS_MULTILANGUAGE) $sitepress->switch_lang($save_lang);
}

function knews_debug($message) {
	
	global $fp, $Knews_plugin;
	
	if ($fp) fwrite($fp, $message . "\r\n");
	if ($Knews_plugin->get_safe('manual','0')!=0) echo $message . '<br>' . "\r\n";
	
}

function knews_search_posts($id_automated, $max) {
	global $wpdb, $Knews_plugin, $knewsOptions, $post;
	
	//$look_posts = get_posts( array('numberposts'=> -1, 'suppress_filters'=>0, 'order'=>'ASC'));
	

	$cpt=array();
	if ($Knews_plugin->im_pro()) {
		$post_types = $Knews_plugin->getCustomPostTypes();
		foreach ($post_types as $pt) {
			$cpt[]=$pt;
		}
	}
	$cpt[]='post';
	
	$look_posts = new WP_Query(array(
		'post_type' => $cpt,
		'posts_per_page' => -1
	));
	
	$pend_posts = array();
	while ($look_posts->have_posts()) {
		$look_posts->the_post();
		$query = 'SELECT * FROM ' . KNEWS_AUTOMATED_POSTS . ' WHERE id_automated=' . $id_automated . ' AND id_post=' . $post->ID;
		$result = $wpdb->get_results($query);
		if (count($result) == 0) {
			$include_option=get_post_meta($post->ID, '_knews_automated', true);
			if ($include_option=='1' || ($include_option=='' && $knewsOptions['def_autom_post']==1)) $pend_posts[] = $post;
		}
		if (count($pend_posts) == $max) break;
	}
	return $pend_posts;
}

function knews_create_news($aj, $pend_posts, $news, $fp, $mobile, $mobile_news_id) {
	
	global $Knews_plugin, $knewsOptions, $wpdb;
	
	$news_mod = $news[0]->html_mailing;
	
	//Cut the newsletter into modules
	$news_mod = explode('<!--[start module]-->', $news_mod);
	
	$news_mod2=array();
	$first=true;
	foreach ($news_mod as $nm) {
		if ($first) {
			$first=false;
			$news_mod2[]=$nm;
		} else {
			$cut_nm = explode('<!--[end module]-->', $nm);
			$news_mod2[]=$cut_nm[0];
			$news_mod2[]=$cut_nm[1];
		}
	}
	$news_mod_map=array();
	$news_mod_map_duplicated=array();
	$total_posts=0;
	
	foreach ($news_mod2 as $nm) {
		$found=true;
		$n=1;
		while ($found) {
			$found=false;
			if (strpos($nm, '%the_title_' . $n . '%') !== false || strpos($nm, '%the_excerpt_' . $n . '%') !== false || strpos($nm, '%the_permalink_' . $n . '%') !== false || strpos($nm, '%the_content_' . $n . '%') !== false) {
				$found=true; $n++;
			}
		}
		$news_mod_map[]=$n-1;
		$news_mod_map_duplicated[]=false;
		$total_posts=$total_posts+$n-1;
	}
	
	/*for ($a=1; $a<10; $a++) {
		$news_mod = str_replace('%the_permalink_' . $a . '%', '%the_permalink%', $news_mod);
		$news_mod = str_replace('%the_title_' . $a . '%', '%the_title%', $news_mod);
		$news_mod = str_replace('%the_excerpt_' . $a . '%', '%the_excerpt%', $news_mod);
		$news_mod = str_replace('%the_content_' . $a . '%', '%the_content%', $news_mod);
	}*/
	
	$s=0;
	while ($total_posts < count($pend_posts) && $total_posts !=0) {

		if ($news_mod_map[$s] != 0 && !$news_mod_map_duplicated[$s]) {
			knews_debug('- enlarging the newsletter, have ' . $total_posts . ' and must have ' . count($pend_posts) . "\r\n");
			array_splice( $news_mod2, $s+1, 0, array('</td></tr><tr class="droppable"><td>' . $news_mod2[$s]));
			array_splice( $news_mod_map, $s+1, 0, array($news_mod_map[$s]));
			array_splice( $news_mod_map_duplicated, $s+1, 0, array(true));
			$total_posts=$total_posts+$news_mod_map[$s];
			knews_debug('- module duplicated, news now can contain ' . $total_posts . " posts inside\r\n");
			$s++;
		}
		$s++;
		if ($s >= count($news_mod_map)) $s=0;
	}					
	
	if ($total_posts == count($pend_posts)) {

		$most_recent=0;
		foreach ($pend_posts as $pp) {
			knews_debug('- including post: ' . $pp->post_title . "\r\n");
			
			$content = $pp->post_content;

			$excerpt = strip_shortcodes( $content );
			if ($knewsOptions['apply_filters_on']=='1') $excerpt = apply_filters('the_content', $excerpt);
			$excerpt = iterative_extract_code('<script', '</script>', $excerpt, true);
			$excerpt = iterative_extract_code('<fb:like', '</fb:like>', $excerpt, true);
			$excerpt = str_replace(']]>', ']]>', $excerpt);
			$excerpt = strip_tags($excerpt);
			$excerpt_length = apply_filters('excerpt_length', 55);
			$words = explode(' ', $excerpt, $excerpt_length + 1);
			if (count($words) > $excerpt_length) {
				array_pop($words);
				//array_push($words, '[...]');
				$excerpt = implode(' ', $words);
			}
			$excerpt = nl2br($excerpt);
			if ($knewsOptions['apply_filters_on']=='1') $content = apply_filters('the_content', $content);
			
			$title = $pp->post_title;
			$permalink = get_permalink($pp->ID);
			
			$s=0;
			while ($news_mod_map[$s]==0 && $s < count($news_mod_map)) { $s++; }
			
			$n=1;
			$found=false;
			while (!$found && $n<20) {
				if (strpos($news_mod2[$s], '%the_title_' . $n . '%') !== false || strpos($news_mod2[$s], '%the_excerpt_' . $n . '%') !== false || strpos($news_mod2[$s], '%the_permalink_' . $n . '%') !== false) {
					$found=true;
				} else {
					$n++;
				}
			}
			
			if ($found) {
				$news_mod_map[$s]--;
				$news_mod2[$s] = str_replace('%the_permalink_' . $n . '%', $permalink, $news_mod2[$s]);
				$news_mod2[$s] = str_replace('%the_title_' . $n . '%', $title, $news_mod2[$s]);
				$news_mod2[$s] = str_replace('%the_excerpt_' . $n . '%', $excerpt, $news_mod2[$s]);
				$news_mod2[$s] = str_replace('%the_content_' . $n . '%', $content, $news_mod2[$s]);
				
				knews_debug('- included: ' . $pp->post_title . "\r\n");
				
				if ($most_recent==0) $most_recent = $Knews_plugin->sql2time($pp->post_modified);
				if ($most_recent < $Knews_plugin->sql2time($pp->post_modified)) $most_recent = $Knews_plugin->sql2time($pp->post_modified);
				$query = "INSERT INTO " . KNEWS_AUTOMATED_POSTS . " (id_automated, id_post, when_scheduled, id_news) VALUES (" . $aj->id . ", ". $pp->ID . ", '" . $Knews_plugin->get_mysql_date() . "', " . "0)";
				$result=$wpdb->query($query);
			}
		}
		if ($most_recent != 0) {
			$news_mod='';
			foreach ($news_mod2 as $nm) {
				$news_mod.=$nm;
			}
			
			$news_mod=str_replace('<span class="chooser"></span>', '', $news_mod);
			
			knews_debug('- saving the created newsletter' . "\r\n");
			$sql = "INSERT INTO " . KNEWS_NEWSLETTERS . "(name, subject, created, modified, template, html_mailing, html_head, html_modules, html_container, lang, automated, mobile, id_mobile) VALUES ('" . mysql_real_escape_string($news[0]->name) . " (" . date('d/m/Y') . ")', '" . mysql_real_escape_string($news[0]->subject) . "', '" . $Knews_plugin->get_mysql_date() . "', '" . $Knews_plugin->get_mysql_date() . "','" . $news[0]->template . "','" . mysql_real_escape_string($news_mod) . "','" . mysql_real_escape_string($news[0]->html_head) . "','" . mysql_real_escape_string($news[0]->html_modules) . "','" . mysql_real_escape_string($news[0]->html_container) . "', '" . $news[0]->lang . "', 1, " . (($mobile) ? '1' : '0') . ", " . $mobile_news_id . ")";
			$results = $wpdb->query($sql);				
			$id_newsletter = $wpdb->insert_id; $id_newsletter2=mysql_insert_id(); if ($id_newsletter==0) $id_newsletter=$id_newsletter2;

			$query = "UPDATE " . KNEWS_AUTOMATED_POSTS . " SET id_news=" . $id_newsletter . " WHERE id_news=0";
			$results = $wpdb->query($query);				

			$query = "UPDATE " . KNEWS_AUTOMATED . " SET last_run='" . $Knews_plugin->get_mysql_date($most_recent) . "' WHERE id=" . $aj->id . " ";
			$results = $wpdb->query($query);				
			
			if ($mobile) return $id_newsletter;
			
			knews_debug('- scheduling the submit' . "\r\n");
			
			$query = "SELECT DISTINCT(" . KNEWS_USERS . ".id) FROM " . KNEWS_USERS . ", " . KNEWS_USERS_PER_LISTS . " WHERE " . KNEWS_USERS . ".id=" . KNEWS_USERS_PER_LISTS . ".id_user AND " . KNEWS_USERS . ".state='2' AND " . KNEWS_USERS_PER_LISTS . ".id_list=" . $aj->target_id;

			$batch_opts = array (
				'minute' => date("i"),
				'hour' => date("H"),
				'day' => date("d"),
				'month' => date("m"),
				'year' => date("Y"),
				'paused' => (($aj->auto==1) ? 0 : 1),
				'priority' => 4,
				'strict_control' => '',
				'emails_at_once' => $aj->emails_at_once
			);
								
			require( KNEWS_DIR . "/includes/submit_batch.php");

			knews_debug('- all done' . "\r\n");
		}
	} else {
		knews_debug('- the newsletter kept more posts of wich there to submit, wait for more' . "\r\n");
	}				
}
?>