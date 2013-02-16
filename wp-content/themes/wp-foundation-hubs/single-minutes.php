<?php get_header(); ?>
			
			<div id="content" class="clearfix">
			
            	<?php get_sidebar('sidebarleft'); // sidebar left ?>

				<div id="main" class="ten columns clearfix" role="main">

					<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class('clearfix'); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
						
						<header>
						
							<?php the_post_thumbnail( 'wpbs-featured' ); ?>
							
							<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>
							
							<p class="meta"><?php _e("Posted", "bonestheme"); ?> <time datetime="<?php echo the_time('Y-m-j'); ?>" pubdate><?php the_time('F jS, Y'); ?></time> 
								<?php _e("by", "bonestheme"); ?> <?php the_author_posts_link(); ?> 
								<span class="amp">|</span>  <?php echo the_terms( $post->ID, 'minutes-category', '', ', ', ' ' ); ?></p>
						
						</header> <!-- end article header -->
					
						<section class="post_content clearfix" itemprop="articleBody">

							
							<?php
							$meetingdate = get_post_meta($post->ID, 'be_meeting_date', true); //get meeting date value
							$meetingagenda = get_post_meta($post->ID, 'be_meeting_agenda', true); //get meeting agenda value
							$meetingattendees = get_post_meta($post->ID, 'be_meeting_attendees', true); //get meeting attendees value
							$meetingrecorder = get_post_meta($post->ID, 'be_meeting_minutes_takers', true);  //get meeting minute taker value
							?>

							<?php

							if (get_post_meta( get_the_ID() )){ //if any custom fields exist, do this
								echo '<ul class="meeting-minutes-meta post-id-' . get_the_ID() . '">';
							
								if (get_post_meta($post->ID, 'be_meeting_date')) {
									echo "<li class=\"meeting-date\">$meetingdate</li>";  //if there is a meeting date, display this
								}
								if (get_post_meta($post->ID, 'be_meeting_minutes_takers')) {
									echo "<li class=\"meeting-minut-taker\"><strong>Minutes Taker</strong>: $meetingrecorder</li>";  //if there is meeting minute taker, display this
								}
								if (get_post_meta($post->ID, 'be_meeting_attendees')) {
									echo "<li class=\"meeting-attendees\"><h2>Attendees</h2> <ul><li>$meetingattendees</li></ul></li>";  //if there is meeting minute taker, display this
								}
								if (get_post_meta($post->ID, 'be_meeting_agenda')) {
									echo "<li class=\"meeting-agenda\"><h2>Agenda</h2> <ul><li>$meetingagenda</li></ul></li>";  //if there is meeting minute taker, display this
								}
							echo '</ul>';
							}

							?>

							<?php the_content(); ?>

					
						</section> <!-- end article section -->
						
						<footer>
			
							<?php the_tags('<p class="tags"><span class="tags-title">Tags:</span> ', ' ', '</p>'); ?>
							
						</footer> <!-- end article footer -->
					
					</article> <!-- end article -->
					
					<?php comments_template(); ?>
					
					<?php endwhile; ?>			
					
					<?php else : ?>
					
					<article id="post-not-found">
					    <header>
					    	<h1>Not Found</h1>
					    </header>
					    <section class="post_content">
					    	<p>Sorry, but the requested resource was not found on this site.</p>
					    </section>
					    <footer>
					    </footer>
					</article>
					
					<?php endif; ?>
			
				</div> <!-- end #main -->
    
				<?php// get_sidebar(); // sidebar 1 ?>
    
			</div> <!-- end #content -->

<?php get_footer(); ?>