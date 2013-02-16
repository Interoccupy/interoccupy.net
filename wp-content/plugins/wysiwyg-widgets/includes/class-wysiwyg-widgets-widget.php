<?php

class WYSIWYG_Widgets_Widget extends WP_Widget
{
	public function __construct() {
		parent::__construct(
	 		'wysiwyg_widgets_widget', // Base ID
			'WYSIWYG Widget', // Name
			array( 'description' => 'Lets you select one of your "WYSIWYG Widgets" and show it in a widget area.' ) // Args
		);
	}

 	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$id = $instance['wysiwyg-widget-id'];

		$title = apply_filters( 'widget_title', $instance['title'] );
		$post = get_post($id);

		echo $before_widget;
		if(!empty($title)) { echo $before_title . $title . $after_title; }

		if($post && !empty($id)) {
			$content = apply_filters('the_content', $post->post_content);
			echo $content;		
		} else {
			if(current_user_can('manage_options')) { ?>
				<p style="color:red;">
					<strong>ADMINS ONLY NOTICE:</strong>
					<?php if(empty($id)) { ?>
						Please select a WYSIWYG Widget post to show in this area.
					<?php } else { ?>
						No post found with ID <?php echo $id; ?>, please select an existing WYSIWYG Widget post.
					<?php } ?>
				</p>
				<?php }
		}

		echo $after_widget;
		
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['wysiwyg-widget-id'] = $new_instance['wysiwyg-widget-id'];

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		$posts = get_posts(array(
			'post_type' => 'wysiwyg-widget',
			'numberposts' => -1
		));

		$title = isset($instance['title']) ? $instance['title'] : 'Just another WYSIWYG Widget';
		$selected_widget_id = (isset($instance['wysiwyg-widget-id'])) ? $instance['wysiwyg-widget-id'] : 0;

		if(empty($posts)) { ?>

			<p>You should first create at least 1 WYSIWYG Widget <a href="<?php echo admin_url('edit.php?post_type=wysiwyg-widget'); ?>">here</a>.</p>

		<?php
		} else { ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'wysiwyg-widget-id' ); ?>"><?php _e( 'Widget Content:' ); ?></label> 
			<select id="<?php echo $this->get_field_id('wysiwyg-widget-id'); ?>" name="<?php echo $this->get_field_name( 'wysiwyg-widget-id' ); ?>">
				<option value="0">Select a WYSIWYG Widget..</option>
				<?php foreach($posts as $p) { ?>
					<option value="<?php echo $p->ID; ?>" <?php if($p->ID == $selected_widget_id) echo 'selected="selected"'; ?>><?php echo $p->post_title; ?></option>
				<?php } ?>
			</select>
		</p>
		<?php 
		}
		?>
				<p style="border: 2px solid green; font-weight:bold; background: #CFC; padding:5px; ">I spent countless hours developing (and offering support) for this plugin for FREE. If you like it, consider <a href="http://dannyvankooten.com/donate/">donating $10, $20 or $50</a> as a token of your appreciation.</p>       
		<?php
	}

}