<?php

class WYSIWYG_Widgets
{

	public function init()
	{
		add_action('init', array($this, 'on_init_action'));
		add_action( 'widgets_init', array($this, 'register_widget'));
		add_action( 'add_meta_boxes', array($this, 'add_meta_box' ) );
	}

	public function on_init_action()
	{
		$labels = array(
		    'name' => 'WYSIWYG Widget',
		    'singular_name' => 'WYSIWYG Widget',
		    'add_new' => 'Add New',
		    'add_new_item' => 'Add New WYSIWYG Widget',
		    'edit_item' => 'Edit Widget',
		    'new_item' => 'New Widget',
		    'all_items' => 'All Widgets',
		    'view_item' => 'View  Widget',
		    'search_items' => 'Search Widgets',
		    'not_found' =>  'No widgets found',
		    'not_found_in_trash' => 'No widgets found in Trash', 
		    'parent_item_colon' => '',
		    'menu_name' => ' WYSIWYG Widgets'
		  );
		$args = array(
			'public' => true,
			'publicly_queryable' => false,
			'show_in_nav_menus' => false,
			'exclude_from_search' => true,
			'labels' => $labels,
			'has_archive' => false,
			'supports' => array('title', 'editor')
		);
   		register_post_type( 'wysiwyg-widget', $args );

	}

	public function add_meta_box()
	{
		add_meta_box( 
        'wysiwyg-widget-donate-box',
	        'Donate a token of your appreciation',
	        array($this, 'meta_donate_box'),
	        'wysiwyg-widget',
	        'side',
            'low'
	    );
	}

	public function register_widget()
	{
		register_widget('WYSIWYG_Widgets_Widget');  
	}

	public function meta_donate_box($post)
	{
		?>
			<p style="border: 2px solid green; font-weight:bold; background: #CFC; padding:5px; ">I spent countless hours developing (and offering support) for this plugin for FREE. If you like it, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>
		<?php
	}
}