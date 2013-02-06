<?php
function pagination($paged, $maxPage, $link_params) {
	$link_params .= '&paged=';
	//$maxPage=ceil(count($users) / $results_per_page);
		
	if ($maxPage > 1) {
?>		
	<div class="tablenav bottom">

		<div class="tablenav-pages">
			<?php /*<span class="displaying-num"><?php echo count($users); ?> <?php _e('users','knews'); ?></span>*/ ?>
			<?php if ($paged > 1) { ?>
			<a href="<?php echo $link_params; ?>1" title="<?php _e('Go to first page','knews'); ?>" class="first-page">&laquo;</a>
			<a href="<?php echo $link_params . ($paged-1); ?>" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page">&lsaquo;</a>
			<?php } else { ?>
			<a href="<?php echo $link_params; ?>" title="<?php _e('Go to first page','knews'); ?>" class="first-page disabled">&laquo;</a>
			<a href="<?php echo $link_params; ?>" title="<?php _e('Go to previous page','knews'); ?>" class="prev-page disabled">&lsaquo;</a>
			<?php } ?>
			<span class="paging-input"><?php echo $paged; ?> <?php _e('of','knews'); ?> <span class="total-pages"><?php echo $maxPage; ?></span></span>
			<?php if ($maxPage > $paged) { ?>
			<a href="<?php echo $link_params . ($paged+1); ?>" title="<?php _e('Go to next page','knews'); ?>" class="next-page">&rsaquo;</a>
			<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to last page','knews'); ?>" class="last-page">&raquo;</a>
			<?php } else { ?>
			<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to next page','knews'); ?>" class="next-page disabled">&rsaquo;</a>
			<a href="<?php echo $link_params . $maxPage; ?>" title="<?php _e('Go to last page','knews'); ?>" class="last-page disabled">&raquo;</a>					
			<?php } ?>
		</div>
	<br class="clear">
	</div>
<?php
	}
}


global $Knews_plugin, $knewsOptions;

if ($Knews_plugin) {

	if (! $Knews_plugin->initialized) $Knews_plugin->init();

	require_once( KNEWS_DIR . '/includes/knews_util.php');

	$ajaxid = $Knews_plugin->get_safe('ajaxid', 0, 'int');
	if ($ajaxid != 0) {
		global $post;
		$post = get_post($ajaxid);
		setup_postdata($post);

		$excerpt = (string) get_the_excerpt();
		
		$text1 = get_the_content();
		$text = strip_shortcodes( $text1 );
		if ($knewsOptions['apply_filters_on']=='1') $text = apply_filters('the_content', $text);
		$text = iterative_extract_code('<script', '</script>', $text, true);
		$text = iterative_extract_code('<fb:like', '</fb:like>', $text, true);
		$text = str_replace(']]>', ']]>', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			//array_push($words, '[...]');
			$text = implode(' ', $words);
		}
		$text = nl2br($text);

		$jsondata['permalink'] = get_permalink($ajaxid);
 	    $jsondata['title'] = get_the_title();
 	    $jsondata['excerpt'] = ($excerpt=='') ? $text : $excerpt;
		if ($knewsOptions['apply_filters_on']=='1') $text1 = apply_filters( 'the_content', $text1 );
		$text1 = iterative_extract_code('<script', '</script>', $text1, true);
		$text1 = iterative_extract_code('<fb:like', '</fb:like>', $text1, true);
 	    $jsondata['content'] = $text1;


		if (has_post_thumbnail( $post->ID ) && $Knews_plugin->im_pro()) {
			$jsondata['image'] = knews_get_image_path();
		} else {
			$jsondata['image'] = '';
		}

 		echo json_encode($jsondata);
		
	} else {
		
		$languages=$Knews_plugin->getLangs();
		$lang = $Knews_plugin->get_safe('lang');
		$s = $Knews_plugin->get_safe('s');
		$type = $Knews_plugin->get_safe('type','post');
		$cat = $Knews_plugin->get_safe('cat', 0, 'int');
		$orderbt = $Knews_plugin->get_safe('orderby');
		$order = $Knews_plugin->get_safe('order', 'asc');
		$paged = $Knews_plugin->get_safe('paged', 1, 'int');
		
		//$url_base =  KNEWS_URL . '/direct/select_post.php';
		$url_base =  get_admin_url() . 'admin-ajax.php';

		if (KNEWS_MULTILANGUAGE && $lang != '' && $knewsOptions['multilanguage_knews']=='wpml') {
			global $sitepress;
			$class_methods = get_class_methods($sitepress);
			if (in_array('switch_lang', $class_methods)) {
				$sitepress->switch_lang($lang);
			} else {
				echo "<p><strong>Please, upgrade WPML</strong></p>";
			}
		}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Select Post</title>
<style type="text/css">
	html,body{ width:100%; height:100%;}
	body, div, dl, dt, dd, ul, ol, li, h1, h2, h3, h4, h5, h6, pre, form, fieldset, input, textarea, p, blockquote, th, td, input, hr { 
		margin:0px; 
		padding:0px; 
		border:none;
		font-family:Verdana, Geneva, sans-serif;
		font-size:12px;
		line-height:100%;
	}
	a {
		text-decoration:none;
		color:#000;
	}
	a:hover {
		color:#d54e21;
	}
	div.content {
		padding:10px 20px 0 20px;
	}
	div.pestanyes {
		background:#fff;
		padding-left:15px;
		display:block;
		height:25px;
	}
	div.pestanyes a {
		border-top-left-radius:3px;
		border-top-right-radius:3px;
		color:#aaa;
		display:inline-block;
		height:20px;
		padding:4px 14px 0 14px;
		border:#dfdfdf 1px solid;
		text-decoration:none;
		margin-left:5px;
		font-family:Georgia,"Times New Roman","Bitstream Charter",Times,serif;
		font-size:14px;
	}
	div.pestanyes a:hover {
		color:#d54e21;
	}
	div.pestanyes a.on {
		color:#000;
		background:#f9f9f9;
		cursor:default;
		border-bottom:#f9f9f9 1px solid;
	}

	p.langs_selector a {
		color:#21759B;
	}
	p.langs_selector a:hover {
		color:#d54e21;
	}
	p {
		padding-bottom:10px;
	}
	div.tablenav,
	div.filters {
		border-top:#dfdfdf 1px solid;
		border-bottom:#dfdfdf 1px solid;
		padding:10px 10px 0 10px;
		height:30px;
		background:#f9f9f9;
		background-image:-moz-linear-gradient(center top , #F9F9F9, #ECECEC);
		margin-bottom:20px;
	}
	input.button {
		border:#888 1px solid;
		background:#fff;
		border-radius:11px;
		cursor:pointer;
		padding:3px 11px;
	}
	input.button:hover {
		border-color:#000;
	}
	input.texte {
		padding:3px;
		border:#DFDFDF 1px solid;
		border-radius:3px;
		margin-right:5px;
	}
	div.left_side {
		width:290px;
		position:absolute;
	}
	div.right_side {
		float:right;
	}
	select {
		border:#DFDFDF 1px solid;
		padding:1px;
	}
	div.tablenav-pages {
		text-align:right;
	}
	div.bottom {
		height:auto;
		margin-top:10px;
	}
</style>

<script type="text/javascript">
function select_post(n, lang) {
	parent.CallBackPost(n, lang);
}
</script>
</head>

<body>
<div class="content">
	<p><strong><?php _e('Select the post to insert in the newsletter','knews'); ?>:</strong></p>
	<?php
		foreach ($languages as $l) {
			if ($l['active']==1 && $lang=='') $lang = $l['language_code'];
		}
		
		//Languages
		if (count($languages) > 1) {
			echo '<p class="langs_selector">';
			$first=true;
			foreach ($languages as $l) {
				if (!$first) echo ' | ';
				$first=false;
				if ($lang==$l['language_code']) echo '<strong>';
				echo '<a href="' . $url_base . '?action=knewsSelPost&lang=' . $l['language_code'] . '&type=' . $type  . '&paged=' . $paged . '">' . $l['native_name'] . '</a>';
				if ($lang==$l['language_code']) echo '</strong>';
			}
			echo '</p>';
		}
		//$url_base .= '&lang=' . $lang;
		
		//Posts / Pages
		echo '<div class="pestanyes">';
		echo (($type=='post') ? '<a class="on"' : '<a') . ' href="' . $url_base . '?action=knewsSelPost&type=post&lang=' . $lang . '">' . __('Posts','knews') . '</a>';
		echo (($type=='page') ? '<a class="on"' : '<a') . ' href="' . $url_base . '?action=knewsSelPost&type=page&lang=' . $lang . '">' . __('Pages','knews') . '</a>';
		
		if ($Knews_plugin->im_pro()) {
			$post_types = $Knews_plugin->getCustomPostTypes();
			foreach ($post_types as $pt) {
				echo (($type==$pt) ? '<a class="on"' : '<a') . ' href="' . $url_base . '?action=knewsSelPost&type=' . urlencode($pt) . '&lang=' . $lang . '">' . $pt . '</a>';		
			}
		}
		echo '</div>';
		
		echo '<div class="filters">';
		//Filters
		if ($type=='post') {
			echo '<div class="left_side">';
			$cats = get_categories(array('hide_empty'=>0));
			if (count($cats)>1) {
				echo '<form action="' . $url_base . '" method="get">';
				echo '<input type="hidden" name="lang" value="' . $lang . '">';
				echo '<input type="hidden" name="type" value="' . $type . '">';
				echo '<input type="hidden" name="action" value="knewsSelPost">';
				echo '<select name="cat" id="cat">';
				echo '<option value="0">' . __('All categories','knews') . '</option>';
				foreach ($cats as $c) {
					echo '<option value="' . $c->cat_ID . '"' . (($c->cat_ID==$cat) ? ' selected="selected"' : '') . '>' . $c->name . '</option>';
				}
				echo '</select> <input type="submit" value="' . __('Filter','knews') . '" class="button" />';
				echo '</form>';
			}
			echo '</div>';
		}
		
		//Search
		echo '<div class="right_side">';
		echo '<form action="' . $url_base . '" method="get">';
		echo '<input type="hidden" name="lang" value="' . $lang . '">';
		echo '<input type="hidden" name="type" value="' . $type . '">';
		echo '<input type="hidden" name="action" value="knewsSelPost">';
		echo '<input type="text" name="s" value="" class="texte">';
		echo '<input type="submit" value="' . __('Search','knews') . '" class="button" />';
		echo '</form>';
		echo '</div>';
		
		echo '</div>';
		/*function new_excerpt_more($more) {
			return '[...]';
		}
		add_filter('excerpt_more', 'new_excerpt_more');*/
	
		$args = array('posts_per_page' =>10, 'paged' => $paged, 'post_type' => $type, 'post_status' => 'publish');
	
		if ($cat != 0) $args['cat'] = $cat;
		if ($s != '') $args['s'] = $s;
	
		$myposts = query_posts($args);
		
		//print_r($myposts);
		
		global $post;
		foreach($myposts as $post) {
			setup_postdata($post);
			echo '<p><a href="#" onclick="select_post(' . $post->ID . ',\'' . $lang . '\')"><strong>';
			$t=get_the_title();
			if ($t=='') {
				echo '{no title}';
			} else {
				echo $t;
			}
			echo '</strong></a><br>';
			echo get_the_excerpt();
			echo '</p>';
		}
	 global $wp_query; 
	 pagination($paged, ceil($wp_query->found_posts/ 10), $url_base . '?action=knewsSelPost&lang=' . $l['language_code'] . '&type=' . $type  . '&cat=' . $cat . '&orderbt=' . $orderbt . '&order=' . $order);
	 ?>
	 </div>
</body>
</html>
<?php 
	}
}
die();
?>
